@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <a href="{{ route('admin.blocks.index') }}" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
        ← Back to Blocked Users
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2">
            <!-- Block Details -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Block Details</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Lead ID</label>
                        <p class="text-lg text-gray-900 font-semibold">#{{ $block->inquiry_id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Reason</label>
                        <p class="text-lg text-gray-900">{{ $block->reason }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Blocked Date</label>
                        <p class="text-lg text-gray-900">{{ $block->created_at->format('M d, Y H:i A') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full {{ $block->active ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                            {{ $block->active ? 'Active Block' : 'Unblocked' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Reporter Info -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Reporter</h3>
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <p class="font-semibold text-lg text-gray-900">{{ $block->blocker->name }}</p>
                        <p class="text-gray-600">{{ $block->blocker->email }}</p>
                        <p class="text-sm text-gray-500 mt-1">Type: <span class="font-semibold">{{ ucfirst($block->blocker->role ?? 'User') }}</span></p>
                    </div>
                    <a href="{{ route('admin.users.show', $block->blocker->id) }}" class="text-blue-600 hover:text-blue-900">
                        View Profile →
                    </a>
                </div>
            </div>

            <!-- Blocked User Info -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Blocked User</h3>
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <p class="font-semibold text-lg text-gray-900">{{ $block->blockedUser->name }}</p>
                        <p class="text-gray-600">{{ $block->blockedUser->email }}</p>
                        <p class="text-sm text-gray-500 mt-1">Type: <span class="font-semibold">{{ ucfirst($block->blockedUser->role ?? 'User') }}</span></p>
                        <p class="text-sm text-gray-500 mt-1">Status: 
                            <span class="font-semibold {{ $block->blockedUser->is_active ? 'text-green-600' : 'text-red-600' }}">
                                {{ $block->blockedUser->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                    <a href="{{ route('admin.users.show', $block->blockedUser->id) }}" class="text-blue-600 hover:text-blue-900">
                        View Profile →
                    </a>
                </div>
            </div>

            <!-- Inquiry Details -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Inquiry Details</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Lead Title</label>
                        <p class="text-gray-900">{{ $block->inquiry->title ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                            {{ ucfirst($block->inquiry->status) }}
                        </span>
                    </div>
                    <div>
                        <a href="{{ route('admin.inquiries.chat', $block->inquiry_id) }}" class="text-blue-600 hover:text-blue-900 font-semibold">
                            View Full Chat →
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 sticky top-4 space-y-4">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Actions</h3>

                @if($block->active)
                    <!-- Send Warning -->
                    <div class="mb-6">
                        <form action="{{ route('admin.blocks.warning', $block) }}" method="POST" class="space-y-3">
                            @csrf
                            <label class="block text-sm font-medium text-gray-700">Send Warning</label>
                            <textarea name="message" rows="4" placeholder="Enter warning message..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
                            <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg transition">
                                Send Warning
                            </button>
                        </form>
                    </div>

                    <hr class="my-4">

                    <!-- Cancel Registration -->
                    <div class="mb-6">
                        <form action="{{ route('admin.blocks.cancel', $block) }}" method="POST" class="space-y-3">
                            @csrf
                            <label class="block text-sm font-medium text-gray-700">Cancel Registration</label>
                            <textarea name="reason" rows="3" placeholder="Provide reason for cancellation..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500" required></textarea>
                            <button type="submit" onclick="return confirm('This will permanently cancel the user registration. Are you sure?')" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                                Cancel Registration
                            </button>
                        </form>
                    </div>

                    <hr class="my-4">

                    <!-- Unblock -->
                    <form action="{{ route('admin.blocks.unblock', $block) }}" method="POST">
                        @csrf
                        <button type="submit" onclick="return confirm('Unblock this user?')" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                            Unblock User
                        </button>
                    </form>
                @else
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-green-800 font-semibold">✓ This block is no longer active</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($errors->any())
    <div class="fixed bottom-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

@if(session('success'))
    <div class="fixed bottom-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
@endif

@endsection
