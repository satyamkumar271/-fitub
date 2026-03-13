@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4 max-w-4xl">
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
                    <form action="{{ route('inquiries.block', $inquiry) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-sm bg-red-600 text-white font-semibold px-3 py-1 rounded-md hover:bg-red-700">Block</button>
                    </form>
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
</div>
@endsection
