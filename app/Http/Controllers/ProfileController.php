<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Display the specified user's public profile (for everyone).
     * Yeh method kisi bhi user (guest, customer, owner) ke liye call hoga
     * aur uski poori details dikhayega.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
     public function show(User $user)
    {
        // Yahan 'user' key ka naam hi view mein variable ka naam banega.
        // Hum 'user' naam ka key bhej rahe hain, to view mein $user available hoga.
        return view('profile.show', [
            'user' => $user
        ]);
    }
}

    

