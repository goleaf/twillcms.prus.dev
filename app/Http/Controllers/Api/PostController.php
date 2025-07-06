<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostSummaryResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{
    /**
     * Get paginated list of published posts
     */
    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'api.posts.'.md5($request->getQueryString() ?? '');

        $data = Cache::remember($cacheKey, 300, function () use ($request) { // 5 minutes cache
            $query = Post::published()
                ->with(['categories'])
                ->withCount('categories')
                ->orderBy('published_at', 'desc');

            // Category filtering
            if ($request->has('category')) {
                $query->whereHas('categories', function ($q) use ($request) {
                    $q->where('slug', $request->category);
                });
            }

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%")
                        ->orWhere('content', 'LIKE', "%{$search}%");
                });
            }

            // Date filtering
            if ($request->has('year')) {
                $query->whereYear('published_at', $request->year);
            }

            if ($request->has('month')) {
                $query->whereMonth('published_at', $request->month);
            }

            // Pagination
            $perPage = min($request->get('per_page', 12), 50); // Max 50 items per page
            $posts = $query->paginate($perPage);

            return [
                'data' => PostSummaryResource::collection($posts->items()),
                'meta' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                    'from' => $posts->firstItem(),
                    'to' => $posts->lastItem(),
                ],
                'links' => [
                    'first' => $posts->url(1),
                    'last' => $posts->url($posts->lastPage()),
                    'prev' => $posts->previousPageUrl(),
                    'next' => $posts->nextPageUrl(),
                ],
            ];
        });

        return response()->json($data)
            ->header('Cache-Control', 'public, max-age=300') // 5 minutes browser cache
            ->header('Vary', 'Accept, Accept-Language');
    }

    /**
     * Get a single post by slug
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $cacheKey = "api.post.{$slug}.".($request->has('include_translations') ? 'with_translations' : 'basic');

        $data = Cache::remember($cacheKey, 600, function () use ($slug, $request) { // 10 minutes cache
            $query = Post::published()
                ->where(function ($q) use ($slug) {
                    $q->where('slug', $slug)
                        ->orWhereRaw('LOWER(slug) = LOWER(?)', [$slug]);
                })
                ->with(['categories']);

            // Include translations if requested (for backward compatibility)
            if ($request->has('include_translations')) {
                $query->with('translations');
            }

            $post = $query->first();

            if (! $post) {
                return null; // Return null to indicate post not found
            }

            // Get related posts (same categories, excluding current post)
            $relatedPosts = Post::published()
                ->whereHas('categories', function ($q) use ($post) {
                    $q->whereIn('categories.id', $post->categories->pluck('id'));
                })
                ->where('id', '!=', $post->id)
                ->limit(3)
                ->get();

            // Create a new PostResource with the related posts data
            $resource = new PostResource($post);
            $resourceArray = $resource->toArray(request());
            $resourceArray['related_posts'] = PostSummaryResource::collection($relatedPosts);

            return $resourceArray;
        });

        // Handle case when post is not found
        if ($data === null) {
            return response()->json([
                'error' => 'Post not found',
                'message' => 'The requested post could not be found.',
                'slug' => $slug,
            ], 404);
        }

        return response()->json($data)
            ->header('Cache-Control', 'public, max-age=600') // 10 minutes browser cache
            ->header('Vary', 'Accept, Accept-Language');
    }

    /**
     * Get popular/featured posts
     */
    public function popular(Request $request): JsonResponse
    {
        $cacheKey = 'api.posts.popular';

        $data = Cache::remember($cacheKey, 3600, function () use ($request) { // 1 hour cache
            $limit = min($request->get('limit', 5), 20); // Max 20 popular posts

            $posts = Post::published()
                ->with(['categories'])
                ->orderBy('published_at', 'desc') // You can implement view counts later
                ->limit($limit)
                ->get();

            return PostSummaryResource::collection($posts);
        });

        return response()->json($data)
            ->header('Cache-Control', 'public, max-age=3600'); // 1 hour browser cache
    }

    /**
     * Get recent posts
     */
    public function recent(Request $request): JsonResponse
    {
        $cacheKey = 'api.posts.recent';

        $data = Cache::remember($cacheKey, 300, function () use ($request) { // 5 minutes cache
            $limit = min($request->get('limit', 5), 20); // Max 20 recent posts

            $posts = Post::published()
                ->with(['categories'])
                ->orderBy('published_at', 'desc')
                ->limit($limit)
                ->get();

            return PostSummaryResource::collection($posts);
        });

        return response()->json($data)
            ->header('Cache-Control', 'public, max-age=300'); // 5 minutes browser cache
    }

    /**
     * Search posts with advanced filtering
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
            'category' => 'sometimes|string|exists:categories,slug',
            'per_page' => 'sometimes|integer|min:1|max:50',
        ]);

        $cacheKey = 'api.posts.search.'.md5($request->getQueryString());

        $data = Cache::remember($cacheKey, 300, function () use ($request) { // 5 minutes cache
            $query = Post::published()
                ->with(['categories'])
                ->where(function ($q) use ($request) {
                    $search = $request->q;
                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%")
                        ->orWhere('content', 'LIKE', "%{$search}%");
                });

            // Category filtering
            if ($request->has('category')) {
                $query->whereHas('categories', function ($q) use ($request) {
                    $q->where('slug', $request->category);
                });
            }

            $perPage = min($request->get('per_page', 12), 50);
            $posts = $query->paginate($perPage);

            return [
                'data' => PostSummaryResource::collection($posts->items()),
                'meta' => [
                    'query' => $request->q,
                    'category' => $request->category,
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ],
                'links' => [
                    'first' => $posts->url(1),
                    'last' => $posts->url($posts->lastPage()),
                    'prev' => $posts->previousPageUrl(),
                    'next' => $posts->nextPageUrl(),
                ],
            ];
        });

        return response()->json($data)
            ->header('Cache-Control', 'public, max-age=300');
    }

    /**
     * Get posts by date archive
     */
    public function archive(Request $request, int $year, ?int $month = null): JsonResponse
    {
        $cacheKey = "api.posts.archive.{$year}".($month ? ".{$month}" : '');

        $data = Cache::remember($cacheKey, 3600, function () use ($year, $month, $request) { // 1 hour cache
            $query = Post::published()
                ->with(['categories'])
                ->whereYear('published_at', $year);

            if ($month) {
                $query->whereMonth('published_at', $month);
            }

            $perPage = min($request->get('per_page', 12), 50);
            $posts = $query->orderBy('published_at', 'desc')->paginate($perPage);

            return [
                'data' => PostSummaryResource::collection($posts->items()),
                'meta' => [
                    'year' => $year,
                    'month' => $month,
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ],
                'links' => [
                    'first' => $posts->url(1),
                    'last' => $posts->url($posts->lastPage()),
                    'prev' => $posts->previousPageUrl(),
                    'next' => $posts->nextPageUrl(),
                ],
            ];
        });

        return response()->json($data)
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
