@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Support Team</h1>
        <p class="text-gray-600">Agar koi issue hai to ticket raise karein.</p>
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
        <div class="lg:col-span-1 bg-white rounded-xl shadow p-5">
            <h2 class="text-lg font-bold text-gray-800 mb-2">Create Ticket</h2>
            <p class="text-xs text-gray-500 mb-4">
                Please share maximum details so our support team can help you faster.
            </p>
            <form action="{{ route('support.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Issue Type</label>
                    <select name="issue_type" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 text-sm" required>
                        <option value="">Select issue type</option>
                        <option value="billing" {{ old('issue_type') === 'billing' ? 'selected' : '' }}>Billing / Payments</option>
                        <option value="login" {{ old('issue_type') === 'login' ? 'selected' : '' }}>Login / Account Access</option>
                        <option value="kyc" {{ old('issue_type') === 'kyc' ? 'selected' : '' }}>KYC / Verification</option>
                        <option value="leads" {{ old('issue_type') === 'leads' ? 'selected' : '' }}>Leads / Chat</option>
                        <option value="technical" {{ old('issue_type') === 'technical' ? 'selected' : '' }}>Technical Issue / Bug</option>
                        <option value="feedback" {{ old('issue_type') === 'feedback' ? 'selected' : '' }}>Suggestion / Feedback</option>
                        <option value="other" {{ old('issue_type') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Priority</label>
                    <select name="priority" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 text-sm" required>
                        <option value="low" {{ old('priority', 'normal') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Subject</label>
                    <input type="text" name="subject" value="{{ old('subject') }}" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Message</label>
                    <textarea name="message" rows="5" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 text-sm" placeholder="Explain your issue step by step..." required>{{ old('message') }}</textarea>
                </div>
                <div class="grid grid-cols-1 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Related Page / Feature (optional)</label>
                        <input type="text" name="related_page" value="{{ old('related_page') }}" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 text-sm" placeholder="e.g. Dashboard &gt; Leads">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Contact Phone (optional)</label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone') }}" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 text-sm" placeholder="+91-XXXXXXXXXX">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Attachment (optional)</label>
                    <input type="file" name="attachment" class="mt-1 w-full text-sm text-gray-700">
                    <p class="mt-1 text-xs text-gray-400">You can upload screenshot or PDF (max 4MB).</p>
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white font-semibold py-2 rounded-lg hover:bg-indigo-700">
                    Raise Ticket
                </button>
            </form>
        </div>

        <div class="lg:col-span-2 space-y-3">
            @forelse($tickets as $ticket)
                <a href="{{ route('support.show', $ticket) }}" class="block bg-white rounded-xl shadow p-5 hover:shadow-lg transition">
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <p class="font-bold text-gray-900">{{ $ticket->subject }}</p>
                            <p class="text-xs text-gray-500">Created {{ $ticket->created_at->format('d M Y, h:i A') }}</p>
                            <div class="flex flex-wrap gap-2">
                                @if($ticket->issue_type)
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700">
                                        {{ ucfirst($ticket->issue_type) }}
                                    </span>
                                @endif
                                @if($ticket->priority)
                                    @php
                                        $priorityColor = match($ticket->priority) {
                                            'urgent' => 'bg-red-100 text-red-800',
                                            'high' => 'bg-orange-100 text-orange-800',
                                            'low' => 'bg-gray-100 text-gray-700',
                                            default => 'bg-green-100 text-green-800',
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $priorityColor }}">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                            @if($ticket->status === 'resolved') bg-green-100 text-green-800
                            @elseif($ticket->status === 'in_progress') bg-amber-100 text-amber-800
                            @else bg-gray-100 text-gray-700
                            @endif uppercase">
                            {{ $ticket->status }}
                        </span>
                    </div>
                </a>
            @empty
                <div class="bg-white rounded-xl shadow p-8 text-center text-gray-500">No support tickets yet.</div>
            @endforelse

            <div class="mt-3">
                {{ $tickets->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
