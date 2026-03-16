@extends('layouts.app')

@section('content')

{{-- 1. Hero Section: Modern Gradient & Better Typography --}}
<div class="relative text-white overflow-hidden">
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1571902943202-507ec2618e8f?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover"/>
        <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-900/80 to-indigo-900/70"></div>
    </div>
    <div class="relative container mx-auto px-6 text-center py-40 md:py-56">
        <h1 class="text-4xl md:text-6xl font-extrabold tracking-tighter leading-tight">
            Transform Your Body, Transform Your Life.
        </h1>
        <p class="mt-4 text-lg md:text-xl text-gray-300 max-w-3xl mx-auto">
            Fitub aapka personal fitness partner hai. Yahan aapko best trainers, gyms  milegi, sab ek hi jagah par.
        </p>
        {{-- =================================================================== --}}
        {{-- ======================= NEW SEARCH BAR SECTION ====================== --}}
        {{-- =================================================================== --}}
        <div class="mt-10 max-w-2xl mx-auto">
            {{-- Yeh form search ko handle karne wale route par data bhejega --}}
            <form action="{{ route('search.handle') }}" method="GET" class="w-full bg-white/90 backdrop-blur-sm rounded-full shadow-2xl p-2 flex items-center transition-all duration-300 focus-within:shadow-indigo-500/50">
                {{-- Type toggle (buttons instead of hidden looking select) --}}
                <input type="hidden" name="type" id="search-type" value="gym">
                <div class="flex-shrink-0 pl-2">
                    <div class="inline-flex bg-gray-100 rounded-full p-1 text-xs md:text-sm font-semibold">
                        <button type="button"
                                id="type-gym-btn"
                                class="px-3 md:px-4 py-1 rounded-full bg-white text-gray-900 shadow-sm">
                            Gyms
                        </button>
                        <button type="button"
                                id="type-trainer-btn"
                                class="px-3 md:px-4 py-1 rounded-full text-gray-600 hover:text-gray-900">
                            Trainers
                        </button>
                    </div>
                </div>

                {{-- Vertical Separator --}}
                <div class="w-px h-6 bg-gray-300 mx-2"></div>

                <input type="text" name="location" placeholder="Enter city, area or keyword..." required
                       class="w-full bg-transparent text-gray-800 placeholder-gray-500 border-none focus:ring-0 py-2 px-3 text-lg">

                <button type="submit" class="flex-shrink-0 ml-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-bold px-6 py-3 rounded-full hover:scale-105 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </button>
            </form>
        </div>
        {{-- ===================== END OF NEW SEARCH BAR SECTION ===================== --}}
        <a href="{{ route('register') }}"
           class="mt-10 inline-block bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-bold text-lg px-10 py-4 rounded-full shadow-2xl hover:shadow-indigo-500/50 transition-all transform hover:scale-105">
            Join Now & Get Started
        </a>
    </div>
</div>

{{-- 2. Features Section: Improved Card Design --}}
<div class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Everything You Need For Fitness</h2>
            <p class="text-gray-600 mt-2">Hum aapki fitness journey ko aasaan aur effective banate hain.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white p-8 text-center rounded-xl shadow-lg border border-gray-200/80 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                <div class="bg-indigo-100 text-indigo-500 rounded-full p-4 w-20 h-20 mx-auto mb-6 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                </div>
                <h3 class="text-xl font-semibold mb-2 text-gray-800">Find Expert Trainers</h3>
                <p class="text-gray-600">Apne sheher ke best certified trainers se judiye aur apne fitness goals ko achieve karein.</p>
            </div>
            <div class="bg-white p-8 text-center rounded-xl shadow-lg border border-gray-200/80 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                <div class="bg-indigo-100 text-indigo-500 rounded-full p-4 w-20 h-20 mx-auto mb-6 flex items-center justify-center">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                </div>
                <h3 class="text-xl font-semibold mb-2 text-gray-800">Discover Gyms Near You</h3>
                <p class="text-gray-600">Apne aas-paas ke top-rated gyms ko explore karein.</p>
            </div>
            <div class="bg-white p-8 text-center rounded-xl shadow-lg border border-gray-200/80 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                <div class="bg-indigo-100 text-indigo-500 rounded-full p-4 w-20 h-20 mx-auto mb-6 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                </div>
                <h3 class="text-xl font-semibold mb-2 text-gray-800">Health Tips & Blogs</h3>
                <p class="text-gray-600">Experts dwaara likhe gaye health tips, diet plans, aur workout routines se updated rahein.</p>
            </div>
        </div>
    </div>
