<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Database\Eloquent\Builder;

class NewsController extends Controller
{
    /**
     * Display a listing of news articles
     */
    public function index(Request $request): View
    {
        $query = Article::published()
            ->with(['tags:id,name,slug,color'])
            ->latest('published_at');

        // Apply tag filter
        if ($request->filled('tag')) {
            $query->whereHas('tags', function (Builder $q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        // Apply search filter
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Apply featured filter
        if ($request->boolean('featured')) {
            $query->featured();
        }

        $articles = $query->paginate(12);
        $popularTags = Tag::getPopular(10);

        return view('news.index', compact('articles', 'popularTags'));
    }

    /**
     * Display the specified article
     */
    public function show(Request $request, Article $article): View
    {
        // Only show published articles
        if (!$article->is_published) {
            abort(404);
        }

        // Increment view count
        $article->incrementViews();

        // Get related articles
        $relatedArticles = $article->getRelated(3);

        // Get popular tags
        $popularTags = Tag::getPopular(10);

        return view('news.show', compact('article', 'relatedArticles', 'popularTags'));
    }

    /**
     * Search articles
     */
    public function search(Request $request): View
    {
        $query = $request->get('q', '');
        $articles = collect();

        if (strlen($query) >= 3) {
            $articles = Article::published()
                ->with(['tags:id,name,slug,color'])
                ->search($query)
                ->latest('published_at')
                ->paginate(12);
        }

        $popularTags = Tag::getPopular(10);

        return view('news.search', compact('articles', 'query', 'popularTags'));
    }
}
