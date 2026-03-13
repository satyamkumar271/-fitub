@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-12 px-4">
    @if (session('status'))
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded-lg">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white p-8 rounded-xl shadow">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Verify Your Email</h1>
        <p class="text-sm text-gray-600 mb-6">Enter the 6-digit OTP sent to <strong>{{ $user->email }}</strong>.</p>

        <form method="POST" action="{{ route('otp.verify.submit', $user) }}" class="space-y-4">
            @csrf
            <div>
                <label for="otp" class="block text-sm font-medium text-gray-700">OTP Code</label>
                <input type="text" id="otp" name="otp" maxlength="6" class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 tracking-widest text-center text-lg" required>
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white font-semibold py-2 rounded-lg hover:bg-indigo-700">
                Verify OTP
            </button>
        </form>

        <form method="POST" action="{{ route('otp.resend', $user) }}" class="mt-3">
            @csrf
            <button type="submit" class="w-full bg-white border border-gray-300 text-gray-700 font-semibold py-2 rounded-lg hover:bg-gray-50">
                Resend OTP
            </button>
        </form>
    </div>
</div>
@endsection
