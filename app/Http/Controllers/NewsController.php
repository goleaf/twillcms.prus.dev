<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use App\Models\Category;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display the main news feed
     */
    public function index()
    {
        // Featured news (breaking news or important articles)
        $featuredNews = Post::published()
            ->featured()
            ->with(['tags', 'categories'])
            ->latest()
            ->take(3)
            ->get();

        // Latest news
        $latestNews = Post::published()
            ->with(['tags', 'categories'])
            ->whereNotIn('id', $featuredNews->pluck('id'))
            ->latest()
            ->paginate(12);

        // Popular tags
        $popularTags = Tag::withCount(['posts' => function ($query) {
            $query->where('status', 'published');
        }])
        ->orderBy('posts_count', 'desc')
        ->take(20)
        ->get();

        // Categories for navigation
        $categories = Category::where('is_active', true)
            ->withCount(['posts' => function ($query) {
                $query->where('status', 'published');
            }])
            ->orderBy('position')
            ->get();

        return view('news.index', compact(
            'featuredNews',
            'latestNews', 
            'popularTags',
            'categories'
        ));
    }

    /**
     * Search functionality
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query)) {
            return redirect()->route('home');
        }

        $posts = Post::published()
            ->where(function ($queryBuilder) use ($query) {
                $queryBuilder->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('content', 'LIKE', "%{$query}%")
                    ->orWhere('excerpt', 'LIKE', "%{$query}%");
            })
            ->orWhereHas('tags', function ($queryBuilder) use ($query) {
                $queryBuilder->where('name', 'LIKE', "%{$query}%");
            })
            ->orWhereHas('categories', function ($queryBuilder) use ($query) {
                $queryBuilder->where('title', 'LIKE', "%{$query}%");
            })
            ->with(['tags', 'categories'])
            ->latest()
            ->paginate(12);

        return view('news.search', compact('posts', 'query'));
    }
} 