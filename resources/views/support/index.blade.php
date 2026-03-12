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
            <h2 class="text-lg font-bold text-gray-800 mb-4">Create Ticket</h2>
            <form action="{{ route('support.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Subject</label>
                    <input type="text" name="subject" value="{{ old('subject') }}" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Message</label>
                    <textarea name="message" rows="5" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 text-sm" required>{{ old('message') }}</textarea>
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
                        <div>
                            <p class="font-bold text-gray-900">{{ $ticket->subject }}</p>
                            <p class="text-xs text-gray-500 mt-1">Created {{ $ticket->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 uppercase">{{ $ticket->status }}</span>
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
