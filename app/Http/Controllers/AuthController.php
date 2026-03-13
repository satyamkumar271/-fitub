<?php

namespace App\Http\Controllers;

use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Customer;
use App\Models\Trainer;
use App\Models\Gym;

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
        'password' => ['required', 'string', 'min:8', 'confirmed'],
        'user_type' => ['required', Rule::in(['customer', 'gymowner', 'trainer'])],
    ]);

    DB::beginTransaction();

    try {

        $userType = $request->user_type;

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'user_type' => $userType,
            'status' => 'email_unverified',
            'is_verified' => false,
            'kyc_status' => $userType === 'customer' ? 'not_required' : 'profile_incomplete',
        ];

        // Create user
        $user = User::create($userData);

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER DATA
        |--------------------------------------------------------------------------
        */

        if ($userType === 'customer') {
            $user->customer()->create([
                'age' => null,
                'phone_number' => null,
                'weight' => null,
                'height' => null,
                'goal' => null,
                'city' => null,
                'state' => null,
            ]);
        }
        elseif ($userType === 'trainer') {
            $user->trainer()->create([
                'phone_number' => null,
                'website_url' => null,
                'specialization' => null,
                'experience' => null,
                'city' => null,
                'state' => null,
                'certifications' => null,
                'social_links' => null,
                'certificate_proof_paths' => null,
            ]);
        }
        elseif ($userType === 'gymowner') {
            $user->gym()->create([
                'gym_name' => null,
                'gym_phone_number' => null,
                'gym_email' => null,
                'gym_website_url' => null,
                'business_doc_path' => null,
                'address_street' => null,
                'address_city' => null,
                'address_state' => null,
                'address_pincode' => null,
                'gym_age' => null,
                'total_members' => null,
                'social_links' => null,
            ]);
        }

        $this->createAndSendEmailOtp($user);

        DB::commit();

        return redirect()->route('otp.verify.form', ['user' => $user->id])
            ->with('status', 'We sent a verification OTP to your email. Please verify to continue.');

    } catch (\Exception $e) {

        DB::rollBack();
        report($e);
        Log::error('Registration failed', [
            'email' => $request->email,
            'user_type' => $request->user_type,
            'error' => $e->getMessage(),
        ]);

        $errorMessage = 'Registration failed. Please try again.';
        $rawMessage = (string) $e->getMessage();
        $rawLower = strtolower($rawMessage);

        if (str_contains($rawLower, 'connection could not be established')
            || str_contains($rawLower, 'failed to authenticate')
            || str_contains($rawLower, 'smtp')) {
            $errorMessage = 'Registration failed: OTP email send nahi ho payi. SMTP settings check karein.';
        } elseif (app()->environment('local')) {
            $errorMessage = 'Registration failed: ' . $rawMessage;
        }

        return back()
            ->withInput($request->except([
                'password',
                'password_confirmation',
                'id_proof',
                'business_doc',
                'certificate_proofs',
            ]))
            ->withErrors([
                'error' => $errorMessage,
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
            if ($user->status === 'email_unverified') {
                Auth::logout();
                return back()->withErrors(['email' => 'Please verify your email OTP first.']);
            }
            if ($user->status === 'pending') {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account is currently under review by verification team. Please wait for approval.']);
            }
            if ($user->status === 'cancelled') {
    Auth::logout();
    return back()->withErrors([
        'email' => 'Your account has been cancelled by admin. Please contact support.'
    ]);
}

            $request->session()->regenerate();

            if ($user->user_type === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            if ($user->status === 'profile_incomplete') {
                return redirect()->route('dashboard')->with('status', 'Please complete your profile and upload required documents to submit verification.');
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

    public function showOtpForm(Request $request, User $user)
    {
        if ($user->status !== 'email_unverified') {
            return redirect()->route('login')->with('status', 'Email is already verified. Please login.');
        }

        return view('auth.verify-otp', [
            'user' => $user,
        ]);
    }

    public function verifyOtp(Request $request, User $user)
    {
        $data = $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $otp = EmailOtp::where('user_id', $user->id)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (!$otp || now()->greaterThan($otp->expires_at)) {
            return back()->withErrors(['otp' => 'OTP expired. Please request a new OTP.']);
        }

        if ($otp->attempts >= 5) {
            return back()->withErrors(['otp' => 'Too many invalid attempts. Please request a new OTP.']);
        }

        if (!Hash::check($data['otp'], $otp->code_hash)) {
            $otp->increment('attempts');
            return back()->withErrors(['otp' => 'Invalid OTP.']);
        }

        DB::transaction(function () use ($user, $otp) {
            $otp->update([
                'verified_at' => now(),
            ]);

            $nextStatus = $user->user_type === 'customer' ? 'active' : 'profile_incomplete';

            $user->update([
                'email_verified_at' => now(),
                'status' => $nextStatus,
                'kyc_status' => $user->user_type === 'customer' ? 'not_required' : 'profile_incomplete',
            ]);
        });

        if ($user->user_type === 'customer') {
            Auth::login($user);
            return redirect()->route('dashboard')->with('status', 'Email verified. Welcome!');
        }

        return redirect()->route('login')->with('status', 'Email verified. Please login and complete your profile to submit for review.');
    }

    public function resendOtp(Request $request, User $user)
    {
        if ($user->status !== 'email_unverified') {
            return redirect()->route('login')->with('status', 'Email already verified.');
        }

        $latest = EmailOtp::where('user_id', $user->id)->latest()->first();
        if ($latest && $latest->created_at && now()->diffInSeconds($latest->created_at) < 30) {
            return back()->withErrors(['otp' => 'Please wait 30 seconds before requesting a new OTP.']);
        }

        $this->createAndSendEmailOtp($user);

        return back()->with('status', 'A new OTP has been sent to your email.');
    }

    private function createAndSendEmailOtp(User $user): void
    {
        $code = (string) random_int(100000, 999999);

        EmailOtp::create([
            'user_id' => $user->id,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::raw("Your Fitub OTP is {$code}. It expires in 10 minutes.", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Fitub Email Verification OTP');
        });
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