</div>


{{-- Latest Blogs Preview --}}
@if(($topBlogs ?? collect())->isNotEmpty())
<div class="py-20 bg-slate-50">
    <div class="container mx-auto px-6">
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Latest Health Tips</h2>
                <p class="mt-2 text-gray-600">Fresh blogs from Fitub to help users stay consistent.</p>
            </div>
            <a href="{{ route('blog.index') }}" class="hidden md:inline-flex text-indigo-600 font-semibold hover:text-indigo-800">View All Blogs →</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($topBlogs as $blog)
                <article class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-lg transition">
                    <a href="{{ route('blog.show', $blog->slug) }}">
                        <img src="{{ $blog->image_path ? Storage::url($blog->image_path) : 'https://images.unsplash.com/photo-1506126613408-4e0520d380b0?q=80&w=1200&auto=format&fit=crop' }}"
                             class="w-full h-52 object-cover"
                             alt="{{ $blog->title }}">
                    </a>
                    <div class="p-5">
                        <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">{{ $blog->category }}</p>
                        <h3 class="mt-2 text-lg font-bold text-slate-900">
                            <a href="{{ route('blog.show', $blog->slug) }}" class="hover:text-indigo-600">{{ $blog->title }}</a>
                        </h3>
                        <p class="mt-2 text-sm text-slate-600">{{ $blog->excerpt ?: Str::limit(strip_tags($blog->content), 110) }}</p>
                        <p class="mt-4 text-xs text-slate-500">
                            {{ $blog->author_name ?: 'Fitub Team' }} • {{ optional($blog->published_at)->format('d M Y') ?: $blog->created_at->format('d M Y') }}
                        </p>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-8 md:hidden">
            <a href="{{ route('blog.index') }}" class="inline-flex text-indigo-600 font-semibold hover:text-indigo-800">View All Blogs →</a>
        </div>
    </div>
</div>
@endif

{{-- 3. How It Works Section --}}
<div class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-14">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900">How Fitub Works</h2>
            <p class="mt-2 text-gray-600">Simple steps to connect with verified fitness professionals.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-7">
                <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold">1</div>
                <h3 class="mt-4 text-xl font-bold text-slate-900">Search by City</h3>
                <p class="mt-2 text-slate-600">Choose your city and find gyms or trainers near you in seconds.</p>
            </div>
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-7">
                <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold">2</div>
                <h3 class="mt-4 text-xl font-bold text-slate-900">Check Verified Profiles</h3>
                <p class="mt-2 text-slate-600">Browse verified profiles, specialization, and key details before connecting.</p>
            </div>
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-7">
                <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold">3</div>
                <h3 class="mt-4 text-xl font-bold text-slate-900">Send Inquiry & Connect</h3>
                <p class="mt-2 text-slate-600">Send inquiry and continue your fitness journey with the right match.</p>
            </div>
        </div>
    </div>
</div>

{{-- 4. Top Cities Section --}}
<div class="py-20 bg-slate-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Popular Fitness Cities</h2>
            <p class="mt-2 text-gray-600">Jump directly into your city search.</p>
        </div>
        @php
            $topCities = ['Delhi', 'Mumbai', 'Bengaluru', 'Hyderabad', 'Pune', 'Ahmedabad', 'Jaipur', 'Chandigarh'];
        @endphp
        <div class="flex flex-wrap justify-center gap-3">
            @foreach($topCities as $city)
                <a href="{{ route('search.handle', ['type' => 'gym', 'location' => $city]) }}"
                   class="px-5 py-2.5 rounded-full bg-white border border-slate-200 text-slate-700 font-semibold hover:bg-indigo-50 hover:border-indigo-300 hover:text-indigo-700 transition">
                    {{ $city }}
                </a>
            @endforeach
        </div>
    </div>
