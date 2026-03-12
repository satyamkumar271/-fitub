@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4 max-w-4xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $ticket->subject }}</h1>
            <p class="text-sm text-gray-500">Ticket #{{ $ticket->id }} | {{ strtoupper($ticket->status) }}</p>
        </div>
        <a href="{{ route('support.index') }}" class="bg-white border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg hover:bg-gray-50 text-sm">
            Back
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg">
        <div class="p-4 h-[420px] overflow-y-auto space-y-3 bg-gray-50">
            @forelse($ticket->messages as $message)
                @php $isMine = $message->sender_id === auth()->id(); @endphp
                @php
                    $senderLabel = $isMine
                        ? (auth()->user()->name ?? 'You')
                        : (($message->sender && $message->sender->user_type === 'admin')
                            ? 'Support Team'
                            : ($message->sender->name ?? 'Support Team'));
                @endphp
                <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[75%] rounded-xl px-4 py-2 shadow {{ $isMine ? 'bg-indigo-600 text-white' : 'bg-white text-gray-800' }}">
                        <p class="text-xs opacity-80 mb-1">{{ $senderLabel }}</p>
                        <p class="text-sm whitespace-pre-wrap">{{ $message->message }}</p>
                        <p class="text-[10px] mt-1 opacity-70 text-right">{{ $message->created_at->format('d M, h:i A') }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center pt-6">No messages in this ticket yet.</p>
            @endforelse
        </div>

        <div class="p-4 border-t border-gray-200">
            <form action="{{ route('support.reply', $ticket) }}" method="POST" class="flex gap-2">
                @csrf
                <textarea name="message" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Write a reply..." required></textarea>
                <button type="submit" class="bg-indigo-600 text-white font-semibold px-5 rounded-lg hover:bg-indigo-700">Send</button>
            </form>
        </div>
    </div>
</div>
@endsection
