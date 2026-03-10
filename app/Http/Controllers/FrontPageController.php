<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FrontPageController extends Controller
{
    public function index()
    {
        $gymCount = User::where('user_type', 'gymowner')->count();
        $trainerCount = User::where('user_type', 'trainer')->count();
        $customerCount = User::where('user_type', 'customer')->count();

        // === START: EAGER LOADING ADD KAREIN ===
        $featuredGyms = User::with('profile') // Profile relationship ko pehle hi load kar lo
                            ->where('user_type', 'gymowner')
                            ->where('is_featured', true)
                            ->latest()
                            ->take(3)
                            ->get();

        $featuredTrainers = User::with('profile') // Profile relationship ko pehle hi load kar lo
                                ->where('user_type', 'trainer')
                                ->where('is_featured', true)
                                ->latest()
                                ->take(3)
                                ->get();
        // === END: EAGER LOADING ADD KAREIN ===

        return view('frontpage', [
            'gymCount' => $gymCount,
            'trainerCount' => $trainerCount,
            'customerCount' => $customerCount,
            'gyms' => $featuredGyms,
            'trainers' => $featuredTrainers,
        ]);
    }
}
