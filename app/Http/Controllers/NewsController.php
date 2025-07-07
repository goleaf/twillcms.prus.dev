<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Post;
use App\Models\Tag;
use App\Repositories\ArticleRepository;
use App\Repositories\TagRepository;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    protected $articleRepository;
    protected $tagRepository;

    public function __construct(ArticleRepository $articleRepository, TagRepository $tagRepository)
    {
        $this->articleRepository = $articleRepository;
        $this->tagRepository = $tagRepository;
    }

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
     * Display a specific article
     */
    public function show(Article $article)
    {
        // Increment view count
        $this->articleRepository->incrementViews($article);

        // Get related articles
        $relatedArticles = $this->articleRepository->getRelated($article, 4);

        // Get article tags
        $tags = $article->tags;

        return view('news.show', compact('article', 'relatedArticles', 'tags'));
    }

    /**
     * Search functionality
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        if (empty($query)) {
            return view('news.search', [
                'articles' => collect(),
                'query' => '',
                'totalResults' => 0
            ]);
        }

        $articles = $this->articleRepository->search($query);
        $totalResults = $articles->total();

        return view('news.search', compact('articles', 'query', 'totalResults'));
    }
}
