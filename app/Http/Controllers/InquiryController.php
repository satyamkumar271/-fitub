<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        Inquiry::create($inquiryData);

        return back()->with('success', 'Your inquiry has been sent successfully! Our team will review it shortly.');
    }
}
