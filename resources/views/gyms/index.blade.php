@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 py-16">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">
                    @if ($searchTerm)
                        Gyms in <span class="text-indigo-600">{{ $searchTerm }}</span>
                    @else
                        All Gyms
                    @endif
                </h1>
                <p class="mt-3 text-lg text-gray-600 max-w-2xl mx-auto">Browse our collection of verified gyms.</p>
            </div>

            @if ($gyms->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @foreach ($gyms as $gym)
                        <div
                            class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col transform hover:-translate-y-1 transition-all duration-300">
                            <div class="p-6 flex-grow">
                                <h3 class="text-xl font-bold text-gray-900">{{ $gym->gym_name }}</h3>
                                @if($gym->user?->is_verified)
                                    <span class="inline-flex items-center mt-2 text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Verified</span>
                                @endif
                                <p class="text-sm text-gray-500 mt-2 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-gray-400"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{-- Code ko safe banaya gaya hai taaki error na aaye agar profile na ho --}}
                                    {{-- Gym ka naam dikhayein --}}


                                    {{-- Location dikhayein --}}
                                <p class="text-sm text-gray-500 mt-2 flex items-center">

                                    {{ $gym->address_city ?? 'N/A' }}, {{ $gym->address_state ?? 'N/A' }}
                                </p>
                                </p>
                            </div>
                            <div class="p-4 bg-gray-50 border-t">
                                {{-- ==================== YEH LINE BADLI GAYI HAI ==================== --}}
                                <a href="{{ route('profile.show', $gym->user->id) }}"
                                    class="text-indigo-600 font-semibold text-sm hover:underline">
                                    View Details →
                                </a>
                                {{-- =================================================================== --}}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{-- Yeh pagination ke liye zaroori hai --}}
                    {{ $gyms->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-16 bg-white rounded-lg shadow">
                    <p class="text-xl font-semibold text-gray-700">There are currently no gyms listed.</p>
                    <p class="text-gray-500 mt-2">
                        @if ($searchTerm)
                            We couldn't find any gyms matching "{{ $searchTerm }}". Try a different location.
                        @else
                            There are currently no gyms listed.
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
