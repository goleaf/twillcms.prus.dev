<?php

namespace App\Services;

use App\Repositories\Interfaces\PostRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Post Service Layer
 * 
 * Provides business logic for news content management,
 * orchestrating repository interactions and implementing
 * enterprise-level content operations.
 */
class PostService
{
    protected PostRepositoryInterface $postRepository;
    protected CategoryRepositoryInterface $categoryRepository;

    /**
     * PostService constructor.
     *
     * @param PostRepositoryInterface $postRepository
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        PostRepositoryInterface $postRepository,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->postRepository = $postRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get homepage content with featured and latest posts
     *
     * @return array
     */
    public function getHomepageContent(): array
    {
        $cacheKey = 'homepage_content';

        return Cache::remember($cacheKey, 900, function () { // 15 minutes
            return [
                'featured_posts' => $this->postRepository->getFeatured(6, ['category', 'media']),
                'latest_posts' => $this->postRepository->getPublished(12, ['category', 'media']),
                'popular_posts' => $this->postRepository->getPopular(5, ['category']),
                'trending_posts' => $this->postRepository->getTrending(7, 5, ['category']),
                'categories' => $this->categoryRepository->getForNavigation(8),
            ];
        });
    }

    /**
     * Get news listing page content
     *
     * @param int $perPage
     * @return array
     */
    public function getNewsListing(int $perPage = 15): array
    {
        return [
            'posts' => $this->postRepository->getPublished($perPage, ['category', 'media']),
            'featured_posts' => $this->postRepository->getFeatured(3, ['category', 'media']),
            'popular_posts' => $this->postRepository->getPopular(5, ['category']),
            'categories' => $this->categoryRepository->getAllActive(['posts']),
        ];
    }

    /**
     * Get category page content
     *
     * @param int $categoryId
     * @param int $perPage
     * @return array
     */
    public function getCategoryContent(int $categoryId, int $perPage = 15): array
    {
        $category = $this->categoryRepository->getWithChildren($categoryId, ['posts']);
        
        if (!$category) {
            throw new \Exception('Category not found');
        }

        return [
            'category' => $category,
            'posts' => $this->postRepository->getByCategory($categoryId, $perPage, ['category', 'media']),
            'subcategories' => $category->children ?? collect(),
            'breadcrumb' => $this->categoryRepository->getParentPath($categoryId),
            'related_categories' => $this->categoryRepository->getMostPopular(5),
        ];
    }

    /**
     * Get single post with related content
     *
     * @param int $postId
     * @return array
     */
    public function getPostDetails(int $postId): array
    {
        $post = $this->postRepository->findWithRelations($postId, ['category', 'media', 'author']);
        
        if (!$post || !$post->published) {
            throw new \Exception('Post not found or not published');
        }

        // Increment view count asynchronously
        $this->incrementPostViews($postId);

        return [
            'post' => $post,
            'related_posts' => $this->postRepository->getRelated($post, 6, ['category', 'media']),
            'category' => $post->category,
            'breadcrumb' => $this->categoryRepository->getParentPath($post->category_id),
            'popular_in_category' => $this->postRepository->getByCategory($post->category_id, 5, ['category']),
        ];
    }

    /**
     * Search posts with enhanced results
     *
     * @param string $query
     * @param int $perPage
     * @return array
     */
    public function searchPosts(string $query, int $perPage = 15): array
    {
        if (strlen($query) < 3) {
            return [
                'posts' => collect(),
                'total' => 0,
                'suggestions' => $this->getSearchSuggestions(),
                'popular_posts' => $this->postRepository->getPopular(5, ['category']),
            ];
        }

        return [
            'posts' => $this->postRepository->search($query, $perPage, ['category', 'media']),
            'categories' => $this->categoryRepository->search($query),
            'popular_posts' => $this->postRepository->getPopular(5, ['category']),
            'query' => $query,
        ];
    }

    /**
     * Get archive content by year/month
     *
     * @param int $year
     * @param int|null $month
     * @param int $perPage
     * @return array
     */
    public function getArchiveContent(int $year, ?int $month = null, int $perPage = 15): array
    {
        return [
            'posts' => $this->postRepository->getArchive($year, $month, $perPage, ['category', 'media']),
            'archive_info' => [
                'year' => $year,
                'month' => $month,
                'month_name' => $month ? date('F', mktime(0, 0, 0, $month, 1)) : null,
            ],
            'archive_navigation' => $this->getArchiveNavigation(),
            'popular_posts' => $this->postRepository->getPopular(5, ['category']),
        ];
    }

