@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4 max-w-5xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Conversations</h1>
        <p class="text-sm text-gray-500">Chats with gyms, trainers and customers.</p>
    </div>

    @if($conversations->count() === 0)
        <div class="bg-white rounded-2xl shadow-sm p-10 text-center text-gray-500">
            No active conversations yet.
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @foreach($conversations as $inquiry)
                @php
                    $otherUser = $inquiry->recipient_id === auth()->id() ? $inquiry->user : $inquiry->recipient;
                    $lastMessage = $inquiry->messages->first();
                @endphp
                <a href="{{ route('inquiries.chat', $inquiry) }}"
                   class="flex items-center justify-between px-5 py-4 hover:bg-gray-50 transition border-b border-gray-50 last:border-b-0">
                    <div class="flex items-center gap-4">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-semibold">
                            {{ strtoupper(substr($otherUser?->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="flex flex-col space-y-0.5">
                            <div class="flex items-center gap-2">
                                <p class="font-semibold text-gray-900 truncate max-w-xs">
                                    {{ $otherUser?->name ?? 'User' }}
                                </p>
                                <span class="text-[11px] text-gray-400">
                                    #{{ $inquiry->id }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 truncate max-w-md">
                                @if($lastMessage)
                                    {{ $lastMessage->sender_id === auth()->id() ? 'You: ' : '' }}{{ \Illuminate\Support\Str::limit($lastMessage->message, 80) }}
                                @else
                                    Service: {{ $inquiry->service_needed }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-col items-end space-y-1">
                        <span class="text-[11px] text-gray-400">
                            {{ $inquiry->updated_at->format('d M, h:i A') }}
                        </span>
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full
                            @if($inquiry->status === 'viewed') bg-green-100 text-green-700
                            @else bg-gray-100 text-gray-700
                            @endif uppercase">
                            {{ $inquiry->status }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection

