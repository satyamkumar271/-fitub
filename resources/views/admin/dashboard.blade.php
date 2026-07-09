@extends('admin.layouts.app')

@section('content')

<div class="space-y-8">

    <!-- ===== Title ===== -->
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
        <p class="text-gray-500">Overview of platform activity</p>
    </div>

    <!-- ===== Top Summary ===== -->
    <!-- <div class="grid grid-cols-2 md:grid-cols-5 gap-4">

        <div class="bg-white p-4 rounded shadow text-center">
            <p class="text-xs text-gray-500">Users</p>
            <p class="font-bold text-lg">{{ $stats['totalUsers'] }}</p>
        </div>

        <div class="bg-white p-4 rounded shadow text-center">
            <p class="text-xs text-gray-500">Customers</p>
            <p class="font-bold text-lg">{{ $stats['totalCustomers'] }}</p>
        </div>

        <div class="bg-white p-4 rounded shadow text-center">
            <p class="text-xs text-gray-500">Trainers</p>
            <p class="font-bold text-lg">{{ $stats['totalTrainers'] }}</p>
        </div>

        <div class="bg-white p-4 rounded shadow text-center">
            <p class="text-xs text-gray-500">Gyms</p>
            <p class="font-bold text-lg">{{ $stats['totalGyms'] }}</p>
        </div>

        <div class="bg-white p-4 rounded shadow text-center">
            <p class="text-xs text-gray-500">Pending Users</p>
            <p class="font-bold text-lg text-red-500">{{ $stats['pendingUsers'] }}</p>
        </div>

    </div> -->

    <!-- ===== Insight Cards ===== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        <div class="bg-indigo-500 text-white p-6 rounded-xl shadow">
            <p>Total Revenue</p>
            <h2 class="text-2xl font-bold mt-2">₹{{ $stats['totalRevenue'] }}</h2>
        </div>

        <div class="bg-yellow-400 text-white p-6 rounded-xl shadow">
            <p>Pending KYC</p>
            <h2 class="text-2xl font-bold mt-2">{{ $quickStats['pendingKyc'] }}</h2>
        </div>

        <div class="bg-red-400 text-white p-6 rounded-xl shadow">
            <p>Pending Payments</p>
            <h2 class="text-2xl font-bold mt-2">{{ $quickStats['pendingInquiries'] }}</h2>
        </div>

        <div class="bg-green-400 text-white p-6 rounded-xl shadow">
            <p>Active Users (7d)</p>
            <h2 class="text-2xl font-bold mt-2">{{ $stats['activeUsers'] }}</h2>
        </div>

    </div>

  <!-- ===== Quick Actions (Clean & Consistent) ===== -->
<div>
    <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">

        <!-- KYC -->
        <a href="{{ route('admin.pending') }}" 
           class="relative bg-white p-2 rounded shadow hover:bg-indigo-50 text-sm flex items-center justify-between">
            
            <span>🔍 Review KYC</span>

            @if($quickStats['pendingKyc'] > 0)
                <span class="bg-red-600 text-white text-xs px-2 py-0.5 rounded-full">
                    {{ $quickStats['pendingKyc'] }}
                </span>
            @endif
        </a>

        <!-- Issues -->
        <a href="{{ route('admin.users.registration-issues') }}" 
           class="relative bg-white p-2 rounded shadow hover:bg-indigo-50 text-sm flex items-center justify-between">
            
            <span>⚠️ Issues</span>

            @if($quickStats['registrationIssues'] > 0)
                <span class="bg-red-600 text-white text-xs px-2 py-0.5 rounded-full">
                    {{ $quickStats['registrationIssues'] }}
                </span>
            @endif
        </a>

        <!-- Inquiries -->
        <a href="{{ route('admin.inquiries.index') }}" 
           class="relative bg-white p-2 rounded shadow hover:bg-indigo-50 text-sm flex items-center justify-between">
            
            <span>💬 Inquiries</span>

            @if($quickStats['pendingInquiries'] > 0)
                <span class="bg-red-600 text-white text-xs px-2 py-0.5 rounded-full">
                    {{ $quickStats['pendingInquiries'] }}
                </span>
            @endif
        </a>

        <!-- Reports -->
        <a href="{{ route('admin.reports.index') }}" 
           class="relative bg-white p-2 rounded shadow hover:bg-indigo-50 text-sm flex items-center justify-between">
            
            <span>🚨 Reports</span>

            @if($quickStats['reportsCount'] > 0)
                <span class="bg-red-600 text-white text-xs px-2 py-0.5 rounded-full">
                    {{ $quickStats['reportsCount'] }}
                </span>
            @endif
        </a>

        <!-- Support Tickets -->
        <a href="{{ route('admin.support.index') }}" 
           class="relative bg-white p-2 rounded shadow hover:bg-indigo-50 text-sm flex items-center justify-between">
            
            <span>🛠️ Support</span>

            @if($quickStats['supportTickets'] > 0)
                <span class="bg-red-600 text-white text-xs px-2 py-0.5 rounded-full">
                    {{ $quickStats['supportTickets'] }}
                </span>
            @endif
        </a>

    </div>
