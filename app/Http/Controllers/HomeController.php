<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Tag;
use App\Repositories\ArticleRepository;
use App\Repositories\TagRepository;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected ArticleRepository $articleRepository;
    protected TagRepository $tagRepository;

    public function __construct(ArticleRepository $articleRepository, TagRepository $tagRepository)
    {
        $this->articleRepository = $articleRepository;
        $this->tagRepository = $tagRepository;
    }

    public function index(Request $request)
    {
        $filters = [
            'tag' => $request->query('tag'),
            'search' => $request->query('search'),
            'featured' => $request->query('featured'),
        ];

        $latestArticles = $this->articleRepository->getAllPaginated(12, $filters);
        $featuredArticles = $this->articleRepository->getFeatured(5);
        $tags = $this->tagRepository->getWithArticleCounts();
        $popularTags = $this->tagRepository->getPopular(10);

        return view('news.home', compact('latestArticles', 'featuredArticles', 'tags', 'popularTags'));
    }

    public function show(Article $article)
    {
        abort_unless($article->is_published, 404);

        $article->load('tags');
        $this->articleRepository->incrementViews($article);

        $relatedArticles = $this->articleRepository->getRelated($article, 6);

        return view('news.show', compact('article', 'relatedArticles'));
    }

    public function tag(Tag $tag)
    {
        $articles = $this->articleRepository->getByTag($tag, 12);

        return view('news.tag', compact('tag', 'articles'));
    }

    public function search(Request $request)
    {
        $query = $request->query('q');
        
        if (!$query) {
            return redirect()->route('home')->with('error', 'Please enter a search term.');
        }

        $articles = $this->articleRepository->search($query, 12);
        $tags = $this->tagRepository->getPopular(10);

        return view('news.search', compact('articles', 'query', 'tags'));
    }
}
