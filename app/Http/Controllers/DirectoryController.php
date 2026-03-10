<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DirectoryController extends Controller
{
    /**
     * Sabhi 'gymowner' users ki list, pagination ke saath dikhaye.
     *
     * @return \Illuminate\View\View
     */
    public function indexGyms()
    {
        $gyms = User::where('user_type', 'gymowner')
                    ->latest() // Naye gyms pehle
                    ->paginate(12); // Ek page par 12 dikhayenge

        return view('gyms.index', ['gyms' => $gyms]);
    }

    /**
     * Sabhi 'trainer' users ki list, pagination ke saath dikhaye.
     *
     * @return \Illuminate\View\View
     */
    public function indexTrainers()
    {
        $trainers = User::where('user_type', 'trainer')
                        ->latest() // Naye trainers pehle
                        ->paginate(12); // Ek page par 12 dikhayenge

        return view('trainers.index', ['trainers' => $trainers]);
    }
}
