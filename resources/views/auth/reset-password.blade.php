@extends('layouts.app')

@section('content')

<div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6 text-center">Reset Your Password</h2>
    <p class="text-gray-600 text-sm mb-6 text-center">Enter your new password below.</p>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-4">
            <label for="email" class="block text-gray-700 font-semibold mb-2">Email Address</label>
            <input type="email" 
                   name="email" 
                   id="email" 
                   class="w-full mt-1 p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" 
                   value="{{ $email ?? old('email') }}" 
                   required 
                   readonly>
        </div>

        <div class="mb-4">
            <label for="password" class="block text-gray-700 font-semibold mb-2">New Password</label>
            <input type="password" 
                   name="password" 
                   id="password" 
                   class="w-full mt-1 p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" 
                   required 
                   autofocus>
            <p class="text-xs text-gray-500 mt-1">Must be at least 8 characters long</p>
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="block text-gray-700 font-semibold mb-2">Confirm New Password</label>
            <input type="password" 
                   name="password_confirmation" 
                   id="password_confirmation" 
                   class="w-full mt-1 p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" 
                   required>
        </div>
        
        <div class="mt-6">
            <button type="submit" class="w-full bg-indigo-600 text-white p-3 rounded-md hover:bg-indigo-700 font-semibold transition duration-200">
                Reset Password
            </button>
        </div>
    </form>

    <div class="mt-6 text-center">
        <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:text-indigo-800 hover:underline">
            ← Back to Login
        </a>
    </div>
</div>

@endsection

