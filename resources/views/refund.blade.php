@extends('layouts.app')

@section('content')
<div class="bg-slate-50">
    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 text-white">
        <div class="container mx-auto px-6 py-20">
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight">Refund & Cancellation Policy</h1>
            <p class="mt-3 text-slate-200">Effective Date: {{ date('d M Y') }}</p>
        </div>
    </section>

    <section class="container mx-auto px-6 py-16">
        <div class="max-w-5xl mx-auto bg-white border border-slate-200 rounded-2xl p-8 space-y-8">
            <div>
                <h2 class="text-xl font-bold text-slate-900">General Policy</h2>
                <p class="mt-2 text-slate-600">Certain platform fees may be non-refundable once services are delivered, consumed, or lead access is granted.</p>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Dispute Cases</h2>
                <p class="mt-2 text-slate-600">Refund consideration may apply in valid duplicate charge, technical failure, or policy breach scenarios after review.</p>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">How to Request</h2>
                <p class="mt-2 text-slate-600">Raise a support ticket with payment details, issue summary, and relevant proof within a reasonable timeline.</p>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Decision Process</h2>
                <p class="mt-2 text-slate-600">Support and review team transaction logs, inquiry context, and abuse signals check karke final decision leti hai.</p>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Contact</h2>
                <p class="mt-2 text-slate-600">For refund and cancellation issues, use dashboard support or contact the support channel listed on Contact page.</p>
            </div>
        </div>
    </section>
</div>
@endsection
