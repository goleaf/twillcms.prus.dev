<?php

namespace App\Repositories\Interfaces;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Post Repository Interface
 * 
 * Defines the contract for post-specific repository operations
 * including news content management, performance optimization,
 * and smart caching capabilities.
 * 
 * @package App\Repositories\Interfaces
 */
interface PostRepositoryInterface extends RepositoryInterface
{
    /**
     * Get all published posts
     *
     * @return Collection
     */
    public function published(): Collection;

    /**
     * Get posts by category
     *
     * @param int $categoryId
     * @return Collection
     */
    public function byCategory(int $categoryId): Collection;

    /**
     * Get popular posts based on views
     *
     * @param int $limit
     * @param int $days
     * @return Collection
     */
    public function popular(int $limit = 10, int $days = 7): Collection;

    /**
     * Get recent posts
     *
     * @param int $limit
     * @return Collection
     */
    public function recent(int $limit = 10): Collection;

    /**
     * Search posts by title and content
     *
     * @param string $query
     * @return Collection
     */
    public function search(string $query): Collection;

    /**
     * Get featured posts
     *
     * @param int $limit
     * @return Collection
     */
    public function featured(int $limit = 5): Collection;

    /**
     * Get trending posts (high engagement recently)
     *
     * @param int $limit
     * @param int $hours
     * @return Collection
     */
    public function trending(int $limit = 10, int $hours = 24): Collection;

    /**
     * Get posts with optimized media loading
     *
     * @return Collection
     */
    public function withOptimizedMedia(): Collection;

    /**
     * Get related posts for a given post
     *
     * @param int $postId
     * @param int $limit
     * @return Collection
     */
    public function relatedPosts(int $postId, int $limit = 5): Collection;

    /**
     * Get posts by slug
     *
     * @param string $slug
     * @return Post|null
     */
    public function findBySlug(string $slug): ?Post;

    /**
     * Get paginated published posts
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function publishedPaginated(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get posts for sitemap generation
     *
     * @return Collection
     */
    public function forSitemap(): Collection;

    /**
     * Get archived posts (older than specified days)
     *
     * @param int $days
     * @return Collection
     */
    public function archived(int $days = 365): Collection;

    /**
     * Update post view count
     *
     * @param int $postId
     * @return bool
     */
    public function incrementViews(int $postId): bool;

    /**
     * Get posts statistics
     *
     * @return array
     */
    public function getStatistics(): array;

    /**
     * Preload posts for cache warming
     *
     * @param array $postIds
     * @return Collection
     */
    public function preloadForCache(array $postIds): Collection;
} 