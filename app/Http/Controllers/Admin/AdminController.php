<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserApproved;
use App\Models\User;
use App\Models\Payment;
use App\Models\Inquiry;
use App\Models\InquiryMessage;
use App\Models\UnlockCreditLog;
use Illuminate\Http\Request;
use App\Models\Trainer;
use App\Models\Gym;
use App\Models\Customer;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'totalUsers' => User::count(),
            'pendingUsers' => User::where('status', 'pending')->count(), // NAYA STAT
            'totalTrainers' => Trainer::count(),
            'totalGyms' => Gym::count(),
            'totalCustomers' => Customer::count(),
            'totalRevenue' => Payment::where('status', 'paid')->sum('amount'),
        ];

        $recentUsers = User::latest()->take(5)->get();
        $recentPayments = Payment::with('user')->where('status', 'paid')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentPayments'));
    }

    /**
     * PENDING USERS KO DEKHNE KE LIYE
     */
    public function pendingUsersIndex()
{
    $pendingUsers = User::where('status', 'pending')->latest()->paginate(20);
    return view('admin.users.pending', compact('pendingUsers'));
}

    /**
     * USER KO APPROVE KARNE KE LIYE
     */
    public function approveUser($id)
    {
        $user = User::findOrFail($id);
    $user->status = 'active';
    $user->save();
        
    // Send approval email
    Mail::to($user->email)->send(new UserApproved($user));

    return back()->with('success', 'User ' . $user->name . ' has been approved successfully! An email has been sent to them.');
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

    public function userDestroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Aap apna khud ka account delete nahi kar sakte.');
        }

        $user->delete();
        return back()->with('success', 'User ko safaltapoorvak delete kar diya gaya hai.');
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
    $user = User::with(['trainer','gym','customer','payments'])->findOrFail($id);
    return view('admin.users.show', compact('user'));
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
