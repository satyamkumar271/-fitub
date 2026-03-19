@extends('admin.layouts.app')

@section('title', 'Analytics - Fitub Admin')

@section('content')
<div class="space-y-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Analytics</h1>
            <p class="text-gray-500">Plans, credits, unlocks, and chat activity</p>
        </div>

        <div class="flex flex-wrap items-end gap-3">
            <form method="GET" action="{{ route('admin.analytics') }}" class="bg-white border rounded-xl p-4 shadow-sm">
                <label class="block text-xs font-semibold text-gray-600">Range</label>
                <select name="range"
                        class="mt-1 border rounded-lg px-3 py-2 text-sm bg-white"
                        onchange="this.form.submit()">
                    <option value="this_month" {{ ($range ?? 'this_month') === 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ ($range ?? '') === 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="last_3_months" {{ ($range ?? '') === 'last_3_months' ? 'selected' : '' }}>Last 3 Months</option>
                    <option value="custom" {{ ($range ?? '') === 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                </select>
            </form>

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
    </div>

    <div class="bg-white border rounded-xl px-5 py-3 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="text-sm text-gray-700">
                <span class="font-semibold">{{ $rangeLabel ?? 'This Month' }}</span>
                <span class="text-gray-400">•</span>
                <span class="text-gray-600">{{ $rangeLabelLong ?? '' }}</span>
            </div>
            <div class="text-xs text-gray-500">
                Revenue ({{ $rangeMonthLabel ?? '' }}): <span class="font-semibold text-gray-800">₹{{ number_format((float) ($paymentStats['revenue'] ?? 0), 0) }}</span>
                <span class="text-gray-400">•</span>
                GST ({{ $rangeMonthLabel ?? '' }}): <span class="font-semibold text-gray-800">₹{{ number_format((float) ($paymentStats['gst'] ?? 0), 0) }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-5 rounded-2xl shadow border">
            <p class="text-xs text-gray-500">Revenue (Paid)</p>
            <p class="mt-2 text-2xl font-black text-gray-900">₹{{ number_format((float) ($paymentStats['revenue'] ?? 0), 0) }}</p>
            <p class="mt-1 text-xs text-gray-500">Paid: {{ $paymentStats['paid'] ?? 0 }} • Created: {{ $paymentStats['created'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow border">
            <p class="text-xs text-gray-500">GST Collected (Paid)</p>
            <p class="mt-2 text-2xl font-black text-gray-900">₹{{ number_format((float) ($paymentStats['gst'] ?? 0), 0) }}</p>
            <p class="mt-1 text-xs text-gray-500">Base: ₹{{ number_format((float) ($paymentStats['base'] ?? 0), 0) }}</p>
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

    {{-- === Advanced SaaS Analytics Additions (Only adds; does not change existing UI) === --}}

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow border lg:col-span-1">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Monthly Comparison</h2>
                    <p class="text-xs text-gray-500 mt-1">This month vs last month (paid revenue)</p>
                </div>
                @php
                    $growth = $revenueGrowthPct;
                    $growthPositive = $growth !== null && $growth >= 0;
                @endphp
                @if($growth !== null)
                    <span class="px-3 py-1 text-xs rounded-full font-bold {{ $growthPositive ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                        {{ $growthPositive ? '+' : '' }}{{ number_format((float) $growth, 1) }}%
                    </span>
                @else
                    <span class="px-3 py-1 text-xs rounded-full font-bold bg-slate-100 text-slate-600">N/A</span>
                @endif
            </div>

            <div class="mt-5 space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">{{ now()->format('F') }}</span>
                    <span class="font-black text-gray-900">&#8377;{{ number_format((float) $thisMonthRevenue, 0) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">{{ now()->subMonthNoOverflow()->format('F') }}</span>
                    <span class="font-black text-gray-900">&#8377;{{ number_format((float) $lastMonthRevenue, 0) }}</span>
                </div>
                <div class="pt-3 border-t text-xs text-gray-500">
                    Based on calendar months (server time).
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow border lg:col-span-2">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Monthly Revenue &amp; GST</h2>
                    <p class="text-xs text-gray-500 mt-1">Grouped by month for selected range</p>
                </div>
                <div class="text-xs text-gray-500">
                    Amounts shown in &#8377;
                </div>
            </div>
            <div class="mt-4">
                <canvas id="revenueGstChart" height="110"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow border">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Plan-wise Revenue Breakdown</h2>
                    <p class="text-xs text-gray-500 mt-1">&#8377;199 / &#8377;1299 / &#8377;4999 (paid revenue)</p>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center justify-center">
                    <canvas id="planBreakdownChart" height="170"></canvas>
                </div>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">&#8377;199 Plan (Starter)</span>
                        <span class="font-black text-gray-900">&#8377;{{ number_format((float) ($planPriceBreakdown['199'] ?? 0), 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">&#8377;1299 Plan (Pro)</span>
                        <span class="font-black text-gray-900">&#8377;{{ number_format((float) ($planPriceBreakdown['1299'] ?? 0), 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">&#8377;4999 Plan (Business)</span>
                        <span class="font-black text-gray-900">&#8377;{{ number_format((float) ($planPriceBreakdown['4999'] ?? 0), 0) }}</span>
                    </div>
                    <div class="pt-3 border-t text-xs text-gray-500">
                        Uses `payments.plan_name` mapping (starter/pro/business).
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow border">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Transactions</h2>
                    <p class="text-xs text-gray-500 mt-1">Payments in selected range</p>
                </div>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2">Date</th>
                            <th class="py-2">Plan Type</th>
                            <th class="py-2 text-right">Amount</th>
                            <th class="py-2 text-right">GST</th>
                            <th class="py-2">Payment Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $t)
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="py-3 text-gray-700">{{ \Carbon\Carbon::parse($t->created_at)->format('d M Y') }}</td>
                                <td class="py-3 font-semibold text-gray-800">{{ ucfirst((string) $t->plan_name) }}</td>
                                <td class="py-3 text-right text-gray-700">&#8377;{{ number_format((float) $t->amount, 0) }}</td>
                                <td class="py-3 text-right text-gray-700">&#8377;{{ number_format((float) ($t->gst_amount ?? 0), 0) }}</td>
                                <td class="py-3">
                                    <span class="px-2 py-1 text-xs rounded-full font-semibold {{ $t->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($t->status === 'failed' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700') }}">
                                        {{ ucfirst((string) $t->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500">No transactions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $transactions->links() }}
            </div>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
  (function () {
    if (!window.Chart) return;

    const monthlyLabels = @json($monthlyLabels ?? []);
    const monthlyRevenue = @json($monthlyRevenue ?? []);
    const monthlyGst = @json($monthlyGst ?? []);

    const revenueCanvas = document.getElementById('revenueGstChart');
    if (revenueCanvas && monthlyLabels.length) {
      new Chart(revenueCanvas, {
        type: 'bar',
        data: {
          labels: monthlyLabels,
          datasets: [
            {
              label: 'Revenue (₹)',
              data: monthlyRevenue,
              backgroundColor: 'rgba(79, 70, 229, 0.35)',
              borderColor: 'rgba(79, 70, 229, 1)',
              borderWidth: 1,
              borderRadius: 8,
            },
            {
              label: 'GST (₹)',
              data: monthlyGst,
              backgroundColor: 'rgba(16, 185, 129, 0.35)',
              borderColor: 'rgba(16, 185, 129, 1)',
              borderWidth: 1,
              borderRadius: 8,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              ticks: {
                callback: (v) => '₹' + v,
              },
              grid: { color: 'rgba(148,163,184,0.25)' },
            },
            x: {
              grid: { display: false },
            },
          },
          plugins: {
            legend: { position: 'top' },
            tooltip: {
              callbacks: {
                label: (ctx) => `${ctx.dataset.label}: ₹${Number(ctx.raw || 0).toLocaleString('en-IN')}`,
              },
            },
          },
        },
      });
    }

    const planBreakdown = @json($planPriceBreakdown ?? []);
    const planCanvas = document.getElementById('planBreakdownChart');
    if (planCanvas) {
      new Chart(planCanvas, {
        type: 'doughnut',
        data: {
          labels: ['₹199 (Starter)', '₹1299 (Pro)', '₹4999 (Business)'],
          datasets: [{
            data: [planBreakdown['199'] || 0, planBreakdown['1299'] || 0, planBreakdown['4999'] || 0],
            backgroundColor: [
              'rgba(99, 102, 241, 0.85)',
              'rgba(16, 185, 129, 0.85)',
              'rgba(245, 158, 11, 0.85)',
            ],
            borderColor: '#ffffff',
            borderWidth: 2,
          }],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '62%',
          plugins: {
            legend: { position: 'bottom' },
            tooltip: {
              callbacks: {
                label: (ctx) => `${ctx.label}: ₹${Number(ctx.raw || 0).toLocaleString('en-IN')}`,
              },
            },
          },
        },
      });
    }
  })();
</script>
@endsection
