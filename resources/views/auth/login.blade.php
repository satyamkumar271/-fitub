@extends('layouts.app')

@section('content')

@if (session('status'))
    <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded-lg">
        {{ session('status') }}
    </div>
@endif
<div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6 text-center">Login to Your Account</h2>

    @error('email')
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <span>{{ $message }}</span>
        </div>
    @enderror

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-4">
            <label for="email" class="block text-gray-700">Email</label>
            <input type="email" name="email" id="email" class="w-full mt-1 p-2 border rounded" value="{{ old('email', request()->query('email')) }}" required>
        </div>
        <div class="mb-4">
            <label for="password" class="block text-gray-700">Password</label>
            <input type="password" name="password" id="password" class="w-full mt-1 p-2 border rounded" required>
        </div>
        <div class="mb-4 text-right">
            <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-800 hover:underline">Forgot Password?</a>
        </div>
        <div class="mt-6">
            <button type="submit" class="w-full bg-indigo-600 text-white p-3 rounded-md hover:bg-indigo-700">Login</button>
        </div>
    </form>
</div>
@endsection
