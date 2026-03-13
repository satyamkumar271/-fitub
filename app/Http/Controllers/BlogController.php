<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $activeCategory = $request->filled('category') ? (string) $request->query('category') : null;

        $basePublishedQuery = Blog::query()
            ->where('is_published', true);
        $publishedQuery = (clone $basePublishedQuery);
        if ($activeCategory) {
            $publishedQuery->where('category', $activeCategory);
        }

        $featuredPost = (clone $publishedQuery)
            ->where('featured', true)
            ->orderByDesc('published_at')
            ->latest('id')
            ->first();

        $posts = (clone $publishedQuery)
            ->when($featuredPost, fn ($query) => $query->where('id', '!=', $featuredPost->id))
            ->orderByDesc('published_at')
            ->latest('id')
            ->paginate(9)
            ->appends($request->query());

        $recentPosts = (clone $publishedQuery)
            ->orderByDesc('published_at')
            ->latest('id')
            ->take(5)
            ->get();

        $categoryCounts = (clone $basePublishedQuery)
            ->selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return view('blog.index', [
            'featuredPost' => $featuredPost,
            'posts' => $posts,
            'recentPosts' => $recentPosts,
            'categoryCounts' => $categoryCounts,
            'activeCategory' => $activeCategory,
        ]);
    }

    public function show(string $slug)
    {
        $post = Blog::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $recentPosts = Blog::where('is_published', true)
            ->where('id', '!=', $post->id)
            ->orderByDesc('published_at')
            ->latest('id')
            ->take(4)
            ->get();

        return view('blog.show', compact('post', 'recentPosts'));
    }
}
