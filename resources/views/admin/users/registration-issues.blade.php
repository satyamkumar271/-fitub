@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Registration Issues</h1>
            <p class="text-sm text-gray-500 mt-1">
                Users jinki registration cancel hui hai ya jinhe warning mili hai.
            </p>
        </div>
    </div>

    @if($users->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warnings</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cancellation Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold
                                    @if($user->user_type === 'customer') bg-blue-100 text-blue-800
                                    @elseif($user->user_type === 'trainer') bg-green-100 text-green-800
                                    @else bg-orange-100 text-orange-800
                                    @endif
                                ">
                                    {{ ucfirst($user->user_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold
                                    @if($user->status === 'cancelled') bg-red-100 text-red-800
                                    @elseif($user->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800
                                    @endif
                                ">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                @if($user->warnings_count > 0)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        {{ $user->warnings_count }} warning{{ $user->warnings_count > 1 ? 's' : '' }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">No warnings</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 max-w-xs">
                                @if($user->registration_cancellation_reason)
                                    <span title="{{ $user->registration_cancellation_reason }}">
                                        {{ \Illuminate\Support\Str::limit($user->registration_cancellation_reason, 80) }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ optional($user->updated_at)->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                    View
                                </a>

                                @if($user->status === 'cancelled')
                                    <form action="{{ route('admin.users.activate', $user) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            onclick="return confirm('Reactivate this user account?')"
                                            class="text-green-600 hover:text-green-900 font-semibold">
                                            Activate
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-600 text-lg">Abhi tak kisi user ki registration cancel ya warning record nahi hai.</p>
        </div>
    @endif
</div>
@endsection

