@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">Choose a Plan</h1>
        <p class="text-gray-600 mt-1">Monthly/Yearly subscription ya single lead unlock purchase karein.</p>
        @if($inquiry)
            <div class="mt-4 p-4 rounded-lg bg-amber-50 border border-amber-200">
                <p class="font-semibold text-amber-900">Unlocking lead for inquiry #{{ $inquiry->id }}</p>
                <p class="text-sm text-amber-800">Service: {{ $inquiry->service_needed }}</p>
            </div>
        @endif
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($plans as $plan)
            <div class="bg-white rounded-2xl shadow-lg border overflow-hidden">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900">{{ $plan['title'] }}</h2>
                    <p class="text-4xl font-extrabold text-indigo-600 mt-4">₹{{ $plan['price'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        @if($plan['key'] === 'single_lead')
                            One-time lead unlock
                        @else
                            Valid for {{ $plan['duration_days'] }} days
                        @endif
                    </p>

                    <div class="mt-6 space-y-2 text-sm text-gray-700">
                        @if($plan['key'] === 'single_lead')
                            <div>Unlock a single customer lead</div>
                            <div>View contact details instantly</div>
                        @else
                            <div>Unlimited lead viewing</div>
                            <div>Grow your business faster</div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('billing.order') }}" class="mt-6">
                        @csrf
                        <input type="hidden" name="plan" value="{{ $plan['key'] }}">
                        @if($inquiry && $plan['key'] === 'single_lead')
                            <input type="hidden" name="inquiry_id" value="{{ $inquiry->id }}">
                        @endif
                        <button type="submit"
                            class="w-full bg-indigo-600 text-white font-semibold py-3 rounded-lg hover:bg-indigo-700 transition">
                            Buy Now
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection


