<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Customer;
use App\Models\Trainer;
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
            ->whereDoesntHave('blocks', function ($q) use ($user) {
                $q->where('active', true)
                  ->where(function ($sub) use ($user) {
                      $sub->where('blocker_id', $user->id)
                          ->orWhere('blocked_user_id', $user->id);
                  });
            })
            ->whereDoesntHave('reports', function ($q) use ($user) {
                $q->whereIn('status', ['open', 'under_review'])
                  ->where(function ($sub) use ($user) {
                      $sub->where('reporter_id', $user->id)
                          ->orWhere('reported_user_id', $user->id);
                  });
            })
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
        $isProfileIncompleteBusiness = in_array($user->user_type, ['trainer', 'gymowner'], true)
            && $user->status === 'profile_incomplete';

        $baseRules = [
            'name' => ['required', 'string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];

        if ($user->user_type === 'customer') {
            $data = $request->validate(array_merge($baseRules, [
                'age' => ['nullable', 'integer', 'min:10', 'max:100'],
                'phone_number' => ['nullable', 'regex:/^[0-9+\-\s()]{7,20}$/'],
                'weight' => ['nullable', 'numeric', 'min:20', 'max:400'],
                'height' => ['nullable', 'numeric', 'min:80', 'max:260'],
                'goal' => ['nullable', 'string', 'max:500'],
                'city' => ['nullable', 'string', 'max:100'],
                'state' => ['nullable', 'string', 'max:100'],
            ]));

            $profileData = ['name' => $data['name']];
            if ($request->hasFile('profile_photo')) {
                if ($user->profile_photo_path) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }
                $profileData['profile_photo_path'] = $request->file('profile_photo')->store('profile_photos', 'public');
            }
            $user->update($profileData);

            $customer = $user->customer ?: Customer::create(['user_id' => $user->id]);
            $customer->update([
                'age' => $data['age'] ?? null,
                'phone_number' => $data['phone_number'] ?? null,
                'weight' => $data['weight'] ?? null,
                'height' => $data['height'] ?? null,
                'goal' => $data['goal'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
            ]);

            return redirect()->route('dashboard')->with('success', 'Profile updated successfully!');
        }

        if ($user->user_type === 'trainer') {
            $data = $request->validate(array_merge($baseRules, [
                'trainer_phone_number' => [Rule::requiredIf($isProfileIncompleteBusiness), 'nullable', 'regex:/^[0-9+\-\s()]{7,20}$/'],
                'trainer_city' => [Rule::requiredIf($isProfileIncompleteBusiness), 'nullable', 'string', 'max:100'],
                'trainer_state' => [Rule::requiredIf($isProfileIncompleteBusiness), 'nullable', 'string', 'max:100'],
                'trainer_website_url' => ['nullable', 'url', 'max:255'],
                'specialization' => [Rule::requiredIf($isProfileIncompleteBusiness), 'nullable', 'string', 'max:150'],
                'experience' => [Rule::requiredIf($isProfileIncompleteBusiness), 'nullable', 'integer', 'min:0', 'max:60'],
                'about_trainer' => ['nullable', 'string', 'max:3000'],
                'id_proof' => [Rule::requiredIf($isProfileIncompleteBusiness && empty($user->id_proof_path)), 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
                'certificate_proofs' => [Rule::requiredIf($isProfileIncompleteBusiness), 'nullable', 'array', 'max:5'],
                'certificate_proofs.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:3072'],
            ]));

            $userData = ['name' => $data['name']];
            if ($request->hasFile('profile_photo')) {
                if ($user->profile_photo_path) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }
                $userData['profile_photo_path'] = $request->file('profile_photo')->store('profile_photos', 'public');
            }
            if ($request->hasFile('id_proof')) {
                if ($user->id_proof_path) {
                    Storage::disk('public')->delete($user->id_proof_path);
                }
                $userData['id_proof_path'] = $request->file('id_proof')->store('id_proofs', 'public');
            }
            $user->update($userData);

            $trainer = $user->trainer ?: Trainer::create(['user_id' => $user->id]);

            $certificatePaths = $trainer->certificate_proof_paths ?? [];
            if ($request->hasFile('certificate_proofs')) {
                $certificatePaths = [];
                foreach ($request->file('certificate_proofs') as $file) {
                    $certificatePaths[] = $file->store('trainer_certificate_proofs', 'public');
                }
            }

            $trainer->update([
                'phone_number' => $data['trainer_phone_number'] ?? $trainer->phone_number,
                'city' => $data['trainer_city'] ?? $trainer->city,
                'state' => $data['trainer_state'] ?? $trainer->state,
                'website_url' => $data['trainer_website_url'] ?? null,
                'specialization' => $data['specialization'] ?? $trainer->specialization,
                'experience' => $data['experience'] ?? $trainer->experience,
                'about' => $data['about_trainer'] ?? $trainer->about,
                'certificate_proof_paths' => $certificatePaths,
            ]);

            if ($isProfileIncompleteBusiness) {
                $user->update([
                    'status' => 'pending',
                    'kyc_status' => 'pending',
                    'is_verified' => false,
                ]);
                return redirect()->route('dashboard')->with('success', 'Profile submitted successfully. Your account is now under review.');
            }

            return redirect()->route('dashboard')->with('success', 'Profile updated successfully!');
        }

        if ($user->user_type === 'gymowner') {
            $data = $request->validate(array_merge($baseRules, [
                'gym_name' => [Rule::requiredIf($isProfileIncompleteBusiness), 'nullable', 'string', 'max:255'],
                'gym_phone_number' => [Rule::requiredIf($isProfileIncompleteBusiness), 'nullable', 'regex:/^[0-9+\-\s()]{7,20}$/'],
                'gym_email' => [Rule::requiredIf($isProfileIncompleteBusiness), 'nullable', 'email', 'max:255'],
                'gym_website_url' => ['nullable', 'url', 'max:255'],
                'gst_number' => ['nullable', 'string', 'max:20'],
                'address_street' => [Rule::requiredIf($isProfileIncompleteBusiness), 'nullable', 'string', 'max:255'],
                'address_city' => [Rule::requiredIf($isProfileIncompleteBusiness), 'nullable', 'string', 'max:100'],
                'address_state' => [Rule::requiredIf($isProfileIncompleteBusiness), 'nullable', 'string', 'max:100'],
                'address_pincode' => [Rule::requiredIf($isProfileIncompleteBusiness), 'nullable', 'regex:/^[0-9]{4,10}$/'],
                'gym_age' => ['nullable', 'integer', 'min:1900', 'max:' . date('Y')],
                'total_members' => ['nullable', 'integer', 'min:0', 'max:100000'],
                'about_gym' => ['nullable', 'string', 'max:3000'],
                'id_proof' => [Rule::requiredIf($isProfileIncompleteBusiness && empty($user->id_proof_path)), 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
                'business_doc' => [Rule::requiredIf($isProfileIncompleteBusiness), 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:3072'],
            ]));

            $userData = ['name' => $data['name']];
            if ($request->hasFile('profile_photo')) {
                if ($user->profile_photo_path) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }
                $userData['profile_photo_path'] = $request->file('profile_photo')->store('profile_photos', 'public');
            }
            if ($request->hasFile('id_proof')) {
                if ($user->id_proof_path) {
                    Storage::disk('public')->delete($user->id_proof_path);
                }
                $userData['id_proof_path'] = $request->file('id_proof')->store('id_proofs', 'public');
            }
            $user->update($userData);

            $gym = $user->gym ?: Gym::create(['user_id' => $user->id]);
            $businessDocPath = $gym->business_doc_path;
            if ($request->hasFile('business_doc')) {
                if ($businessDocPath) {
                    Storage::disk('public')->delete($businessDocPath);
                }
                $businessDocPath = $request->file('business_doc')->store('business_docs', 'public');
            }

            $gym->update([
                'gym_name' => $data['gym_name'] ?? $gym->gym_name,
                'gym_phone_number' => $data['gym_phone_number'] ?? $gym->gym_phone_number,
                'gym_email' => $data['gym_email'] ?? $gym->gym_email,
                'gym_website_url' => $data['gym_website_url'] ?? null,
                'gst_number' => $data['gst_number'] ?? $gym->gst_number,
                'business_doc_path' => $businessDocPath,
                'address_street' => $data['address_street'] ?? $gym->address_street,
                'address_city' => $data['address_city'] ?? $gym->address_city,
                'address_state' => $data['address_state'] ?? $gym->address_state,
                'address_pincode' => $data['address_pincode'] ?? $gym->address_pincode,
                'gym_age' => $data['gym_age'] ?? null,
                'total_members' => $data['total_members'] ?? null,
                'about' => $data['about_gym'] ?? $gym->about,
            ]);

            if ($isProfileIncompleteBusiness) {
                $user->update([
                    'status' => 'pending',
                    'kyc_status' => 'pending',
                    'is_verified' => false,
                ]);
                return redirect()->route('dashboard')->with('success', 'Profile submitted successfully. Your account is now under review.');
            }

            return redirect()->route('dashboard')->with('success', 'Profile updated successfully!');
        }

        return redirect()->route('dashboard');
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

    private function normalizeLineList(string $raw): array
    {
        return collect(preg_split('/\r\n|\r|\n/', $raw))
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->values()
            ->all();
    }
}
