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

        // SaaS pricing (TOTAL prices are GST inclusive)
        // ₹199 = base ~ 168.64 + 18% GST = 199.00
        // ₹1299 = base ~ 1100.85 + 18% GST = 1299.00
        // ₹4999 = base ~ 4236.44 + 18% GST = 4999.00
        $gstRate = (float) config('services.invoice.gst_rate', 18);
        $basePlans = [
            [
                'key' => 'starter',
                'title' => 'Starter Plan',
                'base' => 168.64,
                'duration_days' => 0,
                'credits' => 5, // 3–5 leads pack (we give 5 credits)
            ],
            [
                'key' => 'pro',
                'title' => 'Pro Plan',
                'base' => 1100.85,
                'duration_days' => 30,
                'credits' => null, // subscription
            ],
            [
                'key' => 'business',
                'title' => 'Business Plan',
                'base' => 4236.44,
                'duration_days' => 365,
                'credits' => null, // subscription
                'original_price' => 15588,
                'discount_label' => 'Save 60%',
            ],
        ];

        $plans = array_map(function (array $p) use ($gstRate) {
            $gst = round(((float) $p['base']) * ($gstRate / 100), 2);
            $total = round(((float) $p['base']) + $gst, 2);
            $p['price'] = (float) $total;
            $p['base_amount'] = (float) $p['base'];
            $p['gst_rate'] = (float) $gstRate;
            $p['gst_amount'] = (float) $gst;
            unset($p['base']);
            return $p;
        }, $basePlans);

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
            'plan' => 'required|string|in:starter,pro,business',
            'inquiry_id' => 'nullable|integer',
        ]);

        $plan = $data['plan'];

        $gstRate = (float) config('services.invoice.gst_rate', 18);
        $basePricing = [
            'starter' => 168.64,
            'pro' => 1100.85,
            'business' => 4236.44,
        ];
        $baseAmount = (float) $basePricing[$plan];
        $gstAmount = round($baseAmount * ($gstRate / 100), 2);
        $amount = round($baseAmount + $gstAmount, 2); // GST inclusive total

        $contextType = ($plan === 'starter') ? 'credits_pack' : 'subscription';
        $contextId = null;
        // If user came here from a specific inquiry unlock, we keep inquiry_id as context
        // so after Starter purchase we can instantly unlock that inquiry.
        if (!empty($data['inquiry_id'])) {
            $inquiry = Inquiry::where('id', (int) $data['inquiry_id'])
                ->where('recipient_id', $user->id)
                ->firstOrFail();

            if ($this->recipientAlreadyHasLeadAccess($user->id, $inquiry->id, $inquiry->status)) {
                return redirect()->route('dashboard.leads')->with('success', 'This lead is already unlocked.');
            }

            $contextType = 'lead_unlock';
            $contextId = $inquiry->id;
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
                'amount' => (int) round($amount * 100),
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
            'base_amount' => $baseAmount,
            'gst_rate' => $gstRate,
            'gst_amount' => $gstAmount,
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
            return $this->redirectAfterPayment($payment, $user->id, 'Payment already verified.');
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
                return $this->redirectAfterPayment($payment, $user->id, 'Payment already verified.');
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

        if ($payment->plan_name === 'pro' || $payment->plan_name === 'business') {
            return $this->redirectAfterPayment($payment, $user->id, ucfirst($payment->plan_name) . ' plan activated successfully!');
        }

        if ($payment->plan_name === 'starter') {
            return $this->redirectAfterPayment($payment, $user->id, 'Starter pack activated. Credits added successfully!');
        }

        return $this->redirectAfterPayment($payment, $user->id, 'Payment successful.');
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

            if ($payment->plan_name === 'pro' || $payment->plan_name === 'business') {
                $days = ($payment->plan_name === 'pro') ? 30 : 365;

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

            if ($payment->plan_name === 'starter') {
                $packCredits = 5;

                $lockedUser = User::where('id', $payment->user_id)->lockForUpdate()->firstOrFail();
                $lockedUser->increment('unlock_credits', $packCredits);
                $lockedUser->refresh();

                UnlockCreditLog::create([
                    'user_id' => $lockedUser->id,
                    'delta' => $packCredits,
                    'balance_after' => (int) $lockedUser->unlock_credits,
                    'source_type' => 'starter_pack',
                    'source_id' => $payment->id,
                    'note' => 'Starter pack credits purchased',
                    'created_by' => $lockedUser->id,
                ]);

                // If purchase was initiated for a specific inquiry unlock, unlock it instantly (consume 1 credit).
                if ($payment->context_type === 'lead_unlock' && $payment->context_id) {
                    $inquiry = Inquiry::where('id', $payment->context_id)
                        ->where('recipient_id', $payment->user_id)
                        ->lockForUpdate()
                        ->first();

                    if ($inquiry && $inquiry->status !== 'viewed' && (int) $lockedUser->unlock_credits > 0) {
                        $inquiry->status = 'viewed';
                        $inquiry->save();

                        $lockedUser->decrement('unlock_credits', 1);
                        $lockedUser->refresh();

                        UnlockCreditLog::create([
                            'user_id' => $lockedUser->id,
                            'delta' => -1,
                            'balance_after' => (int) $lockedUser->unlock_credits,
                            'source_type' => 'lead_unlock',
                            'source_id' => $inquiry->id,
                            'note' => 'Lead unlocked using Starter pack credit',
                            'created_by' => $lockedUser->id,
                        ]);
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
            ->whereIn('plan_type', ['pro', 'business'])
            ->where('expires_at', '>', now())
            ->exists();

        if ($hasUnlimited) {
            return true;
        }

        // Lead unlock via credits sets inquiry status to viewed; no separate paid single_lead payments anymore.
        return false;
    }

    private function redirectAfterPayment(Payment $payment, int $currentUserId, string $fallbackMessage)
    {
        if ($payment->context_type === 'lead_unlock' && $payment->context_id) {
            $inquiry = Inquiry::where('id', (int) $payment->context_id)
                ->where('recipient_id', $currentUserId)
                ->first();

            if ($inquiry) {
                return redirect()
                    ->route('inquiries.chat', $inquiry)
                    ->with('success', $fallbackMessage);
            }
        }

        if ($payment->plan_name === 'starter') {
            return redirect()->route('dashboard.leads')->with('success', $fallbackMessage);
        }

        return redirect()->route('dashboard')->with('success', $fallbackMessage);
    }
}
