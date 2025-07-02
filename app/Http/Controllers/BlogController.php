<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    protected $postRepository;

    protected $categoryRepository;

    public function __construct(PostRepository $postRepository, CategoryRepository $categoryRepository)
    {
        $this->postRepository = $postRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function index(Request $request): View
    {
        try {
            $search = $request->get('search');
            $categorySlug = $request->get('category');

                    $query = Post::published()
            ->with(['categories', 'slugs'])
            ->orderBy('published_at', 'desc');

            if ($search) {
                $query->whereTranslationLike('title', "%{$search}%")
                    ->orWhereTranslationLike('content', "%{$search}%");
            }

            if ($categorySlug) {
                $query->whereHas('categories', function ($q) use ($categorySlug) {
                    $q->forSlug($categorySlug);
                });
            }

            $posts = $query->paginate(12);

            // Get categories for filter
            $categories = Category::published()
                ->orderBy('position')
                ->get();

            return view('blog.index', compact('posts', 'categories', 'search', 'categorySlug'));
        } catch (QueryException $e) {
            // Handle case when database tables don't exist yet
            $posts = collect();
            $categories = collect();
            $search = null;
            $categorySlug = null;

            return view('blog.index', compact('posts', 'categories', 'search', 'categorySlug'));
        }
    }

    public function show(string $slug): View
    {
        try {
            $post = Post::published()
                ->forSlug($slug)
                ->with(['categories', 'slugs'])
                ->firstOrFail();

                    // Get previous and next posts
        $previousPost = Post::published()
            ->where('published_at', '<', $post->published_at)
            ->orderBy('published_at', 'desc')
            ->first();

        $nextPost = Post::published()
            ->where('published_at', '>', $post->published_at)
            ->orderBy('published_at', 'asc')
            ->first();

            return view('blog.show', compact('post', 'previousPost', 'nextPost'));
        } catch (QueryException $e) {
            abort(404);
        }
    }

    public function category(string $slug): View
    {
        try {
            $category = Category::published()
                ->forSlug($slug)
                ->firstOrFail();

                    $posts = Post::published()
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('categories.id', $category->id);
            })
            ->with(['categories', 'slugs'])
            ->orderBy('published_at', 'desc')
            ->paginate(12);

            return view('blog.category', compact('category', 'posts'));
        } catch (QueryException $e) {
            abort(404);
        }
    }

    public function categories(): View
    {
        try {
            $categories = Category::published()
                ->withCount(['posts' => function ($query) {
                    $query->published();
                }])
                ->orderBy('position')
                ->get();

            return view('blog.categories', compact('categories'));
        } catch (QueryException $e) {
            $categories = collect();

            return view('blog.categories', compact('categories'));
        }
    }

    /**
     * Display posts for a specific year/month archive
     */
    public function archive(Request $request, int $year, ?int $month = null): View
    {
        try {
            $query = Post::published()
                ->with(['categories', 'slugs'])
                ->whereYear('published_at', $year);

            if ($month) {
                $query->whereMonth('published_at', $month);
            }

            $posts = $query->orderBy('published_at', 'desc')->paginate(12);

            $archiveTitle = $month 
                ? \DateTime::createFromFormat('!m', $month)->format('F') . ' ' . $year
                : $year;

            return view('blog.archive', compact('posts', 'year', 'month', 'archiveTitle'));
        } catch (QueryException $e) {
            abort(404);
        }
    }


}
