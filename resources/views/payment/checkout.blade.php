@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto px-4 py-10">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h1 class="text-2xl font-bold text-gray-900">Checkout</h1>
        <p class="text-gray-600 mt-1">Complete payment to activate your plan.</p>

        <div class="mt-6 p-4 bg-gray-50 rounded-lg border">
            <div class="flex justify-between text-sm text-gray-600">
                <span>Plan</span>
                <span class="font-semibold text-gray-900">{{ strtoupper($plan) }}</span>
            </div>
            <div class="flex justify-between text-sm text-gray-600 mt-2">
                <span>Amount</span>
                <span class="font-semibold text-gray-900">&#8377;{{ $amount }}</span>
            </div>
        </div>

        <form id="verify-form" method="POST" action="{{ route('billing.verify') }}" class="mt-6">
            @csrf
            <input type="hidden" name="razorpay_order_id" id="razorpay_order_id" value="{{ $orderId }}">
            <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
            <input type="hidden" name="razorpay_signature" id="razorpay_signature">

            <button type="button" id="pay-btn"
                class="w-full bg-indigo-600 text-white font-semibold py-3 rounded-lg hover:bg-indigo-700 transition">
                Pay with Razorpay
            </button>
        </form>

        <p class="text-xs text-gray-500 mt-4">
            Note: Payment success ke baad aap automatically dashboard par redirect ho jaoge.
        </p>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    (function () {
        const csrfToken = @json(csrf_token());
        const cancelUrl = @json(route('billing.cancel'));
        const orderId = @json($orderId);
        let completionSent = false;

        function markCancelled(reason, paymentId) {
            if (completionSent) return;
            completionSent = true;

            fetch(cancelUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    razorpay_order_id: orderId,
                    reason: reason || 'checkout_dismissed',
                    razorpay_payment_id: paymentId || null
                }),
                keepalive: true
            }).catch(function () {});
        }

        const options = {
            key: @json($razorpayKey),
            amount: @json((int) $amount * 100),
            currency: "INR",
            name: "Fitub",
            description: "Plan purchase",
            order_id: @json($orderId),
            prefill: {
                name: @json($user->name),
                email: @json($user->email),
            },
            handler: function (response) {
                completionSent = true;
                document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                document.getElementById('razorpay_signature').value = response.razorpay_signature;
                document.getElementById('verify-form').submit();
            },
            modal: {
                ondismiss: function () {
                    markCancelled('checkout_dismissed', null);
                }
            },
            theme: {
                color: "#4f46e5"
            }
        };

        const rzp = new Razorpay(options);
        rzp.on('payment.failed', function (response) {
            const failedPaymentId = response?.error?.metadata?.payment_id || null;
            markCancelled('payment_failed', failedPaymentId);
        });

        document.getElementById('pay-btn').onclick = function (e) {
            e.preventDefault();
            rzp.open();
        };
    })();
</script>
@endsection
