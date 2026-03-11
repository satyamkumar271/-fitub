@extends('layouts.app')

@section('content')
<div class="p-8 bg-white shadow-xl rounded-2xl max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold mb-6 border-b pb-4">
        Verification Profile: {{ $user->name }}
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        {{-- Basic Data --}}
        <div class="space-y-4">
            <h3 class="font-bold text-lg text-indigo-600">
                Personal/Business Info
            </h3>

            <p><strong>Email:</strong> {{ $user->email }}</p>

            <p>
                <strong>Type:</strong>
                <span class="capitalize">{{ $user->user_type }}</span>
            </p>

            @if($user->user_type == 'gymowner')

                <p>
                    <strong>Gym Name:</strong>
                    {{ $user->gym->gym_name ?? 'N/A' }}
                </p>

                <p>
                    <strong>Gym Phone:</strong>
                    {{ $user->gym->gym_phone_number ?? 'N/A' }}
                </p>

                <p>
                    <strong>Address:</strong>
                    {{ $user->gym->address_city ?? '' }}
                    {{ $user->gym->address_state ? ', '.$user->gym->address_state : '' }}
                </p>

            @endif

        </div>

        {{-- Verification Proof --}}
        <div class="space-y-4">
            <h3 class="font-bold text-lg text-red-600">
                Verification Document
            </h3>

            @if($user->id_proof_path)

                <a href="{{ asset('storage/' . $user->id_proof_path) }}"
                   target="_blank"
                   class="text-blue-500 underline">
                   View Uploaded ID/Proof
                </a>

            @else

                <p class="text-gray-500">No document uploaded.</p>

            @endif
        </div>

    </div>

    <div class="mt-8 flex gap-4">

        <form action="{{ route('admin.approve', $user->id) }}" method="POST">
            @csrf
            <button class="bg-green-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-green-700">
                Approve & Activate
            </button>
        </form>

        <a href="{{ route('admin.pending') }}"
           class="bg-gray-500 text-white px-8 py-3 rounded-lg font-bold">
           Back
        </a>

    </div>

</div>
@endsection