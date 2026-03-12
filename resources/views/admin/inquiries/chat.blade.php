@extends('admin.layouts.app')

@section('title', 'Inquiry Chat')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Inquiry Chat #{{ $inquiry->id }}</h1>
            <p class="text-gray-500">
                {{ $inquiry->user->name ?? $inquiry->guest_name ?? 'Guest' }} to {{ $inquiry->recipient->name ?? 'N/A' }}
            </p>
        </div>
        <a href="{{ route('admin.inquiries.index') }}" class="bg-white border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg hover:bg-gray-50">
            Back to Inquiries
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <p class="text-sm text-gray-600"><strong>Service:</strong> {{ $inquiry->service_needed }}</p>
            <p class="text-sm text-gray-600"><strong>Status:</strong> {{ strtoupper($inquiry->status) }}</p>
        </div>
        <div class="p-4 h-[560px] overflow-y-auto space-y-3 bg-gray-50">
            @forelse($messages as $message)
                <div class="bg-white rounded-lg px-4 py-3 shadow-sm">
                    <p class="text-xs text-gray-500">{{ $message->sender->name ?? 'User' }} | {{ $message->created_at->format('d M Y, h:i A') }}</p>
                    <p class="text-sm text-gray-800 mt-1 whitespace-pre-wrap">{{ $message->message }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-500">No chat messages for this inquiry yet.</p>
            @endforelse
        </div>
    </div>
@endsection