</div>

{{-- 5. Mini FAQ Section --}}
<div class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Quick FAQs</h2>
            <p class="mt-2 text-gray-600">Most common questions before you get started.</p>
        </div>
        <div class="max-w-4xl mx-auto space-y-3">
            <details class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                <summary class="font-semibold text-slate-900 cursor-pointer">Kya sirf verified trainers aur gyms dikhte hain?</summary>
                <p class="mt-2 text-slate-600 text-sm">Haan, discover sections me verified and active profiles ko priority di jati hai.</p>
            </details>
            <details class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                <summary class="font-semibold text-slate-900 cursor-pointer">Kya account banane ke liye OTP verification zaroori hai?</summary>
                <p class="mt-2 text-slate-600 text-sm">Haan, email OTP verify ke bina account activation flow complete nahi hota.</p>
            </details>
            <details class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                <summary class="font-semibold text-slate-900 cursor-pointer">Kya trainer aur gym owner registration ke time hi full details fill karte hain?</summary>
                <p class="mt-2 text-slate-600 text-sm">Nahi. Basic account registration ke baad detailed profile, required documents, and verification details dashboard me submit ki jaati hain.</p>
            </details>
            <details class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                <summary class="font-semibold text-slate-900 cursor-pointer">Fake lead issue ho to kya karein?</summary>
                <p class="mt-2 text-slate-600 text-sm">Report/support flow use karein. Valid review case me compensation credit mil sakta hai.</p>
            </details>
            <details class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                <summary class="font-semibold text-slate-900 cursor-pointer">Payment history kahaan dikhegi?</summary>
                <p class="mt-2 text-slate-600 text-sm">Trainer/Gym dashboard me payment section available hai, jahan latest history dikhti hai.</p>
            </details>
        </div>
        <div class="text-center mt-8">
            <a href="{{ route('faq') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-3 rounded-lg transition">
                View Full FAQ
            </a>
        </div>
    </div>
</div>

