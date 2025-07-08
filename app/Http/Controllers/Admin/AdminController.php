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
        $featuredArticles = $this->articleRepository->getFeatured(6);

        return view('admin.dashboard', compact('statistics', 'recentArticles', 'popularArticles', 'featuredArticles'));
    }

    // Articles Management
    public function articles(Request $request)
    {
        $filters = [
            'search' => $request->query('search'),
            'featured' => $request->query('featured'),
            'status' => $request->query('status'),
        ];

        $articles = $this->articleRepository->getAllPaginated(12, $filters);
        $tags = $this->tagRepository->getAll();

        return view('admin.articles.index', compact('articles', 'tags'));
    }

    public function createArticle()
    {
        $tags = $this->tagRepository->getAll();
        return view('admin.articles.create', compact('tags'));
    }

    public function storeArticle(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image',
            'is_featured' => 'nullable|boolean',
            'status' => 'required|in:published,draft',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);
        $data['is_featured'] = $request->has('is_featured') ? true : false;
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }
        if (isset($data['tags'])) {
            $data['tags'] = array_unique($data['tags']);
        }
        if ($data['is_featured']) {
            $data['status'] = 'published';
        }
        $article = $this->articleRepository->create($data);
        return redirect()->route('admin.articles.index')->with('success', __('Article created successfully.'));
    }

    public function showArticle($id)
    {
        $article = $this->articleRepository->getModel()->findOrFail($id);
        return view('admin.articles.show', compact('article'));
    }

    public function editArticle($id)
    {
        $article = $this->articleRepository->getModel()->findOrFail($id);
        $tags = $this->tagRepository->getModel()->all();
        return view('admin.articles.edit', compact('article', 'tags'));
    }

    public function updateArticle(Request $request, $id)
    {
        $article = $this->articleRepository->getModel()->findOrFail($id);
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image',
            'is_featured' => 'nullable|boolean',
            'status' => 'required|in:published,draft',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);
        $article->is_featured = $request->has('is_featured') ? true : false;
        if ($article->is_featured) {
            $article->status = 'published';
        } else {
            $article->status = 'draft';
        }
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }
        $article->update($data);
        if (isset($data['tags'])) {
            $article->tags()->sync($data['tags']);
        }
        return redirect()->route('admin.articles.index')->with('success', __('Article updated successfully.'));
    }

    public function destroyArticle($id)
    {
        $article = $this->articleRepository->getModel()->findOrFail($id);
        $this->articleRepository->delete($article);
        return redirect()->route('admin.articles.index')->with('success', __('Article deleted successfully.'));
    }

    // Tags Management
    public function tags(Request $request)
    {
        $filters = [
            'search' => $request->query('search'),
            'featured' => $request->query('featured'),
        ];

        $tags = $this->tagRepository->getAllPaginatedWithFilters(20, $filters);

        return view('admin.tags.index', compact('tags'));
    }

    public function createTag()
    {
        return view('admin.tags.create');
    }

    public function storeTag(StoreTagRequest $request)
    {
        $data = $request->validated();
        $tag = $this->tagRepository->create($data);
        $this->tagRepository->updateUsageCount($tag);
        return redirect()->route('admin.tags.index')->with('success', __('Tag created successfully.'));
    }

    public function showTag($id)
    {
        $tag = $this->tagRepository->getModel()->findOrFail($id);
        return view('admin.tags.show', compact('tag'));
    }

    public function editTag($id)
    {
        $tag = $this->tagRepository->getModel()->findOrFail($id);
        return view('admin.tags.edit', compact('tag'));
    }

    public function updateTag(UpdateTagRequest $request, $id)
    {
        $tag = $this->tagRepository->getModel()->findOrFail($id);
        $data = $request->validated();
        $tag->is_featured = $request->has('is_featured') ? true : false;
        $tag->fill($data);
        $tag->save();
        $this->tagRepository->updateUsageCount($tag);
        return redirect()->route('admin.tags.index')->with('success', __('Tag updated successfully.'));
    }

    public function destroyTag($id)
    {
        $tag = $this->tagRepository->getModel()->findOrFail($id);
        $tag->forceDelete();
        return redirect()->route('admin.tags.index')->with('success', __('Tag deleted successfully.'));
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

    // Bulk Actions for Articles
    public function bulkActionArticles(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('articles', []);
        if (empty($ids) || !$action) {
            return redirect()->back()->with('error', __('Please select articles and an action.'));
        }
        switch ($action) {
            case 'publish':
                $this->articleRepository->bulkUpdateStatus($ids, 'published');
                break;
            case 'draft':
                $this->articleRepository->bulkUpdateStatus($ids, 'draft');
                break;
            case 'feature':
                $this->articleRepository->bulkUpdateFeatured($ids, true);
                break;
            case 'unfeature':
                $this->articleRepository->bulkUpdateFeatured($ids, false);
                break;
            case 'delete':
                $this->articleRepository->bulkDelete($ids);
                break;
            default:
                return redirect()->back()->with('error', __('Invalid action.'));
        }
        return redirect()->route('admin.articles.index')->with('success', __('Bulk action completed.'));
    }

    // Bulk Actions for Tags
    public function bulkActionTags(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('tags', []);
        if ($action === 'delete' && !empty($ids)) {
            $this->tagRepository->bulkDelete($ids);
        } elseif ($action === 'feature' && !empty($ids)) {
            $this->tagRepository->bulkUpdateFeatured($ids, true);
        } elseif ($action === 'unfeature' && !empty($ids)) {
            $this->tagRepository->bulkUpdateFeatured($ids, false);
        }
        // Always redirect to /admin/tags after processing, regardless of input
        return redirect('/admin/tags')->with('success', __('Bulk action completed.'));
    }
} 