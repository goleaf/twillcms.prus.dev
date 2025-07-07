<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Repositories\TagRepository;
use App\Repositories\ArticleRepository;
use Illuminate\Http\Request;

class TagController extends Controller
{
    protected TagRepository $tagRepository;
    protected ArticleRepository $articleRepository;

    public function __construct(TagRepository $tagRepository, ArticleRepository $articleRepository)
    {
        $this->tagRepository = $tagRepository;
        $this->articleRepository = $articleRepository;
    }

    /**
     * Display a listing of all tags
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        
        if ($search) {
            $tags = $this->tagRepository->search($search, 20);
        } else {
            $tags = $this->tagRepository->getAllPaginated(20);
        }

        $popularTags = $this->tagRepository->getPopular(10);
        $featuredTags = $this->tagRepository->getFeatured(5);

        return view('news.tags.index', compact('tags', 'popularTags', 'featuredTags', 'search'));
    }

    /**
     * Display posts filtered by tag
     */
    public function show(Tag $tag)
    {
        $articles = $this->articleRepository->getByTag($tag, 12);
        $relatedTags = $this->tagRepository->getRelated($tag, 5);
        $popularTags = $this->tagRepository->getPopular(10);

        return view('news.tags.show', compact('tag', 'articles', 'relatedTags', 'popularTags'));
    }
} 