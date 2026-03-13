<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
     * Register Form Submit Hone par
     */
    public function register(Request $request)
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'confirmed'],
        'user_type' => ['required', Rule::in(['customer', 'gymowner', 'trainer'])],
        'customer_city' => ['nullable', 'string', 'max:100'],
        'customer_state' => ['nullable', 'string', 'max:100'],
        'trainer_city' => ['nullable', 'string', 'max:100'],
        'trainer_state' => ['nullable', 'string', 'max:100'],
        'gym_name' => ['required_if:user_type,gymowner', 'nullable', 'string', 'max:255'],
        'id_proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
    ]);

    DB::beginTransaction();

    try {

        $userType = $request->user_type;

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'user_type' => $userType,
            'status' => ($userType === 'customer') ? 'active' : 'pending',
        ];

        // ID proof upload
        if ($request->hasFile('id_proof')) {
            $file = $request->file('id_proof');
            $fileName = time().'_'.$file->getClientOriginalName();
            $path = $file->storeAs('id_proofs',$fileName,'public');
            $userData['id_proof_path'] = $path;
        }

        // Create user
        $user = User::create($userData);

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER DATA
        |--------------------------------------------------------------------------
        */

        if ($userType === 'customer') {

            $user->customer()->create([
                'age' => $request->age,
                'phone_number' => $request->phone_number,
                'weight' => $request->weight,
                'height' => $request->height,
                'goal' => $request->goal,
                'city' => $request->customer_city,
                'state' => $request->customer_state
            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | TRAINER DATA
        |--------------------------------------------------------------------------
        */

        elseif ($userType === 'trainer') {

            $certifications = null;

            if ($request->filled('certification_name')) {

                $certs = [];

                foreach ($request->certification_name as $key => $name) {

                    if (!empty($name)) {

                        $certs[] = [
                            'name' => $name,
                            'issuer' => $request->certification_issuer[$key] ?? ''
                        ];
                    }
                }

                $certifications = json_encode($certs);
            }

            $socialLinks = null;

            if ($request->filled('social_platform')) {

                $socials = [];

                foreach ($request->social_platform as $key => $platform) {

                    if (!empty($request->social_url[$key])) {

                        $socials[] = [
                            'platform' => $platform,
                            'url' => $request->social_url[$key]
                        ];
                    }
                }

                $socialLinks = json_encode($socials);
            }

            $user->trainer()->create([
                'phone_number' => $request->trainer_phone_number,
                'website_url' => $request->trainer_website_url,
                'specialization' => $request->specialization,
                'experience' => $request->experience,
                'city' => $request->trainer_city,
                'state' => $request->trainer_state,
                'certifications' => $certifications,
                'social_links' => $socialLinks
            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | GYM OWNER DATA
        |--------------------------------------------------------------------------
        */

        elseif ($userType === 'gymowner') {

            $socialLinks = null;

            if ($request->filled('social_platform')) {

                $socials = [];

                foreach ($request->social_platform as $key => $platform) {

                    if (!empty($request->social_url[$key])) {

                        $socials[] = [
                            'platform' => $platform,
                            'url' => $request->social_url[$key]
                        ];
                    }
                }

                $socialLinks = json_encode($socials);
            }

            $user->gym()->create([
                'gym_name' => $request->gym_name,
                'gym_phone_number' => $request->gym_phone_number,
                'gym_email' => $request->gym_email,
                'gym_website_url' => $request->gym_website_url,
                'address_street' => $request->address_street,
                'address_city' => $request->address_city,
                'address_state' => $request->address_state,
                'address_pincode' => $request->address_pincode,
                'gym_age' => $request->gym_age,
                'total_members' => $request->total_members,
                'social_links' => $socialLinks
            ]);

        }

        DB::commit();

        if ($user->status === 'pending') {

            return redirect()->route('login')
            ->with('status','Your account is under review. Our team will verify your details, and you will be able to login once approved.');
        }

        Auth::login($user);

        return redirect()->route('dashboard')
        ->with('status','Welcome! Your account has been created successfully.');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()->withErrors([
            'error' => 'Registration failed. Please try again.'
        ]);
    }
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
            $user = Auth::user();

            // PENDING STATUS CHECK
            if ($user->status === 'pending') {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account is currently under review by Admin. Please wait for approval.']);
            }

            $request->session()->regenerate();

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
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Check if user exists and is active (not pending)
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'We can\'t find a user with that email address.']);
        }

        if ($user->status === 'pending') {
            return back()->withErrors(['email' => 'Your account is pending approval. Please contact support.']);
        }

        // Generate token
        $token = Str::random(64);

        // Store token in password_resets table
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // Send email
        $user->sendPasswordResetNotification($token);

        return back()->with('status', 'We have emailed your password reset link!');
    }

    /**
     * Show reset password form
     */
    public function showResetPasswordForm(Request $request, $token = null)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Check if reset record exists
        $resetRecord = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            return back()->withErrors(['email' => 'Invalid reset token.']);
        }

        // Check if token is valid
        if (!Hash::check($request->token, $resetRecord->token)) {
            return back()->withErrors(['email' => 'Invalid or expired reset token.']);
        }

        // Check if token is expired (60 minutes)
        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            DB::table('password_resets')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Reset token has expired. Please request a new one.']);
        }

        // Update user password
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        $user->password = $request->password; // Will be hashed by mutator
        $user->save();

        // Delete reset token
        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Your password has been reset successfully! You can now login with your new password.');
    }

    // ... (baaki methods: showLoginForm, dashboard, logout waise hi rahenge)
    public function showLoginForm() { return view('auth.login'); }
    public function dashboard() { return view('dashboard'); }
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}