<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with('tags')
            ->published()
            ->latest('published_at');

        // Filter by tag if provided
        if ($tag = $request->query('tag')) {
            $query->whereHas('tags', function ($q) use ($tag) {
                $q->where('slug', $tag);
            });
        }

        // Search functionality
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $articles = $query->paginate(12)->withQueryString();
        
        // Get featured articles for the hero section
        $featuredArticles = Article::with('tags')
            ->featured()
            ->published()
            ->latest('published_at')
            ->take(5)
            ->get();

        // Get all tags with their article count
        $tags = Tag::withCount(['articles' => function ($query) {
            $query->published();
        }])->having('articles_count', '>', 0)
          ->orderBy('articles_count', 'desc')
          ->get();

        return view('news.index', compact('articles', 'featuredArticles', 'tags'));
    }

    public function show(Article $article)
    {
        abort_unless($article->is_published, 404);

        $article->load('tags');
        $article->incrementViewCount();

        // Get related articles based on tags
        $relatedArticles = Article::with('tags')
            ->published()
            ->whereHas('tags', function ($query) use ($article) {
                $query->whereIn('tags.id', $article->tags->pluck('id'));
            })
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->take(6)
            ->get();

        return view('news.show', compact('article', 'relatedArticles'));
    }

    public function tag(Tag $tag)
    {
        $articles = Article::with('tags')
            ->published()
            ->whereHas('tags', function ($query) use ($tag) {
                $query->where('tags.id', $tag->id);
            })
            ->latest('published_at')
            ->paginate(12);

        return view('news.tag', compact('tag', 'articles'));
    }
}
