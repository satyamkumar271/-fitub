@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">Choose a Plan</h1>
        <p class="text-gray-600 mt-1">Apne growth ke hisaab se plan select karein (GST included).</p>
        @if($inquiry)
            <div class="mt-4 p-4 rounded-lg bg-amber-50 border border-amber-200">
                <p class="font-semibold text-amber-900">Unlocking lead for inquiry #{{ $inquiry->id }}</p>
                <p class="text-sm text-amber-800">Service: {{ $inquiry->service_needed }}</p>
                @if(($unlockCredits ?? 0) > 0)
                    <p class="text-sm text-amber-800 mt-1">Available unlock credits: <strong>{{ $unlockCredits }}</strong>. Starter plan credits add karke aap lead unlock kar sakte ho.</p>
                @endif
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
            <div class="bg-white rounded-2xl shadow-lg border overflow-hidden transition-all duration-300 transform hover:-translate-y-1 hover:shadow-2xl {{ $plan['key'] === 'pro' ? 'ring-2 ring-indigo-400 shadow-2xl md:scale-[1.02]' : '' }}">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900">{{ $plan['title'] }}</h2>
                    @if($plan['key'] === 'pro')
                        <span class="inline-flex items-center mt-2 text-xs font-extrabold px-3 py-1 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
                            Most Popular
                        </span>
                    @endif
                    <p class="text-4xl font-extrabold text-indigo-600 mt-4">&#8377;{{ number_format((float) $plan['price'], 0) }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        @if($plan['key'] === 'starter')
                            5 lead credits (GST included)
                        @elseif($plan['key'] === 'pro')
                            / month (GST included)
                        @else
                            / year (GST included)
                        @endif
                    </p>
                    @if($plan['key'] === 'business')
                        <p class="mt-2 text-sm text-emerald-700 font-bold">
                            {{ $plan['discount_label'] ?? 'Save 60%' }}
                            <span class="ml-2 text-slate-400 line-through font-semibold">&#8377;{{ number_format((float) ($plan['original_price'] ?? 15588), 0) }}</span>
                        </p>
                    @endif

                    <div class="mt-6 space-y-2 text-sm text-gray-700">
                        @if($plan['key'] === 'starter')
                            <div>3–5 leads pack (we give 5 credits)</div>
                            <div>Unlock leads instantly from dashboard</div>
                            <div>Basic access</div>
                        @elseif($plan['key'] === 'pro')
                            <div>High / unlimited leads access</div>
                            <div>Priority access</div>
                            <div>Faster results</div>
                        @else
                            <div>High leads all year</div>
                            <div>Priority handling</div>
                            <div>Best value annual pricing</div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('billing.order') }}" class="mt-6">
                        @csrf
                        <input type="hidden" name="plan" value="{{ $plan['key'] }}">
                        @if($inquiry)
                            <input type="hidden" name="inquiry_id" value="{{ $inquiry->id }}">
                        @endif

                        <button type="submit"
                            class="w-full {{ $plan['key'] === 'pro' ? 'bg-gradient-to-r from-indigo-500 to-purple-600 hover:shadow-indigo-500/30' : ($plan['key'] === 'business' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-indigo-600 hover:bg-indigo-700') }} text-white font-semibold py-3 rounded-lg transition shadow-md">
                            @if($plan['key'] === 'starter')
                                Get Leads
                            @elseif($plan['key'] === 'pro')
                                Start Subscription
                            @else
                                Go Annual
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
