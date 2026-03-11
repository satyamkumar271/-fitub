@extends('layouts.app')

@section('content')
{{-- Added a wrapper for a gradient background and better spacing --}}
<div class="bg-slate-50 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-3xl mx-auto bg-white p-8 md:p-10 rounded-3xl shadow-2xl border border-slate-200/50">

        {{-- Header with Gradient Text --}}
        <h2 class="text-4xl font-extrabold mb-2 text-center bg-gradient-to-r from-cyan-500 to-blue-500 bg-clip-text text-transparent">
            Create Your Account
        </h2>
        <p class="text-center text-slate-500 mb-8">Join our vibrant fitness community!</p>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 text-red-800 p-4 rounded-lg mb-6 flex" role="alert">
                <svg class="w-5 h-5 mr-3 mt-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                <div>
                    <p class="font-bold">Oops! Something went wrong.</p>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf

            {{-- SECTION 1: BASIC INFORMATION (Koi change nahi) --}}
            <fieldset class="mb-8">
                <legend class="text-xl font-bold mb-6 text-slate-800 border-b border-slate-200 pb-3">1. Basic Information</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Name Input with Icon --}}
                    <div>
                        <label for="name" class="block text-slate-700 font-semibold mb-2">Name</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" /></svg>
                            </div>
                            <input type="text" name="name" id="name" class="w-full p-3 pl-10 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition" value="{{ old('name') }}" required>
                        </div>
                    </div>
                     {{-- Email Input with Icon --}}
                    <div>
                        <label for="email" class="block text-slate-700 font-semibold mb-2">Login Email</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                               <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M2.003 5.884L10 2.25l7.997 3.634A1 1 0 0017 4.5v1.081q.078.03.15.064l3 1.5a1 1 0 01.5 1.342l-3 6a1 1 0 01-1.342.5l-3-1.5a1 1 0 01-.5-1.342l3-6a1 1 0 01.03-.064V5.884z" /><path d="M10 5.25a3 3 0 100 6 3 3 0 000-6zM2.003 5.884L10 10.25l7.997-4.366A1 1 0 0017 5.5v-1.081q-.078-.03-.15-.064l-3-1.5a1 1 0 01-.5-1.342l3-6a1 1 0 011.342-.5l3 1.5a1 1 0 01.5 1.342l-3 6a1 1 0 01-.03.064v9.616a1 1 0 01-1.342.5l-3-1.5a1 1 0 01-.5-1.342l3-6a1 1 0 010-1.216L10 5.25z" /></svg>
                            </div>
                            <input type="email" name="email" id="email" class="w-full p-3 pl-10 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition" value="{{ old('email') }}" required>
                        </div>
                    </div>
                     {{-- Password Input with Icon --}}
                    <div>
                        <label for="password" class="block text-slate-700 font-semibold mb-2">Password</label>
                        <div class="relative">
                           <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                               <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" /></svg>
                           </div>
                           <input type="password" name="password" id="password" class="w-full p-3 pl-10 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition" required>
                        </div>
                    </div>
                    {{-- Confirm Password Input with Icon --}}
                    <div>
                        <label for="password_confirmation" class="block text-slate-700 font-semibold mb-2">Confirm Password</label>
                        <div class="relative">
                           <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                               <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" /></svg>
                           </div>
                           <input type="password" name="password_confirmation" id="password_confirmation" class="w-full p-3 pl-10 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition" required>
                        </div>
                    </div>
                </div>
            </fieldset>

            {{-- SECTION 2: USER TYPE SELECTOR (Koi change nahi) --}}
            <fieldset class="mb-8">
                <legend class="text-xl font-bold mb-6 text-slate-800 border-b border-slate-200 pb-3">2. I am a...</legend>
                <div id="user_type_selector" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                     <label class="flex flex-col items-center justify-center p-5 border-2 border-slate-200 rounded-xl cursor-pointer has-[:checked]:bg-cyan-50 has-[:checked]:border-cyan-400 has-[:checked]:ring-2 has-[:checked]:ring-cyan-200 transition-all duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-lg">
                        <input type="radio" name="user_type" value="customer" class="hidden" onchange="toggleFields()" {{ old('user_type', 'customer') == 'customer' ? 'checked' : '' }}>
                        <svg class="w-12 h-12 mb-3 text-cyan-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                        <span class="text-lg font-bold text-slate-800">Customer</span>
                        <p class="text-sm text-slate-500 text-center">I want to get fit and track my progress.</p>
                     </label>
                     <label class="flex flex-col items-center justify-center p-5 border-2 border-slate-200 rounded-xl cursor-pointer has-[:checked]:bg-cyan-50 has-[:checked]:border-cyan-400 has-[:checked]:ring-2 has-[:checked]:ring-cyan-200 transition-all duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-lg">
                        <input type="radio" name="user_type" value="gymowner" class="hidden" onchange="toggleFields()" {{ old('user_type') == 'gymowner' ? 'checked' : '' }}>
                        <svg class="w-12 h-12 mb-3 text-cyan-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h6.375a.375.375 0 01.375.375v1.5a.375.375 0 01-.375.375H9a.375.375 0 01-.375-.375v-1.5A.375.375 0 019 6.75zM9 12.75h6.375a.375.375 0 01.375.375v1.5a.375.375 0 01-.375.375H9a.375.375 0 01-.375-.375v-1.5A.375.375 0 019 12.75z" /></svg>
                        <span class="text-lg font-bold text-slate-800">Gym Owner</span>
                        <p class="text-sm text-slate-500 text-center">I own a gym and want to grow.</p>
                     </label>
                     <label class="flex flex-col items-center justify-center p-5 border-2 border-slate-200 rounded-xl cursor-pointer has-[:checked]:bg-cyan-50 has-[:checked]:border-cyan-400 has-[:checked]:ring-2 has-[:checked]:ring-cyan-200 transition-all duration-300 ease-in-out transform hover:-translate-y-1 hover:shadow-lg">
                        <input type="radio" name="user_type" value="trainer" class="hidden" onchange="toggleFields()" {{ old('user_type') == 'trainer' ? 'checked' : '' }}>
                        <svg class="w-12 h-12 mb-3 text-cyan-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span class="text-lg font-bold text-slate-800">Trainer</span>
                        <p class="text-sm text-slate-500 text-center">I am a professional trainer for clients.</p>
                     </label>
                </div>
            </fieldset>

            {{-- SECTION 3: DYNAMIC FIELDS --}}
            <div id="dynamic-fields-container" class="mt-8">
                <!-- Customer Fields -->
                <div id="customer-fields" class="hidden-fields space-y-4 p-6 border-t border-dashed border-slate-300">
                    <h3 class="text-lg font-bold text-slate-700">Your Fitness & Contact Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="number" name="age" placeholder="Age" class="w-full p-3 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" value="{{ old('age') }}">
                        <input type="text" name="phone_number" placeholder="Phone Number (Optional)" class="w-full p-3 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" value="{{ old('phone_number') }}">
                        {{-- <<< NAYE FIELDS CUSTOMER KE LIYE --}}
                        <input type="text" name="customer_city" placeholder="Your City" class="w-full p-3 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" value="{{ old('customer_city') }}">
                        <input type="text" name="customer_state" placeholder="Your State" class="w-full p-3 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" value="{{ old('customer_state') }}">
                        <input type="number" name="weight" placeholder="Weight (kg)" class="w-full p-3 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" value="{{ old('weight') }}">
                        <input type="number" name="height" placeholder="Height (cm)" class="w-full p-3 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" value="{{ old('height') }}">
                        <input type="text" name="goal" placeholder="Your Fitness Goal" class="w-full p-3 border border-slate-300 rounded-lg md:col-span-2 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" value="{{ old('goal') }}">
                    </div>
                </div>

                <!-- Gym Owner Fields -->
                <div id="gymowner-fields" class="hidden-fields space-y-6 p-6 border-t border-dashed border-slate-300">
                    <h3 class="text-lg font-bold text-slate-700">Your Gym's Details</h3>
                    {{-- <<< NAYA FIELD GYM KE LIYE --}}
                    <input type="text" name="gym_name" placeholder="Gym Name" class="w-full p-3 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" value="{{ old('gym_name') }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                         <input type="text" name="gym_phone_number" placeholder="Gym's Contact Number" class="w-full p-3 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" value="{{ old('gym_phone_number') }}">
                         <input type="email" name="gym_email" placeholder="Gym's Public Email" class="w-full p-3 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" value="{{ old('gym_email') }}">
                    </div>
                     <input type="text" name="gym_website_url" placeholder="https://your-gym-website.com" class="w-full p-3 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" value="{{ old('gym_website_url') }}">
                    <div>
                         <label class="block font-semibold text-slate-700 mb-2">Gym's Full Address</label>
                         <div class="space-y-2">
                            <input type="text" name="address_street" placeholder="Street Address" class="w-full p-3 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" value="{{ old('address_street') }}">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                 <input type="text" name="address_city" placeholder="City" class="p-3 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" value="{{ old('address_city') }}">
                                 <input type="text" name="address_state" placeholder="State" class="p-3 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" value="{{ old('address_state') }}">
                                 <input type="text" name="address_pincode" placeholder="Pincode" class="p-3 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" value="{{ old('address_pincode') }}">
                            </div>
                         </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="gym_age" placeholder="Gym established in (Year)" class="w-full p-3 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" value="{{ old('gym_age') }}">
                        <input type="number" name="total_members" placeholder="Approx. Total Members" class="w-full p-3 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" value="{{ old('total_members') }}">
                    </div>
                    <div id="gym-social-links-container" class="space-y-3 pt-2"></div>
                    <button type="button" onclick="addDynamicRow('gym-social-links-container', 'social-template')" class="inline-flex items-center gap-2 text-sm text-cyan-600 font-semibold hover:text-cyan-800 transition"><svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" /></svg>Add Social Media Link</button>
                </div>

                <!-- Trainer Fields -->
                <div id="trainer-fields" class="hidden-fields space-y-6 p-6 border-t border-dashed border-slate-300">
                    <h3 class="text-lg font-bold text-slate-700">Your Professional Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                         <input type="text" name="trainer_phone_number" placeholder="Your Contact Number" class="w-full p-3 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" value="{{ old('trainer_phone_number') }}">
                         {{-- <<< NAYE FIELDS TRAINER KE LIYE --}}
                         <input type="text" name="trainer_city" placeholder="Your City" class="w-full p-3 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" value="{{ old('trainer_city') }}">
                         <input type="text" name="trainer_state" placeholder="Your State" class="w-full p-3 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" value="{{ old('trainer_state') }}">
                         <input type="text" name="trainer_website_url" placeholder="https://your-portfolio.com" class="w-full p-3 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" value="{{ old('trainer_website_url') }}">
                         <input type="text" name="specialization" placeholder="Specialization (e.g., Bodybuilding)" class="w-full p-3 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" value="{{ old('specialization') }}">
                         <input type="number" name="experience" placeholder="Experience (in years)" class="w-full p-3 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cyan-500" value="{{ old('experience') }}">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-2">Certifications</label>
                        <div id="certifications-container" class="space-y-3"></div>
                        <button type="button" onclick="addDynamicRow('certifications-container', 'certification-template')" class="mt-2 inline-flex items-center gap-2 text-sm text-cyan-600 font-semibold hover:text-cyan-800 transition"><svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" /></svg>Add Certification</button>
                    </div>
                     <div>
                        <label class="block font-semibold text-slate-700 mb-2">Social Media Profiles</label>
                        <div id="trainer-social-links-container" class="space-y-3"></div>
                        <button type="button" onclick="addDynamicRow('trainer-social-links-container', 'social-template')" class="mt-2 inline-flex items-center gap-2 text-sm text-cyan-600 font-semibold hover:text-cyan-800 transition"><svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" /></svg>Add Social Media Link</button>
                    </div>
                </div>
            </div>

            <div class="mt-6 p-4 border-2 border-dashed border-slate-300 rounded-lg">
        <label class="block font-semibold text-slate-700 mb-2">Upload ID Proof (Aadhaar/PAN/License)</label>
        <input type="file" name="id_proof" class="w-full" required>
        <p class="text-xs text-slate-500 mt-1">Accepted: JPG, PNG, PDF (Max 2MB)</p>
    </div>

            <div class="mt-10">
                <button type="submit" class="w-full bg-gradient-to-r from-cyan-500 to-blue-600 text-white p-4 rounded-lg font-bold text-lg hover:opacity-90 hover:shadow-lg transition-all duration-300">Create Account</button>
            </div>
        </form>
    </div>
