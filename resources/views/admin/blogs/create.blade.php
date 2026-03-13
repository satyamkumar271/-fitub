@extends('admin.layouts.app')

@section('title', 'Create Blog')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Create Blog</h1>
    <p class="text-sm text-gray-500 mt-1">Publish a new post for frontpage and blog listing.</p>
</div>

@if($errors->any())
    <div class="mb-4 rounded-lg bg-red-100 border border-red-200 text-red-800 px-4 py-3">
        {{ $errors->first() }}
    </div>
@endif

<form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow p-6 space-y-5">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title*</label>
            <input type="text" name="title" value="{{ old('title') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Slug (optional)</label>
            <input type="text" name="slug" value="{{ old('slug') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="auto-generated-if-empty">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
            <input type="text" name="category" value="{{ old('category', 'Fitness') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Author Name</label>
            <input type="text" name="author_name" value="{{ old('author_name') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Excerpt</label>
        <textarea name="excerpt" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2">{{ old('excerpt') }}</textarea>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Meta Title (SEO)</label>
            <input type="text" name="meta_title" value="{{ old('meta_title') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" maxlength="255">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Meta Description (SEO)</label>
            <input type="text" name="meta_description" value="{{ old('meta_description') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" maxlength="320">
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Content (HTML/text allowed)*</label>
        <textarea name="content" rows="12" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>{{ old('content') }}</textarea>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Featured Image</label>
            <input type="file" name="image" class="w-full border border-gray-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Publish At (optional)</label>
            <input type="datetime-local" name="published_at" value="{{ old('published_at') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
        </div>
    </div>

    <div class="flex items-center gap-6">
        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
            Publish now
        </label>
        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" name="featured" value="1" {{ old('featured') ? 'checked' : '' }}>
            Mark as featured
        </label>
    </div>

    <div class="pt-2 flex items-center gap-2">
        <button type="submit" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-semibold hover:bg-indigo-700">Save Blog</button>
        <a href="{{ route('admin.blogs.index') }}" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Cancel</a>
    </div>
</form>
@endsection
