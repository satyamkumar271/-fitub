@extends('layouts.app') {{-- Yahan apna admin layout extend karein --}}

@section('content')
<div class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-slate-800">Pending Approvals</h2>
        <span class="bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-sm font-semibold">
            {{ $pendingUsers->count() }} Waiting for approval
        </span>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Joined</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($pendingUsers as $user)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900">{{ $user->name }}</div>
                            <div class="text-sm text-slate-500">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-700 capitalize">
                                {{ $user->user_type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $user->created_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 text-right">
                              <!-- View Details Button -->
    <a href="{{ route('admin.users.show', $user->id) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">
        View Details
    </a>
                            <form action="{{ route('admin.approve', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                                    Approve Access
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-slate-500">
                            No pending users found. Everything is up to date!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $pendingUsers->links() }}
    </div>
</div>
@endsection