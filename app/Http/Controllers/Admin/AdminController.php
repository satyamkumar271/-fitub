<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payment;
use App\Models\Inquiry; // <<< YEH LINE ADD YA THEEK KARNI HAI
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Admin dashboard ko display karta hai.
     * Yeh method dashboard par dikhane ke liye zaroori data (stats, recent activities) fetch karta hai.
     */
    public function dashboard()
    {
        // Dashboard par dikhane ke liye kuch important stats calculate kar rahe hain.
        // Ye wahi stats hain jo aapne users page ke liye use kiye the, yahan bhi kaam aayenge.
        $stats = [
            'totalUsers' => User::count(),
            'totalTrainers' => User::where('user_type', 'trainer')->count(),
            'totalGyms' => User::where('user_type', 'gymowner')->count(),
            'totalRevenue' => Payment::where('status', 'paid')->sum('amount'),
        ];

        // Dashboard par haal hi mein register hue users ko dikhane ke liye
        $recentUsers = User::latest()->take(5)->get();

        // Dashboard par haal hi mein hui payments ko dikhane ke liye
        $recentPayments = Payment::with('user')->where('status', 'paid')->latest()->take(5)->get();

        // Data ko 'admin.dashboard' view mein bhej rahe hain.
        // compact() function ek shortcut hai ['stats' => $stats, 'recentUsers' => $recentUsers, ...] likhne ka.
        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentPayments'));
    }


    /**
     * User management page dikhata hai.
     */
    public function usersIndex()
    {
        $stats = [
            'totalUsers' => User::count(),
            'totalTrainers' => User::where('user_type', 'trainer')->count(),
            'totalGyms' => User::where('user_type', 'gymowner')->count(),
            'totalRevenue' => Payment::where('status', 'paid')->sum('amount'),
        ];

        $users = User::with('payments')
                     ->latest()
                     ->paginate(16);

        return view('admin.users.index', [
            'users' => $users,
            'stats' => $stats,
        ]);
    }

    /**
     * User ko delete karta hai.
     */
    public function userDestroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Aap apna khud ka account delete nahi kar sakte.');
        }

        $user->delete();

        return back()->with('success', 'User ko safaltapoorvak delete kar diya gaya hai.');
    }
     public function inquiriesIndex()
    {
        $inquiries = Inquiry::with('recipient', 'user')->latest()->paginate(20);
        return view('admin.inquiries.index', compact('inquiries'));
    }

    public function forwardInquiry(Inquiry $inquiry)
    {
        // Yahan aap automated logic daal sakte hain
        // $recipient = $inquiry->recipient;
        // if ($recipient->hasActiveSubscription()) { ... }

        $inquiry->status = 'forwarded';
        $inquiry->save();

        return back()->with('success', 'Inquiry forwarded successfully to ' . $inquiry->recipient->name);
    }

}
