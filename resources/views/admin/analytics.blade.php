@extends('admin.layouts.app')

@section('title', 'Analytics - Fitub Admin')

@section('content')
<div class="space-y-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Analytics</h1>
            <p class="text-gray-500">Plans, credits, unlocks, and chat activity</p>
        </div>

        <form method="GET" action="{{ route('admin.analytics') }}" class="flex flex-wrap items-end gap-3 bg-white border rounded-xl p-4 shadow-sm">
            <div>
                <label class="block text-xs font-semibold text-gray-600">From</label>
                <input type="date" name="from" value="{{ $fromDate->toDateString() }}" class="mt-1 border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600">To</label>
                <input type="date" name="to" value="{{ $toDate->toDateString() }}" class="mt-1 border rounded-lg px-3 py-2 text-sm">
            </div>
            <button type="submit" class="h-[42px] rounded-lg bg-indigo-600 px-4 text-sm font-semibold text-white hover:bg-indigo-700">
                Apply
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-5 rounded-2xl shadow border">
            <p class="text-xs text-gray-500">Revenue (Paid)</p>
            <p class="mt-2 text-2xl font-black text-gray-900">₹{{ number_format((float) ($paymentStats['revenue'] ?? 0), 0) }}</p>
            <p class="mt-1 text-xs text-gray-500">Paid: {{ $paymentStats['paid'] ?? 0 }} • Created: {{ $paymentStats['created'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow border">
            <p class="text-xs text-gray-500">Active Subscriptions</p>
            <p class="mt-2 text-2xl font-black text-gray-900">{{ ($subscriptionStats['activePro'] ?? 0) + ($subscriptionStats['activeBusiness'] ?? 0) }}</p>
            <p class="mt-1 text-xs text-gray-500">Pro: {{ $subscriptionStats['activePro'] ?? 0 }} • Business: {{ $subscriptionStats['activeBusiness'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow border">
            <p class="text-xs text-gray-500">Credits (Range)</p>
            <p class="mt-2 text-2xl font-black text-gray-900">+{{ $creditsAdded ?? 0 }} / -{{ $creditsUsed ?? 0 }}</p>
            <p class="mt-1 text-xs text-gray-500">Added / Used</p>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow border">
            <p class="text-xs text-gray-500">Inquiries (Range)</p>
            <p class="mt-2 text-2xl font-black text-gray-900">{{ $inquiriesTotal ?? 0 }}</p>
            <p class="mt-1 text-xs text-gray-500">Unlocked: {{ $inquiriesUnlocked ?? 0 }} • Messages: {{ $messagesCount ?? 0 }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow border">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-gray-800">Payments by Plan (Paid)</h2>
                <a href="{{ route('admin.payments.index') }}" class="text-sm text-indigo-600 font-semibold hover:underline">View all</a>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2">Plan</th>
                            <th class="py-2 text-right">Paid</th>
                            <th class="py-2 text-right">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paymentsByPlan as $row)
                            <tr class="border-b">
                                <td class="py-3 font-semibold text-gray-800">{{ ucfirst((string) $row->plan_name) }}</td>
                                <td class="py-3 text-right text-gray-700">{{ (int) $row->paid_count }}</td>
                                <td class="py-3 text-right text-gray-700">₹{{ number_format((float) $row->revenue, 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-gray-500">No paid payments in this range.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow border">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-gray-800">Credits by Source (Net Delta)</h2>
                <a href="{{ route('admin.credits.index') }}" class="text-sm text-indigo-600 font-semibold hover:underline">View logs</a>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2">Source</th>
                            <th class="py-2 text-right">Events</th>
                            <th class="py-2 text-right">Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($creditsBySource as $row)
                            <tr class="border-b">
                                <td class="py-3 font-semibold text-gray-800">{{ (string) $row->source_type }}</td>
                                <td class="py-3 text-right text-gray-700">{{ (int) $row->events }}</td>
                                <td class="py-3 text-right {{ (float) $row->delta_sum >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ (float) $row->delta_sum >= 0 ? '+' : '' }}{{ (int) $row->delta_sum }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-gray-500">No credit activity in this range.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow border">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-gray-800">Active Subscriptions (Soonest expiry)</h2>
                <span class="text-xs text-gray-500">Expiring 7d: {{ $subscriptionStats['expiring7d'] ?? 0 }} • 30d: {{ $subscriptionStats['expiring30d'] ?? 0 }}</span>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2">User</th>
                            <th class="py-2">Plan</th>
                            <th class="py-2 text-right">Expires</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeSubscriptions as $sub)
                            <tr class="border-b">
                                <td class="py-3 text-gray-800 font-semibold">{{ $sub->user?->name ?? 'User #' . $sub->user_id }}</td>
                                <td class="py-3 text-gray-700">{{ ucfirst((string) $sub->plan_type) }}</td>
                                <td class="py-3 text-right text-gray-700">{{ \Carbon\Carbon::parse($sub->expires_at)->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-gray-500">No active subscriptions.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow border">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-gray-800">Top Credit Users (Used)</h2>
                <a href="{{ route('admin.credits.index') }}" class="text-sm text-indigo-600 font-semibold hover:underline">Filter logs</a>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2">User</th>
                            <th class="py-2 text-right">Used</th>
                            <th class="py-2 text-right">Added</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topCreditUsers as $row)
                            <tr class="border-b">
                                <td class="py-3 font-semibold text-gray-800">{{ $row->user?->name ?? ('User #' . $row->user_id) }}</td>
                                <td class="py-3 text-right text-gray-700">{{ (int) $row->used }}</td>
                                <td class="py-3 text-right text-gray-700">{{ (int) $row->added }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-gray-500">No usage in this range.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow border">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Expiring / Recently Expired (Renewal Email)</h2>
                <p class="text-xs text-gray-500 mt-1">Shows subscriptions expiring in next 30 days or expired in last 30 days.</p>
            </div>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="py-2">User</th>
                        <th class="py-2">Plan</th>
                        <th class="py-2">Status</th>
                        <th class="py-2 text-right">Expires</th>
                        <th class="py-2 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($renewalTargets as $sub)
                        @php
                            $expiresAt = \Carbon\Carbon::parse($sub->expires_at);
                            $isExpired = $expiresAt->lte(now());
                        @endphp
                        <tr class="border-b">
                            <td class="py-3 font-semibold text-gray-800">{{ $sub->user?->name ?? ('User #' . $sub->user_id) }}</td>
                            <td class="py-3 text-gray-700">{{ ucfirst((string) $sub->plan_type) }}</td>
                            <td class="py-3">
                                <span class="px-2 py-1 text-xs rounded-full font-semibold {{ $isExpired ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $isExpired ? 'Expired' : 'Expiring' }}
                                </span>
                            </td>
                            <td class="py-3 text-right text-gray-700">{{ $expiresAt->format('d M Y') }}</td>
                            <td class="py-3 text-right">
                                <form method="POST" action="{{ route('admin.subscriptions.renewal-email', $sub) }}"
                                      onsubmit="return confirm('Send renewal email to {{ $sub->user?->email ?? 'this user' }}?');"
                                      class="inline">
                                    @csrf
                                    <button type="submit" class="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-bold text-white hover:bg-indigo-700">
                                        Send mail
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-500">No expiring/expired subscriptions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow border">
            <h2 class="text-lg font-bold text-gray-800">Inquiries Breakdown</h2>
            <div class="mt-4 space-y-2 text-sm text-gray-700">
                <div class="flex items-center justify-between">
                    <span>Registered</span>
                    <span class="font-bold">{{ $inquiriesRegistered ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Guest</span>
                    <span class="font-bold">{{ $inquiriesGuest ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>With any chat messages</span>
                    <span class="font-bold">{{ $inquiriesWithMessages ?? 0 }}</span>
                </div>
            </div>
            <div class="mt-5">
                <a href="{{ route('admin.inquiries.index') }}" class="text-sm text-indigo-600 font-semibold hover:underline">Go to inquiries</a>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow border">
            <h2 class="text-lg font-bold text-gray-800">Reports</h2>
            <div class="mt-4 space-y-2 text-sm text-gray-700">
                <div class="flex items-center justify-between">
                    <span>Reports (range)</span>
                    <span class="font-bold">{{ $reportsCount ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Open / Under review</span>
                    <span class="font-bold">{{ $openReports ?? 0 }}</span>
                </div>
            </div>
            <div class="mt-5">
                <a href="{{ route('admin.reports.index') }}" class="text-sm text-indigo-600 font-semibold hover:underline">Go to reports</a>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow border">
            <h2 class="text-lg font-bold text-gray-800">Blocks</h2>
            <div class="mt-4 space-y-2 text-sm text-gray-700">
                <div class="flex items-center justify-between">
                    <span>Blocks (range)</span>
                    <span class="font-bold">{{ $blocksCount ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Active blocks</span>
                    <span class="font-bold">{{ $activeBlocks ?? 0 }}</span>
                </div>
            </div>
            <div class="mt-5">
                <a href="{{ route('admin.blocks.index') }}" class="text-sm text-indigo-600 font-semibold hover:underline">Go to blocks</a>
            </div>
        </div>
    </div>
</div>
@endsection
