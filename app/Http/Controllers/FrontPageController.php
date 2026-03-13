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

        // Show active gyms/trainers on homepage without requiring any subscription.
        $gyms = Gym::with('user')
            ->whereHas('user', function ($query) {
                $query->where('user_type', 'gymowner')
                    ->where('status', 'active')
                    ->where('is_verified', true);
            })
            ->latest()
            ->take(6)
            ->get();

        $trainers = Trainer::with('user')
            ->whereHas('user', function ($query) {
                $query->where('user_type', 'trainer')
                    ->where('status', 'active')
                    ->where('is_verified', true);
            })
            ->latest()
            ->take(6)
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
