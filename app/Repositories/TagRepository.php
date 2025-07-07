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
        return $this->model->withCount(['articles' => function ($query) {
                $query->published();
            }])
            ->groupBy('tags.id')
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
            return $this->model->withCount(['articles' => function ($query) {
                    $query->published();
                }])
                ->groupBy('tags.id')
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
            return $this->model->withCount(['articles' => function ($query) {
                    $query->published();
                }])
                ->groupBy('tags.id')
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
            return $this->model->withCount(['articles' => function ($query) {
                    $query->published();
                }])
                ->featured()
                ->groupBy('tags.id')
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
        return $this->model->withCount(['articles' => function ($query) {
                $query->published();
            }])
            ->search($term)
            ->groupBy('tags.id')
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
            return $this->model->withCount(['articles' => function ($query) {
                    $query->published();
                }])
                ->groupBy('tags.id')
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
            return $this->model->withCount(['articles' => function ($query) {
                    $query->published();
                }])
                ->groupBy('tags.id')
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
        $tag = $this->model->create($data);
        $this->clearCache();
        return $tag;
    }

    /**
     * Update a tag
     */
    public function update(Tag $tag, array $data): Tag
    {
        $tag->update($data);
        $this->clearCache();
        return $tag->fresh();
    }

    /**
     * Delete a tag
     */
    public function delete(Tag $tag): bool
    {
        $result = $tag->delete();
        $this->clearCache();
        return $result;
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
}