</div>
    <!-- ===== Recent Users (Enhanced) ===== -->
<div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">

<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-semibold text-gray-800">Recent Users</h2>
        <p class="text-sm text-gray-500">Newest registered users</p>
    </div>

    <span class="text-xs bg-blue-100 text-blue-600 px-3 py-1 rounded-full font-medium">
        {{ count($recentUsers) }} users
    </span>
</div>

<!-- Table -->
<div class="overflow-x-auto">
    <table class="w-full text-sm">

        <thead>
            <tr class="text-left text-gray-500 border-b">
                <th class="pb-3">User</th>
                <th class="pb-3">Email</th>
                <th class="pb-3">Type</th>
                <th class="pb-3 text-right">Joined</th>
            </tr>
        </thead>

        <tbody>

            @forelse($recentUsers as $user)
                <tr class="border-b hover:bg-gray-50 transition">

                    <!-- User -->
                    <td class="py-4 flex items-center gap-3">
                        <div class="h-9 w-9 rounded-full bg-blue-500 text-white flex items-center justify-center text-sm font-bold">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>

                        <div>
                            <p class="font-medium text-gray-800">
                                {{ $user->name }}
                            </p>
                            <p class="text-xs text-gray-400">
                                ID: #{{ $user->id }}
                            </p>
                        </div>
                    </td>

                    <!-- Email -->
                    <td class="py-4 text-gray-600">
                        {{ $user->email }}
                    </td>

                    <!-- Type Badge -->
                    <td class="py-4">
                        <span class="px-3 py-1 text-xs rounded-full font-medium
                            @if($user->user_type == 'trainer')
                                bg-green-100 text-green-700
                            @elseif($user->user_type == 'gymowner')
                                bg-orange-100 text-orange-700
                            @else
                                bg-blue-100 text-blue-700
                            @endif
                        ">
                            {{ ucfirst($user->user_type) }}
                        </span>
                    </td>

                    <!-- Date -->
                    <td class="py-4 text-right text-gray-500">
                        {{ $user->created_at->format('d M Y') }}
                    </td>

                </tr>

            @empty
                <tr>
                    <td colspan="4" class="py-10 text-center text-gray-400">
                        <div class="flex flex-col items-center">
                            <div class="text-4xl mb-2">👤</div>
                            No users found
                        </div>
                    </td>
                </tr>
            @endforelse

        </tbody>

    </table>
</div>

</div>
  <!-- ===== Recent Payments (Enhanced) ===== -->
<div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">

<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-semibold text-gray-800">Recent Payments</h2>
        <p class="text-sm text-gray-500">Latest successful transactions</p>
    </div>

    <span class="text-xs bg-indigo-100 text-indigo-600 px-3 py-1 rounded-full font-medium">
        {{ count($recentPayments) }} records
    </span>
</div>

<!-- Table -->
<div class="overflow-x-auto">
    <table class="w-full text-sm">

        <thead>
            <tr class="text-left text-gray-500 border-b">
                <th class="pb-3">User</th>
                <th class="pb-3">Amount</th>
                <th class="pb-3">Status</th>
                <th class="pb-3 text-right">Date</th>
            </tr>
        </thead>

        <tbody>

            @forelse($recentPayments as $payment)
                <tr class="border-b hover:bg-gray-50 transition">

                    <!-- User -->
                    <td class="py-4 flex items-center gap-3">
                        <div class="h-9 w-9 rounded-full bg-indigo-500 text-white flex items-center justify-center text-sm font-bold">
                            {{ strtoupper(substr($payment->user->name ?? 'U', 0, 1)) }}
                        </div>

                        <div>
                            <p class="font-medium text-gray-800">
                                {{ $payment->user->name ?? 'Unknown User' }}
                            </p>
                            <p class="text-xs text-gray-400">
                                ID: #{{ $payment->id }}
                            </p>
                        </div>
                    </td>

                    <!-- Amount -->
                    <td class="py-4 font-semibold text-gray-800">
                        ₹{{ number_format($payment->amount, 2) }}
                    </td>

                    <!-- Status -->
                    <td class="py-4">
                        <span class="px-3 py-1 text-xs rounded-full font-medium
                            @if($payment->status == 'paid')
                                bg-green-100 text-green-700
                            @elseif($payment->status == 'pending')
                                bg-yellow-100 text-yellow-700
                            @else
                                bg-gray-100 text-gray-600
                            @endif
                        ">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>

                    <!-- Date -->
                    <td class="py-4 text-right text-gray-500">
                        {{ $payment->created_at->format('d M Y') }}
                    </td>

                </tr>

            @empty
                <tr>
                    <td colspan="4" class="py-10 text-center text-gray-400">
                        <div class="flex flex-col items-center">
                            <div class="text-4xl mb-2">💳</div>
                            No recent payments found
                        </div>
                    </td>
                </tr>
            @endforelse

        </tbody>

    </table>
</div>

</div>

@endsection