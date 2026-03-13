<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use Illuminate\Http\Request;

class TrainerController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = $request->input('search');

        // Trainer table se query start
        $trainersQuery = Trainer::with('user')
            ->whereHas('user', function ($q) {
                $q->where('status', 'active')
                    ->where('is_verified', true)
                    ->where('user_type', 'trainer');
            });

        // Search filter
        $trainersQuery->when($searchTerm, function ($query, $term) {

            $query->where(function ($q) use ($term) {

                $q->where('city', 'like', "%{$term}%")
                  ->orWhere('state', 'like', "%{$term}%")
                  ->orWhere('specialization', 'like', "%{$term}%")
                  ->orWhereHas('user', function ($userQuery) use ($term) {
                      $userQuery->where('name', 'like', "%{$term}%");
                  });

            });

        });

        // Pagination
        $trainers = $trainersQuery
            ->latest()
            ->paginate(12);

        return view('trainers.index', [
            'trainers' => $trainers,
            'searchTerm' => $searchTerm
        ]);
    }
}
