@extends('admin.layouts.app')

@section('title', 'Manage Blogs')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Blogs</h1>
        <p class="text-sm text-gray-500 mt-1">Create and manage live blog posts.</p>
    </div>
    <a href="{{ route('admin.blogs.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-indigo-700">Add Blog</a>
</div>

@if(session('success'))
    <div class="mb-4 rounded-lg bg-green-100 border border-green-200 text-green-800 px-4 py-3">{{ session('success') }}</div>
@endif

<div class="mb-5 bg-white rounded-xl shadow p-4">
    <form method="GET" action="{{ route('admin.blogs.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Status</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="all" {{ ($status ?? 'all') === 'all' ? 'selected' : '' }}>All</option>
                <option value="published" {{ ($status ?? 'all') === 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ ($status ?? 'all') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="featured" {{ ($status ?? 'all') === 'featured' ? 'selected' : '' }}>Featured</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Category</label>
            <select name="category" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">All categories</option>
                @foreach(($categories ?? collect()) as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Search</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Title, slug, author" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="bg-indigo-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm">Apply</button>
            <a href="{{ route('admin.blogs.index') }}" class="bg-white border border-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg hover:bg-gray-50 text-sm">Reset</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Published</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($blogs as $blog)
                    <tr>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900">{{ $blog->title }}</p>
                            <p class="text-xs text-gray-500">/{{ $blog->slug }}</p>
                        </td>
                        <td class="px-6 py-4 text-gray-700">{{ $blog->category }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $blog->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $blog->is_published ? 'Published' : 'Draft' }}
                            </span>
                            @if($blog->featured)
                                <span class="ml-2 px-2 py-1 rounded text-xs font-semibold bg-indigo-100 text-indigo-700">Featured</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-700">{{ optional($blog->published_at)->format('d M Y, h:i A') ?: '-' }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('blog.show', $blog->slug) }}" target="_blank" class="px-3 py-1.5 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">View</a>
                                <a href="{{ route('admin.blogs.edit', $blog) }}" class="px-3 py-1.5 rounded bg-blue-600 text-white hover:bg-blue-700">Edit</a>
                                <form action="{{ route('admin.blogs.destroy', $blog) }}" method="POST" onsubmit="return confirm('Delete this blog?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 rounded bg-red-600 text-white hover:bg-red-700">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">No blogs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-5">
    {{ $blogs->links() }}
</div>
@endsection
