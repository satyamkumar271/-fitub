@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">My Payments</h1>
            <p class="text-gray-600">All your subscription and lead unlock payments.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="bg-white border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg hover:bg-gray-50">
            Back to Dashboard
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-4 border-b flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-800">Payment History</h2>
            <div class="text-xs text-gray-500">Latest first</div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Plan</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Payment ID</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Invoice</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($payments as $payment)
                        @php
                            $status = $payment->status;
                            $cls = 'bg-gray-100 text-gray-700';
                            if ($status === 'paid') $cls = 'bg-green-100 text-green-700';
                            elseif ($status === 'failed') $cls = 'bg-red-100 text-red-700';
                            elseif ($status === 'created') $cls = 'bg-amber-100 text-amber-700';
                            elseif ($status === 'cancelled') $cls = 'bg-slate-200 text-slate-700';
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold text-gray-800 capitalize">{{ str_replace('_', ' ', $payment->plan_name) }}</td>
                            <td class="px-6 py-4">&#8377;{{ number_format($payment->amount, 0) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-bold {{ $cls }}">
                                    {{ strtoupper($status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500">{{ $payment->razorpay_payment_id ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-xs text-gray-500">{{ $payment->created_at?->format('d M Y, h:i A') }}</td>
                            <td class="px-6 py-4">
                                @if($payment->status === 'paid')
                                    <a href="{{ route('invoice.user.download', $payment) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-100 text-indigo-700 hover:bg-indigo-200">
                                        Download Invoice
                                    </a>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                No payments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $payments->links() }}
    </div>
</div>
@endsection

