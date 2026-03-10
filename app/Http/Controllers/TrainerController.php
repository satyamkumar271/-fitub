<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TrainerController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = $request->input('search');

        // User model se query shuru karein, sirf 'trainer' type ke users
        $trainersQuery = User::where('user_type', 'trainer');

        // Agar search term hai, to filter lagayein
        $trainersQuery->when($searchTerm, function ($query, $term) {
            return $query->where(function ($subQuery) use ($term) {
                $subQuery->where('name', 'like', "%{$term}%")
                         ->orWhere('trainer_city', 'like', "%{$term}%")
                         ->orWhere('trainer_state', 'like', "%{$term}%")
                         ->orWhere('specialization', 'like', "%{$term}%");
            });
        });

        // Data fetch karein pagination ke saath
        $trainers = $trainersQuery->latest()->paginate(12);

        // View ko data pass karein
        return view('trainers.index', [
            'trainers' => $trainers,
            'searchTerm' => $searchTerm
        ]);
    }
}
