<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeaturedProfessionalLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FeaturedProfessionalController extends Controller
{
    public function index(Request $request)
    {
        $type = (string) $request->query('type', 'all');
        $allowedTypes = ['all', 'trainer', 'gymowner'];
        if (!in_array($type, $allowedTypes, true)) {
            $type = 'all';
        }

        $tab = (string) $request->query('tab', 'all');
        $allowedTabs = ['all', 'featured', 'eligible'];
        if (!in_array($tab, $allowedTabs, true)) {
            $tab = 'all';
        }

        $query = User::query()
            ->whereIn('user_type', ['trainer', 'gymowner'])
            ->with(['activeEligibleSubscription']);

        if ($type !== 'all') {
            $query->where('user_type', $type);
        }

        if ($tab === 'featured') {
            $query->where('is_featured', true);
        } elseif ($tab === 'eligible') {
            $query->whereHas('activeEligibleSubscription');
        }

        if ($request->filled('q')) {
            $term = (string) $request->query('q');
            $query->where(function ($inner) use ($term) {
                $inner->where('name', 'like', '%' . $term . '%')
                    ->orWhere('email', 'like', '%' . $term . '%');
            });
        }

        $users = $query
            ->orderByDesc('is_featured')
            ->orderByDesc('featured_until')
            ->latest('id')
            ->paginate(20)
            ->appends($request->query());

        $userIds = $users->getCollection()->pluck('id')->values();

        $latestLogIds = FeaturedProfessionalLog::query()
            ->selectRaw('MAX(id) as id')
            ->whereIn('user_id', $userIds)
            ->groupBy('user_id')
            ->pluck('id');

        $latestLogs = FeaturedProfessionalLog::with('admin')
            ->whereIn('id', $latestLogIds)
            ->get()
            ->keyBy('user_id');

        $recentLogs = FeaturedProfessionalLog::with(['professional', 'admin'])
            ->latest()
            ->take(30)
            ->get();

        $eligiblePlans = (array) config('featured.eligible_plans', ['pro', 'business']);

        return view('admin.featured-professionals.index', compact(
            'users',
            'type',
            'tab',
            'latestLogs',
            'recentLogs',
            'eligiblePlans',
        ));
    }

    public function feature(Request $request, User $user)
    {
        if (!in_array((string) $user->user_type, ['trainer', 'gymowner'], true)) {
            return back()->with('error', 'Only trainers and gym owners can be featured.');
        }

        $mode = (string) $request->input('mode', 'subscription');
        if (!in_array($mode, ['subscription', 'promo'], true)) {
            $mode = 'subscription';
        }

        if ((string) ($user->status ?? '') !== 'active') {
            return back()->with('error', 'User must be active to be featured.');
        }
        if (!(bool) ($user->is_verified ?? false)) {
            return back()->with('error', 'Only verified professionals can be featured.');
        }

        if ($mode === 'promo') {
            return $this->featurePromo($request, $user);
        }

        $data = $request->validate([
            'featured_until' => ['required', 'date_format:Y-m-d'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $subscription = $user->activeEligibleSubscription()->first();
        if (!$subscription) {
            return back()->with('error', 'This user does not have an active eligible subscription.');
        }

        $requestedUntil = Carbon::createFromFormat('Y-m-d', (string) $data['featured_until'], config('app.timezone'))
            ->endOfDay();

        $subscriptionUntil = Carbon::parse($subscription->expires_at)->endOfDay();
        $finalUntil = $requestedUntil->gt($subscriptionUntil) ? $subscriptionUntil : $requestedUntil;

        if ($finalUntil->lte(now())) {
            return back()->with('error', 'Featured end date must be in the future (and within subscription validity).');
        }

        $beforeUntil = $user->featured_until;
        $beforeFeatured = (bool) ($user->is_featured ?? false);

        $user->is_featured = true;
        $user->featured_until = $finalUntil;
        $user->featured_source = 'subscription';
        $user->save();

        $action = $beforeFeatured ? 'update_until' : 'feature';

        FeaturedProfessionalLog::create([
            'user_id' => $user->id,
            'admin_id' => auth()->id(),
            'action' => $action,
            'source' => 'subscription',
            'featured_until_before' => $beforeUntil,
            'featured_until_after' => $finalUntil,
            'note' => $data['note'] ?? null,
        ]);

        if ($finalUntil->ne($requestedUntil)) {
            return back()->with('success', 'Featured saved. Note: End date was capped to subscription expiry.');
        }

        return back()->with('success', 'Featured saved successfully.');
    }

    public function remove(Request $request, User $user)
    {
        if (!in_array((string) $user->user_type, ['trainer', 'gymowner'], true)) {
            return back()->with('error', 'Only trainers and gym owners can be unfeatured here.');
        }

        $beforeUntil = $user->featured_until;
        $beforeSource = (string) ($user->featured_source ?? '');

        if ($beforeSource === 'promo') {
            $endedAt = $beforeUntil && $beforeUntil->lte(now()) ? $beforeUntil : now();
            $user->promo_featured_last_ended_at = $endedAt;
        }

        $user->is_featured = false;
        $user->featured_until = null;
        $user->featured_source = null;
        $user->save();

        FeaturedProfessionalLog::create([
            'user_id' => $user->id,
            'admin_id' => auth()->id(),
            'action' => 'unfeature',
            'source' => $beforeSource !== '' ? $beforeSource : null,
            'featured_until_before' => $beforeUntil,
            'featured_until_after' => null,
        ]);

        return back()->with('success', 'Removed from featured.');
    }

    private function featurePromo(Request $request, User $user)
    {
        $promoConfig = (array) config('featured.promo', []);
        $allowedDays = array_map('intval', (array) ($promoConfig['allowed_days'] ?? [2, 3, 7]));
        $allowedDays = array_values(array_unique(array_filter($allowedDays, fn ($d) => $d > 0)));
        if ($allowedDays === []) {
            $allowedDays = [2, 3, 7];
        }

        $data = $request->validate([
            'promo_days' => ['required', 'integer'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $days = (int) $data['promo_days'];
        if (!in_array($days, $allowedDays, true)) {
            return back()->with('error', 'Invalid promo duration selected.');
        }

        $maxTotalDays = (int) ($promoConfig['max_total_days_per_user'] ?? 7);
        $maxGrants = (int) ($promoConfig['max_grants_per_user'] ?? 1);
        $cooldownDays = (int) ($promoConfig['cooldown_days'] ?? 30);

        $promoUsed = (int) ($user->promo_featured_days_used ?? 0);
        $promoGrants = (int) ($user->promo_featured_grants ?? 0);

        if ($maxGrants > 0 && $promoGrants >= $maxGrants) {
            return back()->with('error', 'Promo featured already used for this user.');
        }
        if ($maxTotalDays > 0 && ($promoUsed + $days) > $maxTotalDays) {
            return back()->with('error', 'Promo days limit exceeded for this user.');
        }

        // If a previous promo expired naturally and last_ended_at wasn't captured, infer it.
        if ((string) ($user->featured_source ?? '') === 'promo' && $user->featured_until && $user->featured_until->lte(now()) && empty($user->promo_featured_last_ended_at)) {
            $user->promo_featured_last_ended_at = $user->featured_until;
        }

        if (!empty($user->promo_featured_last_ended_at) && $cooldownDays > 0) {
            $cooldownUntil = Carbon::parse($user->promo_featured_last_ended_at)->addDays($cooldownDays);
            if ($cooldownUntil->gt(now())) {
                return back()->with('error', 'Promo cooldown active for this user.');
            }
        }

        $beforeUntil = $user->featured_until;
        $beforeFeatured = (bool) ($user->is_featured ?? false);

        $finalUntil = now()->copy()->addDays($days)->endOfDay();
        if ($finalUntil->lte(now())) {
            return back()->with('error', 'Promo end date must be in the future.');
        }

        $user->is_featured = true;
        $user->featured_until = $finalUntil;
        $user->featured_source = 'promo';
        $user->promo_featured_days_used = $promoUsed + $days;
        $user->promo_featured_grants = $promoGrants + 1;
        $user->save();

        $action = $beforeFeatured ? 'update_until' : 'feature';

        FeaturedProfessionalLog::create([
            'user_id' => $user->id,
            'admin_id' => auth()->id(),
            'action' => $action,
            'source' => 'promo',
            'featured_until_before' => $beforeUntil,
            'featured_until_after' => $finalUntil,
            'note' => $data['note'] ?? null,
        ]);

        return back()->with('success', 'Promo featured saved successfully.');
    }
}
