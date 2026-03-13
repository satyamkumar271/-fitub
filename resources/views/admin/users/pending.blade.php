@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <h2 class="text-2xl font-bold text-slate-800">KYC Reviews</h2>
        <span class="bg-slate-100 text-slate-700 px-3 py-1 rounded-full text-sm font-semibold">
            Total {{ $tabCounts[$tab] ?? 0 }}
        </span>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">{{ $errors->first() }}</div>
    @endif

    <div class="mb-6 flex flex-wrap gap-2">
        <a href="{{ route('admin.pending', ['tab' => 'pending']) }}" class="px-4 py-2 rounded-full text-sm font-semibold {{ $tab === 'pending' ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 border border-slate-200' }}">Pending {{ $tabCounts['pending'] ?? 0 }}</a>
        <a href="{{ route('admin.pending', ['tab' => 'approved']) }}" class="px-4 py-2 rounded-full text-sm font-semibold {{ $tab === 'approved' ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 border border-slate-200' }}">Approved {{ $tabCounts['approved'] ?? 0 }}</a>
        <a href="{{ route('admin.pending', ['tab' => 'rejected']) }}" class="px-4 py-2 rounded-full text-sm font-semibold {{ $tab === 'rejected' ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 border border-slate-200' }}">Rejected {{ $tabCounts['rejected'] ?? 0 }}</a>
        <a href="{{ route('admin.pending', ['tab' => 'all']) }}" class="px-4 py-2 rounded-full text-sm font-semibold {{ $tab === 'all' ? 'bg-slate-900 text-white' : 'bg-white text-slate-700 border border-slate-200' }}">All {{ $tabCounts['all'] ?? 0 }}</a>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">KYC</th>
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
                            @if($user->kyc_status === 'rejected' && $user->kyc_rejection_reason)
                                <div class="text-xs text-red-600 mt-1">Reason: {{ \Illuminate\Support\Str::limit($user->kyc_rejection_reason, 80) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-700 capitalize">{{ $user->user_type }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $kycColor = $user->kyc_status === 'approved'
                                    ? 'bg-emerald-100 text-emerald-700'
                                    : ($user->kyc_status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700');
                            @endphp
                            <span class="px-2 py-1 rounded text-xs capitalize {{ $kycColor }}">{{ $user->kyc_status }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $user->created_at->diffForHumans() }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-lg text-sm font-semibold transition">View</a>
                                @if($user->kyc_status !== 'approved')
                                    <form action="{{ route('admin.approve', $user->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded-lg text-sm font-semibold transition">Approve</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-slate-500">No KYC records found for this tab.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $pendingUsers->links() }}</div>
</div>
@endsection
