<?php

namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TagRepository
{
    protected Tag $model;

    public function __construct(Tag $model)
    {
        $this->model = $model;
    }

    /**
     * Get all tags with pagination
     */
    public function getAllPaginated(int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->select('tags.id', 'tags.name', 'tags.slug', 'tags.color', 'tags.usage_count', 'tags.is_featured', 'tags.description', 'tags.created_at', 'tags.updated_at', 'tags.deleted_at')
            ->withCount(['articles' => function ($query) {
                $query->published();
            }])
            ->groupBy('tags.id', 'tags.name', 'tags.slug', 'tags.color', 'tags.usage_count', 'tags.is_featured', 'tags.description', 'tags.created_at', 'tags.updated_at', 'tags.deleted_at')
            ->orderBy('name')
            ->having('articles_count', '>', 0)
            ->paginate($perPage);
    }

    /**
     * Get all tags
     */
    public function getAll(): Collection
    {
        return Cache::remember('all_tags', 3600, function () {
            return $this->model->select('tags.id', 'tags.name', 'tags.slug', 'tags.color', 'tags.usage_count', 'tags.is_featured', 'tags.description', 'tags.created_at', 'tags.updated_at', 'tags.deleted_at')
                ->withCount(['articles' => function ($query) {
                    $query->published();
                }])
                ->groupBy('tags.id', 'tags.name', 'tags.slug', 'tags.color', 'tags.usage_count', 'tags.is_featured', 'tags.description', 'tags.created_at', 'tags.updated_at', 'tags.deleted_at')
                ->having('articles_count', '>', 0)
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Find tag by slug
     */
    public function findBySlug(string $slug): ?Tag
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Get popular tags
     */
    public function getPopular(int $limit = 10): Collection
    {
        return Cache::remember('popular_tags', 3600, function () use ($limit) {
            return $this->model->select('tags.id', 'tags.name', 'tags.slug', 'tags.color', 'tags.usage_count', 'tags.is_featured', 'tags.description', 'tags.created_at', 'tags.updated_at', 'tags.deleted_at')
                ->withCount(['articles' => function ($query) {
                    $query->published();
                }])
                ->groupBy('tags.id', 'tags.name', 'tags.slug', 'tags.color', 'tags.usage_count', 'tags.is_featured', 'tags.description', 'tags.created_at', 'tags.updated_at', 'tags.deleted_at')
                ->having('articles_count', '>', 0)
                ->orderByDesc('articles_count')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get featured tags
     */
    public function getFeatured(int $limit = 5): Collection
    {
        return Cache::remember('featured_tags', 3600, function () use ($limit) {
            return $this->model->select('tags.id', 'tags.name', 'tags.slug', 'tags.color', 'tags.usage_count', 'tags.is_featured', 'tags.description', 'tags.created_at', 'tags.updated_at', 'tags.deleted_at')
                ->withCount(['articles' => function ($query) {
                    $query->published();
                }])
                ->featured()
                ->groupBy('tags.id', 'tags.name', 'tags.slug', 'tags.color', 'tags.usage_count', 'tags.is_featured', 'tags.description', 'tags.created_at', 'tags.updated_at', 'tags.deleted_at')
                ->having('articles_count', '>', 0)
                ->orderByDesc('articles_count')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Search tags
     */
    public function search(string $term, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->select('tags.id', 'tags.name', 'tags.slug', 'tags.color', 'tags.usage_count', 'tags.is_featured', 'tags.description', 'tags.created_at', 'tags.updated_at', 'tags.deleted_at')
            ->withCount(['articles' => function ($query) {
                $query->published();
            }])
            ->search($term)
            ->groupBy('tags.id', 'tags.name', 'tags.slug', 'tags.color', 'tags.usage_count', 'tags.is_featured', 'tags.description', 'tags.created_at', 'tags.updated_at', 'tags.deleted_at')
            ->having('articles_count', '>', 0)
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Get tags with article counts
     */
    public function getWithArticleCounts(): Collection
    {
        return Cache::remember('tags_with_counts', 1800, function () {
            return $this->model->select('tags.id', 'tags.name', 'tags.slug', 'tags.color', 'tags.usage_count', 'tags.is_featured', 'tags.description', 'tags.created_at', 'tags.updated_at', 'tags.deleted_at')
                ->withCount(['articles' => function ($query) {
                    $query->published();
                }])
                ->groupBy('tags.id', 'tags.name', 'tags.slug', 'tags.color', 'tags.usage_count', 'tags.is_featured', 'tags.description', 'tags.created_at', 'tags.updated_at', 'tags.deleted_at')
                ->having('articles_count', '>', 0)
                ->orderByDesc('articles_count')
                ->get();
        });
    }

    /**
     * Get tag cloud data
     */
    public function getTagCloud(int $limit = 30): Collection
    {
        return Cache::remember('tag_cloud', 3600, function () use ($limit) {
            return $this->model->select('tags.id', 'tags.name', 'tags.slug', 'tags.color', 'tags.usage_count', 'tags.is_featured', 'tags.description', 'tags.created_at', 'tags.updated_at', 'tags.deleted_at')
                ->withCount(['articles' => function ($query) {
                    $query->published();
                }])
                ->groupBy('tags.id', 'tags.name', 'tags.slug', 'tags.color', 'tags.usage_count', 'tags.is_featured', 'tags.description', 'tags.created_at', 'tags.updated_at', 'tags.deleted_at')
                ->having('articles_count', '>', 0)
                ->orderByDesc('articles_count')
                ->limit($limit)
                ->get()
                ->map(function ($tag) {
                    $tag->weight = $this->calculateTagWeight($tag->articles_count);
                    return $tag;
                });
        });
    }

    /**
     * Get related tags based on articles
     */
    public function getRelated(Tag $tag, int $limit = 5): Collection
    {
        $articleIds = $tag->articles()
            ->published()
            ->pluck('articles.id');

        if ($articleIds->isEmpty()) {
            return $this->getPopular($limit);
        }

        return $this->model->whereHas('articles', function ($query) use ($articleIds) {
                $query->whereIn('articles.id', $articleIds);
            })
            ->where('id', '!=', $tag->id)
            ->withCount(['articles' => function ($query) {
                $query->published();
            }])
            ->orderByDesc('articles_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get tags statistics
     */
    public function getStatistics(): array
    {
        Cache::forget('tags_statistics');
        return [
            'total' => $this->model->count(),
            'active' => $this->model->whereHas('articles', function ($query) {
                $query->published();
            })->count(),
            'featured' => $this->model->featured()->count(),
            'most_used' => $this->model->withCount(['articles' => function ($query) {
                $query->published();
            }])->orderByDesc('articles_count')->first(['name', 'articles_count']),
            'recent_tags' => $this->model->whereNotNull('created_at')->latest()->take(5)->get(['name', 'created_at']),
        ];
    }

    /**
     * Get trending tags (tags with recent articles)
     */
    public function getTrending(int $days = 7, int $limit = 10): Collection
    {
        $cacheKey = "trending_tags_{$days}_{$limit}";

        return Cache::remember($cacheKey, 1800, function () use ($days, $limit) {
            return $this->model->whereHas('articles', function ($query) use ($days) {
                    $query->published()
                        ->where('published_at', '>=', now()->subDays($days));
                })
                ->withCount(['articles' => function ($query) use ($days) {
                    $query->published()
                        ->where('published_at', '>=', now()->subDays($days));
                }])
                ->orderByDesc('articles_count')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Create a new tag
     */
    public function create(array $data): Tag
    {
        // Enforce uniqueness for name and slug
        if (Tag::where('name', $data['name'])->exists()) {
            throw new \Exception(__('validation.tag_name_unique'));
        }
        if (Tag::where('slug', $data['slug'])->exists()) {
            throw new \Exception(__('validation.tag_slug_unique'));
        }
        $tag = $this->model->create($data);
        $this->clearCache();
        return $tag;
    }

    /**
     * Update a tag
     */
    public function update(Tag $tag, array $data): Tag
    {
        // Enforce uniqueness for name and slug (ignore current tag)
        if (isset($data['name']) && Tag::where('name', $data['name'])->where('id', '!=', $tag->id)->exists()) {
            throw new \Exception(__('validation.tag_name_unique'));
        }
        if (isset($data['slug']) && Tag::where('slug', $data['slug'])->where('id', '!=', $tag->id)->exists()) {
            throw new \Exception(__('validation.tag_slug_unique'));
        }
        $tag->update($data);
        $this->clearCache();
        return $tag;
    }

    /**
     * Delete a tag (soft delete by default)
     */
    public function delete(Tag $tag): bool
    {
        $this->clearCache();
        return $tag->delete(); // Always soft delete
    }

    /**
     * Merge tags
     */
    public function merge(Tag $sourceTag, Tag $targetTag): bool
    {
        $result = $sourceTag->mergeWith($targetTag);
        $this->clearCache();
        return $result;
    }

    /**
     * Calculate tag weight for tag cloud
     */
    private function calculateTagWeight(int $count): int
    {
        // Simple weight calculation (1-5 scale)
        $maxCount = $this->model->withCount('articles')->max('articles_count') ?: 1;
        $weight = ceil(($count / $maxCount) * 5);
        return max(1, min(5, $weight));
    }

    /**
     * Clear cache
     */
    public function clearCache(): void
    {
        $cacheKeys = [
            'all_tags',
            'popular_tags',
            'featured_tags',
            'tags_with_counts',
            'tag_cloud',
            'tags_statistics',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Clear trending cache for different periods
        for ($days = 1; $days <= 30; $days++) {
            for ($limit = 5; $limit <= 20; $limit += 5) {
                Cache::forget("trending_tags_{$days}_{$limit}");
            }
        }
    }

    /**
     * Bulk delete tags (soft delete by default)
     */
    public function bulkDelete(array $ids): void
    {
        $tags = $this->model->whereIn('id', $ids)->get();
        foreach ($tags as $tag) {
            $this->delete($tag);
        }
        $this->clearCache();
    }

    public function bulkUpdateFeatured(array $ids, bool $isFeatured): void
    {
        $this->model->whereIn('id', $ids)->update(['is_featured' => $isFeatured]);
        $this->clearCache();
    }

    /**
     * Get all tags with pagination and filters (admin)
     */
    public function getAllPaginatedWithFilters(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $perPage = request('per_page', $perPage); // Allow override via request
        $query = $this->model->select('tags.id', 'tags.name', 'tags.slug', 'tags.color', 'tags.usage_count', 'tags.is_featured', 'tags.description', 'tags.created_at', 'tags.updated_at', 'tags.deleted_at')
            ->orderBy('name');

        // Case-insensitive search on name and description
        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
            });
        }
        // Featured filter (accepts string or boolean)
        if (isset($filters['featured']) && $filters['featured'] !== '') {
            if ($filters['featured'] === '1' || $filters['featured'] === 1 || $filters['featured'] === true) {
                $query->where('is_featured', true);
            } elseif ($filters['featured'] === '0' || $filters['featured'] === 0 || $filters['featured'] === false) {
                $query->where('is_featured', false);
            }
        }
        return $query->paginate($perPage)->withQueryString();
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getPopularWithPostCounts($limit = 20)
    {
        return $this->model->withCount(['posts' => function ($query) {
            $query->where('status', 'published');
        }])
        ->orderBy('posts_count', 'desc')
        ->take($limit)
        ->get();
    }

    /**
     * Update usage_count for a tag based on attached articles
     */
    public function updateUsageCount(Tag $tag): void
    {
        $tag->usage_count = $tag->articles()->count();
        $tag->save();
    }

    /**
     * Attach a tag to an article and update usage count
     */
    public function attachToArticle(Tag $tag, $articleId): void
    {
        $tag->articles()->attach($articleId);
        $tag->usage_count = $tag->articles()->count();
        $tag->save();
        $this->clearCache();
    }

    /**
     * Detach a tag from an article and update usage count
     */
    public function detachFromArticle(Tag $tag, $articleId): void
    {
        $tag->articles()->detach($articleId);
        $tag->usage_count = $tag->articles()->count();
        $tag->save();
        $this->clearCache();
    }
}
