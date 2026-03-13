@extends('admin.layouts.app')

@section('title', 'Inquiry Reports')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 tracking-tight">Inquiry Reports</h1>
        <p class="mt-1 text-md text-gray-500">Moderate abuse, spam, and fake lead disputes.</p>
    </div>

    <div class="mb-6 flex flex-wrap gap-2">
        @php
            $tabs = ['open' => 'Open', 'under_review' => 'Under Review', 'resolved_valid' => 'Resolved Valid', 'resolved_rejected' => 'Resolved Rejected', 'all' => 'All'];
        @endphp
        @foreach($tabs as $key => $label)
            <a href="{{ route('admin.reports.index', ['status' => $key]) }}"
               class="px-4 py-2 rounded-full text-sm font-semibold {{ ($status ?? 'open') === $key ? 'bg-gray-900 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="space-y-4">
        @forelse($reports as $report)
            <a href="{{ route('admin.reports.show', $report) }}" class="block bg-white rounded-xl shadow p-5 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-bold text-gray-900">{{ strtoupper($report->reason) }}</p>
                        <p class="text-sm text-gray-600">
                            Reporter: {{ $report->reporter->name ?? 'N/A' }}
                            @if($report->reportedUser) | Reported: {{ $report->reportedUser->name }} @endif
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Inquiry #{{ $report->inquiry_id }} | {{ $report->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 uppercase">
                        {{ str_replace('_', ' ', $report->status) }}
                    </span>
                </div>
            </a>
        @empty
            <div class="bg-white rounded-xl shadow p-8 text-center text-gray-500">No reports found.</div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $reports->links() }}
    </div>
@endsection

