@extends('layouts.app')

@section('content')
<div class="bg-slate-50">
    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 text-white">
        <div class="container mx-auto px-6 py-20">
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight">Terms & Conditions</h1>
            <p class="mt-3 text-slate-200">Effective Date: {{ date('d M Y') }}</p>
        </div>
    </section>

    <section class="container mx-auto px-6 py-16">
        <div class="max-w-5xl mx-auto bg-white border border-slate-200 rounded-2xl p-8 space-y-8">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Platform Usage</h2>
                <p class="mt-2 text-slate-600">Users must provide accurate information and must not use the platform for spam, abuse, fraud, or misrepresentation.</p>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Verification & KYC</h2>
                <p class="mt-2 text-slate-600">Trainer and gym profiles must complete their detailed dashboard profile and submit required documents for verification. The review team may approve, reject, or ask the user to complete a fresh registration process.</p>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Leads & Communication</h2>
                <p class="mt-2 text-slate-600">Lead access and chat features are controlled by role and policy. Any misuse may lead to account restriction.</p>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Payments</h2>
                <p class="mt-2 text-slate-600">Payment status is governed by gateway confirmation. Platform may record transaction metadata for support and audit.</p>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Suspension/Termination</h2>
                <p class="mt-2 text-slate-600">We may suspend or terminate accounts violating policies, including fake profiles and repeated abuse reports.</p>
            </div>
        </div>
    </section>
</div>
@endsection
