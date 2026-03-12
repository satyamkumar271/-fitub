<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\UnlockCreditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;

class PaymentController extends Controller
{
    public function plans(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->user_type, ['trainer', 'gymowner'])) {
            abort(403, 'Only trainers and gym owners can purchase plans.');
        }

        $inquiryId = $request->query('inquiry_id');
        $inquiry = null;
        if ($inquiryId) {
            $inquiry = Inquiry::where('id', $inquiryId)->where('recipient_id', $user->id)->firstOrFail();
        }

        // You can change pricing here
        $plans = [
            [
                'key' => 'monthly',
                'title' => 'Monthly Plan',
                'price' => 999,
                'duration_days' => 30,
                'leads_remaining' => null, // unlimited
            ],
            [
                'key' => 'yearly',
                'title' => 'Yearly Plan',
                'price' => 3999,
                'duration_days' => 365,
                'leads_remaining' => null,
            ],
            [
                'key' => 'single_lead',
                'title' => 'Single Lead Unlock',
                'price' => 99,
                'duration_days' => 0,
                'leads_remaining' => 1,
            ],
        ];

        return view('payment.plans', [
            'plans' => $plans,
            'inquiry' => $inquiry,
            'unlockCredits' => (int) ($user->unlock_credits ?? 0),
        ]);
    }

    public function createOrder(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->user_type, ['trainer', 'gymowner'])) {
            abort(403, 'Only trainers and gym owners can purchase plans.');
        }

        $data = $request->validate([
            'plan' => 'required|string|in:monthly,yearly,single_lead',
            'inquiry_id' => 'nullable|integer',
        ]);

        $plan = $data['plan'];

        $pricing = [
            'monthly' => 999,
            'yearly' => 3999,
            'single_lead' => 99,
        ];
        $amount = $pricing[$plan];

        $contextType = ($plan === 'single_lead') ? 'lead_unlock' : 'subscription';
        $contextId = null;
        if ($plan === 'single_lead') {
            $inquiryId = $data['inquiry_id'] ?? null;
            if (!$inquiryId) {
                return back()->withErrors(['plan' => 'Inquiry is required for single lead unlock.']);
            }
            $inquiry = Inquiry::where('id', $inquiryId)->where('recipient_id', $user->id)->firstOrFail();
            $contextId = $inquiry->id;

            if ($this->recipientAlreadyHasLeadAccess($user->id, $inquiry->id, $inquiry->status)) {
                return redirect()->route('dashboard.leads')->with('success', 'This lead is already unlocked.');
            }

            if ((int) ($user->unlock_credits ?? 0) > 0) {
                $usedCredit = DB::transaction(function () use ($user, $inquiry) {
                    $lockedUser = User::where('id', $user->id)->lockForUpdate()->firstOrFail();
                    $lockedInquiry = Inquiry::where('id', $inquiry->id)
                        ->where('recipient_id', $user->id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ((int) $lockedUser->unlock_credits <= 0) {
                        return false;
                    }

                    if ($this->recipientAlreadyHasLeadAccess($user->id, $lockedInquiry->id, $lockedInquiry->status)) {
                        return false;
                    }

                    $lockedUser->decrement('unlock_credits', 1);
                    $lockedUser->refresh();
                    $lockedInquiry->status = 'viewed';
                    $lockedInquiry->save();

                    Payment::create([
                        'user_id' => $user->id,
                        'plan_name' => 'single_lead',
                        'amount' => 0,
                        'currency' => 'INR',
                        'status' => 'paid',
                        'razorpay_order_id' => 'credit_' . $user->id . '_' . $lockedInquiry->id . '_' . now()->timestamp,
                        'context_type' => 'lead_unlock',
                        'context_id' => $lockedInquiry->id,
                        'meta' => [
                            'source' => 'unlock_credit',
                            'note' => 'Lead unlocked using compensation credit',
                        ],
                    ]);

                    UnlockCreditLog::create([
                        'user_id' => $user->id,
                        'delta' => -1,
                        'balance_after' => (int) $lockedUser->unlock_credits,
                        'source_type' => 'lead_unlock',
                        'source_id' => $lockedInquiry->id,
                        'note' => 'Lead unlocked using credit',
                        'created_by' => $user->id,
                    ]);

                    return true;
                });

                if ($usedCredit) {
                    return redirect()->route('dashboard.leads')->with('success', 'Lead unlocked using 1 credit. No payment charged.');
                }

                return redirect()->route('dashboard.leads')->with('success', 'This lead is already unlocked.');
            }
        }

        $key = config('services.razorpay.key');
        $secret = config('services.razorpay.secret');
        if (!$key || !$secret) {
            return back()->withErrors(['payment' => 'Razorpay keys are not configured in .env (RAZORPAY_KEY, RAZORPAY_SECRET).']);
        }

        $api = new Api($key, $secret);
        try {
            $order = $api->order->create([
                'receipt' => 'fitub_' . $user->id . '_' . time(),
                'amount' => $amount * 100,
                'currency' => 'INR',
                'notes' => [
                    'user_id' => (string) $user->id,
                    'plan' => $plan,
                    'context_type' => $contextType,
                    'context_id' => $contextId ? (string) $contextId : '',
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['payment' => 'Unable to create payment order right now. Please try again.']);
        }

        Payment::create([
            'user_id' => $user->id,
            'plan_name' => $plan,
            'amount' => $amount,
            'currency' => 'INR',
            'status' => 'created',
            'razorpay_order_id' => $order['id'],
            'context_type' => $contextType,
            'context_id' => $contextId,
            'meta' => [
                'receipt' => $order['receipt'] ?? null,
            ],
        ]);

        return view('payment.checkout', [
            'razorpayKey' => $key,
            'orderId' => $order['id'],
            'amount' => $amount,
            'plan' => $plan,
            'inquiryId' => $contextId,
            'user' => $user,
        ]);
    }

    public function verify(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->user_type, ['trainer', 'gymowner'])) {
            abort(403, 'Only trainers and gym owners can purchase plans.');
        }

        $data = $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $key = config('services.razorpay.key');
        $secret = config('services.razorpay.secret');
        if (!$key || !$secret) {
            return redirect()->route('billing.plans')->withErrors(['payment' => 'Razorpay keys are not configured.']);
        }

        $api = new Api($key, $secret);

        $payment = Payment::where('razorpay_order_id', $data['razorpay_order_id'])->firstOrFail();
        if ($payment->user_id !== $user->id) {
            abort(403, 'Unauthorized payment.');
        }
        if ($payment->status === 'paid') {
            return redirect()->route('dashboard')->with('success', 'Payment already verified.');
        }

        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $data['razorpay_order_id'],
                'razorpay_payment_id' => $data['razorpay_payment_id'],
                'razorpay_signature' => $data['razorpay_signature'],
            ]);
        } catch (\Exception $e) {
            $payment->refresh();
            if ($payment->status === 'paid') {
                return redirect()->route('dashboard')->with('success', 'Payment already verified.');
            }

            $payment->update([
                'status' => 'failed',
                'razorpay_payment_id' => $data['razorpay_payment_id'],
                'razorpay_signature' => $data['razorpay_signature'],
            ]);
            return redirect()->route('billing.plans')->withErrors(['payment' => 'Payment verification failed. Please try again.']);
        }

        $this->markPaymentAsPaidAndApplyEntitlement(
            $payment,
            $data['razorpay_payment_id'],
            $data['razorpay_signature']
        );

        if ($payment->plan_name === 'monthly' || $payment->plan_name === 'yearly') {
            return redirect()->route('dashboard')->with('success', ucfirst($payment->plan_name) . ' plan activated successfully!');
        }

        if ($payment->plan_name === 'single_lead') {
            return redirect()->route('dashboard')->with('success', 'Lead unlocked successfully!');
        }

        return redirect()->route('dashboard')->with('success', 'Payment successful.');
    }

    public function cancel(Request $request)
    {
        $user = auth()->user();
        if (!in_array($user->user_type, ['trainer', 'gymowner'], true)) {
            abort(403, 'Only trainers and gym owners can cancel payments.');
        }

        $data = $request->validate([
            'razorpay_order_id' => 'required|string',
            'reason' => 'nullable|string|max:255',
            'razorpay_payment_id' => 'nullable|string',
        ]);

        $payment = Payment::where('razorpay_order_id', $data['razorpay_order_id'])->firstOrFail();
        if ($payment->user_id !== $user->id) {
            abort(403, 'Unauthorized payment.');
        }

        if ($payment->status === 'created') {
            $meta = $payment->meta ?? [];
            $meta['cancel_reason'] = $data['reason'] ?? 'checkout_dismissed';
            $meta['cancelled_at'] = now()->toDateTimeString();

            $updateData = [
                'status' => 'cancelled',
                'meta' => $meta,
            ];

            if (!empty($data['razorpay_payment_id'])) {
                $updateData['razorpay_payment_id'] = $data['razorpay_payment_id'];
            }

            $payment->update($updateData);
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('dashboard.payments')->with('success', 'Payment marked as cancelled.');
    }

    public function webhook(Request $request)
    {
        $webhookSecret = config('services.razorpay.webhook_secret');
        if (!$webhookSecret) {
            return response('Webhook secret not configured.', 500);
        }

        $signature = $request->header('X-Razorpay-Signature');
        if (!$signature) {
            return response('Missing webhook signature.', 400);
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
        if (!hash_equals($expectedSignature, $signature)) {
            return response('Invalid webhook signature.', 400);
        }

        $event = $request->input('event');
        if (!in_array($event, ['payment.captured', 'order.paid'], true)) {
            return response('Ignored event.', 200);
        }

        $paymentEntity = $request->input('payload.payment.entity', []);
        $orderId = $paymentEntity['order_id'] ?? null;
        $paymentId = $paymentEntity['id'] ?? null;

        if (!$orderId) {
            return response('Missing order id.', 200);
        }

        $payment = Payment::where('razorpay_order_id', $orderId)->first();
        if (!$payment) {
            return response('Order not found.', 200);
        }

        if ($paymentId && $payment->razorpay_payment_id && $payment->razorpay_payment_id !== $paymentId) {
            return response('Conflicting payment id for order.', 409);
        }

        $this->markPaymentAsPaidAndApplyEntitlement($payment, $paymentId, $signature);

        return response('Webhook processed.', 200);
    }

    private function markPaymentAsPaidAndApplyEntitlement(Payment $payment, ?string $paymentId, ?string $signature): void
    {
        DB::transaction(function () use ($payment, $paymentId, $signature) {
            $payment = Payment::where('id', $payment->id)->lockForUpdate()->firstOrFail();

            if ($payment->status === 'paid') {
                return;
            }

            $updateData = ['status' => 'paid'];
            if ($paymentId) {
                $updateData['razorpay_payment_id'] = $paymentId;
            }
            if ($signature) {
                $updateData['razorpay_signature'] = $signature;
            }
            $payment->update($updateData);

            if ($payment->plan_name === 'monthly' || $payment->plan_name === 'yearly') {
                $days = ($payment->plan_name === 'monthly') ? 30 : 365;

                $current = Subscription::where('user_id', $payment->user_id)
                    ->where('plan_type', $payment->plan_name)
                    ->where('expires_at', '>', now())
                    ->latest()
                    ->first();

                $start = $current?->expires_at && $current->expires_at->gt(now()) ? $current->expires_at : now();

                Subscription::create([
                    'user_id' => $payment->user_id,
                    'plan_type' => $payment->plan_name,
                    'expires_at' => $start->copy()->addDays($days),
                ]);
            }

            if ($payment->plan_name === 'single_lead') {
                Subscription::create([
                    'user_id' => $payment->user_id,
                    'plan_type' => 'single_lead',
                    'expires_at' => now()->addMinutes(5),
                ]);

                if ($payment->context_id) {
                    $inquiry = Inquiry::where('id', $payment->context_id)
                        ->where('recipient_id', $payment->user_id)
                        ->lockForUpdate()
                        ->first();

                    if ($inquiry) {
                        $inquiry->status = 'viewed';
                        $inquiry->save();
                    }
                }
            }
        });
    }

    private function recipientAlreadyHasLeadAccess(int $userId, int $inquiryId, string $inquiryStatus): bool
    {
        if ($inquiryStatus === 'viewed') {
            return true;
        }

        $hasUnlimited = Subscription::where('user_id', $userId)
            ->whereIn('plan_type', ['monthly', 'yearly'])
            ->where('expires_at', '>', now())
            ->exists();

        if ($hasUnlimited) {
            return true;
        }

        return Payment::where('user_id', $userId)
            ->where('status', 'paid')
            ->where('plan_name', 'single_lead')
            ->where('context_type', 'lead_unlock')
            ->where('context_id', $inquiryId)
            ->exists();
    }
}

