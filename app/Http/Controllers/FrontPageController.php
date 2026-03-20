<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Gym;
use App\Models\Trainer;
use App\Models\User;

class FrontPageController extends Controller
{
    public function index()
    {
        $gymCount = User::where('user_type', 'gymowner')->where('status', 'active')->where('is_verified', true)->count();
        $trainerCount = User::where('user_type', 'trainer')->where('status', 'active')->where('is_verified', true)->count();
        $customerCount = User::where('user_type', 'customer')->where('status', 'active')->count();

        // Home page featured professionals (admin-controlled + time-bound).
        // Only show currently-active featured items.
        $gymLimit = (int) (config('featured.home_limits.gymowner', 6) ?? 6);
        $trainerLimit = (int) (config('featured.home_limits.trainer', 6) ?? 6);

        $gyms = Gym::query()
            ->select('gyms.*')
            ->join('users', 'users.id', '=', 'gyms.user_id')
            ->where('users.user_type', 'gymowner')
            ->where('users.status', 'active')
            ->where('users.is_verified', true)
            ->where('users.is_featured', true)
            ->whereNotNull('users.featured_until')
            ->where('users.featured_until', '>', now())
            ->orderByRaw("CASE users.featured_source WHEN 'subscription' THEN 2 WHEN 'promo' THEN 1 ELSE 0 END DESC")
            ->orderByDesc('users.featured_until')
            ->orderByDesc('gyms.id')
            ->with('user')
            ->take($gymLimit)
            ->get();

        $trainers = Trainer::query()
            ->select('trainers.*')
            ->join('users', 'users.id', '=', 'trainers.user_id')
            ->where('users.user_type', 'trainer')
            ->where('users.status', 'active')
            ->where('users.is_verified', true)
            ->where('users.is_featured', true)
            ->whereNotNull('users.featured_until')
            ->where('users.featured_until', '>', now())
            ->orderByRaw("CASE users.featured_source WHEN 'subscription' THEN 2 WHEN 'promo' THEN 1 ELSE 0 END DESC")
            ->orderByDesc('users.featured_until')
            ->orderByDesc('trainers.id')
            ->with('user')
            ->take($trainerLimit)
            ->get();

        $topBlogs = Blog::where('is_published', true)
            ->orderByDesc('published_at')
            ->latest('id')
            ->take(3)
            ->get();

        return view('frontpage', [
            'gymCount' => $gymCount,
            'trainerCount' => $trainerCount,
            'customerCount' => $customerCount,
            'gyms' => $gyms,
            'trainers' => $trainers,
            'topBlogs' => $topBlogs,
        ]);
    }
}
