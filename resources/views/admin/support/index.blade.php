@extends('admin.layouts.app')

@section('title', 'Support Tickets')

@section('content')
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 tracking-tight">Support Tickets</h1>
        <p class="mt-1 text-md text-gray-500">User support requests and issue resolution.</p>
    </div>

    <div class="mb-6 flex flex-wrap gap-2">
        @php
            $tabs = ['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'all' => 'All'];
        @endphp
        @foreach($tabs as $key => $label)
            <a href="{{ route('admin.support.index', ['status' => $key]) }}"
               class="px-4 py-2 rounded-full text-sm font-semibold {{ ($status ?? 'open') === $key ? 'bg-gray-900 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="space-y-4">
        @forelse($tickets as $ticket)
            <a href="{{ route('admin.support.show', $ticket) }}" class="block bg-white rounded-xl shadow p-5 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-bold text-gray-900">{{ $ticket->subject }}</p>
                        <p class="text-sm text-gray-600">User: {{ $ticket->user->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500 mt-1">#{{ $ticket->id }} | {{ $ticket->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 uppercase">
                        {{ $ticket->status }}
                    </span>
                </div>
            </a>
        @empty
            <div class="bg-white rounded-xl shadow p-8 text-center text-gray-500">No support tickets found.</div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $tickets->links() }}
    </div>
@endsection
