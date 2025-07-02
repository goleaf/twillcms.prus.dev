<?php

namespace App\Repositories\Interfaces;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

/**
 * Category Repository Interface
 * 
 * Defines the contract for category-specific repository operations
 * including news categorization, post relationships, and 
 * performance-optimized category management.
 * 
 * @package App\Repositories\Interfaces
 */
interface CategoryRepositoryInterface extends RepositoryInterface
{
    /**
     * Get all active categories
     *
     * @return Collection
     */
    public function active(): Collection;

    /**
     * Get categories with post counts
     *
     * @return Collection
     */
    public function withPostCounts(): Collection;

    /**
     * Get categories with published post counts
     *
     * @return Collection
     */
    public function withPublishedPostCounts(): Collection;

    /**
     * Find category by slug
     *
     * @param string $slug
     * @return Category|null
     */
    public function findBySlug(string $slug): ?Category;

    /**
     * Get popular categories (by post count)
     *
     * @param int $limit
     * @return Collection
     */
    public function popular(int $limit = 10): Collection;

    /**
     * Get categories with recent posts
     *
     * @param int $days
     * @return Collection
     */
    public function withRecentPosts(int $days = 30): Collection;

    /**
     * Get parent categories only
     *
     * @return Collection
     */
    public function parents(): Collection;

    /**
     * Get child categories for a parent
     *
     * @param int $parentId
     * @return Collection
     */
    public function children(int $parentId): Collection;

    /**
     * Get categories for navigation menu
     *
     * @return Collection
     */
    public function forNavigation(): Collection;

    /**
     * Get categories with their hierarchical structure
     *
     * @return Collection
     */
    public function hierarchical(): Collection;

    /**
     * Get categories for sitemap
     *
     * @return Collection
     */
    public function forSitemap(): Collection;

    /**
     * Search categories by name
     *
     * @param string $query
     * @return Collection
     */
    public function search(string $query): Collection;

    /**
     * Get category statistics
     *
     * @return array
     */
    public function getStatistics(): array;

    /**
     * Get categories ordered by position
     *
     * @return Collection
     */
    public function ordered(): Collection;

    /**
     * Get featured categories
     *
     * @param int $limit
     * @return Collection
     */
    public function featured(int $limit = 5): Collection;

    /**
     * Update category position
     *
     * @param int $categoryId
     * @param int $position
     * @return bool
     */
    public function updatePosition(int $categoryId, int $position): bool;

    /**
     * Get categories with trending posts
     *
     * @param int $hours
     * @return Collection
     */
    public function withTrendingPosts(int $hours = 24): Collection;
} 