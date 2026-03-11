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

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
<div class="bg-blue-500 text-white p-6 rounded-2xl shadow-lg"><p class="text-sm">Total Users</p><p class="text-3xl font-bold">{{ $stats['totalUsers'] }}</p></div>
<div class="bg-green-500 text-white p-6 rounded-2xl shadow-lg"><p class="text-sm">Total Trainers</p><p class="text-3xl font-bold">{{ $stats['totalTrainers'] }}</p></div>
<div class="bg-orange-500 text-white p-6 rounded-2xl shadow-lg"><p class="text-sm">Total Gyms</p><p class="text-3xl font-bold">{{ $stats['totalGyms'] }}</p></div>
<div class="bg-indigo-500 text-white p-6 rounded-2xl shadow-lg"><p class="text-sm">Total Revenue</p><p class="text-3xl font-bold">₹{{ number_format($stats['totalRevenue'], 0) }}</p></div>
</div>

<div class="flex justify-between items-center mb-6 bg-white p-4 rounded-lg shadow-sm">
<div class="flex space-x-2 bg-gray-200 p-1 rounded-lg">
<button @click="filter = 'all'" :class="{ 'bg-indigo-600 text-white': filter === 'all' }" class="px-4 py-1.5 rounded-md text-sm font-semibold">All</button>
<button @click="filter = 'customer'" :class="{ 'bg-indigo-600 text-white': filter === 'customer' }" class="px-4 py-1.5 rounded-md text-sm font-semibold">Customers</button>
<button @click="filter = 'trainer'" :class="{ 'bg-indigo-600 text-white': filter === 'trainer' }" class="px-4 py-1.5 rounded-md text-sm font-semibold">Trainers</button>
<button @click="filter = 'gymowner'" :class="{ 'bg-indigo-600 text-white': filter === 'gymowner' }" class="px-4 py-1.5 rounded-md text-sm font-semibold">Gym Owners</button>
</div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

@forelse ($users as $user)

<div x-show="filter === 'all' || filter === '{{ $user->user_type }}'" class="relative bg-white rounded-lg shadow-lg overflow-hidden flex flex-col">

<div class="p-5 flex-grow">

<div class="flex items-center space-x-4">
<img class="h-14 w-14 rounded-full object-cover"
src="{{ $user->profile_photo_path ? Storage::url($user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random&color=fff' }}">
<div>
<p class="text-lg font-bold text-gray-900 truncate">{{ $user->name }}</p>
<p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
</div>
</div>

<div class="mt-4 flex justify-between items-center text-xs">
<span class="capitalize inline-block px-3 py-1 font-bold rounded-full
@if($user->user_type == 'customer') bg-blue-100 text-blue-800
@elseif($user->user_type == 'trainer') bg-green-100 text-green-800
@else bg-orange-100 text-orange-800
@endif">
{{ $user->user_type }}
</span>

<span class="text-gray-500 font-semibold">
Spent: ₹{{ $user->payments->where('status','paid')->sum('amount') }}
</span>
</div>

</div>

<div class="border-t border-gray-200 p-3 bg-gray-50 flex justify-end">
<button @click="openUserModal({{ $user->load(['payments','trainer','gym','customer']) }})"
class="text-sm text-indigo-600 font-semibold">View Details</button>
</div>

</div>

@empty

<div class="col-span-full text-center py-12">
<p class="text-gray-500">No users found.</p>
</div>

@endforelse

</div>

<div class="mt-8">{{ $users->links() }}</div>

{{-- MODAL --}}

<div x-show="userModalOpen" x-cloak class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4">

<div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-hidden flex flex-col">

<div class="flex justify-between items-center p-5 border-b bg-gray-50">
<h2 class="text-xl font-bold" x-text="selectedUser?.name"></h2>
<button @click="userModalOpen = false" class="text-3xl">×</button>
</div>

<div class="p-6 overflow-y-auto space-y-6">

<div class="space-y-4 text-sm">

<p><strong>Email:</strong> <span x-text="selectedUser?.email"></span></p>
<p><strong>Role:</strong> <span x-text="selectedUser?.user_type"></span></p>

{{-- CUSTOMER DATA --}}

<div x-show="selectedUser?.user_type === 'customer'">

<p><strong>Phone:</strong>
<span x-text="selectedUser.customer?.phone_number || 'N/A'"></span>
</p>

<p><strong>Location:</strong>
<span x-text="selectedUser.customer ?
(selectedUser.customer.city + ', ' + selectedUser.customer.state) : 'N/A'"></span>
</p>

<p><strong>Weight:</strong>
<span x-text="selectedUser.customer?.weight || 'N/A'"></span>
</p>

<p><strong>Height:</strong>
<span x-text="selectedUser.customer?.height || 'N/A'"></span>
</p>

<p><strong>Goal:</strong>
<span x-text="selectedUser.customer?.goal || 'N/A'"></span>
</p>

</div>

{{-- TRAINER DATA --}}

<div x-show="selectedUser?.user_type === 'trainer'">

<p><strong>Phone:</strong>
<span x-text="selectedUser.trainer?.phone_number || 'N/A'"></span>
</p>

<p><strong>Location:</strong>
<span x-text="selectedUser.trainer ?
(selectedUser.trainer.city + ', ' + selectedUser.trainer.state) : 'N/A'"></span>
</p>

<p><strong>Specialization:</strong>
<span x-text="selectedUser.trainer?.specialization || 'N/A'"></span>
</p>

<p><strong>Experience:</strong>
<span x-text="selectedUser.trainer?.experience || 'N/A'"></span>
</p>

</div>

{{-- GYM DATA --}}

<div x-show="selectedUser?.user_type === 'gymowner'">

<p><strong>Gym Name:</strong>
<span x-text="selectedUser.gym?.gym_name || 'N/A'"></span>
</p>

<p><strong>Location:</strong>
<span x-text="selectedUser.gym ?
(selectedUser.gym.address_city + ', ' + selectedUser.gym.address_state) : 'N/A'"></span>
</p>

<p><strong>Total Members:</strong>
<span x-text="selectedUser.gym?.total_members || 'N/A'"></span>
</p>

</div>

</div>

</div>

</div>

</div>

</div>
@endsection