@extends('layouts.app')

@section('content')
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
            <input type="email" name="email" id="email" class="w-full mt-1 p-2 border rounded" value="{{ old('email') }}" required>
        </div>
        <div class="mb-4">
            <label for="password" class="block text-gray-700">Password</label>
            <input type="password" name="password" id="password" class="w-full mt-1 p-2 border rounded" required>
        </div>
        <div class="mt-6">
            <button type="submit" class="w-full bg-indigo-600 text-white p-3 rounded-md hover:bg-indigo-700">Login</button>
        </div>
    </form>
</div>
@endsection
