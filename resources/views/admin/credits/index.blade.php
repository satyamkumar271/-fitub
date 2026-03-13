@extends('admin.layouts.app')

@section('title', 'Credit History')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 tracking-tight">Unlock Credit History</h1>
        <p class="mt-1 text-md text-gray-500">Track compensation grants and credit usage.</p>
    </div>

    <div class="bg-white rounded-xl shadow p-5 mb-6">
        <form method="GET" action="{{ route('admin.credits.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs text-gray-500 mb-1">User ID</label>
                <input type="number" name="user_id" value="{{ request('user_id') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Source</label>
                <select name="source_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">All</option>
                    <option value="inquiry_report_compensation" {{ request('source_type') === 'inquiry_report_compensation' ? 'selected' : '' }}>Report Compensation</option>
                    <option value="lead_unlock" {{ request('source_type') === 'lead_unlock' ? 'selected' : '' }}>Lead Unlock Usage</option>
                </select>
            </div>
            <div class="md:col-span-2 flex items-end gap-2">
                <button type="submit" class="bg-indigo-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm">Apply Filter</button>
                <a href="{{ route('admin.credits.index') }}" class="bg-white border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg hover:bg-gray-50 text-sm">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Delta</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Balance After</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Source</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Details</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-xs text-gray-500">{{ $log->created_at?->format('d M Y, h:i A') }}</td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-800">{{ $log->user->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">ID: {{ $log->user_id }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-bold {{ $log->delta > 0 ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $log->delta > 0 ? '+' : '' }}{{ $log->delta }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-700">{{ $log->balance_after ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ $log->source_type ?? 'manual' }}</td>
                            <td class="px-6 py-4">
                                <div class="text-gray-700">{{ $log->note ?? '-' }}</div>
                                @if($log->source_type === 'inquiry_report_compensation')
                                    <a href="{{ route('admin.reports.show', $log->source_id) }}" class="text-xs text-indigo-600 hover:underline">View report #{{ $log->source_id }}</a>
                                @elseif($log->source_type === 'lead_unlock')
                                    <span class="text-xs text-gray-500">Inquiry #{{ $log->source_id }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-700">{{ $log->creator->name ?? 'System' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500">No credit history found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $logs->links() }}
    </div>
@endsection
