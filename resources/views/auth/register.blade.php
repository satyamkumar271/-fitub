@extends('layouts.app')

@section('content')
<div class="bg-slate-50 min-h-screen flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-2xl bg-white p-8 md:p-10 rounded-2xl shadow-xl border border-slate-200">
        <h2 class="text-3xl font-extrabold mb-2 text-center text-slate-900">Create Your Account</h2>
        <p class="text-center text-slate-500 mb-8">Basic registration first. Detailed profile will be completed in dashboard.</p>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 text-red-800 p-4 rounded-lg mb-6">
                <p class="font-bold">Oops! Something went wrong.</p>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full p-3 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Login Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full p-3 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full p-3 border border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required class="w-full p-3 border border-slate-300 rounded-lg">
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-semibold text-slate-700 mb-2">I am a...</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <label class="border border-slate-300 rounded-lg p-3 text-center cursor-pointer">
                        <input type="radio" name="user_type" value="customer" class="mr-1" {{ old('user_type', 'customer') === 'customer' ? 'checked' : '' }}>
                        Customer
                    </label>
                    <label class="border border-slate-300 rounded-lg p-3 text-center cursor-pointer">
                        <input type="radio" name="user_type" value="gymowner" class="mr-1" {{ old('user_type') === 'gymowner' ? 'checked' : '' }}>
                        Gym Owner
                    </label>
                    <label class="border border-slate-300 rounded-lg p-3 text-center cursor-pointer">
                        <input type="radio" name="user_type" value="trainer" class="mr-1" {{ old('user_type') === 'trainer' ? 'checked' : '' }}>
                        Trainer
                    </label>
                </div>
            </div>

            <div class="mt-8">
                <button type="submit" class="w-full bg-indigo-600 text-white p-3 rounded-lg font-bold hover:bg-indigo-700">
                    Create Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
