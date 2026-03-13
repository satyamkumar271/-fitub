@extends('layouts.app')

@section('content')
<div class="bg-slate-50">
    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 text-white">
        <div class="container mx-auto px-6 py-20">
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight">Privacy Policy</h1>
            <p class="mt-3 text-slate-200">Effective Date: {{ date('d M Y') }}</p>
        </div>
    </section>

    <section class="container mx-auto px-6 py-16">
        <div class="max-w-5xl mx-auto bg-white border border-slate-200 rounded-2xl p-8 space-y-8">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Information We Collect</h2>
                <p class="mt-2 text-slate-600">We may collect account details, profile data, KYC documents, inquiry/chat records, payment metadata, and support tickets.</p>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Why We Use Data</h2>
                <p class="mt-2 text-slate-600">Data is used for account creation, OTP verification, fraud prevention, lead matching, dispute handling, and support resolution.</p>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Visibility & Sharing</h2>
                <p class="mt-2 text-slate-600">Public profile visibility depends on role, verification status, and platform rules. Sensitive data is restricted based on workflow controls.</p>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Data Security</h2>
                <p class="mt-2 text-slate-600">We use reasonable technical and administrative safeguards, but no internet system is fully risk-free.</p>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">User Requests</h2>
                <p class="mt-2 text-slate-600">For profile correction, account issues, or data concerns, contact support through the platform support flow.</p>
            </div>
        </div>
    </section>
</div>
@endsection
