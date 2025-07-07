<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display a specific news article
     */
    public function show($slug)
    {
        $article = Post::where('slug', $slug)
            ->where('status', 'published')
            ->with(['tags', 'categories'])
            ->firstOrFail();

        // Increment view count
        $article->increment('view_count');

        // Get related articles based on shared tags
        $relatedArticles = Post::published()
            ->whereHas('tags', function ($query) use ($article) {
                $query->whereIn('tags.id', $article->tags->pluck('id'));
            })
            ->where('id', '!=', $article->id)
            ->with(['tags', 'categories'])
            ->latest()
            ->take(6)
            ->get();

        // If no related articles by tags, get latest from same category
        if ($relatedArticles->isEmpty() && $article->categories->isNotEmpty()) {
            $relatedArticles = Post::published()
                ->whereHas('categories', function ($query) use ($article) {
                    $query->whereIn('categories.id', $article->categories->pluck('id'));
                })
                ->where('id', '!=', $article->id)
                ->with(['tags', 'categories'])
                ->latest()
                ->take(6)
                ->get();
        }

        return view('news.show', compact('article', 'relatedArticles'));
    }
} 