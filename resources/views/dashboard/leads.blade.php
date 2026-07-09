@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Leads & Inquiries</h1>
            <p class="text-gray-600">Manage all customer leads from one page.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="bg-white border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg hover:bg-gray-50">
            Back to Dashboard
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if($hasUnlimitedPlan && $activeUnlimitedSubscription)
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-r-lg mb-6">
            <p class="font-bold">Your {{ ucfirst($activeUnlimitedSubscription->plan_type) }} plan is active!</p>
            <p class="text-sm">Expires on: {{ \Carbon\Carbon::parse($activeUnlimitedSubscription->expires_at)->format('d M, Y') }}</p>
        </div>
    @endif

    <div class="bg-indigo-50 border border-indigo-200 rounded-lg px-4 py-3 mb-6">
        <p class="text-sm text-indigo-800">
            <strong>Unlock Credits:</strong> {{ $unlockCredits ?? 0 }}
        </p>
    </div>

    <div class="space-y-4">
        @forelse($leads as $lead)
            <div class="bg-white p-5 rounded-xl shadow-lg">
                <div class="flex items-center justify-between">
                    <p class="font-bold text-indigo-700">{{ $lead->service_needed }}</p>
                    <p class="text-xs text-gray-500">Received: {{ $lead->created_at->format('d M, Y') }}</p>
                </div>
                <blockquote class="text-sm text-gray-600 border-l-4 border-gray-200 pl-3 mt-3 italic">"{{ Str::limit($lead->message, 160) }}"</blockquote>

                <div class="mt-4">
                    @if($hasUnlimitedPlan || in_array((int) $lead->id, $unlockedLeadIds, true) || $lead->status === 'viewed')
                        <div class="text-sm bg-green-50 p-3 rounded-md w-full border border-green-200">
                            <h4 class="font-bold text-green-800 mb-1">Contact Details Unlocked</h4>
                            <p><strong>Name:</strong> {{ $lead->user->name ?? $lead->guest_name }}</p>
                            <p><strong>Email:</strong> {{ $lead->user->email ?? $lead->guest_email }}</p>
                            <p><strong>Phone:</strong> Hidden by platform policy</p>
                            @if($lead->user_id && $lead->user)
                                <a href="{{ route('inquiries.chat', $lead) }}"
                                   class="mt-3 inline-block bg-indigo-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-indigo-700 text-sm">
                                    Open Chat
                                </a>
                            @else
                                <div class="mt-3 text-xs text-gray-600 bg-white border border-gray-200 rounded-md px-3 py-2">
                                    Customer has no account (guest inquiry). Chat is not available for this lead Contact by email.
                                </div>
                            @endif
                        </div>
                    @else
                        @if(($unlockCredits ?? 0) > 0)
                            <form method="POST" action="{{ route('dashboard.leads.unlock', $lead) }}">
                                @csrf
                                <button type="submit"
                                   class="inline-block bg-emerald-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-emerald-700 text-sm">
                                    Unlock using 1 Credit
                                </button>
                            </form>
                        @else
                            <a href="{{ route('billing.plans', ['inquiry_id' => $lead->id]) }}"
                               class="inline-block bg-green-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-green-700 text-sm">
                                Unlock via Starter (&#8377;199)
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center bg-white p-8 rounded-lg shadow-lg">
                <p class="text-gray-600 font-semibold">No leads yet.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $leads->links() }}
    </div>
</div>
@endsection
