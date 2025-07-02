<?php

namespace App\Repositories\Eloquent;

use App\Models\Post;
use App\Repositories\Interfaces\PostRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Enterprise Post Repository Implementation
 * 
 * High-performance repository for post management with intelligent caching,
 * query optimization, and enterprise-level features for news content.
 */
class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    /**
     * PostRepository constructor.
     *
     * @param Post $model
     */
    public function __construct(Post $model)
    {
        $this->model = $model;
    }

    /**
     * Get published posts with performance optimization
     *
     * @param int $perPage
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function getPublished(int $perPage = 15, array $relations = []): LengthAwarePaginator
    {
        $cacheKey = $this->generateCacheKey('published', [
            'per_page' => $perPage,
            'relations' => $relations,
            'page' => request('page', 1)
        ]);

        return $this->getCachedOrExecute($cacheKey, function () use ($perPage, $relations) {
            return $this->optimizeQuery(
                $this->model->published()
                    ->with($relations)
                    ->orderBy('published_at', 'desc')
            )->paginate($perPage);
        });
    }

    /**
     * Get featured posts for homepage
     *
     * @param int $limit
     * @param array $relations
     * @return Collection
     */
    public function getFeatured(int $limit = 6, array $relations = []): Collection
    {
        $cacheKey = $this->generateCacheKey('featured', [
            'limit' => $limit,
            'relations' => $relations
        ]);

        return $this->getCachedOrExecute($cacheKey, function () use ($limit, $relations) {
            return $this->optimizeQuery(
                $this->model->published()
                    ->featured()
                    ->with($relations)
                    ->orderBy('published_at', 'desc')
            )->limit($limit)->get();
        });
    }

    /**
     * Get popular posts based on views
     *
     * @param int $limit
     * @param array $relations
     * @return Collection
     */
    public function getPopular(int $limit = 10, array $relations = []): Collection
    {
        $cacheKey = $this->generateCacheKey('popular', [
            'limit' => $limit,
            'relations' => $relations
        ]);

        return $this->getCachedOrExecute($cacheKey, function () use ($limit, $relations) {
            return $this->optimizeQuery(
                $this->model->published()
                    ->with($relations)
                    ->orderBy('view_count', 'desc')
                    ->orderBy('published_at', 'desc')
            )->limit($limit)->get();
        });
    }

    /**
     * Get trending posts (popular in recent time)
     *
     * @param int $days
     * @param int $limit
     * @param array $relations
     * @return Collection
     */
    public function getTrending(int $days = 7, int $limit = 10, array $relations = []): Collection
    {
        $cacheKey = $this->generateCacheKey('trending', [
            'days' => $days,
            'limit' => $limit,
            'relations' => $relations
        ]);

        return $this->getCachedOrExecute($cacheKey, function () use ($days, $limit, $relations) {
            $since = Carbon::now()->subDays($days);
            
            return $this->optimizeQuery(
                $this->model->published()
                    ->where('published_at', '>=', $since)
                    ->with($relations)
                    ->orderBy('view_count', 'desc')
                    ->orderBy('published_at', 'desc')
            )->limit($limit)->get();
        });
    }

    /**
     * Get posts by category with performance optimization
     *
     * @param int $categoryId
     * @param int $perPage
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function getByCategory(int $categoryId, int $perPage = 15, array $relations = []): LengthAwarePaginator
    {
        $cacheKey = $this->generateCacheKey('by_category', [
            'category_id' => $categoryId,
            'per_page' => $perPage,
            'relations' => $relations,
            'page' => request('page', 1)
        ]);

        return $this->getCachedOrExecute($cacheKey, function () use ($categoryId, $perPage, $relations) {
            return $this->optimizeQuery(
                $this->model->published()
                    ->where('category_id', $categoryId)
                    ->with($relations)
                    ->orderBy('published_at', 'desc')
            )->paginate($perPage);
        });
    }

    /**
     * Search posts with optimized full-text search
     *
     * @param string $query
     * @param int $perPage
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function search(string $query, int $perPage = 15, array $relations = []): LengthAwarePaginator
    {
        $cacheKey = $this->generateCacheKey('search', [
            'query' => $query,
            'per_page' => $perPage,
            'relations' => $relations,
            'page' => request('page', 1)
        ]);

        return $this->getCachedOrExecute($cacheKey, function () use ($query, $perPage, $relations) {
            return $this->optimizeQuery(
                $this->model->published()
                    ->where(function ($q) use ($query) {
                        $q->where('title', 'LIKE', "%{$query}%")
                          ->orWhere('description', 'LIKE', "%{$query}%")
                          ->orWhere('content', 'LIKE', "%{$query}%");
                    })
                    ->with($relations)
                    ->orderBy('published_at', 'desc')
            )->paginate($perPage);
        });
    }

    /**
     * Get related posts based on category and tags
     *
     * @param Post $post
     * @param int $limit
     * @param array $relations
     * @return Collection
     */
    public function getRelated(Post $post, int $limit = 5, array $relations = []): Collection
    {
        $cacheKey = $this->generateCacheKey('related', [
            'post_id' => $post->id,
            'limit' => $limit,
            'relations' => $relations
        ]);

        return $this->getCachedOrExecute($cacheKey, function () use ($post, $limit, $relations) {
            return $this->optimizeQuery(
                $this->model->published()
                    ->where('id', '!=', $post->id)
                    ->where('category_id', $post->category_id)
                    ->with($relations)
                    ->orderBy('published_at', 'desc')
            )->limit($limit)->get();
        });
    }

    /**
     * Get posts archive by year and month
     *
     * @param int $year
     * @param int|null $month
     * @param int $perPage
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function getArchive(int $year, ?int $month = null, int $perPage = 15, array $relations = []): LengthAwarePaginator
    {
        $cacheKey = $this->generateCacheKey('archive', [
            'year' => $year,
            'month' => $month,
            'per_page' => $perPage,
            'relations' => $relations,
            'page' => request('page', 1)
        ]);

        return $this->getCachedOrExecute($cacheKey, function () use ($year, $month, $perPage, $relations) {
            $query = $this->model->published()->whereYear('published_at', $year);
            
            if ($month) {
                $query->whereMonth('published_at', $month);
            }

            return $this->optimizeQuery(
                $query->with($relations)->orderBy('published_at', 'desc')
            )->paginate($perPage);
        });
    }

    /**
     * Get post statistics for dashboard
     *
     * @return array
     */
    public function getStatistics(): array
    {
        $cacheKey = $this->generateCacheKey('statistics');

        return $this->getCachedOrExecute($cacheKey, function () {
            return [
                'total_posts' => $this->model->count(),
                'published_posts' => $this->model->published()->count(),
                'draft_posts' => $this->model->draft()->count(),
                'featured_posts' => $this->model->published()->featured()->count(),
                'total_views' => $this->model->sum('view_count'),
                'average_views' => $this->model->avg('view_count'),
                'posts_this_month' => $this->model->published()
                    ->whereMonth('published_at', Carbon::now()->month)
                    ->whereYear('published_at', Carbon::now()->year)
                    ->count(),
                'most_viewed_post' => $this->model->published()
                    ->orderBy('view_count', 'desc')
                    ->first(['id', 'title', 'view_count']),
            ];
        }, 3600); // Cache for 1 hour
    }

    /**
     * Increment view count for a post
     *
     * @param int $postId
     * @return bool
     */
    public function incrementViews(int $postId): bool
    {
        $result = $this->model->where('id', $postId)->increment('view_count');
        
        // Clear related caches
        $this->clearRelatedCaches($postId);
        
        return $result > 0;
    }

    /**
     * Warm up critical caches
     *
     * @return bool
     */
    public function warmCache(): bool
    {
        try {
            // Warm featured posts
            $this->getFeatured(6, ['category', 'media']);
            
            // Warm popular posts
            $this->getPopular(10, ['category', 'media']);
            
            // Warm trending posts
            $this->getTrending(7, 10, ['category', 'media']);
            
            // Warm statistics
            $this->getStatistics();
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Post cache warming failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate sitemap data
     *
     * @return Collection
     */
    public function getSitemapData(): Collection
    {
        $cacheKey = $this->generateCacheKey('sitemap');

        return $this->getCachedOrExecute($cacheKey, function () {
            return $this->model->published()
                ->select(['id', 'slug', 'updated_at', 'published_at'])
                ->orderBy('published_at', 'desc')
                ->get();
        }, 21600); // Cache for 6 hours
    }

    /**
     * Clear related caches when post is updated
     *
     * @param int $postId
     * @return void
     */
    private function clearRelatedCaches(int $postId): void
    {
        $cacheKeys = [
            'published',
            'featured',
            'popular',
            'trending',
            'statistics',
            'sitemap',
            "related.{$postId}"
        ];

        foreach ($cacheKeys as $key) {
            $this->clearCache($this->generateCacheKey($key));
        }
    }
} 