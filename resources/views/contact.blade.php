@extends('layouts.app')

@section('content')
<div class="bg-slate-50">
    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 text-white">
        <div class="container mx-auto px-6 py-20 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight">Contact Fitub</h1>
            <p class="mt-4 text-lg text-slate-200 max-w-3xl mx-auto">
                Need help with account verification, lead issues, payments, or support tickets? Reach out here.
            </p>
        </div>
    </section>

    <section class="container mx-auto px-6 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">General Support</h2>
                <p class="mt-3 text-slate-600">For login, OTP, profile, and dashboard issues.</p>
                <p class="mt-4 text-sm text-slate-500">Email</p>
                <p class="text-slate-800 font-semibold">support@fitub.in</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">Business & Partnerships</h2>
                <p class="mt-3 text-slate-600">For gym onboarding, trainer growth, and collaboration.</p>
                <p class="mt-4 text-sm text-slate-500">Email</p>
                <p class="text-slate-800 font-semibold">partners@fitub.in</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">Working Hours</h2>
                <p class="mt-3 text-slate-600">Monday to Saturday</p>
                <p class="text-slate-800 font-semibold">10:00 AM to 7:00 PM (IST)</p>
            </div>
        </div>
    </section>

    <section class="container mx-auto px-6 pb-16">
        <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm">
            <h2 class="text-2xl font-bold text-slate-900">Quick Actions</h2>
            <p class="mt-2 text-slate-600">
                If you already have an account, raise a ticket from Support Team in your dashboard for faster tracking.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                @auth
                    <a href="{{ auth()->user()->user_type === 'admin' ? route('admin.support.index') : route('support.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2.5 rounded-lg">Open Support</a>
                @else
                    <a href="{{ route('login') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2.5 rounded-lg">Login to Raise Ticket</a>
                @endauth
                <a href="{{ route('register') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold px-5 py-2.5 rounded-lg">Create New Account</a>
            </div>
        </div>
    </section>
</div>
@endsection
