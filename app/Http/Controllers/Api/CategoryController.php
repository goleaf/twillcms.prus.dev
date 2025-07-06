<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PostSummaryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    /**
     * Get all published categories
     */
    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'api.categories.index.'.($request->has('include_translations') ? 'with_translations' : 'basic');

        $data = Cache::remember($cacheKey, 1800, function () use ($request) { // 30 minutes cache
            $query = Category::published()
                ->withCount('posts')
                ->orderBy('position');

            // Include translations if requested
            if ($request->has('include_translations')) {
                $query->with('translations');
            }

            $categories = $query->get();

            return [
                'data' => CategoryResource::collection($categories),
            ];
        });

        return response()->json($data)
            ->header('Cache-Control', 'public, max-age=1800') // 30 minutes browser cache
            ->header('Vary', 'Accept, Accept-Language');
    }

    /**
     * Get a single category with its posts
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $cacheKey = "api.category.{$slug}.".md5($request->getQueryString() ?? '');

        $data = Cache::remember($cacheKey, 600, function () use ($slug, $request) { // 10 minutes cache
            $query = Category::published()
                ->where('slug', $slug)
                ->withCount(['posts' => function ($query) {
                    $query->published();
                }]);

            // Include translations if requested
            if ($request->has('include_translations')) {
                $query->with('translations');
            }

            $category = $query->firstOrFail();

            // Get posts for this category with pagination
            $perPage = min($request->get('per_page', 12), 50);
            $posts = $category->posts()
                ->published()
                ->with(['categories'])
                ->orderBy('published_at', 'desc')
                ->paginate($perPage);

            $categoryData = new CategoryResource($category);
            $categoryArray = $categoryData->toArray($request);

            // Add posts directly (not nested in data)
            $categoryArray['posts'] = PostSummaryResource::collection($posts->items());

            return $categoryArray;
        });

        return response()->json($data)
            ->header('Cache-Control', 'public, max-age=600') // 10 minutes browser cache
            ->header('Vary', 'Accept, Accept-Language');
    }

    /**
     * Get categories with post counts for navigation
     */
    public function navigation(Request $request): JsonResponse
    {
        $cacheKey = 'api.categories.navigation';

        $data = Cache::remember($cacheKey, 3600, function () { // 1 hour cache
            $categories = Category::published()
                ->withCount(['posts' => function ($query) {
                    $query->published();
                }])
                ->orderBy('position')
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->title, // Use title instead of name
                        'slug' => $category->slug,
                        'color' => $category->color ?? '#3B82F6',
                        'posts_count' => $category->posts_count,
                        'url' => "/categories/{$category->slug}",
                    ];
                });

            return $categories;
        });

        return response()->json($data)
            ->header('Cache-Control', 'public, max-age=3600'); // 1 hour browser cache
    }

    /**
     * Get popular categories (with most posts)
     */
    public function popular(Request $request): JsonResponse
    {
        $cacheKey = 'api.categories.popular';

        $data = Cache::remember($cacheKey, 1800, function () use ($request) { // 30 minutes cache
            $limit = min($request->get('limit', 5), 20); // Max 20 popular categories

            // Get categories with post counts, then filter in PHP for SQLite compatibility
            $categories = Category::published()
                ->withCount(['posts' => function ($query) {
                    $query->published();
                }])
                ->orderBy('posts_count', 'desc')
                ->get()
                ->filter(function ($category) {
                    return $category->posts_count > 0;
                })
                ->take($limit);

            return $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->title, // Use title instead of name
                    'slug' => $category->slug,
                    'color' => $category->color ?? '#3B82F6',
                    'posts_count' => (int) $category->posts_count,
                    'url' => "/categories/{$category->slug}",
                ];
            });
        });

        return response()->json($data)
            ->header('Cache-Control', 'public, max-age=1800'); // 30 minutes browser cache
    }
}
