<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; // Yeh import karna zaroori hai

class AuthController extends Controller
{
    /**
     * Register Form Dikhane ke liye
     */
    public function showRegisterForm()
    {
         return view('auth.register');
    }

    /**
     * Register Form Submit Hone par (Poori tarah se sahi kiya gaya)
     */
    public function register(Request $request)
    {
        // STEP 1: VALIDATION
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed'],
            'user_type' => ['required', Rule::in(['customer', 'gymowner', 'trainer'])],

            // Naye fields ke liye optional validation
            'customer_city' => ['nullable', 'string', 'max:100'],
            'customer_state' => ['nullable', 'string', 'max:100'],
            'trainer_city' => ['nullable', 'string', 'max:100'],
            'trainer_state' => ['nullable', 'string', 'max:100'],
            'gym_name' => ['required_if:user_type,gymowner', 'nullable', 'string', 'max:255'],

        ]);

        // STEP 2: DATA PREPARATION
        $data = $request->only('name', 'email', 'user_type');
        $data['password'] = $request->password;

        $userType = $request->user_type;

        if ($userType === 'customer') {
            $data = array_merge($data, $request->only(
                'age', 'phone_number', 'weight', 'height', 'goal',
                'customer_city', 'customer_state' // <<< NAYE FIELDS
            ));
        }
        elseif ($userType === 'trainer') {
            $data = array_merge($data, $request->only(
                'trainer_phone_number', 'trainer_website_url', 'specialization', 'experience',
                'trainer_city', 'trainer_state' // <<< NAYE FIELDS
            ));

            if ($request->filled('certification_name')) {
                $certs = [];
                foreach ($request->certification_name as $key => $name) {
                    if (!empty($name)) {
                        $certs[] = ['name' => $name, 'issuer' => $request->certification_issuer[$key] ?? ''];
                    }
                }
                $data['certifications'] = $certs;
            }
        }
        elseif ($userType === 'gymowner') {
            $data = array_merge($data, $request->only(
                'gym_name', // <<< NAYA FIELD
                'gym_phone_number', 'gym_email', 'gym_website_url',
                'address_street', 'address_city', 'address_state', 'address_pincode',
                'gym_age', 'total_members'
            ));
        }

        if (($userType === 'trainer' || $userType === 'gymowner') && $request->filled('social_platform')) {
             $socials = [];
            foreach ($request->social_platform as $key => $platform) {
                if (!empty($request->social_url[$key])) {
                    $socials[] = ['platform' => $platform, 'url' => $request->social_url[$key]];
                }
            }
            $data['social_links'] = $socials;
        }

        // STEP 3: USER CREATE KAREIN AUR LOGIN KAREIN
        $user = User::create($data);
        Auth::login($user);

        // STEP 4: DASHBOARD PAR REDIRECT KAREIN
        return redirect()->route('dashboard')->with('status', 'Welcome! Your account has been created successfully.');
    }

    /**
     * Login Form Dikhane ke liye
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Login karne ke liye
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->user_type === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Dashboard Page
     */
    public function dashboard()
    {
        return view('dashboard');
    }

    /**
     * Logout karne ke liye
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
