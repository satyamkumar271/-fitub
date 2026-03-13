<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;
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

        return view('payment.plans', compact('plans', 'inquiry'));
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
        }

        $key = env('RAZORPAY_KEY');
        $secret = env('RAZORPAY_SECRET');
        if (!$key || !$secret) {
            return back()->withErrors(['payment' => 'Razorpay keys are not configured in .env (RAZORPAY_KEY, RAZORPAY_SECRET).']);
        }

        $api = new Api($key, $secret);

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

        $key = env('RAZORPAY_KEY');
        $secret = env('RAZORPAY_SECRET');
        if (!$key || !$secret) {
            return redirect()->route('billing.plans')->withErrors(['payment' => 'Razorpay keys are not configured.']);
        }

        $api = new Api($key, $secret);

        $payment = Payment::where('razorpay_order_id', $data['razorpay_order_id'])->firstOrFail();
        if ($payment->user_id !== $user->id) {
            abort(403, 'Unauthorized payment.');
        }

        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $data['razorpay_order_id'],
                'razorpay_payment_id' => $data['razorpay_payment_id'],
                'razorpay_signature' => $data['razorpay_signature'],
            ]);
        } catch (\Exception $e) {
            $payment->update([
                'status' => 'failed',
                'razorpay_payment_id' => $data['razorpay_payment_id'],
                'razorpay_signature' => $data['razorpay_signature'],
            ]);
            return redirect()->route('billing.plans')->withErrors(['payment' => 'Payment verification failed. Please try again.']);
        }

        // Mark paid
        $payment->update([
            'status' => 'paid',
            'razorpay_payment_id' => $data['razorpay_payment_id'],
            'razorpay_signature' => $data['razorpay_signature'],
        ]);

        // Activate subscription / unlock lead
        if ($payment->plan_name === 'monthly' || $payment->plan_name === 'yearly') {
            $days = ($payment->plan_name === 'monthly') ? 30 : 365;

            $current = Subscription::where('user_id', $user->id)
                ->where('plan_type', $payment->plan_name)
                ->where('expires_at', '>', now())
                ->latest()
                ->first();

            $start = $current?->expires_at && $current->expires_at->gt(now()) ? $current->expires_at : now();

            Subscription::create([
                'user_id' => $user->id,
                'plan_type' => $payment->plan_name,
                'expires_at' => $start->copy()->addDays($days),
            ]);

            return redirect()->route('dashboard')->with('success', ucfirst($payment->plan_name) . ' plan activated successfully!');
        }

        if ($payment->plan_name === 'single_lead') {
            $inquiryId = $payment->context_id;
            $inquiry = Inquiry::where('id', $inquiryId)->where('recipient_id', $user->id)->firstOrFail();

            Subscription::create([
                'user_id' => $user->id,
                'plan_type' => 'single_lead',
                'expires_at' => now()->addMinutes(5),
            ]);

            $inquiry->status = 'viewed';
            $inquiry->save();

            return redirect()->route('dashboard')->with('success', 'Lead unlocked successfully!');
        }

        return redirect()->route('dashboard')->with('success', 'Payment successful.');
    }
}

