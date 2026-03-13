@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">My Inquiries</h1>
        <p class="text-gray-600">Track your inquiries and continue chat where available.</p>
    </div>

    <div class="space-y-4">
        @forelse($inquiries as $inquiry)
            <div class="bg-white rounded-xl shadow p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-bold text-gray-900">{{ $inquiry->service_needed }}</p>
                        <p class="text-sm text-gray-500">To: {{ $inquiry->recipient->name ?? 'N/A' }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700 uppercase">{{ $inquiry->status }}</span>
                </div>
                <p class="text-sm text-gray-600 mt-2">{{ \Illuminate\Support\Str::limit($inquiry->message, 150) }}</p>
                <div class="mt-4">
                    <a href="{{ route('inquiries.chat', $inquiry) }}" class="inline-block bg-indigo-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm">
                        Open Chat
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow p-8 text-center text-gray-500">
                No inquiries found.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $inquiries->links() }}
    </div>
</div>
@endsection

