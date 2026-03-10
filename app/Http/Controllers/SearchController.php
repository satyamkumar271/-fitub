<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SearchController extends Controller
{
    /**
     * Handle the incoming search request from the homepage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleSearch(Request $request)
    {
        // Form se 'type' aur 'location' ki value lein
        $searchType = $request->input('type');
        $location = $request->input('location');

        // Yeh check karega ki user ne Gym search kiya hai ya Trainer
        if ($searchType === 'gym') {
            // Agar gym search kiya hai, to 'gyms.index' route par redirect karein
            // Saath mein location ko query parameter bana kar bhej denge
            // URL aisa dikhega: /gyms?search=delhi
            return Redirect::route('gyms.index', ['search' => $location]);

        } elseif ($searchType === 'trainer') {
            // Agar trainer search kiya hai, to 'trainers.index' route par redirect karein
            // Saath mein location ko query parameter bana kar bhej denge
            // URL aisa dikhega: /trainers?search=mumbai
            return Redirect::route('trainers.index', ['search' => $location]);
        }

        // Agar kuch galat ho to homepage par wapas bhej dein
        return Redirect::route('homepage'); // 'homepage' aapke homepage route ka naam hona chahiye
    }
}
