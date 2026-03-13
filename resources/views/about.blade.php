@extends('layouts.app')

@section('content')
<div class="bg-slate-50">
    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 text-white">
        <div class="container mx-auto px-6 py-20 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight">About Fitub</h1>
            <p class="mt-4 text-lg text-slate-200 max-w-3xl mx-auto">
                Fitub is a fitness discovery and lead platform connecting customers with verified gyms and trainers.
            </p>
        </div>
    </section>

    <section class="container mx-auto px-6 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900">What We Solve</h2>
                <p class="mt-3 text-slate-600">
                    Customers can find trusted fitness professionals quickly, while gyms and trainers get real inquiries from interested users.
                </p>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900">Trust & Verification</h2>
                <p class="mt-3 text-slate-600">
                    Fitub uses email OTP, KYC checks, and admin review so only verified profiles receive full visibility on the platform.
                </p>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-xl font-bold text-slate-900">Business Value</h2>
                <p class="mt-3 text-slate-600">
                    Gym owners and trainers manage leads, unlock contacts, and track outcomes from one dashboard.
                </p>
            </div>
        </div>
    </section>

    <section class="container mx-auto px-6 pb-16">
        <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm">
            <h2 class="text-2xl font-bold text-slate-900">How Fitub Works</h2>
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm font-semibold text-indigo-600">Step 1</p>
                    <h3 class="mt-1 font-bold text-slate-900">Register</h3>
                    <p class="mt-2 text-slate-600">Customer, trainer, or gym owner creates an account and verifies email.</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-indigo-600">Step 2</p>
                    <h3 class="mt-1 font-bold text-slate-900">Verify</h3>
                    <p class="mt-2 text-slate-600">Trainer and gym profiles complete KYC and admin approval before full visibility.</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-indigo-600">Step 3</p>
                    <h3 class="mt-1 font-bold text-slate-900">Connect</h3>
                    <p class="mt-2 text-slate-600">Users send inquiries, businesses unlock leads, and both sides chat securely.</p>
                </div>
            </div>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('register') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2.5 rounded-lg">Create Account</a>
                <a href="{{ route('contact') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold px-5 py-2.5 rounded-lg">Contact Us</a>
            </div>
        </div>
    </section>
</div>
@endsection
