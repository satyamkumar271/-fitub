@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 py-16">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">
                    @if ($searchTerm)
                        Trainers for <span class="text-purple-600">{{ $searchTerm }}</span>
                    @else
                        All Trainers
                    @endif
                </h1>
                <p class="mt-3 text-lg text-gray-600 max-w-2xl mx-auto">Find certified professional trainers near you.</p>
            </div>

            @if ($trainers->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @foreach ($trainers as $trainer)
                        <div
                            class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col transform hover:-translate-y-1 transition-all duration-300">
                            <div class="p-6 flex-grow">
                                <h3 class="text-xl font-bold text-gray-900">{{ $trainer->name }}</h3>
                                <p class="text-sm text-gray-500 mt-2 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-gray-400"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{-- Data ko profile relationship se access kiya gaya hai --}}
                                    <!-- Ab aisa hoga -->
                                    {{-- Location dikhayein --}}
                                <p class="text-sm text-gray-500 mt-2 flex items-center">

                                    {{ $trainer->trainer_city ?? 'N/A' }}, {{ $trainer->trainer_state ?? 'N/A' }}
                                </p>

                                {{-- Specialization dikhayein --}}
                                <p class="text-xs text-purple-600 font-bold mt-2 uppercase">
                                    {{ $trainer->specialization ?? 'Fitness Expert' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 border-t">
                                {{-- ==================== YEH LINE BADLI GAYI HAI ==================== --}}
                                <a href="{{ route('profile.show', $trainer) }}"
                                    class="text-purple-600 font-semibold text-sm hover:underline">
                                    View Profile →
                                </a>
                                {{-- =================================================================== --}}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{-- Pagination ke liye zaroori hai --}}
                    {{ $trainers->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-16 bg-white rounded-lg shadow">
                    <p class="text-xl font-semibold text-gray-700">No trainers found.</p>
                    <p class="text-gray-500 mt-2">
                        @if ($searchTerm)
                            We couldn't find any trainers matching "{{ $searchTerm }}". Try a different search.
                        @else
                            There are currently no trainers listed.
                        @endif
                    </p>
                    <a href="{{ url('/') }}"
                        class="mt-6 inline-block bg-indigo-600 text-white font-semibold px-6 py-2 rounded-lg">Back to
                        Home</a>
                </div>
            @endif
        </div>
    </div>
@endsection