    /**
     * Get dashboard analytics data
     *
     * @return array
     */
    public function getDashboardAnalytics(): array
    {
        $cacheKey = 'dashboard_analytics';

        return Cache::remember($cacheKey, 3600, function () { // 1 hour
            return [
                'post_statistics' => $this->postRepository->getStatistics(),
                'category_statistics' => $this->categoryRepository->getStatistics(),
                'recent_posts' => $this->postRepository->getPublished(5, ['category']),
                'top_categories' => $this->categoryRepository->getMostPopular(5),
                'performance_metrics' => $this->getPerformanceMetrics(),
            ];
        });
    }

    /**
     * Warm up all critical caches
     *
     * @return bool
     */
    public function warmAllCaches(): bool
    {
        try {
            Log::info('Starting cache warming for PostService');

            // Warm repository caches
            $this->postRepository->warmCache();
            $this->categoryRepository->warmCache();

            // Warm service-level caches
            $this->getHomepageContent();
            $this->getDashboardAnalytics();

            Log::info('Cache warming completed successfully');
            return true;
        } catch (\Exception $e) {
            Log::error('Cache warming failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get sitemap data for SEO
     *
     * @return array
     */
    public function getSitemapData(): array
    {
        return [
            'posts' => $this->postRepository->getSitemapData(),
            'categories' => $this->categoryRepository->getSitemapData(),
            'last_modified' => now(),
        ];
    }

    /**
     * Get RSS feed data
     *
     * @param int $limit
     * @return array
     */
    public function getRssFeedData(int $limit = 20): array
    {
        return [
            'posts' => $this->postRepository->getPublished($limit, ['category', 'media']),
            'categories' => $this->categoryRepository->getForRssFeed(),
            'site_info' => [
                'title' => config('app.name'),
                'description' => 'Latest news and articles',
                'url' => config('app.url'),
                'last_build_date' => now(),
            ],
        ];
    }

    /**
     * Increment post views asynchronously
     *
     * @param int $postId
     * @return void
     */
    private function incrementPostViews(int $postId): void
    {
        try {
            // Queue job for async processing or direct increment
            $this->postRepository->incrementViews($postId);
        } catch (\Exception $e) {
            Log::warning('Failed to increment views for post ' . $postId . ': ' . $e->getMessage());
        }
    }

    /**
     * Get search suggestions when query is too short
     *
     * @return Collection
     */
    private function getSearchSuggestions(): Collection
    {
        return $this->categoryRepository->getMostPopular(8);
    }

    /**
     * Get archive navigation data
     *
     * @return array
     */
    private function getArchiveNavigation(): array
    {
        $cacheKey = 'archive_navigation';

        return Cache::remember($cacheKey, 3600, function () {
            // This would typically come from a dedicated query
            // For now, return basic navigation structure
            return [
                'years' => range(date('Y'), date('Y') - 5),
                'months' => [
                    1 => 'January', 2 => 'February', 3 => 'March',
                    4 => 'April', 5 => 'May', 6 => 'June',
                    7 => 'July', 8 => 'August', 9 => 'September',
                    10 => 'October', 11 => 'November', 12 => 'December'
                ],
            ];
        });
    }

    /**
     * Get performance metrics for dashboard
     *
     * @return array
     */
    private function getPerformanceMetrics(): array
    {
        return [
            'cache_hit_ratio' => $this->calculateCacheHitRatio(),
            'average_response_time' => $this->getAverageResponseTime(),
            'total_page_views' => $this->getTotalPageViews(),
            'bounce_rate' => $this->getBounceRate(),
        ];
    }

    /**
     * Calculate cache hit ratio (placeholder implementation)
     *
     * @return float
     */
    private function calculateCacheHitRatio(): float
    {
        // This would integrate with actual cache metrics
        return 0.85; // 85% hit ratio placeholder
    }

    /**
     * Get average response time (placeholder implementation)
     *
     * @return float
     */
    private function getAverageResponseTime(): float
    {
        // This would integrate with performance monitoring
        return 0.045; // 45ms placeholder
    }

    /**
     * Get total page views (placeholder implementation)
     *
     * @return int
     */
    private function getTotalPageViews(): int
    {
        // This would integrate with analytics
        return $this->postRepository->getStatistics()['total_views'] ?? 0;
    }

    /**
     * Get bounce rate (placeholder implementation)
     *
     * @return float
     */
    private function getBounceRate(): float
    {
        // This would integrate with analytics
        return 0.35; // 35% bounce rate placeholder
    }
} 