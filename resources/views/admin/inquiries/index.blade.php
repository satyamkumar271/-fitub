@extends('admin.layouts.app')

@section('title', 'Customer Inquiries')

@section('content')

    {{-- 1. Page Header with a subtle gradient --}}
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 tracking-tight">Inquiry Inbox</h1>
        <p class="mt-1 text-md text-gray-500">The latest customer requests ready for your action.</p>
    </div>

    {{-- 2. Success Message --}}
    @if (session('success'))
        <div class="bg-teal-50 border-l-4 border-teal-500 text-teal-800 p-4 mb-6 rounded-md shadow-sm" role="alert">
            <p class="font-bold">{{ session('success') }}</p>
        </div>
    @endif

    {{-- 3. The Main Inquiry List Container --}}
    <div class="space-y-4">

        @forelse($inquiries as $inquiry)
            {{-- Each Inquiry is a Card --}}
            <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-shadow duration-300 ease-in-out overflow-hidden">
                <div class="flex">
                    {{-- Status Color Bar --}}
                    <div class="w-2
                        @if($inquiry->status == 'pending') bg-amber-400
                        @elseif($inquiry->status == 'forwarded') bg-blue-500
                        @elseif($inquiry->status == 'viewed') bg-green-500
                        @else bg-gray-300 @endif">
                    </div>

                    <div class="flex-1 p-5 sm:p-6">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-x-6 gap-y-4">

                            {{-- Avatars & Names (Customer -> Recipient) --}}
                            <div class="md:col-span-4 flex items-center space-x-4">
                                {{-- Customer Avatar --}}
                                <img class="h-12 w-12 rounded-full object-cover ring-2 ring-white" src="{{ 'https://ui-avatars.com/api/?name=' . urlencode($inquiry->user->name ?? $inquiry->guest_name) . '&background=fde68a&color=92400e' }}" alt="">

                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>

                                {{-- Recipient Avatar --}}
                                <img class="h-12 w-12 rounded-full object-cover ring-2 ring-white" src="{{ 'https://ui-avatars.com/api/?name=' . urlencode($inquiry->recipient->name) . '&background=93c5fd&color=1e3a8a' }}" alt="">

                                {{-- Names (Visible on mobile, hidden on desktop) --}}
                                <div class="md:hidden">
                                     <p class="font-bold text-gray-800">{{ $inquiry->user->name ?? $inquiry->guest_name }}</p>
                                     <p class="text-sm text-gray-500">to {{ $inquiry->recipient->name }}</p>
                                </div>
                            </div>

                            {{-- Details Section --}}
                            <div class="md:col-span-5">
                                {{-- Names (Hidden on mobile, visible on desktop) --}}
                                <div class="hidden md:block">
                                     <p class="font-bold text-gray-800">{{ $inquiry->user->name ?? $inquiry->guest_name }}</p>
                                     <p class="text-sm text-gray-500">to {{ $inquiry->recipient->name }} ({{ $inquiry->recipient->user_type }})</p>
                                </div>
                                <p class="mt-2 text-gray-700 font-semibold">{{ $inquiry->service_needed }}</p>
                                <p class="text-sm text-gray-600 truncate" title="{{ $inquiry->message }}">{{ $inquiry->message }}</p>
                            </div>

                            {{-- Action Section --}}
                            <div class="md:col-span-3 flex items-center justify-end space-x-4">
                                <div class="text-right">
                                    <p class="text-xs text-gray-400">{{ $inquiry->created_at->diffForHumans() }}</p>
                                    @if($inquiry->status == 'pending')
                                        <form class="mt-2" action="{{ route('admin.inquiries.forward', $inquiry) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white font-bold py-2 px-5 rounded-lg shadow-md hover:scale-105 transform transition-all duration-300">
                                                Forward
                                            </button>
                                        </form>
                                    @else
                                        <div class="mt-2 flex items-center justify-end space-x-2 text-green-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="font-semibold text-sm">Completed</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @empty
            <div class="text-center py-20 px-6 bg-white rounded-lg shadow-md">
                <svg class="mx-auto h-16 w-16 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
                <h3 class="mt-4 text-xl font-semibold text-gray-800">Inbox Zero!</h3>
                <p class="mt-1 text-md text-gray-500">You're all caught up. New inquiries will appear here.</p>
            </div>
        @endforelse
    </div>

    {{-- 4. Pagination --}}
    @if ($inquiries->hasPages())
        <div class="mt-8">
           {{ $inquiries->links() }}
        </div>
    @endif

@endsection
