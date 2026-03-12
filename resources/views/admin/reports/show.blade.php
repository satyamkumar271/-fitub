@extends('admin.layouts.app')

@section('title', 'Report Detail')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Report #{{ $report->id }}</h1>
            <p class="text-gray-500">Inquiry #{{ $report->inquiry_id }} | {{ strtoupper($report->reason) }}</p>
        </div>
        <a href="{{ route('admin.reports.index') }}" class="bg-white border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg hover:bg-gray-50">
            Back
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow p-5">
                <h3 class="font-bold text-gray-800 mb-3">Report Info</h3>
                <p class="text-sm text-gray-600"><strong>Status:</strong> {{ str_replace('_', ' ', strtoupper($report->status)) }}</p>
                <p class="text-sm text-gray-600 mt-1"><strong>Reporter:</strong> {{ $report->reporter->name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600 mt-1"><strong>Reported User:</strong> {{ $report->reportedUser->name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600 mt-1"><strong>Compensation Requested:</strong> {{ $report->compensation_requested ? 'Yes' : 'No' }}</p>
                <p class="text-sm text-gray-600 mt-1"><strong>Credit Granted:</strong> {{ !empty($creditLog) ? 'Yes' : 'No' }}</p>
                @if(!empty($creditLog))
                    <p class="text-sm text-gray-600 mt-1"><strong>Granted At:</strong> {{ $creditLog->created_at?->format('d M Y, h:i A') }}</p>
                @endif
                @if($report->details)
                    <p class="text-sm text-gray-600 mt-3"><strong>Details:</strong> {{ $report->details }}</p>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow p-5">
                <h3 class="font-bold text-gray-800 mb-3">Resolve</h3>
                <form action="{{ route('admin.reports.resolve', $report) }}" method="POST" class="space-y-3">
                    @csrf
                    <select name="action" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                        <option value="under_review">Mark Under Review</option>
                        <option value="valid">Resolve Valid</option>
                        <option value="rejected">Resolve Rejected</option>
                    </select>
                    <textarea name="admin_note" rows="4" placeholder="Admin note..." class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"></textarea>
                    @if($report->compensation_requested && empty($creditLog))
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="grant_credit" value="1" class="h-4 w-4 text-indigo-600 rounded border-gray-300">
                            <span class="text-sm text-gray-700">Grant 1 unlock credit</span>
                        </label>
                    @elseif($report->compensation_requested && !empty($creditLog))
                        <p class="text-sm text-green-700 bg-green-50 border border-green-200 rounded-md px-3 py-2">
                            Compensation credit already granted for this report.
                        </p>
                    @endif
                    <button type="submit" class="w-full bg-indigo-600 text-white font-semibold py-2 rounded-lg hover:bg-indigo-700">
                        Save Decision
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-800">Chat Transcript</h3>
                </div>
                <div class="p-4 h-[560px] overflow-y-auto space-y-3 bg-gray-50">
                    @forelse($messages as $message)
                        <div class="bg-white rounded-lg px-4 py-3 shadow-sm">
                            <p class="text-xs text-gray-500">{{ $message->sender->name ?? 'User' }} | {{ $message->created_at->format('d M Y, h:i A') }}</p>
                            <p class="text-sm text-gray-800 mt-1 whitespace-pre-wrap">{{ $message->message }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No chat messages available for this inquiry.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
