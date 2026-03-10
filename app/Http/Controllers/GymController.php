<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class GymController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = $request->input('search');

        // User model se query shuru karein, sirf 'gymowner' type ke users
        $gymsQuery = User::where('user_type', 'gymowner');

        // Agar search term hai, to filter lagayein
        // when() function tabhi chalta hai jab pehli condition ($searchTerm) true ho
        $gymsQuery->when($searchTerm, function ($query, $term) {
            return $query->where(function ($subQuery) use ($term) {
                $subQuery->where('gym_name', 'like', "%{$term}%")
                         ->orWhere('address_city', 'like', "%{$term}%")
                         ->orWhere('address_state', 'like', "%{$term}%");
            });
        });

        // Data fetch karein pagination ke saath
        $gyms = $gymsQuery->latest()->paginate(12);

        // View ko data pass karein
        return view('gyms.index', [
            'gyms' => $gyms,
            'searchTerm' => $searchTerm
        ]);
    }
}
