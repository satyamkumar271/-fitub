<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserApproved;
use App\Mail\UserKycRejected;
use App\Models\User;
use App\Models\Payment;
use App\Models\Inquiry;
use App\Models\InquiryMessage;
use App\Models\InquiryReport;
use App\Models\SupportTicket;
use App\Models\UnlockCreditLog;
use App\Models\Subscription;
use App\Models\InquiryBlock;
use Illuminate\Http\Request;
use App\Models\Trainer;
use App\Models\Gym;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    public function dashboard()
{
    // ===== Main Stats =====
    $stats = [
        'totalUsers'      => User::count(),
        'pendingUsers'    => User::where('status', 'pending')->count(),

        'totalTrainers'   => Trainer::count(),
        'totalGyms'       => Gym::count(),
        'totalCustomers'  => Customer::count(),

        'totalRevenue'    => Payment::where('status', 'paid')->sum('amount'),

        'activeUsers'     => User::where('created_at', '>=', now()->subDays(7))->count(),
    ];

    // ===== Quick Stats =====
    // ===== Quick Stats =====
$quickStats = [
    'pendingKyc' => User::whereIn('user_type', ['trainer','gymowner'])
                        ->where('kyc_status', 'pending')
                        ->count(),

    'registrationIssues' => User::where('status', 'warning')->count(),

    'pendingInquiries' => Inquiry::where('status', 'pending')->count(),

    'reportsCount' => InquiryReport::where('status', 'pending')->count(),

    'supportTickets' => SupportTicket::where('status', 'pending')->count(),
];

    // ===== Recent Data =====
    $recentUsers = \App\Models\User::latest()->take(5)->get();

    $recentPayments = \App\Models\Payment::with('user')
        ->where('status', 'paid')
        ->latest()
        ->take(5)
        ->get();

    return view('admin.dashboard', compact(
        'stats',
        'quickStats',
        'recentUsers',
        'recentPayments'
    ));
}

    public function analytics(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $range = (string) $request->query('range', '');
        if ($range === '' && ($from || $to)) {
            $range = 'custom';
        }
        if ($range === '') {
            $range = 'this_month';
        }

        $fromDate = null;
        $toDate = null;

        if ($range === 'last_month') {
            $fromDate = now()->subMonthNoOverflow()->startOfMonth()->startOfDay();
            $toDate = now()->subMonthNoOverflow()->endOfMonth()->endOfDay();
        } elseif ($range === 'last_3_months') {
            $fromDate = now()->subMonthsNoOverflow(2)->startOfMonth()->startOfDay();
            $toDate = now()->endOfMonth()->endOfDay();
        } elseif ($range === 'custom') {
            try {
                $fromDate = $from ? \Carbon\Carbon::parse($from)->startOfDay() : now()->startOfMonth()->startOfDay();
            } catch (\Throwable $e) {
                $fromDate = now()->startOfMonth()->startOfDay();
            }
            try {
                $toDate = $to ? \Carbon\Carbon::parse($to)->endOfDay() : now()->endOfDay();
            } catch (\Throwable $e) {
                $toDate = now()->endOfDay();
            }
        } else {
            $range = 'this_month';
            $fromDate = now()->startOfMonth()->startOfDay();
            $toDate = now()->endOfMonth()->endOfDay();
        }

        if ($fromDate->gt($toDate)) {
            [$fromDate, $toDate] = [$toDate->copy()->startOfDay(), $fromDate->copy()->endOfDay()];
        }

        $rangeLabel = match ($range) {
            'last_month' => 'Last Month',
            'last_3_months' => 'Last 3 Months',
            'custom' => 'Custom Range',
            default => 'This Month',
        };

        $rangeLabelLong = $fromDate->format('d M Y') . ' - ' . $toDate->format('d M Y');
        $rangeMonthLabel = $fromDate->isSameMonth($toDate) ? $fromDate->format('F Y') : $rangeLabelLong;

        $paymentsBase = Payment::query()
            ->whereBetween('created_at', [$fromDate, $toDate]);

        $paymentsPaid = (clone $paymentsBase)->where('status', 'paid');

        $paymentsByPlan = (clone $paymentsPaid)
            ->select('plan_name', DB::raw('COUNT(*) as paid_count'), DB::raw('SUM(amount) as revenue'))
            ->groupBy('plan_name')
            ->orderByDesc('revenue')
            ->get();

        $gstCollected = (float) ((clone $paymentsPaid)->sum('gst_amount') ?? 0);
        $baseCollected = (float) ((clone $paymentsPaid)->sum('base_amount') ?? 0);

        $paymentStatusCounts = (clone $paymentsBase)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->all();

        $planRevenueByKey = (clone $paymentsPaid)
            ->select('plan_name', DB::raw('SUM(amount) as revenue'))
            ->groupBy('plan_name')
            ->pluck('revenue', 'plan_name')
            ->all();

        $planPriceBreakdown = [
            '199' => (float) ($planRevenueByKey['starter'] ?? 0),
            '1299' => (float) ($planRevenueByKey['pro'] ?? 0),
            '4999' => (float) ($planRevenueByKey['business'] ?? 0),
        ];

        $driver = DB::connection()->getDriverName();
        $monthExpr = $driver === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $monthlyRevenueRows = (clone $paymentsPaid)
            ->selectRaw($monthExpr . " as ym, SUM(amount) as revenue, SUM(COALESCE(gst_amount,0)) as gst")
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $monthlyLabels = [];
        $monthlyRevenue = [];
        $monthlyGst = [];
        foreach ($monthlyRevenueRows as $r) {
            $ym = (string) $r->ym;
            $label = $ym;
            try {
                $label = \Carbon\Carbon::createFromFormat('Y-m', $ym)->format('M Y');
            } catch (\Throwable $e) {
                // keep raw ym
            }
            $monthlyLabels[] = $label;
            $monthlyRevenue[] = (float) ($r->revenue ?? 0);
            $monthlyGst[] = (float) ($r->gst ?? 0);
        }

        $thisMonthStart = now()->startOfMonth();
        $thisMonthEnd = now()->endOfMonth();
        $lastMonthStart = now()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd = now()->subMonthNoOverflow()->endOfMonth();

        $thisMonthRevenue = (float) Payment::where('status', 'paid')
            ->whereBetween('created_at', [$thisMonthStart, $thisMonthEnd])
            ->sum('amount');
        $lastMonthRevenue = (float) Payment::where('status', 'paid')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');

        $growth = null;
        if ($lastMonthRevenue > 0) {
            $growth = (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        }

        $paymentStats = [
            'revenue' => (float) ((clone $paymentsPaid)->sum('amount') ?? 0),
            'base' => $baseCollected,
            'gst' => $gstCollected,
            'paid' => (int) ($paymentStatusCounts['paid'] ?? 0),
            'created' => (int) ($paymentStatusCounts['created'] ?? 0),
            'failed' => (int) ($paymentStatusCounts['failed'] ?? 0),
            'cancelled' => (int) ($paymentStatusCounts['cancelled'] ?? 0),
        ];

        $transactions = (clone $paymentsBase)
            ->select(['id', 'created_at', 'plan_name', 'amount', 'gst_amount', 'status'])
            ->orderByDesc('created_at')
            ->paginate(20)
            ->appends($request->query());

        $activeSubscriptions = Subscription::with('user')
            ->whereIn('plan_type', ['pro', 'business'])
            ->where('expires_at', '>', now())
            ->orderBy('expires_at')
            ->take(25)
            ->get();

        $renewalTargets = Subscription::with('user')
            ->whereIn('plan_type', ['pro', 'business'])
            ->whereBetween('expires_at', [now()->copy()->subDays(30), now()->copy()->addDays(30)])
            ->orderBy('expires_at')
            ->take(50)
            ->get();

        $subscriptionStats = [
            'activePro' => (int) Subscription::where('plan_type', 'pro')->where('expires_at', '>', now())->count(),
            'activeBusiness' => (int) Subscription::where('plan_type', 'business')->where('expires_at', '>', now())->count(),
            'expiring7d' => (int) Subscription::whereIn('plan_type', ['pro', 'business'])
                ->whereBetween('expires_at', [now(), now()->copy()->addDays(7)])
                ->count(),
            'expiring30d' => (int) Subscription::whereIn('plan_type', ['pro', 'business'])
                ->whereBetween('expires_at', [now(), now()->copy()->addDays(30)])
                ->count(),
        ];

        $creditsBase = UnlockCreditLog::query()
            ->whereBetween('created_at', [$fromDate, $toDate]);

        $creditsAdded = (int) ((clone $creditsBase)->where('delta', '>', 0)->sum('delta') ?? 0);
        $creditsUsed = (int) abs((int) ((clone $creditsBase)->where('delta', '<', 0)->sum('delta') ?? 0));

        $creditsBySource = (clone $creditsBase)
            ->select('source_type', DB::raw('SUM(delta) as delta_sum'), DB::raw('COUNT(*) as events'))
            ->groupBy('source_type')
            ->orderByDesc('events')
            ->get();

        $topCreditUsers = (clone $creditsBase)
            ->select('user_id', DB::raw('SUM(CASE WHEN delta < 0 THEN -delta ELSE 0 END) as used'), DB::raw('SUM(CASE WHEN delta > 0 THEN delta ELSE 0 END) as added'))
            ->groupBy('user_id')
            ->orderByDesc('used')
            ->with('user')
            ->take(15)
            ->get();

        $inquiriesBase = Inquiry::query()->whereBetween('created_at', [$fromDate, $toDate]);
        $inquiriesTotal = (int) (clone $inquiriesBase)->count();
        $inquiriesGuest = (int) (clone $inquiriesBase)->whereNull('user_id')->count();
        $inquiriesRegistered = $inquiriesTotal - $inquiriesGuest;
        $inquiriesUnlocked = (int) (clone $inquiriesBase)->where('status', 'viewed')->count();

        $messagesBase = InquiryMessage::query()->whereBetween('created_at', [$fromDate, $toDate]);
        $messagesCount = (int) $messagesBase->count();

        $inquiriesWithMessages = (int) Inquiry::whereHas('messages')->whereBetween('created_at', [$fromDate, $toDate])->count();

        $reportsCount = (int) InquiryReport::whereBetween('created_at', [$fromDate, $toDate])->count();
        $openReports = (int) InquiryReport::whereIn('status', ['open', 'under_review'])->count();

        $blocksCount = (int) InquiryBlock::whereBetween('created_at', [$fromDate, $toDate])->count();
        $activeBlocks = (int) InquiryBlock::where('active', true)->count();

        return view('admin.analytics', [
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'range' => $range,
            'rangeLabel' => $rangeLabel,
            'rangeLabelLong' => $rangeLabelLong,
            'rangeMonthLabel' => $rangeMonthLabel,
            'paymentStats' => $paymentStats,
            'paymentsByPlan' => $paymentsByPlan,
            'planPriceBreakdown' => $planPriceBreakdown,
            'monthlyLabels' => $monthlyLabels,
            'monthlyRevenue' => $monthlyRevenue,
            'monthlyGst' => $monthlyGst,
            'thisMonthRevenue' => $thisMonthRevenue,
            'lastMonthRevenue' => $lastMonthRevenue,
            'revenueGrowthPct' => $growth,
            'transactions' => $transactions,
            'subscriptionStats' => $subscriptionStats,
            'activeSubscriptions' => $activeSubscriptions,
            'renewalTargets' => $renewalTargets,
            'creditsAdded' => $creditsAdded,
            'creditsUsed' => $creditsUsed,
            'creditsBySource' => $creditsBySource,
            'topCreditUsers' => $topCreditUsers,
            'inquiriesTotal' => $inquiriesTotal,
            'inquiriesGuest' => $inquiriesGuest,
            'inquiriesRegistered' => $inquiriesRegistered,
            'inquiriesUnlocked' => $inquiriesUnlocked,
            'messagesCount' => $messagesCount,
            'inquiriesWithMessages' => $inquiriesWithMessages,
            'reportsCount' => $reportsCount,
            'openReports' => $openReports,
            'blocksCount' => $blocksCount,
            'activeBlocks' => $activeBlocks,
        ]);
    }

    public function sendRenewalEmail(Request $request, Subscription $subscription)
    {
        if (!$subscription->user) {
            $subscription->load('user');
        }

        $user = $subscription->user;
        if (!$user || empty($user->email)) {
            return back()->with('error', 'User email not found for this subscription.');
        }

        if (!in_array((string) $subscription->plan_type, ['pro', 'business'], true)) {
            return back()->with('error', 'Renewal email is only for Pro/Business subscriptions.');
        }

        $cooldownKey = 'admin:renewal_email:subscription:' . (int) $subscription->id;
        if (Cache::has($cooldownKey)) {
            return back()->with('error', 'Renewal email recently sent. Please wait before sending again.');
        }

        $expiresAt = \Carbon\Carbon::parse($subscription->expires_at);
        $isExpired = $expiresAt->lte(now());
        $days = now()->startOfDay()->diffInDays($expiresAt->startOfDay(), false);

        $subject = $isExpired
            ? 'Fitub: Your ' . ucfirst((string) $subscription->plan_type) . ' plan has expired — renew to continue'
            : 'Fitub: Your ' . ucfirst((string) $subscription->plan_type) . ' plan expires soon — renew to continue';

        try {
            Mail::send('emails.subscription-renewal', [
                'user' => $user,
                'subscription' => $subscription,
                'expiresAt' => $expiresAt,
                'isExpired' => $isExpired,
                'days' => $days,
                'renewUrl' => url('/billing/plans'),
            ], function ($message) use ($user, $subject) {
                $message->to($user->email)->subject($subject);
            });
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Unable to send email right now. Please check SMTP settings.');
        }

        Cache::put($cooldownKey, true, now()->addHours(12));

        return back()->with('success', 'Renewal email sent to ' . $user->email);
    }
//old code 
// public function dashboard()
// {
//     $stats = [
//         'totalUsers' => User::count(),
//         'pendingUsers' => User::where('status', 'pending')->count(), // NAYA STAT
//         'totalTrainers' => Trainer::count(),
//         'totalGyms' => Gym::count(),
//         'totalCustomers' => Customer::count(),
//         'totalRevenue' => Payment::where('status', 'paid')->sum('amount'),
//     ];

//     $recentUsers = User::latest()->take(5)->get();
//     $recentPayments = Payment::with('user')->where('status', 'paid')->latest()->take(5)->get();

//     return view('admin.dashboard', compact('stats', 'recentUsers', 'recentPayments'));
// }


    /**
     * PENDING USERS KO DEKHNE KE LIYE
     */
    public function pendingUsersIndex(Request $request)
    {
        $tab = (string) $request->query('tab', 'pending');
        $allowedTabs = ['pending', 'approved', 'rejected', 'all'];
        if (!in_array($tab, $allowedTabs, true)) {
            $tab = 'pending';
        }

        $query = User::whereIn('user_type', ['trainer', 'gymowner']);

        if ($tab === 'pending') {
            $query->where('kyc_status', 'pending');
        } elseif ($tab === 'approved') {
            $query->where('kyc_status', 'approved');
        } elseif ($tab === 'rejected') {
            $query->where('kyc_status', 'rejected');
        } else {
            $query->whereIn('kyc_status', ['pending', 'approved', 'rejected']);
        }

        $pendingUsers = $query->latest()->paginate(20)->appends(['tab' => $tab]);

        $tabCounts = [
            'pending' => User::whereIn('user_type', ['trainer', 'gymowner'])->where('kyc_status', 'pending')->count(),
            'approved' => User::whereIn('user_type', ['trainer', 'gymowner'])->where('kyc_status', 'approved')->count(),
            'rejected' => User::whereIn('user_type', ['trainer', 'gymowner'])->where('kyc_status', 'rejected')->count(),
        ];
        $tabCounts['all'] = $tabCounts['pending'] + $tabCounts['approved'] + $tabCounts['rejected'];

        return view('admin.users.pending', compact('pendingUsers', 'tab', 'tabCounts'));
    }

    /**
     * USER KO APPROVE KARNE KE LIYE
     */
    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'active';
        if (in_array($user->user_type, ['trainer', 'gymowner'], true)) {
            $user->is_verified = true;
            $user->kyc_status = 'approved';
            $user->kyc_rejection_reason = null;
            $user->kyc_reviewed_by = auth()->id();
            $user->kyc_reviewed_at = now();
        }
        $user->save();
        
        // Send approval email
        Mail::to($user->email)->send(new UserApproved($user));

        return back()->with('success', 'User ' . $user->name . ' approved successfully and marked verified.');
    }

    public function rejectUser(Request $request, $id)
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $user = User::findOrFail($id);
        if (!in_array($user->user_type, ['trainer', 'gymowner'], true)) {
            return back()->with('error', 'KYC rejection is only for trainer or gym owner.');
        }

        $userName = $user->name;
        $registerUrl = url('/auth/register');

        $user->update([
            'status' => 'pending',
            'is_verified' => false,
            'kyc_status' => 'rejected',
            'kyc_rejection_reason' => $data['reason'],
            'kyc_reviewed_by' => auth()->id(),
            'kyc_reviewed_at' => now(),
        ]);

        Mail::to($user->email)->send(new UserKycRejected(
            $user,
            $data['reason'],
            $registerUrl
        ));

        $user->delete();

        return back()->with('success', 'KYC rejected for ' . $userName . '. Rejection mail sent and account removed.');
    }

    /**
     * User management page
     */
    public function usersIndex()
    {
        $stats = [
            'totalUsers' => User::count(),
            'totalTrainers' => User::where('user_type', 'trainer')->count(),
            'totalGyms' => User::where('user_type', 'gymowner')->count(),
            'totalRevenue' => Payment::where('status', 'paid')->sum('amount'),
        ];

        $users = User::with(['payments','trainer','gym','customer'])
        ->latest()
        ->paginate(16);
        
        return view('admin.users.index', [
            'users' => $users,
            'stats' => $stats,
        ]);
    }

    /**
     * Users with registration issues (cancelled or warned)
     */
    public function registrationIssuesIndex()
    {
        $users = User::query()
            ->where('status', 'cancelled')
            ->orWhereExists(function ($query) {
                $query->from('inquiry_block_warnings')
                    ->join('inquiry_blocks', 'inquiry_blocks.id', '=', 'inquiry_block_warnings.block_id')
                    ->whereColumn('inquiry_blocks.blocked_user_id', 'users.id');
            })
            ->with(['trainer', 'gym', 'customer'])
            ->select('users.*')
            ->addSelect([
                'warnings_count' => function ($sub) {
                    $sub->from('inquiry_block_warnings')
                        ->join('inquiry_blocks', 'inquiry_blocks.id', '=', 'inquiry_block_warnings.block_id')
                        ->whereColumn('inquiry_blocks.blocked_user_id', 'users.id')
                        ->selectRaw('COUNT(*)');
                },
            ])
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('admin.users.registration-issues', compact('users'));
    }

    public function userDestroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Aap apna khud ka account delete nahi kar sakte.');
        }

        $user->delete();
        return back()->with('success', 'User ko safaltapoorvak delete kar diya gaya hai.');
    }

    /**
     * Reactivate a cancelled user
     */
    public function activateUser(User $user)
    {
        if ($user->status !== 'cancelled') {
            return back()->with('error', 'Sirf cancelled users ko activate kiya ja sakta hai.');
        }

        $user->update([
            'status' => 'active',
            'registration_cancelled_at' => null,
            'registration_cancellation_reason' => null,
        ]);

        return back()->with('success', 'User account ko dobara active kar diya gaya hai.');
    }

    public function inquiriesIndex(Request $request)
    {
        $serviceFilter = $request->query('service', 'all');
        $allowedFilters = ['all', 'visit_booking'];
        if (!in_array($serviceFilter, $allowedFilters, true)) {
            $serviceFilter = 'all';
        }

        $serviceCounts = [
            'all' => Inquiry::count(),
            'visit_booking' => Inquiry::where('service_needed', 'Visit Booking')->count(),
        ];

        $query = Inquiry::with('recipient', 'user')->latest();

        if ($serviceFilter === 'visit_booking') {
            $query->where('service_needed', 'Visit Booking');
        }

        $inquiries = $query->paginate(20)->appends([
            'service' => $serviceFilter,
        ]);

        return view('admin.inquiries.index', compact('inquiries', 'serviceFilter', 'serviceCounts'));
    }

    public function paymentsIndex()
    {
        $stats = [
            'totalRevenue' => Payment::where('status', 'paid')->sum('amount'),
            'paidCount' => Payment::where('status', 'paid')->count(),
            'createdCount' => Payment::where('status', 'created')->count(),
            'failedCount' => Payment::where('status', 'failed')->count(),
        ];

        $payments = Payment::with('user')->latest()->paginate(20);

        return view('admin.payments.index', compact('stats', 'payments'));
    }

    public function forwardInquiry(Inquiry $inquiry)
    {
        $inquiry->status = 'forwarded';
        $inquiry->save();

        return back()->with('success', 'Inquiry forwarded successfully to ' . $inquiry->recipient->name);
    }

    public function show($id)
    {
        $user = User::with(['trainer', 'gym', 'customer', 'payments'])->findOrFail($id);
        $reviewedBy = null;
        if ($user->kyc_reviewed_by) {
            $reviewedBy = User::find($user->kyc_reviewed_by);
        }

        return view('admin.users.show', compact('user', 'reviewedBy'));
    }

    public function inquiryChat(Inquiry $inquiry)
    {
        $inquiry->load(['recipient', 'user']);

        $messages = InquiryMessage::with('sender')
            ->where('inquiry_id', $inquiry->id)
            ->oldest()
            ->get();

        return view('admin.inquiries.chat', compact('inquiry', 'messages'));
    }

    public function creditHistory(Request $request)
    {
        $query = UnlockCreditLog::with(['user', 'creator'])->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->query('user_id'));
        }

        if ($request->filled('source_type')) {
            $query->where('source_type', (string) $request->query('source_type'));
        }

        $logs = $query->paginate(30)->appends($request->query());

        return view('admin.credits.index', compact('logs'));
    }
}
