<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Gym;
use App\Models\Inquiry;
use App\Models\Payment;
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
        $hasUnlimitedPlan = false;
        $activeUnlimitedSubscription = null;
        $unlockedLeadIds = [];
        $paymentHistory = collect();
        $unlockCredits = (int) ($user->unlock_credits ?? 0);

        if (in_array($user->user_type, ['trainer', 'gymowner'])) {
            $leadAccess = $this->getLeadAccessData($user->id);
            $hasUnlimitedPlan = $leadAccess['hasUnlimitedPlan'];
            $activeUnlimitedSubscription = $leadAccess['activeUnlimitedSubscription'];
            $unlockedLeadIds = $leadAccess['unlockedLeadIds'];

            $leads = Inquiry::where('recipient_id', $user->id)
                ->whereIn('status', ['forwarded', 'viewed'])
                ->with('user')->latest()->get();

            $paymentHistory = Payment::where('user_id', $user->id)
                ->latest()
                ->take(8)
                ->get();
        }

        return view('dashboard', compact(
            'user',
            'leads',
            'hasUnlimitedPlan',
            'activeUnlimitedSubscription',
            'unlockedLeadIds',
            'paymentHistory',
            'unlockCredits'
        ));
    }

    public function leads()
    {
        $user = auth()->user();
        $this->authorizeBusinessUser($user->user_type);

        $leadAccess = $this->getLeadAccessData($user->id);

        $leads = Inquiry::where('recipient_id', $user->id)
            ->whereIn('status', ['forwarded', 'viewed'])
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('dashboard.leads', [
            'user' => $user,
            'leads' => $leads,
            'hasUnlimitedPlan' => $leadAccess['hasUnlimitedPlan'],
            'activeUnlimitedSubscription' => $leadAccess['activeUnlimitedSubscription'],
            'unlockedLeadIds' => $leadAccess['unlockedLeadIds'],
            'unlockCredits' => (int) ($user->unlock_credits ?? 0),
        ]);
    }

    public function payments()
    {
        $user = auth()->user();
        $this->authorizeBusinessUser($user->user_type);

        $payments = Payment::where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        return view('dashboard.payments', [
            'user' => $user,
            'payments' => $payments,
        ]);
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

    public function updateGymLeadServices(Request $request)
    {
        $user = Auth::user();

        if ($user->user_type !== 'gymowner') {
            abort(403, 'Only gym owners can update this section.');
        }

        $data = $request->validate([
            'allow_visit_booking' => 'nullable|boolean',
            'lead_services_note' => 'nullable|string|max:255',
        ]);

        $gym = $user->gym;
        if (!$gym) {
            $gym = Gym::create(['user_id' => $user->id]);
        }

        $gym->allow_visit_booking = $request->boolean('allow_visit_booking');
        $gym->lead_services_note = $data['lead_services_note'] ?? null;
        $gym->save();

        return redirect()->route('dashboard')->with('success', 'Lead services updated successfully!');
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

        // If already unlocked, do nothing
        if ($inquiry->status === 'viewed') {
            return redirect()->route('dashboard');
        }

        // Redirect to billing plans with inquiry context (paid unlock)
        return redirect()->route('billing.plans', ['inquiry_id' => $inquiry->id]);
    }

    private function authorizeBusinessUser(string $userType): void
    {
        if (!in_array($userType, ['trainer', 'gymowner'], true)) {
            abort(403, 'Only trainers and gym owners can access this section.');
        }
    }

    private function getLeadAccessData(int $userId): array
    {
        $activeUnlimitedSubscription = Subscription::where('user_id', $userId)
            ->whereIn('plan_type', ['monthly', 'yearly'])
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        $hasUnlimitedPlan = (bool) $activeUnlimitedSubscription;
        $unlockedLeadIds = [];

        if (!$hasUnlimitedPlan) {
            $unlockedLeadIds = Payment::where('user_id', $userId)
                ->where('status', 'paid')
                ->where('plan_name', 'single_lead')
                ->where('context_type', 'lead_unlock')
                ->whereNotNull('context_id')
                ->pluck('context_id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();
        }

        return [
            'hasUnlimitedPlan' => $hasUnlimitedPlan,
            'activeUnlimitedSubscription' => $activeUnlimitedSubscription,
            'unlockedLeadIds' => $unlockedLeadIds,
        ];
    }
}
