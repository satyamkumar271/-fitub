{{-- Path: resources/views/admin/dashboard.blade.php --}}

@extends('admin.layouts.app')  {{-- Yeh admin ke layout ko istemaal karega --}}

@section('content')
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Admin Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users Card -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-gray-500 text-sm font-semibold">Total Users</h3>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalUsers ?? 0 }}</p>
        </div>
        <!-- Customers Card -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-gray-500 text-sm font-semibold">Customers</h3>
            <p class="text-3xl font-bold text-blue-500 mt-2">{{ $customerCount ?? 0 }}</p>
        </div>
        <!-- Trainers Card -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-gray-500 text-sm font-semibold">Trainers</h3>
            <p class="text-3xl font-bold text-green-500 mt-2">{{ $trainerCount ?? 0 }}</p>
        </div>
        <!-- Gym Owners Card -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-gray-500 text-sm font-semibold">Gym Owners</h3>
            <p class="text-3xl font-bold text-orange-500 mt-2">{{ $gymOwnerCount ?? 0 }}</p>
        </div>
    </div>

    <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold">Recent Activities</h2>
        <p class="mt-4 text-gray-600">Activity log will be shown here...</p>
    </div>
@endsection