</div>

{{-- TEMPLATES (Koi change nahi) --}}
<template id="certification-template">
    <div class="flex items-center space-x-2 dynamic-row">
        <input type="text" name="certification_name[]" placeholder="Certification Name" class="w-1/2 p-2 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-1 focus:ring-cyan-500">
        <input type="text" name="certification_issuer[]" placeholder="Issuing Body" class="w-1/2 p-2 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-1 focus:ring-cyan-500">
        <button type="button" onclick="removeDynamicRow(this)" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-100 rounded-full transition">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
        </button>
    </div>
</template>
<template id="plan-template">
     <div class="flex items-center space-x-2 dynamic-row">
        <input type="text" name="plan_name[]" placeholder="Plan Name (e.g., Monthly)" class="w-1/3 p-2 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-1 focus:ring-cyan-500">
        <input type="number" name="plan_price[]" placeholder="Price (INR)" class="w-1/3 p-2 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-1 focus:ring-cyan-500">
        <input type="text" name="plan_duration[]" placeholder="Duration (e.g., 30 days)" class="w-1/3 p-2 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-1 focus:ring-cyan-500">
        <button type="button" onclick="removeDynamicRow(this)" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-100 rounded-full transition">
             <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
        </button>
    </div>
</template>
<template id="social-template">
    <div class="flex items-center space-x-2 dynamic-row">
        <select name="social_platform[]" class="p-2 border border-slate-300 rounded-lg bg-slate-100 focus:outline-none focus:ring-1 focus:ring-cyan-500">
            <option value="instagram">Instagram</option>
            <option value="facebook">Facebook</option>
            <option value="linkedin">LinkedIn</option>
            <option value="youtube">YouTube</option>
        </select>
        <input type="text" name="social_url[]" placeholder="https://..." class="flex-grow p-2 border border-slate-300 rounded-lg bg-slate-50 focus:outline-none focus:ring-1 focus:ring-cyan-500">
        <button type="button" onclick="removeDynamicRow(this)" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-100 rounded-full transition">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
        </button>
    </div>
</template>
@endsection

@push('scripts')
<script>
    function toggleFields() {
        // Hides all dynamic sections
        document.querySelectorAll('.hidden-fields').forEach(el => el.style.display = 'none');

        // Shows the selected one
        const selectedType = document.querySelector('input[name="user_type"]:checked').value;
        if (selectedType) {
            const element = document.getElementById(selectedType + '-fields');
            if(element) element.style.display = 'block';
        }
    }

    function addDynamicRow(containerId, templateId) {
        const container = document.getElementById(containerId);
        const template = document.getElementById(templateId);
        if(container && template) {
            const clone = template.content.cloneNode(true);
            container.appendChild(clone);
        }
    }

    function removeDynamicRow(button) {
        button.closest('.dynamic-row').remove();
    }

    // Run on page load to set the initial state
    document.addEventListener('DOMContentLoaded', toggleFields);
</script>
@endpush
