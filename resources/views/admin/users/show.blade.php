@extends('layouts.app')

@section('content')
<div class="p-8 bg-white shadow-xl rounded-2xl max-w-4xl mx-auto">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6 border-b pb-4">
        <h2 class="text-2xl font-bold">Verification Profile: {{ $user->name }}</h2>
        @php
            $kycColor = $user->kyc_status === 'approved'
                ? 'bg-emerald-100 text-emerald-700'
                : ($user->kyc_status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700');
        @endphp
        <span class="px-3 py-1 rounded-full text-xs font-bold capitalize {{ $kycColor }}">
            KYC {{ $user->kyc_status ?? 'pending' }}
        </span>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-lg bg-green-100 border border-green-200 text-green-800 px-4 py-3">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-5 rounded-lg bg-red-100 border border-red-200 text-red-800 px-4 py-3">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-5 rounded-lg bg-red-100 border border-red-200 text-red-800 px-4 py-3">{{ $errors->first() }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        <div class="space-y-4">
            <h3 class="font-bold text-lg text-indigo-600">Personal/Business Info</h3>

            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Account Status:</strong> <span class="capitalize">{{ $user->status }}</span></p>
            <p><strong>Verified Badge:</strong> {{ $user->is_verified ? 'Yes' : 'No' }}</p>

            <p><strong>Type:</strong> <span class="capitalize">{{ $user->user_type }}</span></p>

            @if($user->user_type == 'gymowner')
                <p><strong>Gym Name:</strong> {{ $user->gym->gym_name ?? 'N/A' }}</p>
                <p><strong>Gym Phone:</strong> {{ $user->gym->gym_phone_number ?? 'N/A' }}</p>
                <p><strong>Address:</strong> {{ $user->gym->address_city ?? '' }}{{ $user->gym && $user->gym->address_state ? ', '.$user->gym->address_state : '' }}</p>
            @elseif($user->user_type == 'trainer')
                <p><strong>Specialization:</strong> {{ $user->trainer->specialization ?? 'N/A' }}</p>
                <p><strong>Experience:</strong> {{ $user->trainer->experience ?? 'N/A' }} years</p>
            @endif

            @if($user->kyc_status === 'rejected' && $user->kyc_rejection_reason)
                <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                    <strong>Last Rejection Reason:</strong> {{ $user->kyc_rejection_reason }}
                </div>
            @endif

            @if($user->kyc_reviewed_at)
                <p class="text-sm text-slate-500">
                    Reviewed on {{ $user->kyc_reviewed_at->format('d M Y, h:i A') }}
                    @if($reviewedBy)
                        by {{ $reviewedBy->name }}
                    @endif
                </p>
            @endif
        </div>

        <div class="space-y-4">
            <h3 class="font-bold text-lg text-red-600">Verification Documents</h3>

            @if($user->id_proof_path)
                <p>
                    <a href="{{ asset('storage/' . $user->id_proof_path) }}" target="_blank" class="text-blue-500 underline">
                        View Uploaded ID Proof
                    </a>
                </p>
            @else
                <p class="text-gray-500">No ID proof uploaded.</p>
            @endif

            @if($user->user_type === 'gymowner')
                @if(optional($user->gym)->business_doc_path)
                    <p>
                        <a href="{{ asset('storage/' . $user->gym->business_doc_path) }}" target="_blank" class="text-blue-500 underline">
                            View Business Document
                        </a>
                    </p>
                @else
                    <p class="text-gray-500">No business document uploaded.</p>
                @endif
            @endif

            @if($user->user_type === 'trainer')
                @php $certProofs = is_array(optional($user->trainer)->certificate_proof_paths) ? $user->trainer->certificate_proof_paths : []; @endphp
                @if(count($certProofs))
                    <div class="space-y-1">
                        <p class="font-semibold text-slate-700">Certificate Proofs</p>
                        @foreach($certProofs as $path)
                            <p>
                                <a href="{{ asset('storage/' . $path) }}" target="_blank" class="text-blue-500 underline">
                                    View Certificate {{ $loop->iteration }}
                                </a>
                            </p>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No certificate proofs uploaded.</p>
                @endif
            @endif
        </div>

    </div>

    @if(in_array($user->user_type, ['trainer', 'gymowner'], true))
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
            <form action="{{ route('admin.approve', $user->id) }}" method="POST" class="rounded-xl border border-green-200 bg-green-50 p-4">
                @csrf
                <h4 class="font-bold text-green-700 mb-3">Approve KYC</h4>
                <p class="text-sm text-green-700 mb-3">User will become active, verified, and visible in listings.</p>
                <button class="bg-green-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-green-700">Approve & Verify</button>
            </form>

            <form action="{{ route('admin.reject', $user->id) }}" method="POST" class="rounded-xl border border-red-200 bg-red-50 p-4">
                @csrf
                <h4 class="font-bold text-red-700 mb-3">Reject KYC</h4>
                <label for="reason" class="block text-sm font-semibold text-red-700 mb-2">Reason</label>
                <textarea id="reason" name="reason" rows="3" required maxlength="500" class="w-full rounded-lg border border-red-200 px-3 py-2 text-sm" placeholder="Please upload clear business/training proof."></textarea>
                <button class="mt-3 bg-red-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-red-700">Reject with Reason</button>
            </form>
        </div>
    @endif

    <div class="mt-6">
        <a href="{{ route('admin.pending') }}" class="bg-gray-600 text-white px-6 py-2 rounded-lg font-bold inline-flex">Back to KYC List</a>
    </div>

</div>
@endsection
