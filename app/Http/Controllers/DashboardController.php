<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Inquiry;
use App\Models\Subscription;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * User ka dashboard dikhata hai.
     */
    public function index()
    {
        $user = auth()->user();
        $leads = collect();
        $subscription = null;

        if (in_array($user->user_type, ['trainer', 'gymowner'])) {
            $subscription = Subscription::where('user_id', $user->id)
                ->where(function ($query) {
                    $query->where('expires_at', '>', now())
                          ->orWhere(function ($subQuery) {
                              $subQuery->where('plan_type', 'single_lead')
                                       ->where('created_at', '>', now()->subMinutes(5));
                          });
                })->latest()->first();

            $leads = Inquiry::where('recipient_id', $user->id)
                ->whereIn('status', ['forwarded', 'viewed'])
                ->with('user')->latest()->get();
        }

        return view('dashboard', compact('user', 'leads', 'subscription'));
    }

       /**
     * User ka profile (naam, goal, aur anya details) update karta hai.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'goal' => 'nullable|string|max:1000',
            'age' => 'nullable|integer|min:0',
            'phone_number' => 'nullable|string|max:20',
            'weight' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
        ]);

        // Use the update method which is cleaner for mass assignment
        $user->update($validatedData);

        return redirect()->route('dashboard')->with('success', 'Profile updated successfully!');
    }
    /**
     * NAYI METHOD: User ki gallery mein photos add karti hai.
     */
    public function updateGallery(Request $request)
    {
        $request->validate([
            'gallery.*' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ], ['gallery.*.image' => 'The uploaded file must be an image.']);

        $user = Auth::user();
        $gallery = $user->gallery_images ?? [];

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $path = $file->store('gallery-images', 'public');
                $gallery[] = $path;
            }
        }

        $user->gallery_images = $gallery;
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Gallery updated successfully!');
    }

    /**
     * NAYI METHOD: User ki gallery se ek photo delete karti hai.
     */
    public function deleteGalleryImage(Request $request)
    {
        $request->validate(['image_path' => 'required|string']);

        $user = Auth::user();
        $pathToDelete = $request->input('image_path');

        $gallery = $user->gallery_images ?? [];

        // Agar photo gallery mein hai
        if (in_array($pathToDelete, $gallery)) {
            // 1. File ko storage se delete karein
            Storage::disk('public')->delete($pathToDelete);

            // 2. Array se photo ka path hatayein
            $newGallery = array_filter($gallery, function($path) use ($pathToDelete) {
                return $path !== $pathToDelete;
            });

            // 3. Database update karein
            $user->gallery_images = array_values($newGallery); // Re-index the array
            $user->save();

            return redirect()->route('dashboard')->with('success', 'Image deleted from gallery.');
        }

        return redirect()->route('dashboard')->with('error', 'Image not found.');
    }

    // ... (aapka unlockLead method yahan aayega) ...
    public function unlockLead(Request $request, Inquiry $inquiry)
    {
        if ($inquiry->recipient_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        Subscription::create([
            'user_id' => auth()->id(),
            'plan_type' => 'single_lead',
            'expires_at' => now()->addMinutes(5)
        ]);
        $inquiry->status = 'viewed';
        $inquiry->save();
        return redirect()->route('dashboard')->with('success', 'Lead Unlocked! You can now see the contact details.');
    }
}
