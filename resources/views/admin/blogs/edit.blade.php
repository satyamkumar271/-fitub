@extends('admin.layouts.app')

@section('title', 'Edit Blog')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Edit Blog</h1>
    <p class="text-sm text-gray-500 mt-1">Update blog details and publish status.</p>
</div>

@if($errors->any())
    <div class="mb-4 rounded-lg bg-red-100 border border-red-200 text-red-800 px-4 py-3">
        {{ $errors->first() }}
    </div>
@endif

<form action="{{ route('admin.blogs.update', $blog) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow p-6 space-y-5">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title*</label>
            <input type="text" name="title" value="{{ old('title', $blog->title) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Slug (optional)</label>
            <input type="text" name="slug" value="{{ old('slug', $blog->slug) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
            <input type="text" name="category" value="{{ old('category', $blog->category) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Author Name</label>
            <input type="text" name="author_name" value="{{ old('author_name', $blog->author_name) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Excerpt</label>
        <textarea name="excerpt" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2">{{ old('excerpt', $blog->excerpt) }}</textarea>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Meta Title (SEO)</label>
            <input type="text" name="meta_title" value="{{ old('meta_title', $blog->meta_title) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" maxlength="255">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Meta Description (SEO)</label>
            <input type="text" name="meta_description" value="{{ old('meta_description', $blog->meta_description) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" maxlength="320">
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Content (HTML/text allowed)*</label>
        <textarea name="content" rows="12" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>{{ old('content', $blog->content) }}</textarea>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Replace Featured Image</label>
            <input type="file" name="image" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            @if($blog->image_path)
                <img src="{{ Storage::url($blog->image_path) }}" class="mt-2 h-24 w-40 rounded object-cover border border-gray-200" alt="Current image">
            @endif
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Publish At (optional)</label>
            <input type="datetime-local" name="published_at" value="{{ old('published_at', $blog->published_at ? $blog->published_at->format('Y-m-d\TH:i') : '') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
        </div>
    </div>

    <div class="flex items-center gap-6">
        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" name="is_published" value="1" {{ old('is_published', $blog->is_published) ? 'checked' : '' }}>
            Published
        </label>
        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" name="featured" value="1" {{ old('featured', $blog->featured) ? 'checked' : '' }}>
            Featured
        </label>
    </div>

    <div class="pt-2 flex items-center gap-2">
        <button type="submit" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-semibold hover:bg-indigo-700">Update Blog</button>
        <a href="{{ route('admin.blogs.index') }}" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Cancel</a>
    </div>
</form>
@endsection
