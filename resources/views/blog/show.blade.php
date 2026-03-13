@extends('layouts.app')

@section('meta_title', $post->meta_title ?: $post->title . ' | Fitub Blog')
@section('meta_description', $post->meta_description ?: ($post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 150)))

@section('content')
<div class="bg-slate-50">
    <div class="container mx-auto px-6 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-10">
            <article class="lg:col-span-3 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <img src="{{ $post->image_path ? Storage::url($post->image_path) : 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?q=80&w=1600&auto=format&fit=crop' }}"
                     class="w-full h-72 md:h-96 object-cover"
                     alt="{{ $post->title }}">
                <div class="p-8">
                    <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">{{ $post->category }}</p>
                    <h1 class="mt-2 text-3xl md:text-4xl font-extrabold text-slate-900">{{ $post->title }}</h1>
                    <p class="mt-3 text-sm text-slate-500">
                        {{ $post->author_name ?: 'Fitub Team' }} • {{ optional($post->published_at)->format('d M Y') ?: $post->created_at->format('d M Y') }}
                    </p>
                    <div class="mt-7 prose max-w-none prose-slate">
                        {!! $post->content !!}
                    </div>
                </div>
            </article>

            <aside class="space-y-6">
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <h4 class="text-lg font-bold text-slate-900 mb-3">More Articles</h4>
                    <div class="space-y-4">
                        @forelse($recentPosts as $recent)
                            <a href="{{ route('blog.show', $recent->slug) }}" class="block">
                                <p class="text-sm font-semibold text-slate-800 hover:text-indigo-600">{{ $recent->title }}</p>
                                <p class="text-xs text-slate-500 mt-1">{{ optional($recent->published_at)->format('d M Y') ?: $recent->created_at->format('d M Y') }}</p>
                            </a>
                        @empty
                            <p class="text-sm text-slate-500">No additional posts yet.</p>
                        @endforelse
                    </div>
                </div>
                <a href="{{ route('blog.index') }}" class="inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                    <- Back to all blogs
                </a>
            </aside>
        </div>
    </div>
</div>
@endsection
