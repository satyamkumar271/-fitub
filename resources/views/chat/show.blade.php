@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4 max-w-4xl" x-data="{ blockModalOpen: false }">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Inquiry Chat</h1>
            <p class="text-sm text-gray-500">{{ $inquiry->service_needed }} | {{ $otherUser?->name ?? 'No customer account' }}</p>
        </div>
        <div class="flex items-center gap-2">
            @if(auth()->id() === $inquiry->user_id)
                <a href="{{ route('inquiries.mine') }}" class="bg-white border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg hover:bg-gray-50 text-sm">Back</a>
            @else
                <a href="{{ route('dashboard.leads') }}" class="bg-white border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg hover:bg-gray-50 text-sm">Back</a>
            @endif
        </div>
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

    <div class="mb-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
        Use chat only for genuine inquiry follow-up. Abuse, spam, fake offers, or suspicious payment requests are not allowed. This conversation may be reviewed for support and dispute resolution.
    </div>

    <div class="bg-white rounded-xl shadow-lg">
        <div class="p-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-2">
            <div class="text-sm text-gray-600">
                Status: <span class="font-semibold text-gray-800 uppercase">{{ $inquiry->status }}</span>
            </div>
            <div class="flex items-center gap-2">
                @if($otherUser)
                    <form action="{{ route('inquiries.report', $inquiry) }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        <select name="reason" class="text-sm border border-gray-300 rounded-md px-2 py-1">
                            <option value="abuse">Abuse</option>
                            <option value="spam">Spam</option>
                            <option value="fake_lead">Fake Lead</option>
                            <option value="other">Other</option>
                        </select>
                        <button type="submit" class="text-sm bg-amber-500 text-white font-semibold px-3 py-1 rounded-md hover:bg-amber-600">Report</button>
                    </form>
                    <button type="button"
                            @click="blockModalOpen = true"
                            class="text-sm bg-red-600 text-white font-semibold px-3 py-1 rounded-md hover:bg-red-700">
                        Block
                    </button>
                @endif
            </div>
        </div>

        <div class="p-4 h-[420px] overflow-y-auto space-y-3 bg-gray-50">
            @forelse($messages as $message)
                @php $isMine = $message->sender_id === auth()->id(); @endphp
                <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[75%] rounded-xl px-4 py-2 shadow {{ $isMine ? 'bg-indigo-600 text-white' : 'bg-white text-gray-800' }}">
                        <p class="text-xs opacity-80 mb-1">{{ $message->sender->name ?? 'User' }}</p>
                        <p class="text-sm whitespace-pre-wrap">{{ $message->message }}</p>
                        <p class="text-[10px] mt-1 opacity-70 text-right">{{ $message->created_at->format('d M, h:i A') }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center pt-6">No chat messages yet.</p>
            @endforelse
        </div>

        <div class="p-4 border-t border-gray-200">
            @if($isBlocked)
                <p class="text-sm text-red-600 font-semibold">Chat is blocked for this inquiry.</p>
            @elseif(!$canSend)
                <p class="text-sm text-gray-500">Chat will be enabled after lead access/unlock.</p>
            @else
                <form action="{{ route('inquiries.chat.send', $inquiry) }}" method="POST" class="flex gap-2">
                    @csrf
                    <textarea name="message" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Type your message..." required></textarea>
                    <button type="submit" class="bg-indigo-600 text-white font-semibold px-5 rounded-lg hover:bg-indigo-700">Send</button>
                </form>
            @endif
        </div>
    </div>

    {{-- Block User Modal --}}
    @if($otherUser)
        <div x-show="blockModalOpen"
             x-cloak
             class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-50">
            <div @click.away="blockModalOpen = false"
                 class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
                <div class="px-5 py-4 border-b flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Block User</h2>
                    <button type="button" @click="blockModalOpen = false" class="text-gray-500 hover:text-gray-700 text-xl leading-none">&times;</button>
                </div>
                <form action="{{ route('inquiries.block', $inquiry) }}" method="POST" class="px-5 py-4 space-y-4">
                    @csrf
                    <p class="text-sm text-gray-600">
                        You are about to block <span class="font-semibold">{{ $otherUser->name }}</span> for this inquiry.
                        Please provide a clear reason. This will be visible to the admin team.
                    </p>
                    <div>
                        <label for="block_reason" class="block text-sm font-medium text-gray-700 mb-1">
                            Reason for blocking <span class="text-red-500">*</span>
                        </label>
                        <textarea id="block_reason"
                                  name="reason"
                                  rows="3"
                                  required
                                  maxlength="1000"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                  placeholder="Example: User is sending abusive messages / spam / irrelevant offers."></textarea>
                        <p class="mt-1 text-xs text-gray-400">Maximum 1000 characters.</p>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button"
                                @click="blockModalOpen = false"
                                class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700">
                            Confirm Block
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