{{-- 3. Meet Our Community Section --}}
<div class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-extrabold text-gray-900 tracking-tight">Meet Our Thriving Community</h2>
            <p class="mt-3 text-lg text-gray-600 max-w-2xl mx-auto">Join a network of India's top-rated gyms, certified trainers, and thousands of fitness enthusiasts.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-20">
            @php
                $stats = [
                    ['count' => $gymCount, 'title' => 'Verified Gyms', 'route' => 'gyms.index', 'button' => 'View All Gyms', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>'],
                    ['count' => $trainerCount, 'title' => 'Expert Trainers', 'route' => 'trainers.index', 'button' => 'View All Trainers', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>'],
                    ['count' => $customerCount.'+', 'title' => 'Happy Members', 'route' => 'register', 'button' => 'Become a Member', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>']
                ];
            @endphp
            @foreach($stats as $stat)
            <div class="flex flex-col items-center justify-between bg-gray-50 p-8 text-center rounded-2xl shadow-sm border border-gray-200/80 hover:bg-white hover:shadow-2xl hover:border-indigo-500/50 transition-all duration-300 transform hover:-translate-y-2">
                <div class="text-indigo-500 mb-4">{!! $stat['icon'] !!}</div>
                <div class="flex-grow">
                    <p class="text-5xl font-extrabold text-gray-900">{{ $stat['count'] }}</p>
                    <h3 class="text-xl font-semibold mt-2 text-gray-800">{{ $stat['title'] }}</h3>
                </div>
                <a href="{{ route($stat['route']) }}" class="mt-6 inline-block {{ $loop->last ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }} font-semibold text-sm px-6 py-2.5 rounded-full transition-colors">
                    {{ $stat['button'] }}
                </a>
            </div>
            @endforeach
        </div>

        @if($gyms->isNotEmpty() || $trainers->isNotEmpty())
            <div class="mt-24">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Our Featured Professionals</h2>
                    <p class="mt-3 text-lg text-gray-600 max-w-2xl mx-auto">Handpicked gyms and trainers to kickstart your fitness journey.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    @if($gyms->isNotEmpty())
                        <div class="space-y-6">
                            @foreach($gyms as $gym)
                                @php
                                    $gymUser = $gym->user;
                                    $gymImage = $gymUser?->profile_photo_path
                                        ? Storage::url($gymUser->profile_photo_path)
                                        : 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=1200&auto=format&fit=crop';
                                @endphp
                                <a href="{{ route('profile.show', $gym->user) }}" class="flex items-center bg-white p-4 rounded-2xl shadow-md hover:shadow-xl hover:scale-[1.02] transition-all duration-300 border border-transparent hover:border-indigo-400/50">
                                    <img src="{{ $gymImage }}" alt="{{ $gym->gym_name ?: ($gym->user->name ?? 'Gym') }}" class="flex-shrink-0 h-20 w-20 rounded-xl object-cover border border-gray-200">
                                    <div class="ml-4 flex-grow">
                                        <h4 class="text-lg font-bold text-gray-900">{{ $gym->gym_name ?: ($gym->user->name ?? 'Gym') }}</h4>
                                        @if($gymUser?->is_verified)
                                            <span class="inline-flex items-center mt-1 text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Verified</span>
                                        @endif
                                        <p class="text-sm text-indigo-600 font-semibold">Gym Owner</p>
                                        <p class="text-xs text-gray-500 mt-1">Members: {{ $gym->total_members ?? 'N/A' }}</p>
                                        <p class="text-sm text-gray-500 mt-1 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                                            {{ $gym->address_city ?: ($gym->address_state ?: 'Location not set') }}
                                        </p>
                                    </div>
                                    <div class="ml-auto text-gray-400 group-hover:text-indigo-600 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                     @if($trainers->isNotEmpty())
                        <div class="space-y-6">
                             @foreach($trainers as $trainer)
                                @php
                                    $trainerUser = $trainer->user;
                                    $trainerImage = $trainerUser?->profile_photo_path
                                        ? Storage::url($trainerUser->profile_photo_path)
                                        : 'https://images.unsplash.com/photo-1550345332-09e3ac987658?q=80&w=1200&auto=format&fit=crop';
                                @endphp
                                <a href="{{ route('profile.show', $trainer->user) }}" class="flex items-center bg-white p-4 rounded-2xl shadow-md hover:shadow-xl hover:scale-[1.02] transition-all duration-300 border border-transparent hover:border-purple-400/50">
                                    <img src="{{ $trainerImage }}" alt="{{ $trainer->user->name ?? 'Trainer' }}" class="flex-shrink-0 h-20 w-20 rounded-xl object-cover border border-gray-200">
                                    <div class="ml-4 flex-grow">
                                        <h4 class="text-lg font-bold text-gray-900">{{ $trainer->user->name ?? 'Trainer' }}</h4>
                                        @if($trainerUser?->is_verified)
                                            <span class="inline-flex items-center mt-1 text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Verified</span>
                                        @endif
                                        <p class="text-sm text-purple-600 font-semibold">Certified Trainer</p>
                                        <p class="text-xs text-gray-500 mt-1">Specialization: {{ $trainer->specialization ?: 'General Fitness' }}</p>
                                        <p class="text-sm text-gray-500 mt-1 flex items-center">
                                           <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                                            {{ $trainer->city ?: ($trainer->state ?: 'Location not set') }}
                                        </p>
                                    </div>
                                    <div class="ml-auto text-gray-400 group-hover:text-purple-600 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

{{-- 4. BMR & Daily Calorie Calculator Section --}}
<div class="bg-gray-50 py-20">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-extrabold text-gray-900 tracking-tight">Know Your Calorie Needs</h2>
            <p class="mt-3 text-lg text-gray-600 max-w-2xl mx-auto">Calculate your Basal Metabolic Rate (BMR) and daily calorie requirements to reach your fitness goals faster.</p>
        </div>

        <div class="max-w-6xl mx-auto grid md:grid-cols-5 gap-0 bg-white rounded-2xl shadow-2xl border border-gray-200/80 overflow-hidden">
            {{-- Left Side: Form --}}
            <div class="md:col-span-2 p-8 md:p-10 border-r border-gray-200">
                 <h3 class="text-2xl font-bold text-gray-900 mb-6">Enter Your Details</h3>
                 <form id="bmrForm" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Gender</label>
                        <div class="mt-2 flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="gender" value="male" checked class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                <span class="ml-2 text-gray-700">Male</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="gender" value="female" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                <span class="ml-2 text-gray-700">Female</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label for="age" class="block text-sm font-medium text-gray-700">Age (years)</label>
                        <input type="number" id="age" name="age" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 25">
                    </div>
                     <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700">Weight (kg)</label>
                        <input type="number" id="weight" name="weight" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 70">
                    </div>
                    <div>
                        <label for="height" class="block text-sm font-medium text-gray-700">Height (cm)</label>
                        <input type="number" id="height" name="height" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 175">
                    </div>
                    <div>
                        <label for="activity" class="block text-sm font-medium text-gray-700">Weekly Activity Level</label>
                        <select id="activity" name="activity" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="1.2">Sedentary (little or no exercise)</option>
                            <option value="1.375">Lightly Active (1-3 days/week)</option>
                            <option value="1.55" selected>Moderately Active (3-5 days/week)</option>
                            <option value="1.725">Very Active (6-7 days/week)</option>
                            <option value="1.9">Extra Active (very hard exercise & physical job)</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="w-full mt-2 bg-indigo-600 text-white font-bold text-lg px-8 py-3 rounded-lg shadow-lg hover:bg-indigo-700 transition-transform transform hover:scale-105">
                            Calculate My Calories
                        </button>
                    </div>
                </form>
            </div>

            {{-- Right Side: Results --}}
            <div class="md:col-span-3 p-8 md:p-10 bg-gradient-to-br from-indigo-600 to-purple-700 text-white">
                <div id="bmrResultContainer" class="hidden">
                     <h3 class="text-2xl font-bold text-white mb-2">Your Results</h3>
                     <p class="text-indigo-200 mb-6">Here are your estimated daily calorie needs based on your goal.</p>
                     <div class="space-y-4">
                        <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm">
                            <p class="text-sm font-medium text-indigo-200">Basal Metabolic Rate (BMR)</p>
                            <p class="text-2xl font-bold"><span id="bmrValue">0</span> Calories/day</p>
                            <p class="text-xs text-indigo-300">Calories your body burns at complete rest.</p>
                        </div>
                         <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm">
                            <p class="text-sm font-medium text-indigo-200">Daily Calorie Goals</p>
                            <div class="mt-3 grid grid-cols-2 gap-3 text-center">
                                <div class="bg-green-500/20 p-3 rounded-lg border border-green-400">
                                    <p class="text-sm font-bold text-green-200">Maintain Weight</p>
                                    <p class="text-lg font-extrabold text-white"><span id="maintain-calories">0</span></p>
                                </div>
                                <div class="bg-orange-500/20 p-3 rounded-lg border border-orange-400">
                                    <p class="text-sm font-bold text-orange-200">Weight Loss</p>
                                    <p class="text-lg font-extrabold text-white"><span id="loss-calories">0</span></p>
                                </div>
                                <div class="bg-yellow-500/20 p-3 rounded-lg border border-yellow-400">
                                    <p class="text-sm font-bold text-yellow-200">Mild Weight Loss</p>
                                    <p class="text-lg font-extrabold text-white"><span id="mild-loss-calories">0</span></p>
                                </div>
                                <div class="bg-blue-500/20 p-3 rounded-lg border border-blue-400">
                                    <p class="text-sm font-bold text-blue-200">Weight Gain</p>
                                    <p class="text-lg font-extrabold text-white"><span id="gain-calories">0</span></p>
                                </div>
                            </div>
                         </div>
                         <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="text-sm font-medium text-indigo-200">Macro Split Planner</p>
                                <span class="text-xs text-indigo-200">Goal: <strong id="selected-goal-label">Maintain</strong></span>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <button type="button" class="macro-goal-btn bg-emerald-500/30 border border-emerald-300 text-emerald-100 px-3 py-1.5 rounded-full text-xs font-semibold" data-goal="maintain">Maintain</button>
                                <button type="button" class="macro-goal-btn bg-orange-500/20 border border-orange-300 text-orange-100 px-3 py-1.5 rounded-full text-xs font-semibold" data-goal="loss">Weight Loss</button>
                                <button type="button" class="macro-goal-btn bg-blue-500/20 border border-blue-300 text-blue-100 px-3 py-1.5 rounded-full text-xs font-semibold" data-goal="gain">Weight Gain</button>
                            </div>
                            <p class="mt-3 text-xs text-indigo-200">Calorie Target: <span id="macro-calorie-target">0</span> kcal/day</p>
                            <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                                <div class="bg-white/10 rounded-lg p-2">
                                    <p class="text-[11px] text-indigo-200">Protein</p>
                                    <p class="text-sm font-bold"><span id="macro-protein-grams">0</span>g</p>
                                </div>
                                <div class="bg-white/10 rounded-lg p-2">
                                    <p class="text-[11px] text-indigo-200">Carbs</p>
                                    <p class="text-sm font-bold"><span id="macro-carbs-grams">0</span>g</p>
                                </div>
                                <div class="bg-white/10 rounded-lg p-2">
                                    <p class="text-[11px] text-indigo-200">Fats</p>
                                    <p class="text-sm font-bold"><span id="macro-fats-grams">0</span>g</p>
                                </div>
                            </div>
                         </div>
                         <div class="bg-white/10 p-4 rounded-lg backdrop-blur-sm">
                            <p class="text-sm font-medium text-indigo-200">What to do next</p>
                            <div class="mt-3 grid sm:grid-cols-2 gap-3">
                                <a href="{{ route('trainers.index') }}" class="block bg-white/10 hover:bg-white/20 border border-white/20 rounded-lg p-3 transition">
                                    <p class="font-semibold text-white text-sm">Find Verified Trainers</p>
                                    <p class="text-xs text-indigo-100 mt-1">Get personalized guidance based on this result.</p>
                                </a>
                                <a href="{{ route('gyms.index') }}" class="block bg-white/10 hover:bg-white/20 border border-white/20 rounded-lg p-3 transition">
                                    <p class="font-semibold text-white text-sm">Find Verified Gyms</p>
                                    <p class="text-xs text-indigo-100 mt-1">Compare nearby options and send inquiry.</p>
                                </a>
                            </div>
                         </div>
                     </div>
                     <p class="text-xs text-indigo-300 mt-6 text-center">*All calculations are estimates. Consult a professional for personal advice.</p>
                </div>
                <div id="bmrInitialState" class="flex flex-col items-center justify-center h-full text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-indigo-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                    <h3 class="text-2xl font-bold">Your Results Will Appear Here</h3>
                    <p class="mt-2 text-indigo-200">Fill out the form to see your personalized calorie estimates.</p>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    const bmrForm = document.getElementById('bmrForm');
    let latestTargets = {
        maintain: 0,
        loss: 0,
        gain: 0,
    };

    function setMacroGoal(goal) {
        const goalLabelMap = {
            maintain: 'Maintain',
            loss: 'Weight Loss',
            gain: 'Weight Gain',
        };
        const macroRatios = {
            maintain: { protein: 0.30, carbs: 0.40, fats: 0.30 },
            loss: { protein: 0.35, carbs: 0.35, fats: 0.30 },
            gain: { protein: 0.30, carbs: 0.45, fats: 0.25 },
        };

        const targetCalories = latestTargets[goal] || 0;
        const ratio = macroRatios[goal];
        const proteinGrams = Math.round((targetCalories * ratio.protein) / 4);
        const carbsGrams = Math.round((targetCalories * ratio.carbs) / 4);
        const fatsGrams = Math.round((targetCalories * ratio.fats) / 9);

        document.getElementById('selected-goal-label').textContent = goalLabelMap[goal];
        document.getElementById('macro-calorie-target').textContent = targetCalories;
        document.getElementById('macro-protein-grams').textContent = proteinGrams;
        document.getElementById('macro-carbs-grams').textContent = carbsGrams;
        document.getElementById('macro-fats-grams').textContent = fatsGrams;

        document.querySelectorAll('.macro-goal-btn').forEach((btn) => {
            const isActive = btn.dataset.goal === goal;
            btn.classList.toggle('ring-2', isActive);
            btn.classList.toggle('ring-white', isActive);
            btn.classList.toggle('ring-offset-1', isActive);
            btn.classList.toggle('ring-offset-transparent', isActive);
        });
    }

    if (bmrForm) {
        document.querySelectorAll('.macro-goal-btn').forEach((btn) => {
            btn.addEventListener('click', () => setMacroGoal(btn.dataset.goal));
        });

        bmrForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const gender = document.querySelector('input[name="gender"]:checked').value;
            const age = parseFloat(document.getElementById('age').value);
            const weight = parseFloat(document.getElementById('weight').value);
            const height = parseFloat(document.getElementById('height').value);
            const activityMultiplier = parseFloat(document.getElementById('activity').value);
            const resultContainer = document.getElementById('bmrResultContainer');
            const initialState = document.getElementById('bmrInitialState');
            if (isNaN(age) || isNaN(weight) || isNaN(height) || age <= 0 || weight <= 0 || height <= 0) {
                alert('Please enter valid age, weight, and height.');
                return;
            }
            let bmr = 0;
            if (gender === 'male') {
                bmr = (10 * weight) + (6.25 * height) - (5 * age) + 5;
            } else {
                bmr = (10 * weight) + (6.25 * height) - (5 * age) - 161;
            }
            const tdee = bmr * activityMultiplier;
            const maintainCalories = Math.round(tdee);
            const mildLossCalories = Math.round(tdee - 250); // Adjusted for mild deficit
            const lossCalories = Math.round(tdee - 500);     // Adjusted for standard deficit
            const gainCalories = Math.round(tdee + 300);     // Adjusted for mild surplus
            latestTargets = {
                maintain: maintainCalories,
                loss: lossCalories,
                gain: gainCalories,
            };
            document.getElementById('bmrValue').textContent = Math.round(bmr);
            document.getElementById('maintain-calories').textContent = maintainCalories;
            document.getElementById('mild-loss-calories').textContent = mildLossCalories;
            document.getElementById('loss-calories').textContent = lossCalories;
            document.getElementById('gain-calories').textContent = gainCalories;
            setMacroGoal('maintain');
            resultContainer.classList.remove('hidden');
            initialState.classList.add('hidden');
        });
    }
</script>

<script>
    // Simple toggle for Gyms / Trainers so user ko clearly dikhe ki yeh clickable hai
    document.addEventListener('DOMContentLoaded', function () {
        const typeInput = document.getElementById('search-type');
        const gymBtn = document.getElementById('type-gym-btn');
        const trainerBtn = document.getElementById('type-trainer-btn');

        if (!typeInput || !gymBtn || !trainerBtn) return;

        function setType(type) {
            typeInput.value = type;
            if (type === 'gym') {
                gymBtn.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
                trainerBtn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
                trainerBtn.classList.add('text-gray-600');
            } else {
                trainerBtn.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
                gymBtn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
                gymBtn.classList.add('text-gray-600');
            }
        }

        gymBtn.addEventListener('click', function () { setType('gym'); });
        trainerBtn.addEventListener('click', function () { setType('trainer'); });
    });
</script>

@endsection
