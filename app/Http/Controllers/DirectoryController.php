<?php

namespace App\Http\Controllers;

use App\Models\Gym;
use App\Models\Trainer;
use Illuminate\Http\Request;

class DirectoryController extends Controller
{
    /**
     * Gym directory
     */
    public function indexGyms()
    {
        $gyms = Gym::with('user')   // user relation load
                    ->latest()
                    ->paginate(12);

        return view('gyms.index', [
            'gyms' => $gyms
        ]);
    }

    /**
     * Trainer directory
     */
    public function indexTrainers()
    {
        $trainers = Trainer::with('user')  // user relation load
                        ->latest()
                        ->paginate(12);

        return view('trainers.index', [
            'trainers' => $trainers
        ]);
    }
}