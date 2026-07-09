<?php

namespace App\Http\Controllers;

use App\Models\Gym;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GymController extends Controller
{
    public function index(Request $request, $city = null)
    {
        // Agar old style ?search=city aaya ho, to SEO friendly URL par redirect karein
        if ($request->filled('search') && $city === null) {
            $raw = (string) $request->query('search');
            $slugCity = Str::slug($raw, '-');
            return redirect()->route('gyms.city', ['city' => $slugCity]);
        }

        // URL wale slug ko search phrase me convert karein
        $searchTerm = null;
        if ($city) {
            $phrase = str_replace('-', ' ', urldecode($city));
            $lower = strtolower($phrase);

            // Agar slug "gym-in-chandigarh" ya "best-gym-in-chandigarh" type ho,
            // to "in" ke baad wala part city/keyword maan lo.
            if (str_contains($lower, ' in ')) {
                $afterIn = substr($phrase, strripos($lower, ' in ') + 4);
                $phrase = trim($afterIn);
            }

            $searchTerm = $phrase;
        }

        // Gym table se query start
        $gymsQuery = Gym::with('user')
            ->whereHas('user', function ($q) {
                $q->where('status', 'active')
                    ->where('is_verified', true)
                    ->where('user_type', 'gymowner');
            });

        // Search filter – multi-keyword (gym name + city + state + owner name)
        $gymsQuery->when($searchTerm, function ($query, $term) {
            $tokens = collect(preg_split('/\s+/', (string) $term))
                ->map(fn ($t) => trim($t))
                ->filter()
                ->all();

            if (empty($tokens)) {
                return;
            }

            $query->where(function ($q) use ($tokens) {
                foreach ($tokens as $token) {
                    $like = "%{$token}%";
                    $q->orWhere('gym_name', 'like', $like)
                      ->orWhere('address_city', 'like', $like)
                      ->orWhere('address_state', 'like', $like)
                      ->orWhereHas('user', function ($userQuery) use ($like) {
                          $userQuery->where('name', 'like', $like);
                      });
                }
            });
        });

        // Pagination
        $gyms = $gymsQuery
            ->latest()
            ->paginate(12);

        return view('gyms.index', [
            'gyms' => $gyms,
            'searchTerm' => $searchTerm,
        ]);
    }
}
