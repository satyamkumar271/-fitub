@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Blocked Users & Inquiries</h1>
        <span class="bg-red-100 text-red-800 px-4 py-2 rounded-lg font-semibold">
            Total Blocks: {{ $blocks->total() }}
        </span>
    </div>

    @if($blocks->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lead ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reporter</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Blocked User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($blocks as $block)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('admin.inquiries.chat', $block->inquiry_id) }}" class="text-blue-600 hover:text-blue-900 font-semibold">
                                    #{{ $block->inquiry_id }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $block->blocker->name }}</div>
                                <div class="text-sm text-gray-500">{{ $block->blocker->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $block->blockedUser->name }}</div>
                                <div class="text-sm text-gray-500">{{ $block->blockedUser->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    {{ $block->reason }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $block->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.blocks.show', $block) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                    View
                                </a>
                                <form action="{{ route('admin.blocks.cancel', $block) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Cancel registration? This cannot be undone.')" class="text-red-600 hover:text-red-900">
                                        Cancel Reg
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $blocks->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-600 text-lg">No blocked users at this time.</p>
        </div>
    @endif
</div>
@endsection
