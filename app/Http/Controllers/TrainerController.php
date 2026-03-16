<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TrainerController extends Controller
{
    public function index(Request $request, $city = null)
    {
        // Agar old style ?search=city aaya ho, to SEO friendly URL par redirect karein
        if ($request->filled('search') && $city === null) {
            $raw = (string) $request->query('search');
            $slugCity = Str::slug($raw, '-');
            return redirect()->route('trainers.city', ['city' => $slugCity]);
        }

        // URL wale slug ko search phrase me convert karein
        $searchPhrase = null;
        if ($city) {
            $phrase = str_replace('-', ' ', urldecode($city));
            $searchPhrase = trim($phrase);
        }

        // Trainer table se query start
        $trainersQuery = Trainer::with('user')
            ->whereHas('user', function ($q) {
                $q->where('status', 'active')
                    ->where('is_verified', true)
                    ->where('user_type', 'trainer');
            });

        // Search logic:
        // - Agar phrase me "in" hai -> specialization + city alag-alag handle (AND condition)
        // - Warna: sirf ek hi phrase, jise specialization OR name me use karenge (city-only case me bhi)
        $locationPart = null;
        $specializationPart = null;

        if ($searchPhrase) {
            $lower = strtolower($searchPhrase);

            if (str_contains($lower, ' in ')) {
                // Example: "best yoga trainer in chandigarh"
                $pos = strripos($lower, ' in ');
                $specializationPart = trim(substr($searchPhrase, 0, $pos)) ?: null;
                $locationPart = trim(substr($searchPhrase, $pos + 4)) ?: null;
            } else {
                // Single phrase: could be city OR specialization
                $specializationPart = $searchPhrase;
            }
        }

        // 1) Location filter (sirf city/state pe)
        if ($locationPart) {
            $tokens = collect(preg_split('/\s+/', $locationPart))
                ->map(fn ($t) => trim($t))
                ->filter()
                ->all();

            $trainersQuery->where(function ($q) use ($tokens) {
                foreach ($tokens as $token) {
                    $like = "%{$token}%";
                    $q->orWhere('city', 'like', $like)
                      ->orWhere('state', 'like', $like);
                }
            });
        }

        // 2) Specialization / name filter
        if ($specializationPart) {
            $tokens = collect(preg_split('/\s+/', $specializationPart))
                ->map(fn ($t) => trim($t))
                ->filter()
                ->all();

            $trainersQuery->where(function ($q) use ($tokens) {
                foreach ($tokens as $token) {
                    $like = "%{$token}%";
                    $q->orWhere('specialization', 'like', $like)
                      ->orWhereHas('user', function ($userQuery) use ($like) {
                          $userQuery->where('name', 'like', $like);
                      });
                }
            });
        }

        // Pagination
        $trainers = $trainersQuery
            ->latest()
            ->paginate(12);

        return view('trainers.index', [
            'trainers' => $trainers,
            'searchTerm' => $searchPhrase,
        ]);
    }
}
