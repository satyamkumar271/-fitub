@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-100 px-4 py-12">
    <div class="mx-auto w-full max-w-4xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_30px_80px_rgba(15,23,42,0.12)]">
        <div class="grid lg:grid-cols-[1.05fr,1.45fr]">
            <div class="relative hidden overflow-hidden bg-[radial-gradient(circle_at_top,_rgba(129,140,248,0.35),_transparent_42%),linear-gradient(160deg,#0f172a_0%,#111827_48%,#1e1b4b_100%)] p-10 text-white lg:block">
                <div class="relative z-10 flex h-full flex-col">
                    <span class="inline-flex w-fit rounded-full border border-white/15 bg-white/10 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-indigo-100">
                        Fitub Onboarding
                    </span>
                    <h1 class="mt-8 text-4xl font-black leading-tight">
                        Start simple.
                        <span class="block text-indigo-200">Complete the rest inside your dashboard.</span>
                    </h1>
                    <p class="mt-5 max-w-md text-sm leading-7 text-slate-200">
                        Create your basic account here. Trainers and gym owners can complete their full profile and upload required documents after logging in.
                    </p>

                    <div class="mt-10 space-y-4">
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-sm">
                            <p class="text-sm font-semibold text-white">Customer</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-sm">
                            <p class="text-sm font-semibold text-white">Trainer / Gym Owner</p>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-20 -left-16 h-48 w-48 rounded-full bg-indigo-400/20 blur-3xl"></div>
                <div class="absolute -right-14 top-8 h-40 w-40 rounded-full bg-cyan-300/10 blur-3xl"></div>
            </div>

            <div class="p-6 sm:p-8 lg:p-10">
                <div class="mx-auto max-w-2xl">
                    <h2 class="text-center text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Create Your Account</h2>
                    <p class="mt-3 text-center text-sm text-slate-500 sm:text-base">
                        Basic registration first. Detailed profile will be completed in dashboard.
                    </p>

                    @if ($errors->any())
                        <div class="mt-8 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 shadow-sm">
                            <p class="font-bold">Oops! Something went wrong.</p>
                            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" class="mt-8">
                        @csrf

                        <div class="rounded-3xl border border-slate-200 bg-slate-50/80 p-5 sm:p-6">
                            <div class="mb-6">
                                <p class="text-2xl font-black text-sky-500">1. Basic Information</p>
                                <div class="mt-3 h-px w-44 bg-gradient-to-r from-slate-300 via-slate-200 to-transparent"></div>
                            </div>

                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">Name</label>
                                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">Login Email</label>
                                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
                                    <input type="password" name="password" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">Confirm Password</label>
                                    <input type="password" name="password_confirmation" required class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 rounded-3xl border border-slate-200 bg-slate-50/80 p-5 sm:p-6">
                            <div class="mb-6">
                                <p class="text-2xl font-black text-slate-900">2. I am a...</p>
                                <div class="mt-3 h-px w-28 bg-gradient-to-r from-slate-300 via-slate-200 to-transparent"></div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <label class="group cursor-pointer rounded-2xl border border-slate-300 bg-white px-4 py-4 text-center transition hover:border-sky-300 hover:shadow-sm">
                                    <input type="radio" name="user_type" value="customer" class="mr-2 accent-sky-500" {{ old('user_type', 'customer') === 'customer' ? 'checked' : '' }}>
                                    <span class="font-semibold text-slate-800">Customer</span>
                                </label>
                                <label class="group cursor-pointer rounded-2xl border border-slate-300 bg-white px-4 py-4 text-center transition hover:border-sky-300 hover:shadow-sm">
                                    <input type="radio" name="user_type" value="gymowner" class="mr-2 accent-sky-500" {{ old('user_type') === 'gymowner' ? 'checked' : '' }}>
                                    <span class="font-semibold text-slate-800">Gym Owner</span>
                                </label>
                                <label class="group cursor-pointer rounded-2xl border border-slate-300 bg-white px-4 py-4 text-center transition hover:border-sky-300 hover:shadow-sm">
                                    <input type="radio" name="user_type" value="trainer" class="mr-2 accent-sky-500" {{ old('user_type') === 'trainer' ? 'checked' : '' }}>
                                    <span class="font-semibold text-slate-800">Trainer</span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-8">
                            <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-indigo-600 via-violet-600 to-indigo-600 px-6 py-4 text-base font-extrabold text-white shadow-lg shadow-indigo-500/20 transition hover:translate-y-[-1px] hover:shadow-xl hover:shadow-indigo-500/30">
                                Create Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
