@extends('admin.layouts.app')

@section('title', 'Payments')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 tracking-tight">Payments Dashboard</h1>
        <p class="mt-1 text-md text-gray-500">Track subscriptions and lead unlock payments.</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-indigo-600 text-white p-6 rounded-2xl shadow-lg">
            <p class="text-sm opacity-90">Total Revenue (Paid)</p>
            <p class="text-3xl font-bold">₹{{ number_format($stats['totalRevenue'] ?? 0, 0) }}</p>
        </div>
        <div class="bg-emerald-600 text-white p-6 rounded-2xl shadow-lg">
            <p class="text-sm opacity-90">Paid Payments</p>
            <p class="text-3xl font-bold">{{ $stats['paidCount'] ?? 0 }}</p>
        </div>
        <div class="bg-amber-500 text-white p-6 rounded-2xl shadow-lg">
            <p class="text-sm opacity-90">Created (Pending)</p>
            <p class="text-3xl font-bold">{{ $stats['createdCount'] ?? 0 }}</p>
        </div>
        <div class="bg-rose-600 text-white p-6 rounded-2xl shadow-lg">
            <p class="text-sm opacity-90">Failed Payments</p>
            <p class="text-3xl font-bold">{{ $stats['failedCount'] ?? 0 }}</p>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
            <p class="font-bold">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Payments Table --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-4 border-b flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-800">All Payments</h2>
            <div class="text-xs text-gray-500">Latest first</div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Plan</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Razorpay Payment ID</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">{{ $payment->user->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $payment->user->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-xs bg-gray-100 text-gray-700 capitalize">
                                    {{ $payment->user->user_type ?? 'n/a' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-800">{{ $payment->plan_name }}</td>
                            <td class="px-6 py-4">₹{{ number_format($payment->amount, 0) }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $status = $payment->status;
                                    $cls = 'bg-gray-100 text-gray-700';
                                    if ($status === 'paid') $cls = 'bg-green-100 text-green-700';
                                    elseif ($status === 'failed') $cls = 'bg-red-100 text-red-700';
                                    elseif ($status === 'created') $cls = 'bg-amber-100 text-amber-700';
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-bold {{ $cls }}">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                {{ $payment->razorpay_payment_id ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                {{ $payment->created_at?->format('d M Y, h:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500">
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
@endsection


