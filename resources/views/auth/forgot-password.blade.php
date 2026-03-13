@extends('layouts.app')

@section('content')

@if (session('status'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
        {{ session('status') }}
    </div>
@endif

<div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6 text-center">Reset Your Password</h2>
    <p class="text-gray-600 text-sm mb-6 text-center">Enter your email address and we'll send you a link to reset your password.</p>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-4">
            <label for="email" class="block text-gray-700 font-semibold mb-2">Email Address</label>
            <input type="email" 
                   name="email" 
                   id="email" 
                   class="w-full mt-1 p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" 
                   value="{{ old('email') }}" 
                   placeholder="Enter your email"
                   required 
                   autofocus>
        </div>
        
        <div class="mt-6">
            <button type="submit" class="w-full bg-indigo-600 text-white p-3 rounded-md hover:bg-indigo-700 font-semibold transition duration-200">
                Send Password Reset Link
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

