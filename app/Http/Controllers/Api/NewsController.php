<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * News API Controller
 *
 * Demonstrates enterprise-level API implementation using the PostService
 * and repository pattern for high-performance news content delivery.
 */
class NewsController extends Controller
{
    protected PostService $postService;

    /**
     * NewsController constructor.
     */
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Get homepage content
     */
    public function homepage(): JsonResponse
    {
        try {
            $data = $this->postService->getHomepageContent();

            return response()->json([
                'success' => true,
                'data' => $data,
                'meta' => [
                    'cached' => true,
                    'performance' => 'optimized',
                    'timestamp' => now()->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load homepage content',
                'error' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get news listing
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $data = $this->postService->getNewsListing($perPage);

            return response()->json([
                'success' => true,
                'data' => $data,
                'meta' => [
                    'per_page' => $perPage,
                    'performance' => 'optimized',
                    'timestamp' => now()->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load news listing',
                'error' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get posts by category
     */
    public function category(Request $request, int $categoryId): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $data = $this->postService->getCategoryContent($categoryId, $perPage);

            return response()->json([
                'success' => true,
                'data' => $data,
                'meta' => [
                    'category_id' => $categoryId,
                    'per_page' => $perPage,
                    'performance' => 'optimized',
                    'timestamp' => now()->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load category content',
                'error' => app()->environment('local') ? $e->getMessage() : null,
            ], 404);
        }
    }

    /**
     * Get single post details
     */
    public function show(int $postId): JsonResponse
    {
        try {
            $data = $this->postService->getPostDetails($postId);

            return response()->json([
                'success' => true,
                'data' => $data,
                'meta' => [
                    'post_id' => $postId,
                    'performance' => 'optimized',
                    'timestamp' => now()->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found',
                'error' => app()->environment('local') ? $e->getMessage() : null,
            ], 404);
        }
    }

    /**
     * Search posts
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q', '');
            $perPage = $request->get('per_page', 15);

            if (empty($query)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search query is required',
                ], 400);
            }

            $data = $this->postService->searchPosts($query, $perPage);

            return response()->json([
                'success' => true,
                'data' => $data,
                'meta' => [
                    'query' => $query,
                    'per_page' => $perPage,
                    'performance' => 'optimized',
                    'timestamp' => now()->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get archive by year/month
     */
    public function archive(Request $request, int $year, ?int $month = null): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $data = $this->postService->getArchiveContent($year, $month, $perPage);

            return response()->json([
                'success' => true,
                'data' => $data,
                'meta' => [
                    'year' => $year,
                    'month' => $month,
                    'per_page' => $perPage,
                    'performance' => 'optimized',
                    'timestamp' => now()->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load archive',
                'error' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get dashboard analytics (admin endpoint)
     */
    public function analytics(): JsonResponse
    {
        try {
            // This would typically have authentication/authorization middleware
            $data = $this->postService->getDashboardAnalytics();

            return response()->json([
                'success' => true,
                'data' => $data,
                'meta' => [
                    'cached' => true,
                    'performance' => 'optimized',
                    'timestamp' => now()->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load analytics',
                'error' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Warm up caches (admin endpoint)
     */
    public function warmCache(): JsonResponse
    {
        try {
            // This would typically have authentication/authorization middleware
            $success = $this->postService->warmAllCaches();

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Caches warmed successfully' : 'Cache warming failed',
                'meta' => [
                    'timestamp' => now()->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cache warming failed',
                'error' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get sitemap data
     */
    public function sitemap(): JsonResponse
    {
        try {
            $data = $this->postService->getSitemapData();

            return response()->json([
                'success' => true,
                'data' => $data,
                'meta' => [
                    'cached' => true,
                    'performance' => 'optimized',
                    'timestamp' => now()->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate sitemap',
                'error' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get RSS feed data
     */
    public function rss(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 20);
            $data = $this->postService->getRssFeedData($limit);

            return response()->json([
                'success' => true,
                'data' => $data,
                'meta' => [
                    'limit' => $limit,
                    'cached' => true,
                    'performance' => 'optimized',
                    'timestamp' => now()->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate RSS feed',
                'error' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
