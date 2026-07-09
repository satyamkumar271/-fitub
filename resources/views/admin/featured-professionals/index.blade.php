@extends('admin.layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Featured Professionals</h1>
            <p class="mt-1 text-sm text-gray-600">
                Home page par sirf wahi trainers/gyms dikhenge jo active subscription ke andar featured hain.
                Eligible plans: <span class="font-semibold">{{ implode(', ', array_map('ucfirst', $eligiblePlans)) }}</span>
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Type</label>
                <select name="type" class="w-full rounded-lg border-gray-300">
                    <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All</option>
                    <option value="trainer" {{ $type === 'trainer' ? 'selected' : '' }}>Trainers</option>
                    <option value="gymowner" {{ $type === 'gymowner' ? 'selected' : '' }}>Gym Owners</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Tab</label>
                <select name="tab" class="w-full rounded-lg border-gray-300">
                    <option value="all" {{ $tab === 'all' ? 'selected' : '' }}>All</option>
                    <option value="featured" {{ $tab === 'featured' ? 'selected' : '' }}>Featured</option>
                    <option value="eligible" {{ $tab === 'eligible' ? 'selected' : '' }}>Eligible (Active Subscription)</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Search</label>
                <input name="q" value="{{ request('q') }}" placeholder="Name or email..." class="w-full rounded-lg border-gray-300" />
            </div>
            <div class="md:col-span-4 flex gap-2">
                <button class="px-4 py-2 rounded-lg bg-gray-900 text-white font-semibold">Apply</button>
                <a href="{{ route('admin.featured-professionals.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-800 font-semibold">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Professional</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Subscription</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Featured</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Last Change</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                        @php
                            $sub = $user->activeEligibleSubscription;
                            $eligible = (bool) $sub;
                            $isActiveFeatured = method_exists($user, 'isFeaturedActive') ? $user->isFeaturedActive() : false;
                            $hasFeaturedFlag = (bool) ($user->is_featured ?? false);
                            $lastLog = $latestLogs[$user->id] ?? null;
                            $suggestedUntil = $user->featured_until?->toDateString()
                                ?? $sub?->expires_at?->toDateString()
                                ?? now()->addDays(30)->toDateString();
                            $source = (string) ($user->featured_source ?? '');
                            $promoAllowedDays = (array) config('featured.promo.allowed_days', [2, 3, 7]);
                            $promoDaysValue = (int) ($promoAllowedDays[0] ?? 2);
                            $isVerified = (bool) ($user->is_verified ?? false);
                        @endphp
                        <tr class="{{ $isActiveFeatured ? 'bg-emerald-50/30' : '' }}">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold {{ $user->user_type === 'trainer' ? 'bg-purple-100 text-purple-700' : 'bg-indigo-100 text-indigo-700' }}">
                                    {{ $user->user_type === 'trainer' ? 'Trainer' : 'Gym Owner' }}
                                </span>
                                @if($user->is_verified)
                                    <span class="ml-2 inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Verified</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($eligible)
                                    <div class="font-semibold text-gray-900">{{ ucfirst((string) $sub->plan_type) }}</div>
                                    <div class="text-xs text-gray-500">Expires: {{ \Carbon\Carbon::parse($sub->expires_at)->format('d M Y') }}</div>
                                @else
                                    <div class="text-sm text-gray-500">Not eligible</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($isActiveFeatured)
                                    <div class="inline-flex items-center gap-2">
                                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Active</span>
                                        <span class="text-xs text-gray-600">Until: {{ $user->featured_until?->format('d M Y') }}</span>
                                        @if($source === 'promo')
                                            <span class="inline-flex px-2 py-1 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700">Promo</span>
                                        @elseif($source === 'subscription')
                                            <span class="inline-flex px-2 py-1 rounded-full text-[10px] font-semibold bg-indigo-100 text-indigo-700">Subscription</span>
                                        @endif
                                    </div>
                                @elseif($hasFeaturedFlag)
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                        {{ $user->featured_until ? 'Expired' : 'Invalid' }}
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">No</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($lastLog)
                                    <div class="text-xs text-gray-700">
                                        <span class="font-semibold">{{ str_replace('_', ' ', ucfirst((string) $lastLog->action)) }}</span>
                                        on {{ \Carbon\Carbon::parse($lastLog->created_at)->format('d M Y, h:i A') }}
                                    </div>
                                    @if($lastLog->admin)
                                        <div class="text-xs text-gray-500">by {{ $lastLog->admin->name }}</div>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-500">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    @if($eligible)
                                        <form method="POST" action="{{ route('admin.featured-professionals.feature', $user) }}" class="flex items-end gap-2">
                                            @csrf
                                            <input type="hidden" name="mode" value="subscription" />
                                            <div>
                                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">Until</label>
                                                <input type="date" name="featured_until" value="{{ $suggestedUntil }}" class="rounded-lg border-gray-300 text-sm">
                                            </div>
                                            <button class="px-3 py-2 rounded-lg text-sm font-semibold bg-emerald-600 text-white hover:bg-emerald-700">
                                                {{ $isActiveFeatured ? 'Update' : 'Feature' }}
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.featured-professionals.feature', $user) }}" class="flex items-end gap-2">
                                            @csrf
                                            <input type="hidden" name="mode" value="promo" />
                                            <div>
                                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">Days</label>
                                                <select name="promo_days" class="rounded-lg border-gray-300 text-sm" {{ $isVerified ? '' : 'disabled' }}>
                                                    @foreach($promoAllowedDays as $d)
                                                        <option value="{{ (int) $d }}" {{ (int) $d === $promoDaysValue ? 'selected' : '' }}>{{ (int) $d }} days</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <button class="px-3 py-2 rounded-lg text-sm font-semibold {{ $isVerified ? 'bg-amber-600 text-white hover:bg-amber-700' : 'bg-gray-200 text-gray-500 cursor-not-allowed' }}" {{ $isVerified ? '' : 'disabled' }}>
                                                {{ $isActiveFeatured ? 'Update' : 'Promo Feature' }}
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('admin.featured-professionals.remove', $user) }}">
                                        @csrf
                                        <button class="px-3 py-2 rounded-lg text-sm font-semibold bg-rose-600 text-white hover:bg-rose-700" {{ $hasFeaturedFlag ? '' : 'disabled' }}>
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-500">No professionals found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $users->links() }}
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Recent Featured Activity</h2>
            <p class="text-xs text-gray-600">Last 30 actions (feature/unfeature/update).</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">When</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Professional</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentLogs as $log)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, h:i A') }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ str_replace('_', ' ', ucfirst((string) $log->action)) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $log->professional?->name ?? 'Deleted user' }}
                                <span class="text-xs text-gray-500">(#{{ $log->user_id }})</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $log->admin?->name ?? 'System' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-gray-500">No activity yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
