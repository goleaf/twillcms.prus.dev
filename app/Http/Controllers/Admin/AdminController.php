<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Models\Article;
use App\Models\Tag;
use App\Repositories\ArticleRepository;
use App\Repositories\TagRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    protected ArticleRepository $articleRepository;
    protected TagRepository $tagRepository;

    public function __construct(ArticleRepository $articleRepository, TagRepository $tagRepository)
    {
        $this->articleRepository = $articleRepository;
        $this->tagRepository = $tagRepository;
    }

    // Dashboard
    public function dashboard()
    {
        $statistics = [
            'articles' => $this->articleRepository->getStatistics(),
            'tags' => $this->tagRepository->getStatistics(),
        ];

        $recentArticles = $this->articleRepository->getLatest(5);
        $popularArticles = $this->articleRepository->getPopular(5);
        $featuredArticles = $this->articleRepository->getFeatured(5);

        return view('admin.dashboard', compact('statistics', 'recentArticles', 'popularArticles', 'featuredArticles'));
    }

    // Articles Management
    public function articles(Request $request)
    {
        $filters = [
            'search' => $request->query('search'),
            'featured' => $request->query('featured'),
        ];

        $articles = $this->articleRepository->getAllPaginated(20, $filters);
        $tags = $this->tagRepository->getAll();

        return view('admin.articles.index', compact('articles', 'tags'));
    }

    public function createArticle()
    {
        $tags = $this->tagRepository->getAll();
        return view('admin.articles.create', compact('tags'));
    }

    public function storeArticle(StoreArticleRequest $request)
    {
        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        $article = Article::create($data);

        // Attach tags
        if ($request->has('tags')) {
            $article->tags()->attach($request->tags);
            
            // Update tag usage counts
            Tag::whereIn('id', $request->tags)->increment('usage_count');
        }

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article created successfully!');
    }

    public function editArticle(Article $article)
    {
        $tags = $this->tagRepository->getAll();
        $article->load('tags');
        
        return view('admin.articles.edit', compact('article', 'tags'));
    }

    public function updateArticle(UpdateArticleRequest $request, Article $article)
    {
        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        $article->update($data);

        // Sync tags
        $oldTagIds = $article->tags->pluck('id')->toArray();
        $newTagIds = $request->tags ?? [];

        $article->tags()->sync($newTagIds);

        // Update tag usage counts
        $removedTags = array_diff($oldTagIds, $newTagIds);
        $addedTags = array_diff($newTagIds, $oldTagIds);

        if (!empty($removedTags)) {
            Tag::whereIn('id', $removedTags)->decrement('usage_count');
        }
        if (!empty($addedTags)) {
            Tag::whereIn('id', $addedTags)->increment('usage_count');
        }

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article updated successfully!');
    }

    public function destroyArticle(Article $article)
    {
        // Delete image
        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }

        // Update tag usage counts
        $tagIds = $article->tags->pluck('id')->toArray();
        if (!empty($tagIds)) {
            Tag::whereIn('id', $tagIds)->decrement('usage_count');
        }

        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article deleted successfully!');
    }

    // Tags Management
    public function tags(Request $request)
    {
        $search = $request->query('search');
        
        if ($search) {
            $tags = $this->tagRepository->search($search, 20);
        } else {
            $tags = $this->tagRepository->getAllPaginated(20);
        }

        return view('admin.tags.index', compact('tags', 'search'));
    }

    public function createTag()
    {
        return view('admin.tags.create');
    }

    public function storeTag(StoreTagRequest $request)
    {
        $this->tagRepository->create($request->validated());

        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag created successfully!');
    }

    public function editTag(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function updateTag(UpdateTagRequest $request, Tag $tag)
    {
        $this->tagRepository->update($tag, $request->validated());

        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag updated successfully!');
    }

    public function destroyTag(Tag $tag)
    {
        if ($tag->articles()->count() > 0) {
            return redirect()->route('admin.tags.index')
                ->with('error', 'Cannot delete tag that has articles. Please remove articles first.');
        }

        $this->tagRepository->delete($tag);

        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag deleted successfully!');
    }

    // Statistics
    public function statistics()
    {
        $articleStats = $this->articleRepository->getStatistics();
        $tagStats = $this->tagRepository->getStatistics();
        $trendingArticles = $this->articleRepository->getTrending(7, 10);
        $trendingTags = $this->tagRepository->getTrending(7, 10);

        return view('admin.statistics', compact('articleStats', 'tagStats', 'trendingArticles', 'trendingTags'));
    }

    // Analytics
    public function analytics()
    {
        $popularArticles = $this->articleRepository->getPopular(20);
        $recentArticles = $this->articleRepository->getLatest(20);
        $featuredArticles = $this->articleRepository->getFeatured(20);
        $popularTags = $this->tagRepository->getPopular(20);

        return view('admin.analytics', compact('popularArticles', 'recentArticles', 'featuredArticles', 'popularTags'));
    }
} 