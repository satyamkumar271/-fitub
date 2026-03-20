<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\InquiryMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class InquiryController extends Controller
{
    public function store(Request $request)
    {
        // Validation rules
        $data = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'service_needed' => 'required|string|max:255',
            'message' => 'required|string',
            // required_without:user_id rule yahan kaam nahi karega, hum ise manually handle karenge
            'guest_name' => 'nullable|string|max:255',
            'guest_email' => 'nullable|email|max:255',
            'guest_phone' => 'nullable|string|max:20',
        ]);

        $inquiryData = [
            'recipient_id' => $data['recipient_id'],
            'service_needed' => $data['service_needed'],
            'message' => $data['message'],
        ];

        // Check karein user logged in hai ya guest
        if (Auth::check()) {
            $inquiryData['user_id'] = Auth::id();
        } else {
            // Agar guest hai, to guest fields zaroori hain
            $request->validate([
                'guest_name' => 'required|string|max:255',
                'guest_email' => 'required|email|max:255',
                'guest_phone' => 'required|string|max:20',
            ]);
            $inquiryData['guest_name'] = $request->guest_name;
            $inquiryData['guest_email'] = $request->guest_email;
            $inquiryData['guest_phone'] = $request->guest_phone;
        }

        // Mass assignment ka istemal karke data create karein
        $inquiry = Inquiry::create($inquiryData);

        // If inquiry came from a logged-in customer, seed the first chat message.
        if ($inquiry && !empty($inquiry->user_id) && !empty($inquiry->message)) {
            InquiryMessage::create([
                'inquiry_id' => $inquiry->id,
                'sender_id' => $inquiry->user_id,
                'message' => $inquiry->message,
            ]);
        }

        // Guest inquiry: email a signed "enable chat" link (verify-first flow).
        if ($inquiry && empty($inquiry->user_id) && !empty($inquiry->guest_email)) {
            try {
                $claimUrl = URL::temporarySignedRoute(
                    'inquiries.claim',
                    now()->addDays(2),
                    ['inquiry' => $inquiry->id]
                );

                Mail::send('emails.guest-enable-chat', [
                    'inquiry' => $inquiry,
                    'claimUrl' => $claimUrl,
                ], function ($message) use ($inquiry) {
                    $message->to($inquiry->guest_email)
                        ->subject('Fitub: Verify email to enable chat & track your inquiry');
                });
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('success', 'Your inquiry has been sent successfully! Our team will review it shortly.');
    }

    public function claim(Request $request, Inquiry $inquiry)
    {
        if (!empty($inquiry->user_id)) {
            return redirect()->route('inquiries.chat', $inquiry);
        }

        if (empty($inquiry->guest_email)) {
            return redirect('/')->withErrors(['error' => 'This inquiry cannot be claimed.']);
        }

        $request->session()->put('claim_inquiry_id', (int) $inquiry->id);
        $request->session()->put('claim_inquiry_email', (string) $inquiry->guest_email);
        $request->session()->put('claim_inquiry_name', (string) ($inquiry->guest_name ?? ''));

        if (Auth::check()) {
            $user = Auth::user();
            if (strcasecmp((string) $user->email, (string) $inquiry->guest_email) !== 0) {
                Auth::logout();
                $response = redirect()
                    ->route('login')
                    ->withErrors(['email' => 'Please login using the same email used for this inquiry to enable chat.']);

                // Important: prepare the response/flash first.
                // If we invalidate before `withErrors()`, the flash message can be lost.
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return $response;
            }

            $inquiry->user_id = $user->id;
            $inquiry->save();

            if (!empty($inquiry->message) && !InquiryMessage::where('inquiry_id', $inquiry->id)->exists()) {
                InquiryMessage::create([
                    'inquiry_id' => $inquiry->id,
                    'sender_id' => $user->id,
                    'message' => $inquiry->message,
                ]);
            }

            $request->session()->forget(['claim_inquiry_id', 'claim_inquiry_email', 'claim_inquiry_name']);

            return redirect()->route('inquiries.chat', $inquiry)->with('success', 'Inquiry linked to your account. Chat is enabled.');
        }

        $existing = \App\Models\User::where('email', $inquiry->guest_email)->first();
        if ($existing) {
            return redirect()
                ->route('password.request', ['email' => $inquiry->guest_email])
                ->with('status', 'We found an existing account on this email. Please reset your password (if needed) and login to enable chat.');
        }

        return redirect()
            ->route('register', [
                'name' => $inquiry->guest_name,
                'email' => $inquiry->guest_email,
                'user_type' => 'customer',
            ])
            ->with('status', 'Create an account and verify OTP to enable chat.');
    }
}
