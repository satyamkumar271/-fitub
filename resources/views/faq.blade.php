@extends('layouts.app')

@section('content')
<div class="bg-slate-50">
    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 text-white">
        <div class="container mx-auto px-6 py-20 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight">Frequently Asked Questions</h1>
            <p class="mt-4 text-lg text-slate-200 max-w-3xl mx-auto">Quick answers about registration, KYC, leads, payments, and support.</p>
        </div>
    </section>

    <section class="container mx-auto px-6 py-16">
        <div class="space-y-4 max-w-4xl mx-auto">
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-bold text-slate-900 cursor-pointer">Without OTP verification, is the account active?</summary>
                            <p class="mt-3 text-slate-600">No. The account activation flow is incomplete without email OTP verification.</p>
                        </details>
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-bold text-slate-900 cursor-pointer">During registration, are all details filled in for the trainer or gym owner?</summary>
                <p class="mt-3 text-slate-600">No. In the registration form, basic account details are filled. Detailed business profile, documents, and verification details are submitted in the dashboard after login.</p>
            </details>
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-bold text-slate-900 cursor-pointer">Is KYC mandatory for trainers and gym owners?</summary>
                <p class="mt-3 text-slate-600">Yes. Trainers and gym owners need to provide certificate proofs and business documents. These documents are uploaded in the dashboard after login.</p>
            </details>
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-bold text-slate-900 cursor-pointer">What happens if my KYC is rejected?</summary>
                <p class="mt-3 text-slate-600">A rejection email is sent with the reason and the user needs to follow the fresh registration process.</p>
            </details>
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-bold text-slate-900 cursor-pointer">Before unlocking a lead, is the customer's phone number visible?</summary>
                <p class="mt-3 text-slate-600">No. Contact visibility is controlled based on unlock/payment and policy conditions.</p>
            </details>
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-bold text-slate-900 cursor-pointer">How do I report a fake inquiry or abuse?</summary>
                <p class="mt-3 text-slate-600">Inquiry chat/report flow or support ticket through report can be raised; the review team will verify the case.</p>
            </details>
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-bold text-slate-900 cursor-pointer">How does the use of single lead unlock payment work?</summary>
                <p class="mt-3 text-slate-600">Single lead unlock payment (for example 99 INR) unlocks a specific inquiry. The payment is used for lead access.</p>
            </details>
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-bold text-slate-900 cursor-pointer">If I receive a fake lead, what happens to the payment?</summary>
                <p class="mt-3 text-slate-600">Valid dispute proof may result in compensation unlock credit. This credit is used for the next lead unlock and no additional payment is charged in that case.</p>
            </details>
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-bold text-slate-900 cursor-pointer">How does unlock credit work?</summary>
                <p class="mt-3 text-slate-600">When unlock credit is available in the account, it is consumed before the next eligible lead unlock. The lead unlock occurs without any additional charge when credit is used.</p>
            </details>
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-bold text-slate-900 cursor-pointer">What to do if I encounter a payment issue?</summary>
                <p class="mt-3 text-slate-600">Raise a ticket in the support section of the dashboard with payment reference and issue details.</p>
            </details>
        </div>
    </section>
</div>
@endsection
