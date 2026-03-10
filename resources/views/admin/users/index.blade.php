{{--
    IMPORTANT: In your Admin/UserController, make sure you are fetching all user data.
    The `$user` object passed to this view should have all columns from the database.
    Example Controller method:
    public function index()
    {
        $users = User::with('payments')->latest()->paginate(12); // Using paginate is good practice
        // ... gather stats ...
        return view('admin.users.index', compact('users', 'stats'));
    }
--}}

@extends('admin.layouts.app')

@section('content')
<div x-data="{
    userModalOpen: false,
    selectedUser: null,
    filter: 'all',
    openUserModal(user) {
        this.selectedUser = user;
        this.userModalOpen = true;
    }
}">

    <h1 class="text-4xl font-bold text-gray-800 mb-6">User Management Dashboard</h1>

    {{-- === 1. STATS CARDS === --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-blue-500 text-white p-6 rounded-2xl shadow-lg"><p class="text-sm">Total Users</p><p class="text-3xl font-bold">{{ $stats['totalUsers'] }}</p></div>
        <div class="bg-green-500 text-white p-6 rounded-2xl shadow-lg"><p class="text-sm">Total Trainers</p><p class="text-3xl font-bold">{{ $stats['totalTrainers'] }}</p></div>
        <div class="bg-orange-500 text-white p-6 rounded-2xl shadow-lg"><p class="text-sm">Total Gyms</p><p class="text-3xl font-bold">{{ $stats['totalGyms'] }}</p></div>
        <div class="bg-indigo-500 text-white p-6 rounded-2xl shadow-lg"><p class="text-sm">Total Revenue</p><p class="text-3xl font-bold">₹{{ number_format($stats['totalRevenue'], 0) }}</p></div>
    </div>


    <!-- 2. Filter & Controls -->
    <div class="flex justify-between items-center mb-6 bg-white p-4 rounded-lg shadow-sm">
        <div class="flex space-x-2 bg-gray-200 p-1 rounded-lg">
            <button @click="filter = 'all'" :class="{ 'bg-indigo-600 text-white': filter === 'all' }" class="px-4 py-1.5 rounded-md text-sm font-semibold transition-colors">All</button>
            <button @click="filter = 'customer'" :class="{ 'bg-indigo-600 text-white': filter === 'customer' }" class="px-4 py-1.5 rounded-md text-sm font-semibold transition-colors">Customers</button>
            <button @click="filter = 'trainer'" :class="{ 'bg-indigo-600 text-white': filter === 'trainer' }" class="px-4 py-1.5 rounded-md text-sm font-semibold transition-colors">Trainers</button>
            <button @click="filter = 'gymowner'" :class="{ 'bg-indigo-600 text-white': filter === 'gymowner' }" class="px-4 py-1.5 rounded-md text-sm font-semibold transition-colors">Gym Owners</button>
        </div>
    </div>

    @if (session('success')) <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-r-lg" role="alert"><p>{{ session('success') }}</p></div> @endif
    @if (session('error')) <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-r-lg" role="alert"><p>{{ session('error') }}</p></div> @endif

    <!-- 3. User Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($users as $user)
            <div x-show="filter === 'all' || filter === '{{ $user->user_type }}'" x-transition class="relative bg-white rounded-lg shadow-lg overflow-hidden flex flex-col">
                <div class="p-5 flex-grow">
                    @if($user->is_featured)
                        <div class="absolute top-2 right-2" title="This user is featured">
                            <span class="text-yellow-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                            </span>
                        </div>
                    @endif
                    <div class="flex items-center space-x-4">
                        <img class="h-14 w-14 rounded-full object-cover" src="{{ $user->profile_photo_path ? Storage::url($user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random&color=fff' }}" alt="Profile Photo">
                        <div>
                            <p class="text-lg font-bold text-gray-900 truncate">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-between items-center text-xs">
                        <span class="capitalize inline-block px-3 py-1 font-bold rounded-full @if($user->user_type == 'customer') bg-blue-100 text-blue-800 @elseif($user->user_type == 'trainer') bg-green-100 text-green-800 @else bg-orange-100 text-orange-800 @endif">
                            {{ $user->user_type }}
                        </span>
                        <span class="text-gray-500 font-semibold">Spent: ₹{{ $user->payments->where('status', 'paid')->sum('amount') }}</span>
                    </div>
                </div>
                <div class="border-t border-gray-200 p-3 bg-gray-50 flex justify-end space-x-3">
                    <button @click="openUserModal({{ $user->load('payments') }})" class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold">View Details</button>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12"><p class="text-gray-500">No users found.</p></div>
        @endforelse
    </div>

    <div class="mt-8">{{ $users->links() }}</div>

    {{-- === 4. POWERFUL USER DETAILS MODAL (UPDATED) === --}}
    <div x-show="userModalOpen" x-cloak class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4">
        <div @click.away="userModalOpen = false" class="bg-white rounded-2xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-hidden flex flex-col" x-transition>

            <div class="flex justify-between items-center p-5 border-b bg-gray-50">
                <h2 class="text-xl font-bold text-gray-800" x-text="selectedUser ? selectedUser.name : ''"></h2>
                <button @click="userModalOpen = false" class="text-gray-400 hover:text-gray-600 text-3xl">×</button>
            </div>

            <div class="p-6 overflow-y-auto space-y-6" x-show="selectedUser">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- User Details Section --}}
                    <div class="space-y-4 text-sm">
                        <h3 class="font-bold text-lg text-gray-700 border-b pb-2">User Details</h3>

                        {{-- Common Details --}}
                        <p><strong>Email:</strong> <span x-text="selectedUser.email || 'N/A'"></span></p>
                        <p><strong>Role:</strong> <span class="capitalize" x-text="selectedUser.user_type || 'N/A'"></span></p>
                        <p><strong>Registered:</strong> <span x-text="new Date(selectedUser.created_at).toLocaleDateString()"></span></p>
                        <p><strong>Age:</strong> <span x-text="selectedUser.age || 'N/A'"></span></p>

                        {{-- Customer Specific Details --}}
                        <div x-show="selectedUser.user_type === 'customer'" class="border-t pt-4 mt-4 space-y-2">
                             <h4 class="font-semibold text-gray-600">Customer Profile</h4>
                             <p><strong>Phone:</strong> <span x-text="selectedUser.phone_number || 'N/A'"></span></p>
                             <p><strong>Location:</strong> <span x-text="(selectedUser.customer_city || '') + (selectedUser.customer_state ? ', ' + selectedUser.customer_state : '') || 'N/A'"></span></p>
                             <p><strong>Weight:</strong> <span x-text="selectedUser.weight ? selectedUser.weight + ' kg' : 'N/A'"></span></p>
                             <p><strong>Height:</strong> <span x-text="selectedUser.height ? selectedUser.height + ' cm' : 'N/A'"></span></p>
                             <p><strong>Goal:</strong> <span class="block text-gray-600 whitespace-pre-wrap" x-text="selectedUser.goal || 'N/A'"></span></p>
                        </div>

                        {{-- Trainer Specific Details --}}
                        <div x-show="selectedUser.user_type === 'trainer'" class="border-t pt-4 mt-4 space-y-2">
                             <h4 class="font-semibold text-gray-600">Trainer Profile</h4>
                             <p><strong>Phone:</strong> <span x-text="selectedUser.trainer_phone_number || 'N/A'"></span></p>
                             <p><strong>Location:</strong> <span x-text="(selectedUser.trainer_city || '') + (selectedUser.trainer_state ? ', ' + selectedUser.trainer_state : '') || 'N/A'"></span></p>
                             <p><strong>Specialization:</strong> <span x-text="selectedUser.specialization || 'N/A'"></span></p>
                             <p><strong>Experience:</strong> <span x-text="selectedUser.experience ? selectedUser.experience + ' years' : 'N/A'"></span></p>
                             <p><strong>Website:</strong> <a :href="selectedUser.trainer_website_url" target="_blank" class="text-indigo-600 hover:underline" x-text="selectedUser.trainer_website_url || 'N/A'"></a></p>
                             <p><strong>Certifications:</strong> <span class="block text-gray-600 whitespace-pre-wrap" x-text="selectedUser.certifications || 'N/A'"></span></p>
                        </div>

                        {{-- Gym Owner Specific Details --}}
                        <div x-show="selectedUser.user_type === 'gymowner'" class="border-t pt-4 mt-4 space-y-2">
                            <h4 class="font-semibold text-gray-600">Gym Profile</h4>
                            <p><strong>Gym Name:</strong> <span class="font-bold" x-text="selectedUser.gym_name || 'N/A'"></span></p>
                            <p><strong>Gym Phone:</strong> <span x-text="selectedUser.gym_phone_number || 'N/A'"></span></p>
                            <p><strong>Gym Email:</strong> <span x-text="selectedUser.gym_email || 'N/A'"></span></p>
                            <p><strong>Address:</strong> <span x-text="`${selectedUser.address_street || ''}, ${selectedUser.address_city || ''}, ${selectedUser.address_state || ''} - ${selectedUser.address_pincode || ''}`.replace(/^, |, $/g, '').replace(/^- | -$/g, '') || 'N/A'"></span></p>
                            <p><strong>Established:</strong> <span x-text="selectedUser.gym_age || 'N/A'"></span></p>
                            <p><strong>Total Members:</strong> <span x-text="selectedUser.total_members || 'N/A'"></span></p>
                            <p><strong>Website:</strong> <a :href="selectedUser.gym_website_url" target="_blank" class="text-indigo-600 hover:underline" x-text="selectedUser.gym_website_url || 'N/A'"></a></p>
                            <p><strong>Social Links:</strong> <span class="block text-gray-600 whitespace-pre-wrap" x-text="selectedUser.social_links || 'N/A'"></span></p>
                        </div>
                    </div>

                    {{-- Admin Actions Section --}}
                    <div class="space-y-4">
                        <h3 class="font-bold text-lg text-gray-700 border-b pb-2">Admin Actions</h3>

                        {{-- Feature/Unfeature User Action --}}
                        {{-- NOTE: This requires you to create these routes in web.php and controller methods --}}
                        {{-- Route::post('/admin/users/{user}/feature', [UserController::class, 'feature'])->name('admin.users.feature'); --}}
                        {{-- Route::post('/admin/users/{user}/unfeature', [UserController::class, 'unfeature'])->name('admin.users.unfeature'); --}}

                        {{-- Delete User Action --}}
                        <div class="bg-red-50 border border-red-200 p-4 rounded-lg">
                             <form :action="`/admin/users/${selectedUser.id}`" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this user? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-semibold text-red-800">Delete User</p>
                                        <p class="text-xs text-red-600">This action is permanent.</p>
                                    </div>
                                    <button type="submit" class="bg-red-600 text-white text-sm font-semibold rounded-lg px-4 py-2 hover:bg-red-700">Delete</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Payment History Section --}}
                <div>
                    <h3 class="font-bold text-lg text-gray-700 border-b pb-2 mt-6">Payment History</h3>
                    <template x-if="selectedUser.payments && selectedUser.payments.length > 0">
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-100"><tr><th class="p-2 text-left">Date</th><th class="p-2 text-left">Plan</th><th class="p-2 text-left">Amount</th><th class="p-2 text-left">Status</th><th class="p-2 text-left">Payment ID</th></tr></thead>
                                <tbody>
                                    <template x-for="payment in selectedUser.payments" :key="payment.id">
                                        <tr class="border-b"><td class="p-2" x-text="new Date(payment.created_at).toLocaleDateString()"></td><td class="p-2 capitalize" x-text="payment.plan_name"></td><td class="p-2" x-text="`₹${payment.amount}`"></td><td class="p-2"><span class="px-2 py-1 text-xs rounded-full" :class="{'bg-green-100 text-green-800': payment.status === 'paid', 'bg-red-100 text-red-800': payment.status === 'failed'}" x-text="payment.status"></span></td><td class="p-2 text-gray-400" x-text="payment.razorpay_payment_id || 'N/A'"></td></tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                    <template x-if="!selectedUser.payments || selectedUser.payments.length === 0">
                        <div class="text-center text-gray-500 p-6 bg-gray-50 rounded-lg mt-4">This user has not made any payments.</div>
                    </template>
                </div>

            </div>
            <div class="p-4 bg-gray-50 border-t text-right"><button @click="userModalOpen = false" class="bg-gray-600 text-white px-5 py-2 rounded-lg hover:bg-gray-700">Close</button></div>
        </div>
    </div>
</div>
@endsection
