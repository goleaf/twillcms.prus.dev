<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Enterprise Category Repository Implementation
 * 
 * High-performance repository for category management with intelligent caching,
 * hierarchical navigation, and enterprise-level optimization features.
 */
class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    /**
     * CategoryRepository constructor.
     *
     * @param Category $model
     */
    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    /**
     * Get all active categories with post counts
     *
     * @param array $relations
     * @return Collection
     */
    public function getAllActive(array $relations = []): Collection
    {
        $cacheKey = $this->generateCacheKey('all_active', [
            'relations' => $relations
        ]);

        return $this->getCachedOrExecute($cacheKey, function () use ($relations) {
            return $this->optimizeQuery(
                $this->model->active()
                    ->with($relations)
                    ->withCount(['posts' => function ($query) {
                        $query->published();
                    }])
                    ->orderBy('position')
                    ->orderBy('name')
            )->get();
        });
    }

    /**
     * Get hierarchical category tree for navigation
     *
     * @param array $relations
     * @return Collection
     */
    public function getHierarchical(array $relations = []): Collection
    {
        $cacheKey = $this->generateCacheKey('hierarchical', [
            'relations' => $relations
        ]);

        return $this->getCachedOrExecute($cacheKey, function () use ($relations) {
            return $this->optimizeQuery(
                $this->model->active()
                    ->with(array_merge($relations, ['children' => function ($query) {
                        $query->active()->orderBy('position')->orderBy('name');
                    }]))
                    ->whereNull('parent_id')
                    ->withCount(['posts' => function ($query) {
                        $query->published();
                    }])
                    ->orderBy('position')
                    ->orderBy('name')
            )->get();
        });
    }

    /**
     * Get categories for navigation menu
     *
     * @param int $limit
     * @return Collection
     */
    public function getForNavigation(int $limit = 10): Collection
    {
        $cacheKey = $this->generateCacheKey('navigation', [
            'limit' => $limit
        ]);

        return $this->getCachedOrExecute($cacheKey, function () use ($limit) {
            return $this->optimizeQuery(
                $this->model->active()
                    ->where('show_in_navigation', true)
                    ->withCount(['posts' => function ($query) {
                        $query->published();
                    }])
                    ->orderBy('position')
                    ->orderBy('name')
            )->limit($limit)->get();
        });
    }

    /**
     * Get category with all subcategories
     *
     * @param int $categoryId
     * @param array $relations
     * @return Category|null
     */
    public function getWithChildren(int $categoryId, array $relations = []): ?Category
    {
        $cacheKey = $this->generateCacheKey('with_children', [
            'category_id' => $categoryId,
            'relations' => $relations
        ]);

        return $this->getCachedOrExecute($cacheKey, function () use ($categoryId, $relations) {
            return $this->optimizeQuery(
                $this->model->active()
                    ->with(array_merge($relations, [
                        'children' => function ($query) {
                            $query->active()
                                ->withCount(['posts' => function ($q) {
                                    $q->published();
                                }])
                                ->orderBy('position')
                                ->orderBy('name');
                        }
                    ]))
                    ->withCount(['posts' => function ($query) {
                        $query->published();
                    }])
            )->find($categoryId);
        });
    }

    /**
     * Get parent categories (breadcrumb path)
     *
     * @param int $categoryId
     * @return Collection
     */
    public function getParentPath(int $categoryId): Collection
    {
        $cacheKey = $this->generateCacheKey('parent_path', [
            'category_id' => $categoryId
        ]);

        return $this->getCachedOrExecute($cacheKey, function () use ($categoryId) {
            $path = collect();
            $category = $this->model->find($categoryId);

            while ($category && $category->parent) {
                $path->prepend($category->parent);
                $category = $category->parent;
            }

            return $path;
        });
    }

    /**
     * Get most popular categories by post count
     *
     * @param int $limit
     * @param array $relations
     * @return Collection
     */
    public function getMostPopular(int $limit = 10, array $relations = []): Collection
    {
        $cacheKey = $this->generateCacheKey('most_popular', [
            'limit' => $limit,
            'relations' => $relations
        ]);

        return $this->getCachedOrExecute($cacheKey, function () use ($limit, $relations) {
            return $this->optimizeQuery(
                $this->model->active()
                    ->with($relations)
                    ->withCount(['posts' => function ($query) {
                        $query->published();
                    }])
                    ->having('posts_count', '>', 0)
                    ->orderBy('posts_count', 'desc')
                    ->orderBy('name')
            )->limit($limit)->get();
        });
    }

    /**
     * Search categories by name or description
     *
     * @param string $query
     * @param array $relations
     * @return Collection
     */
    public function search(string $query, array $relations = []): Collection
    {
        $cacheKey = $this->generateCacheKey('search', [
            'query' => $query,
            'relations' => $relations
        ]);

        return $this->getCachedOrExecute($cacheKey, function () use ($query, $relations) {
            return $this->optimizeQuery(
                $this->model->active()
                    ->where(function ($q) use ($query) {
                        $q->where('name', 'LIKE', "%{$query}%")
                          ->orWhere('description', 'LIKE', "%{$query}%");
                    })
                    ->with($relations)
                    ->withCount(['posts' => function ($q) {
                        $q->published();
                    }])
                    ->orderBy('name')
            )->get();
        });
    }

    /**
     * Get category statistics for dashboard
     *
     * @return array
     */
    public function getStatistics(): array
    {
        $cacheKey = $this->generateCacheKey('statistics');

        return $this->getCachedOrExecute($cacheKey, function () {
            $totalCategories = $this->model->count();
            $activeCategories = $this->model->active()->count();
            $categoriesWithPosts = $this->model->active()
                ->whereHas('posts', function ($query) {
                    $query->published();
                })
                ->count();

            $mostPopular = $this->model->active()
                ->withCount(['posts' => function ($query) {
                    $query->published();
                }])
                ->orderBy('posts_count', 'desc')
                ->first(['id', 'name', 'posts_count']);

            return [
                'total_categories' => $totalCategories,
                'active_categories' => $activeCategories,
                'inactive_categories' => $totalCategories - $activeCategories,
                'categories_with_posts' => $categoriesWithPosts,
                'empty_categories' => $activeCategories - $categoriesWithPosts,
                'most_popular_category' => $mostPopular,
                'average_posts_per_category' => $categoriesWithPosts > 0 
                    ? round(DB::table('posts')->where('published', true)->count() / $categoriesWithPosts, 2)
                    : 0,
            ];
        }, 3600); // Cache for 1 hour
    }

    /**
     * Get categories for sitemap generation
     *
     * @return Collection
     */
    public function getSitemapData(): Collection
    {
        $cacheKey = $this->generateCacheKey('sitemap');

        return $this->getCachedOrExecute($cacheKey, function () {
            return $this->model->active()
                ->select(['id', 'slug', 'updated_at'])
                ->whereHas('posts', function ($query) {
                    $query->published();
                })
                ->orderBy('position')
                ->orderBy('name')
                ->get();
        }, 21600); // Cache for 6 hours
    }

    /**
     * Get categories for RSS feed
     *
     * @return Collection
     */
    public function getForRssFeed(): Collection
    {
        $cacheKey = $this->generateCacheKey('rss_feed');

        return $this->getCachedOrExecute($cacheKey, function () {
            return $this->model->active()
                ->where('include_in_rss', true)
                ->whereHas('posts', function ($query) {
                    $query->published();
                })
                ->withCount(['posts' => function ($query) {
                    $query->published();
                }])
                ->orderBy('position')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Warm up critical caches
     *
     * @return bool
     */
    public function warmCache(): bool
    {
        try {
            // Warm navigation categories
            $this->getForNavigation();
            
            // Warm hierarchical structure
            $this->getHierarchical(['children']);
            
            // Warm popular categories
            $this->getMostPopular(10);
            
            // Warm statistics
            $this->getStatistics();
            
            // Warm sitemap data
            $this->getSitemapData();

            return true;
        } catch (\Exception $e) {
            \Log::error('Category cache warming failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update category position for sorting
     *
     * @param int $categoryId
     * @param int $position
     * @return bool
     */
    public function updatePosition(int $categoryId, int $position): bool
    {
        $result = $this->model->where('id', $categoryId)
            ->update(['position' => $position]);

        if ($result) {
            $this->clearRelatedCaches();
        }

        return $result > 0;
    }

    /**
     * Clear related caches when category is updated
     *
     * @return void
     */
    private function clearRelatedCaches(): void
    {
        $cacheKeys = [
            'all_active',
            'hierarchical',
            'navigation',
            'most_popular',
            'statistics',
            'sitemap',
            'rss_feed'
        ];

        foreach ($cacheKeys as $key) {
            $this->clearCache($this->generateCacheKey($key));
        }
    }
} 