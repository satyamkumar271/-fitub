<?php

namespace App\Http\Controllers;

use App\Models\Gym;
use Illuminate\Http\Request;

class GymController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = $request->input('search');

        // Gym table se query start
        $gymsQuery = Gym::with('user');

        // Search filter
        $gymsQuery->when($searchTerm, function ($query, $term) {

            $query->where(function ($q) use ($term) {

                $q->where('gym_name', 'like', "%{$term}%")
                  ->orWhere('address_city', 'like', "%{$term}%")
                  ->orWhere('address_state', 'like', "%{$term}%")
                  ->orWhereHas('user', function ($userQuery) use ($term) {
                      $userQuery->where('name', 'like', "%{$term}%");
                  });

            });

        });

        // Pagination
        $gyms = $gymsQuery
            ->latest()
            ->paginate(12);

        return view('gyms.index', [
            'gyms' => $gyms,
            'searchTerm' => $searchTerm
        ]);
    }
}