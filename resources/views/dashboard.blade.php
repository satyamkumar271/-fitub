@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4">
    @php
        $currentUser = Auth::user();
        $customerProfile = $currentUser->customer;
        $trainerProfile = $currentUser->trainer;
        $gymProfile = $currentUser->gym;
        $isBusinessProfileIncomplete = in_array($currentUser->user_type, ['trainer', 'gymowner']) && $currentUser->status === 'profile_incomplete';
    @endphp

    <h1 class="text-3xl font-bold text-gray-800">Welcome, {{ Auth::user()->name }}</h1>
    <p class="text-gray-600 mb-8">This is your dashboard. Manage your profile and connect with the community.</p>

    {{-- Flash Messages --}}
    @if (session('success'))<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert"><p>{{ session('success') }}</p></div>@endif
    @if (session('error'))<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg" role="alert"><p>{{ session('error') }}</p></div>@endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- === LEFT COLUMN: PROFILE & GALLERY MANAGEMENT === --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- 1. PROFILE MANAGEMENT (Yeh sabke liye hai) --}}
            <div class="bg-white p-6 rounded-2xl shadow-xl">
                <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-4">Manage Profile</h2>
                @if($isBusinessProfileIncomplete)
                    <div class="mb-4 bg-amber-100 border-l-4 border-amber-500 text-amber-800 p-4 rounded-lg">
                        Please complete all required details and upload documents. After submit, your account will move to review.
                    </div>
                @endif
                <form action="{{ route('dashboard.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1 flex flex-col items-center">
                            <img class="h-32 w-32 rounded-full object-cover shadow-lg mb-4" src="{{ $currentUser->profile_photo_path ? Storage::url($currentUser->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($currentUser->name) . '&size=128' }}" alt="Profile Photo">
                            <label for="profile_photo" class="cursor-pointer bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg text-sm">Change Photo</label>
                            <input id="profile_photo" name="profile_photo" type="file" class="hidden">
                            <p class="text-xs text-gray-500 mt-2">JPG, PNG up to 2MB</p>
                        </div>

                        <div class="md:col-span-2 space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Display Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $currentUser->name) }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                            </div>

                            @if($currentUser->user_type === 'customer')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Age</label>
                                        <input type="number" name="age" value="{{ old('age', $customerProfile?->age) }}" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                                        <input type="text" name="phone_number" value="{{ old('phone_number', $customerProfile?->phone_number) }}" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Weight (kg)</label>
                                        <input type="number" name="weight" value="{{ old('weight', $customerProfile?->weight) }}" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Height (cm)</label>
                                        <input type="number" name="height" value="{{ old('height', $customerProfile?->height) }}" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">City</label>
                                        <input type="text" name="city" value="{{ old('city', $customerProfile?->city) }}" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">State</label>
                                        <input type="text" name="state" value="{{ old('state', $customerProfile?->state) }}" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Goal</label>
                                    <textarea name="goal" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">{{ old('goal', $customerProfile?->goal) }}</textarea>
                                </div>
                            @elseif($currentUser->user_type === 'trainer')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Phone *</label>
                                        <input type="text" name="trainer_phone_number" value="{{ old('trainer_phone_number', $trainerProfile?->phone_number) }}" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Website</label>
                                        <input type="text" name="trainer_website_url" value="{{ old('trainer_website_url', $trainerProfile?->website_url) }}" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">City *</label>
                                        <input type="text" name="trainer_city" value="{{ old('trainer_city', $trainerProfile?->city) }}" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">State *</label>
                                        <input type="text" name="trainer_state" value="{{ old('trainer_state', $trainerProfile?->state) }}" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Specialization *</label>
                                        <input type="text" name="specialization" value="{{ old('specialization', $trainerProfile?->specialization) }}" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Experience (years) *</label>
                                        <input type="number" name="experience" value="{{ old('experience', $trainerProfile?->experience) }}" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Certifications (one per line)</label>
                                    <textarea name="certifications_text" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">{{ old('certifications_text', is_array($trainerProfile?->certifications) ? implode("\n", $trainerProfile->certifications) : '') }}</textarea>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">ID Proof {{ $isBusinessProfileIncomplete ? '*' : '' }}</label>
                                        <input type="file" name="id_proof" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Certificate Proof Files {{ $isBusinessProfileIncomplete ? '*' : '' }}</label>
                                        <input type="file" name="certificate_proofs[]" multiple class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                </div>
                            @elseif($currentUser->user_type === 'gymowner')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Gym Name *</label>
                                        <input type="text" name="gym_name" value="{{ old('gym_name', $gymProfile?->gym_name) }}" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Gym Phone *</label>
                                        <input type="text" name="gym_phone_number" value="{{ old('gym_phone_number', $gymProfile?->gym_phone_number) }}" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Gym Email *</label>
                                        <input type="email" name="gym_email" value="{{ old('gym_email', $gymProfile?->gym_email) }}" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Website</label>
                                        <input type="text" name="gym_website_url" value="{{ old('gym_website_url', $gymProfile?->gym_website_url) }}" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Street Address *</label>
                                        <input type="text" name="address_street" value="{{ old('address_street', $gymProfile?->address_street) }}" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">City *</label>
                                        <input type="text" name="address_city" value="{{ old('address_city', $gymProfile?->address_city) }}" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">State *</label>
                                        <input type="text" name="address_state" value="{{ old('address_state', $gymProfile?->address_state) }}" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Pincode *</label>
                                        <input type="text" name="address_pincode" value="{{ old('address_pincode', $gymProfile?->address_pincode) }}" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Gym Age (year)</label>
                                        <input type="number" name="gym_age" value="{{ old('gym_age', $gymProfile?->gym_age) }}" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Total Members</label>
                                        <input type="number" name="total_members" value="{{ old('total_members', $gymProfile?->total_members) }}" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">ID Proof {{ $isBusinessProfileIncomplete ? '*' : '' }}</label>
                                        <input type="file" name="id_proof" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Business Document {{ $isBusinessProfileIncomplete ? '*' : '' }}</label>
                                        <input type="file" name="business_doc" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="text-right mt-6">
                        <button type="submit" class="bg-indigo-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-indigo-700">
                            {{ $isBusinessProfileIncomplete ? 'Submit for Review' : 'Save Profile' }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- ======================================================== --}}
            {{-- === GALLERY SECTION (SIRF TRAINER/GYMOWNER KE LIYE) === --}}
            {{-- ======================================================== --}}
            @if(in_array(Auth::user()->user_type, ['trainer', 'gymowner']))
                <div class="bg-white p-6 rounded-2xl shadow-xl">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-4">
                        @if(Auth::user()->user_type == 'trainer')
                            Client Transformations Gallery
                        @else
                            Gym Photo Gallery
                        @endif
                    </h2>
                    <form action="{{ route('dashboard.gallery.update') }}" method="POST" enctype="multipart/form-data" class="bg-gray-50 p-4 rounded-lg border border-dashed">
                        @csrf
                        <label for="gallery-upload" class="block text-sm font-medium text-gray-700 mb-2">Upload new photos (you can select multiple):</label>
                        <div class="flex items-center space-x-4">
                            <input type="file" name="gallery[]" id="gallery-upload" multiple class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <button type="submit" class="bg-indigo-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-indigo-700 text-sm flex-shrink-0">Upload</button>
                        </div>
                    </form>
                    <div class="mt-6">
                        @if(!empty(Auth::user()->gallery_images))
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                @foreach(Auth::user()->gallery_images as $imagePath)
                                    <div class="relative group">
                                        <img src="{{ Storage::url($imagePath) }}" class="rounded-lg object-cover aspect-square">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all flex items-center justify-center">
                                            <form action="{{ route('dashboard.gallery.delete') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this image?');">
                                                @csrf
                                                <input type="hidden" name="image_path" value="{{ $imagePath }}">
                                                <button type="submit" class="text-white opacity-0 group-hover:opacity-100 transition-opacity p-2 bg-red-600 rounded-full">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-center text-gray-500 py-8">Your gallery is empty. Upload some photos to get started!</p>
                        @endif
                    </div>
                </div>
            @endif
            {{-- =================== END OF GALLERY SECTION =================== --}}

            @if(Auth::user()->user_type == 'gymowner')
                <div class="bg-white p-6 rounded-2xl shadow-xl">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-4">Lead Service Settings</h2>
                    <p class="text-sm text-gray-600 mb-4">
                        Yahan se decide karein ki public profile par Visit Booking option dikhna chahiye ya nahi.
                    </p>

                    <form action="{{ route('dashboard.gym-services.update') }}" method="POST" class="space-y-4">
                        @csrf
                        <label class="flex items-center gap-3">
                            <input type="checkbox" name="allow_visit_booking" value="1" class="h-4 w-4 text-indigo-600 rounded border-gray-300"
                                {{ old('allow_visit_booking', $gymProfile?->allow_visit_booking) ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-gray-700">Enable Visit Booking</span>
                        </label>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Public Note (optional)</label>
                            <input type="text" name="lead_services_note" value="{{ old('lead_services_note', $gymProfile?->lead_services_note) }}"
                                placeholder="Example: Visit booking slots: 10AM - 8PM"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                        </div>

                        <div class="text-right">
                            <button type="submit" class="bg-indigo-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-indigo-700">
                                Save Service Settings
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        {{-- === RIGHT COLUMN: CUSTOMER LEADS & SUBSCRIPTION === --}}
        <div class="lg:col-span-1">
             @if(in_array(Auth::user()->user_type, ['trainer', 'gymowner']))
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold text-gray-800">Customer Leads</h2>
                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg px-4 py-3">
                        <p class="text-sm text-indigo-800">
                            <strong>Unlock Credits:</strong> {{ $unlockCredits ?? 0 }}
                        </p>
                    </div>

                    {{-- Subscription Status Message --}}
                    @if($hasUnlimitedPlan && $activeUnlimitedSubscription)
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-r-lg">
                            <p class="font-bold">Your {{ ucfirst($activeUnlimitedSubscription->plan_type) }} plan is active!</p>
                            <p class="text-sm">Expires on: {{ \Carbon\Carbon::parse($activeUnlimitedSubscription->expires_at)->format('d M, Y') }}</p>
                        </div>
                    @elseif($leads->isNotEmpty() && !$hasUnlimitedPlan)
                         <div class="bg-gradient-to-r from-purple-500 to-indigo-600 text-white p-6 rounded-2xl shadow-lg">
                            <h3 class="font-bold text-xl">Unlock Leads & Grow Your Business!</h3>
                            <p class="opacity-90 mt-1 text-sm">You have new leads waiting. Choose a plan to view their details.</p>
                            <div class="mt-4 space-y-2">
                                <a href="{{ route('billing.plans') }}" class="block w-full text-center bg-white text-indigo-600 font-semibold px-5 py-2 rounded-lg hover:bg-gray-100 text-sm">View Plans</a>
                            </div>
                        </div>
                    @endif

                    {{-- Leads ki List --}}
                    @forelse($leads as $lead)
                        <div class="bg-white p-4 rounded-lg shadow-lg">
                            <p class="font-bold text-indigo-700">{{ $lead->service_needed }}</p>
                            <p class="text-xs text-gray-500 mb-2">Received: {{ $lead->created_at->format('d M, Y') }}</p>
                            <blockquote class="text-sm text-gray-600 border-l-4 border-gray-200 pl-3 mb-4 italic">"{{ Str::limit($lead->message, 100) }}"</blockquote>

                            @if($hasUnlimitedPlan || in_array((int) $lead->id, $unlockedLeadIds, true) || $lead->status == 'viewed')
                                <div class="text-sm bg-green-50 p-3 rounded-md w-full border border-green-200">
                                    <h4 class="font-bold text-green-800 mb-1">Contact Details Unlocked</h4>
                                    <p><strong>Name:</strong> {{ $lead->user->name ?? $lead->guest_name }}</p>
                                    <p><strong>Email:</strong> {{ $lead->user->email ?? $lead->guest_email }}</p>
                                    <p><strong>Phone:</strong> Hidden by platform policy</p>
                                    <a href="{{ route('inquiries.chat', $lead) }}"
                                       class="mt-3 inline-block bg-indigo-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-indigo-700 text-sm">
                                        Open Chat
                                    </a>
                                </div>
                            @else
                                <a href="{{ route('billing.plans', ['inquiry_id' => $lead->id]) }}"
                                   class="block w-full text-center bg-green-600 text-white font-semibold py-2 rounded-lg hover:bg-green-700 text-sm">
                                    Unlock for &#8377;99
                                </a>
                            @endif
                        </div>
                    @empty
                        <div class="text-center bg-white p-8 rounded-lg shadow-lg">
                            <p class="text-gray-600 font-semibold">No new leads yet.</p>
                            <p class="text-sm text-gray-500 mt-1">We'll notify you when a new inquiry arrives!</p>

                            @if(!$hasUnlimitedPlan)
                                <div class="mt-5">
                                    <a href="{{ route('billing.plans') }}"
                                       class="inline-block bg-indigo-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-indigo-700 text-sm">
                                        View Plans / Buy Subscription
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforelse

                    {{-- Payment History --}}
                    <div class="bg-white p-4 rounded-lg shadow-lg">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-lg font-bold text-gray-800">My Payments</h3>
                            <span class="text-xs text-gray-500">Latest 8</span>
                        </div>

                        @forelse($paymentHistory as $payment)
                            @php
                                $status = $payment->status;
                                $statusClass = 'bg-gray-100 text-gray-700';
                                if ($status === 'paid') $statusClass = 'bg-green-100 text-green-700';
                                elseif ($status === 'failed') $statusClass = 'bg-red-100 text-red-700';
                                elseif ($status === 'created') $statusClass = 'bg-amber-100 text-amber-700';
                                elseif ($status === 'cancelled') $statusClass = 'bg-slate-200 text-slate-700';
                            @endphp
                            <div class="py-2 border-b border-gray-100 last:border-0">
                                <div class="flex items-center justify-between">
                                    <p class="font-semibold text-sm text-gray-800">{{ ucfirst(str_replace('_', ' ', $payment->plan_name)) }}</p>
                                    <p class="text-sm font-bold text-gray-800">&#8377;{{ number_format($payment->amount, 0) }}</p>
                                </div>
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-xs text-gray-500">{{ $payment->created_at?->format('d M Y, h:i A') }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">{{ strtoupper($status) }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No payment history yet.</p>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

