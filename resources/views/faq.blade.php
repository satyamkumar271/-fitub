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
                <summary class="font-bold text-slate-900 cursor-pointer">OTP verify ke bina account active hota hai?</summary>
                <p class="mt-3 text-slate-600">Nahi. Email OTP verify ke bina account activation flow complete nahi hota.</p>
            </details>
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-bold text-slate-900 cursor-pointer">Registration ke time trainer ya gym owner ko sab details bharni hoti hain?</summary>
                <p class="mt-3 text-slate-600">Nahi. Registration form me basic account details fill hoti hain. Detailed business profile, documents, and verification details dashboard me submit ki jaati hain after login.</p>
            </details>
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-bold text-slate-900 cursor-pointer">Trainer/Gym ka KYC mandatory hai?</summary>
                <p class="mt-3 text-slate-600">Haan. Trainer ko certificate proof aur gym owner ko business document dena zaroori hai. Ye documents dashboard me upload kiye jaate hain.</p>
            </details>
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-bold text-slate-900 cursor-pointer">KYC reject hone par kya hota hai?</summary>
                <p class="mt-3 text-slate-600">Rejection reason ke saath email bheja jata hai aur user ko fresh registration process follow karna hota hai.</p>
            </details>
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-bold text-slate-900 cursor-pointer">Lead unlock se pehle customer phone number dikhta hai?</summary>
                <p class="mt-3 text-slate-600">Nahi. Contact visibility unlock/payment aur policy conditions ke basis par controlled hai.</p>
            </details>
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-bold text-slate-900 cursor-pointer">Fake inquiry ya abuse report kaise karte hain?</summary>
                <p class="mt-3 text-slate-600">Inquiry chat/report flow ya support ticket ke through report raise ki ja sakti hai; review team case verify karti hai.</p>
            </details>
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-bold text-slate-900 cursor-pointer">Single lead unlock payment ka use kaise hota hai?</summary>
                <p class="mt-3 text-slate-600">Single lead unlock payment (for example 99 INR) ek specific inquiry ko unlock karta hai. Is payment ka use ek lead access ke liye hota hai.</p>
            </details>
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-bold text-slate-900 cursor-pointer">Agar fake lead milti hai to paisa ka kya?</summary>
                <p class="mt-3 text-slate-600">Valid dispute prove hone par compensation unlock credit diya ja sakta hai. Ye credit next lead unlock me use hota hai aur us case me naya payment charge nahi hota.</p>
            </details>
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-bold text-slate-900 cursor-pointer">Unlock credit use kaise hota hai?</summary>
                <p class="mt-3 text-slate-600">Jab account me unlock credit available hota hai, next eligible lead unlock ke time pehle credit consume hota hai. Credit use hone par lead unlock ho jati hai bina extra charge ke.</p>
            </details>
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-bold text-slate-900 cursor-pointer">Payment issue ke liye kya karein?</summary>
                <p class="mt-3 text-slate-600">Dashboard ke support section me ticket raise karein with payment reference and issue details.</p>
            </details>
        </div>
    </section>
</div>
@endsection
