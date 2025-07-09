<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class CategoryRepository
{
    protected Category $model;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    /**
     * Get all active categories
     */
    public function getAll(): Collection
    {
        return Cache::remember('all_categories', 3600, function () {
            return $this->model->active()
                ->withCount(['articles' => function ($query) {
                    $query->where('is_published', true);
                }])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get categories with pagination
     */
    public function getAllPaginated(int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->active()
            ->withCount(['articles' => function ($query) {
                $query->where('is_published', true);
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Find category by slug
     */
    public function findBySlug(string $slug): ?Category
    {
        return Cache::remember("category_slug_{$slug}", 3600, function () use ($slug) {
            return $this->model->active()
                ->where('slug', $slug)
                ->withCount(['articles' => function ($query) {
                    $query->where('is_published', true);
                }])
                ->first();
        });
    }

    /**
     * Get popular categories (those with most articles)
     */
    public function getPopular(int $limit = 10): Collection
    {
        return Cache::remember("popular_categories_{$limit}", 3600, function () use ($limit) {
            return $this->model->active()
                ->withCount(['articles' => function ($query) {
                    $query->where('is_published', true);
                }])
                ->having('articles_count', '>', 0)
                ->orderByDesc('articles_count')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get featured categories
     */
    public function getFeatured(int $limit = 5): Collection
    {
        return Cache::remember("featured_categories_{$limit}", 3600, function () use ($limit) {
            return $this->model->active()
                ->where('is_featured', true)
                ->withCount(['articles' => function ($query) {
                    $query->where('is_published', true);
                }])
                ->orderBy('sort_order')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Create a new category
     */
    public function create(array $data): Category
    {
        $category = $this->model->create($data);
        $this->clearCache();
        return $category;
    }

    /**
     * Update a category
     */
    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        $this->clearCache();
        return $category;
    }

    /**
     * Delete a category
     */
    public function delete(Category $category): bool
    {
        $result = $category->delete();
        if ($result) {
            $this->clearCache();
        }
        return $result;
    }

    /**
     * Get category statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'active' => $this->model->active()->count(),
            'with_articles' => $this->model->active()
                ->whereHas('articles', function ($query) {
                    $query->where('is_published', true);
                })
                ->count(),
            'most_used' => $this->model->withCount(['articles' => function ($query) {
                    $query->where('is_published', true);
                }])
                ->orderByDesc('articles_count')
                ->first(['name', 'articles_count']),
        ];
    }

    /**
     * Clear cache
     */
    public function clearCache(): void
    {
        $keys = [
            'all_categories',
            'popular_categories_*',
            'featured_categories_*',
            'category_slug_*'
        ];

        foreach ($keys as $key) {
            if (str_contains($key, '*')) {
                // For wildcard patterns, we'd need Redis to clear them properly
                // For now, just clear common ones
                for ($i = 1; $i <= 20; $i++) {
                    Cache::forget(str_replace('*', $i, $key));
                }
            } else {
                Cache::forget($key);
            }
        }
    }

    /**
     * Get model instance
     */
    public function getModel(): Category
    {
        return $this->model;
    }
} 