@extends('admin.layouts.app')

@section('title', 'Support Ticket Detail')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ $ticket->subject }}</h1>
            <p class="text-gray-500">Ticket #{{ $ticket->id }} | {{ strtoupper($ticket->status) }}</p>
        </div>
        <a href="{{ route('admin.support.index') }}" class="bg-white border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg hover:bg-gray-50">
            Back
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow p-5 space-y-2">
                <h3 class="font-bold text-gray-800 mb-3">Ticket Info</h3>
                <p class="text-sm text-gray-600"><strong>User:</strong> {{ $ticket->user->name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600"><strong>Status:</strong> {{ strtoupper($ticket->status) }}</p>
                <p class="text-sm text-gray-600"><strong>Created:</strong> {{ $ticket->created_at->format('d M Y, h:i A') }}</p>
                @if($ticket->issue_type)
                    <p class="text-sm text-gray-600"><strong>Issue Type:</strong> {{ ucfirst($ticket->issue_type) }}</p>
                @endif
                @if($ticket->priority)
                    <p class="text-sm text-gray-600">
                        <strong>Priority:</strong>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                            @if($ticket->priority === 'urgent') bg-red-100 text-red-800
                            @elseif($ticket->priority === 'high') bg-orange-100 text-orange-800
                            @elseif($ticket->priority === 'low') bg-gray-100 text-gray-700
                            @else bg-green-100 text-green-800
                            @endif">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </p>
                @endif
                @if($ticket->related_page)
                    <p class="text-sm text-gray-600"><strong>Related Page:</strong> {{ $ticket->related_page }}</p>
                @endif
                @if($ticket->contact_phone)
                    <p class="text-sm text-gray-600"><strong>Contact Phone:</strong> {{ $ticket->contact_phone }}</p>
                @endif
                @if($ticket->attachment_path)
                    <p class="text-sm text-gray-600">
                        <strong>Attachment:</strong>
                        <a href="{{ Storage::disk('public')->url($ticket->attachment_path) }}"
                           target="_blank"
                           class="text-indigo-600 hover:text-indigo-800">
                            View file
                        </a>
                    </p>
                @endif
                @if($ticket->resolver)
                    <p class="text-sm text-gray-600"><strong>Resolved By:</strong> {{ $ticket->resolver->name }}</p>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow p-5">
                <h3 class="font-bold text-gray-800 mb-3">Status</h3>
                <form action="{{ route('admin.support.resolve', $ticket) }}" method="POST" class="space-y-3">
                    @csrf
                    <select name="status" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                        <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                    <button type="submit" class="w-full bg-indigo-600 text-white font-semibold py-2 rounded-lg hover:bg-indigo-700">
                        Update Status
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow">
                <div class="p-4 h-[520px] overflow-y-auto space-y-3 bg-gray-50">
                    @forelse($ticket->messages as $message)
                        @php $isMine = $message->sender_id === auth()->id(); @endphp
                        <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[75%] rounded-xl px-4 py-2 shadow {{ $isMine ? 'bg-indigo-600 text-white' : 'bg-white text-gray-800' }}">
                                <p class="text-xs opacity-80 mb-1">{{ $message->sender->name ?? 'User' }}</p>
                                <p class="text-sm whitespace-pre-wrap">{{ $message->message }}</p>
                                <p class="text-[10px] mt-1 opacity-70 text-right">{{ $message->created_at->format('d M, h:i A') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center pt-6">No messages yet.</p>
                    @endforelse
                </div>
                <div class="p-4 border-t border-gray-200">
                    <form action="{{ route('admin.support.reply', $ticket) }}" method="POST" class="flex gap-2">
                        @csrf
                        <textarea name="message" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Write reply for user..." required></textarea>
                        <button type="submit" class="bg-indigo-600 text-white font-semibold px-5 rounded-lg hover:bg-indigo-700">Send</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
