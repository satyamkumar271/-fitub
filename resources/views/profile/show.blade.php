@extends('layouts.app')

@section('content')
{{-- Alpine.js is required for the modal --}}
<script src="//unpkg.com/alpinejs" defer></script>

<div x-data="{ contactPanelOpen: false, recipientId: {{ $user->id }} }">
    <div class="bg-slate-100 font-sans">
        <div class="container mx-auto py-8 px-4">

            {{-- Flash Message with Icon --}}
            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 text-green-800 p-4 mb-8 rounded-lg shadow-md flex items-center" role="alert">
                    <svg class="w-6 h-6 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                    <div>
                        <p class="font-bold">Success!</p>
                        <p>{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            {{-- START: PROFILE HEADER --}}
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden mb-8">
                {{-- Banner with a subtle pattern --}}
                <div class="h-48 md:h-64 relative">
                    <div class="w-full h-full bg-gradient-to-r from-indigo-600 to-purple-700">
                        {{-- Subtle pattern for texture --}}
                        <svg class="absolute inset-0 w-full h-full text-white/10" xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="currentColor" fill-opacity="0.2">
                            <defs><pattern id="p" width="100" height="100" patternUnits="userSpaceOnUse" patternTransform="scale(0.5)"><path d="M50 0 L100 25 L100 75 L50 100 L0 75 L0 25 Z" stroke-width="2.5" stroke="currentColor" fill="none"></path></pattern></defs><rect width="100%" height="100%" fill="url(#p)"></rect>
                        </svg>
                    </div>
                    <div class="absolute inset-0 bg-black bg-opacity-30"></div>
                </div>
                {{-- Profile Info --}}
                <div class="px-6 md:px-10 pb-8 -mt-20 md:-mt-24 relative z-10">
                    <div class="flex flex-col md:flex-row items-center md:items-end">
                        <img class="h-32 w-32 md:h-40 md:w-40 rounded-full object-cover border-4 border-white shadow-lg" src="{{ $user->profile_photo_path ? Storage::url($user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=1f2937&color=fff&size=160' }}" alt="Profile Photo">

                        <div class="md:ml-6 mt-4 md:mt-0 text-center md:text-left flex-grow">
                            <h1 class="text-3xl lg:text-4xl font-extrabold text-white" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
                                {{ $user->gym_name ?? $user->name }}
                            </h1>
                             @if($user->user_type == 'gymowner' && ($user->address_city || $user->address_state))
                                <p class="font-semibold text-lg text-slate-200" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">{{ $user->address_city ?? 'Fitness Center' }}{{ $user->address_city && $user->address_state ? ',' : '' }} {{ $user->address_state ?? '' }}</p>
                            @elseif($user->user_type == 'trainer')
                                <p class="font-semibold text-lg capitalize text-slate-200" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">{{ $user->specialization ?? 'Certified Professional' }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            {{-- END: PROFILE HEADER --}}

            {{-- START: MAIN LAYOUT GRID --}}
            <div class="grid grid-cols-12 gap-8">

                {{-- Left Side - Main Content (8 columns) --}}
                <div class="col-span-12 lg:col-span-8 space-y-8">
                    {{-- About Section --}}
                    <div class="bg-white p-8 rounded-2xl shadow-xl border border-slate-200/50">
                        <h3 class="text-2xl font-bold text-slate-800 mb-4">About {{ $user->user_type == 'gymowner' ? 'The Gym' : 'Me' }}</h3>
                        <div class="prose prose-slate max-w-none">
                           <p>{{ $user->goal ?? 'Details about this ' . $user->user_type . ' will be updated soon. They are dedicated to providing the best fitness experience.' }}</p>
                        </div>
                    </div>

                    {{-- Certifications Section (Conditional) --}}
                    @if($user->user_type == 'trainer' && !empty($user->certifications) && is_array(json_decode($user->certifications, true)))
                        <div class="bg-white p-8 rounded-2xl shadow-xl border border-slate-200/50">
                            <h3 class="text-2xl font-bold text-slate-800 mb-6">Certifications</h3>
                            <div class="flex flex-wrap gap-3">
                                 @foreach(json_decode($user->certifications, true) as $cert)
                                    <span class="flex items-center bg-cyan-100 text-cyan-800 text-sm font-semibold px-4 py-2 rounded-full shadow-sm">
                                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
                                        {{ $cert['name'] ?? 'N/A' }}
                                    </span>
                                 @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Gallery Section (Fully Implemented) --}}
                    @if(in_array($user->user_type, ['trainer', 'gymowner']))
                        <div class="bg-white p-8 rounded-2xl shadow-xl border border-slate-200/50">
                            <h3 class="text-2xl font-bold text-slate-800 mb-6">{{ $user->user_type == 'trainer' ? 'Client Transformations' : 'Our Gallery' }}</h3>
                            @if(!empty($user->gallery_images))
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    @foreach($user->gallery_images as $imagePath)
                                        <a href="{{ Storage::url($imagePath) }}" data-fancybox="gallery" class="block group rounded-lg overflow-hidden">
                                            <img src="{{ Storage::url($imagePath) }}" alt="{{ $user->name }} gallery image" class="aspect-square object-cover w-full h-full transform group-hover:scale-110 transition-transform duration-300">
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12 px-6 bg-slate-50 rounded-lg border-2 border-dashed border-slate-300">
                                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l-1.586-1.586a2 2 0 01-2.828 0L6 14" /></svg>
                                    <h3 class="mt-2 text-sm font-medium text-slate-900">No photos uploaded yet</h3>
                                    <p class="mt-1 text-sm text-slate-500">The gallery will be available soon.</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Right Side - Sidebar (4 columns) --}}
                <div class="col-span-12 lg:col-span-4 space-y-8">
                    {{-- Contact Button --}}
                    <div class="bg-white p-6 rounded-2xl shadow-xl border border-slate-200/50">
                        <button @click="contactPanelOpen = true" class="w-full flex items-center justify-center bg-indigo-600 text-white font-bold py-4 px-8 rounded-xl hover:bg-indigo-700 transition-all duration-300 shadow-lg hover:shadow-indigo-500/50 transform hover:-translate-y-1">
                            <svg class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            Send Inquiry
                        </button>
                    </div>

                    {{-- Key Information Card --}}
                    <div class="bg-white p-6 rounded-2xl shadow-xl border border-slate-200/50">
                        <h3 class="text-xl font-bold text-slate-800 mb-6 border-b border-slate-200 pb-4">Key Information</h3>
                        <ul class="space-y-5">
                            @if($user->user_type == 'trainer')
                            <li class="flex items-start"><span class="bg-purple-100 p-3 rounded-full mr-4"><svg class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg></span><div><p class="text-sm text-slate-500">Experience</p><p class="font-bold text-slate-700">{{ $user->experience ?? 'N/A' }} years</p></div></li>
                            <li class="flex items-start"><span class="bg-pink-100 p-3 rounded-full mr-4"><svg class="h-5 w-5 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg></span><div><p class="text-sm text-slate-500">Specialization</p><p class="font-bold text-slate-700">{{ $user->specialization ?? 'N/A' }}</p></div></li>
                            @endif
                            @if($user->user_type == 'gymowner')
                            <li class="flex items-start"><span class="bg-teal-100 p-3 rounded-full mr-4"><svg class="h-5 w-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg></span><div><p class="text-sm text-slate-500">Established</p><p class="font-bold text-slate-700">{{ $user->gym_age ?? 'New' }}</p></div></li>
                            <li class="flex items-start"><span class="bg-blue-100 p-3 rounded-full mr-4"><svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg></span><div><p class="text-sm text-slate-500">Members</p><p class="font-bold text-slate-700">{{ $user->total_members ?? '0' }}+</p></div></li>
                            @endif
                            @if($user->address_city)
                            <li class="flex items-start"><span class="bg-orange-100 p-3 rounded-full mr-4"><svg class="h-5 w-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg></span><div><p class="text-sm text-slate-500">Location</p><p class="font-bold text-slate-700">{{ $user->address_city }}, {{ $user->address_state }}</p></div></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === SLIDE-OVER CONTACT PANEL (STYLED) === --}}
    <div x-show="contactPanelOpen" x-cloak class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div class="absolute inset-0 overflow-hidden">
            <div x-show="contactPanelOpen" @click="contactPanelOpen = false" class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
            <section class="absolute inset-y-0 right-0 pl-10 max-w-full flex" aria-labelledby="slide-over-title">
                <div x-show="contactPanelOpen" class="w-screen max-w-md" x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
                    <div class="h-full flex flex-col bg-white shadow-xl">
                        <div class="p-6 bg-indigo-700 text-white">
                            <h2 class="text-lg font-medium" id="slide-over-title">Contact Form</h2>
                            <p class="text-sm text-indigo-300 mt-1">Send an inquiry to {{ $user->gym_name ?? $user->name }}</p>
                        </div>
                        <div class="relative flex-1 py-6 px-4 sm:px-6 overflow-y-auto">
                            <form action="{{ route('inquiries.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="recipient_id" :value="recipientId">
                                <div class="space-y-5">
                                    @if(!Auth::check())
                                    <div><label for="guest_name_panel" class="font-medium text-gray-700 text-sm">Your Name*</label><input type="text" name="guest_name" id="guest_name_panel" value="{{ old('guest_name') }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required></div>
                                    <div><label for="guest_email_panel" class="font-medium text-gray-700 text-sm">Your Email*</label><input type="email" name="guest_email" id="guest_email_panel" value="{{ old('guest_email') }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required></div>
                                    <div><label for="guest_phone_panel" class="font-medium text-gray-700 text-sm">Your Phone*</label><input type="tel" name="guest_phone" id="guest_phone_panel" value="{{ old('guest_phone') }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required></div>
                                    @endif
                                    <div>
                                        <label for="service_needed_panel" class="font-medium text-gray-700 text-sm">Service Needed*</label>
                                        <select name="service_needed" id="service_needed_panel" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" required>
                                            <option value="" disabled selected>Select a service...</option>
                                            @if($user->user_type == 'trainer')
                                            <option value="Personal Training">Personal Training</option>
                                            <option value="Diet Plan">Diet Plan</option>
                                            <option value="Online Coaching">Online Coaching</option>
                                            @else
                                            <option value="Gym Membership">Gym Membership</option>
                                            <option value="Gym Tour">Gym Tour</option>
                                            @endif
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div><label for="message_panel" class="font-medium text-gray-700 text-sm">Message*</label><textarea name="message" id="message_panel" rows="4" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required>{{ old('message') }}</textarea></div>
                                </div>
                                <div class="pt-6 flex justify-end">
                                    <button @click="contactPanelOpen = false" type="button" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Cancel</button>
                                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Send</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Add Fancybox for a better gallery experience --}}
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
<script>
  document.addEventListener('DOMContentLoaded', () => {
    Fancybox.bind("[data-fancybox]", {
      // Your custom options can go here
      buttons: ["zoom", "slideShow", "thumbs", "close"],
      loop: true,
    });
  });
</script>
@endpush
