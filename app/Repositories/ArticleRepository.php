<?php

namespace App\Repositories;

use App\Models\Article;
use App\Models\Tag;
use Aliziodev\LaravelTaxonomy\Models\Taxonomy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ArticleRepository
{
    protected Article $model;
    protected TaxonomyRepository $taxonomyRepository;

    public function __construct(Article $model, TaxonomyRepository $taxonomyRepository)
    {
        $this->model = $model;
        $this->taxonomyRepository = $taxonomyRepository;
    }

    /**
     * Get all published articles with pagination and taxonomy filtering
     */
    public function getAllPaginated(int $perPage = 12, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['taxonomies' => function ($query) {
                $query->select(['id', 'name', 'slug', 'type', 'meta']);
            }])
            ->select(['id', 'title', 'slug', 'excerpt', 'image', 'published_at', 'reading_time', 'view_count', 'is_featured', 'status'])
            ->whereNull('deleted_at')
            ->latest('published_at');

        // Apply status filter (published/draft)
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply featured filter
        if (isset($filters['featured']) && $filters['featured'] !== '') {
            if ($filters['featured'] === '1' || $filters['featured'] === 1) {
                $query->where('is_featured', true);
            } elseif ($filters['featured'] === '0' || $filters['featured'] === 0) {
                $query->where('is_featured', false);
            }
        }

        // Apply taxonomy filters
        if (!empty($filters['taxonomy'])) {
            $query->withTaxonomySlug($filters['taxonomy']);
        }

        // Apply tag filter
        if (!empty($filters['tag'])) {
            $query->withTaxonomySlug($filters['tag'], 'tag');
        }

        // Apply category filter
        if (!empty($filters['category'])) {
            $query->withTaxonomySlug($filters['category'], 'category');
        }

        // Apply multiple taxonomy filters (OR condition)
        if (!empty($filters['taxonomies'])) {
            $taxonomyIds = is_array($filters['taxonomies']) ? $filters['taxonomies'] : [$filters['taxonomies']];
            $query->withAnyTaxonomies($taxonomyIds);
        }

        // Apply search filter
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get articles by taxonomy with pagination
     */
    public function getByTaxonomy(Taxonomy $taxonomy, int $perPage = 12): LengthAwarePaginator
    {
        return $this->model->with(['taxonomies' => function ($query) {
                $query->select(['id', 'name', 'slug', 'type', 'meta']);
            }])
            ->published()
            ->whereNull('deleted_at')
            ->withTaxonomy($taxonomy->id)
            ->select(['id', 'title', 'slug', 'excerpt', 'image', 'published_at', 'reading_time', 'view_count'])
            ->latest('published_at')
            ->paginate($perPage);
    }

    /**
     * Get articles by taxonomy slug
     */
    public function getByTaxonomySlug(string $slug, string $type = null, int $perPage = 12): LengthAwarePaginator
    {
        return $this->model->with(['taxonomies' => function ($query) {
                $query->select(['id', 'name', 'slug', 'type', 'meta']);
            }])
            ->published()
            ->whereNull('deleted_at')
            ->withTaxonomySlug($slug, $type)
            ->select(['id', 'title', 'slug', 'excerpt', 'image', 'published_at', 'reading_time', 'view_count'])
            ->latest('published_at')
            ->paginate($perPage);
    }

    /**
     * Get articles by multiple taxonomies (OR condition)
     */
    public function getByAnyTaxonomies(array $taxonomyIds, int $perPage = 12): LengthAwarePaginator
    {
        return $this->model->with(['taxonomies' => function ($query) {
                $query->select(['id', 'name', 'slug', 'type', 'meta']);
            }])
            ->published()
            ->whereNull('deleted_at')
            ->withAnyTaxonomies($taxonomyIds)
            ->select(['id', 'title', 'slug', 'excerpt', 'image', 'published_at', 'reading_time', 'view_count'])
            ->latest('published_at')
            ->paginate($perPage);
    }

    /**
     * Get articles by multiple taxonomies (AND condition)
     */
    public function getByAllTaxonomies(array $taxonomyIds, int $perPage = 12): LengthAwarePaginator
    {
        return $this->model->with(['taxonomies' => function ($query) {
                $query->select(['id', 'name', 'slug', 'type', 'meta']);
            }])
            ->published()
            ->whereNull('deleted_at')
            ->withAllTaxonomies($taxonomyIds)
            ->select(['id', 'title', 'slug', 'excerpt', 'image', 'published_at', 'reading_time', 'view_count'])
            ->latest('published_at')
            ->paginate($perPage);
    }

    /**
     * Get articles by tag (backward compatibility)
     */
    public function getByTag(Tag $tag, int $perPage = 12): LengthAwarePaginator
    {
        // Find the corresponding taxonomy for this tag
        $taxonomy = $this->taxonomyRepository->getBySlug($tag->slug, 'tag');
        
        if ($taxonomy) {
            return $this->getByTaxonomy($taxonomy, $perPage);
        }

        // Fallback to original method if taxonomy not found
        return $this->model->with(['tags:id,name,slug,color'])
            ->published()
            ->whereNull('deleted_at')
            ->whereHas('tags', function ($query) use ($tag) {
                $query->where('tags.id', $tag->id);
            })
            ->select(['id', 'title', 'slug', 'excerpt', 'image', 'published_at', 'reading_time', 'view_count'])
            ->latest('published_at')
            ->paginate($perPage);
    }

    /**
     * Get featured articles with taxonomy support
     */
    public function getFeatured(int $limit = 6): Collection
    {
        return Cache::remember('featured_articles_taxonomy', 3600, function () use ($limit) {
            return $this->model->with(['taxonomies' => function ($query) {
                    $query->select(['id', 'name', 'slug', 'type', 'meta']);
                }])
                ->featured()
                ->published()
                ->whereNull('deleted_at')
                ->select(['id', 'title', 'slug', 'excerpt', 'image', 'published_at', 'reading_time', 'view_count'])
                ->latest('published_at')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get latest articles with taxonomy support
     */
    public function getLatest(int $limit = 10): Collection
    {
        return Cache::remember('latest_articles_taxonomy', 1800, function () use ($limit) {
            return $this->model->with(['taxonomies' => function ($query) {
                    $query->select(['id', 'name', 'slug', 'type', 'meta']);
                }])
                ->published()
                ->whereNull('deleted_at')
                ->select(['id', 'title', 'slug', 'excerpt', 'image', 'published_at', 'reading_time', 'view_count'])
                ->latest('published_at')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get popular articles with taxonomy support
     */
    public function getPopular(int $limit = 10): Collection
    {
        return Cache::remember('popular_articles_taxonomy', 3600, function () use ($limit) {
            return $this->model->with(['taxonomies' => function ($query) {
                    $query->select(['id', 'name', 'slug', 'type', 'meta']);
                }])
                ->published()
                ->whereNull('deleted_at')
                ->select(['id', 'title', 'slug', 'excerpt', 'image', 'published_at', 'reading_time', 'view_count'])
                ->orderByDesc('view_count')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Find article by slug with taxonomy support
     */
    public function findBySlug(string $slug): ?Article
    {
        return $this->model->with(['taxonomies' => function ($query) {
                $query->select(['id', 'name', 'slug', 'type', 'meta']);
            }])
            ->where('slug', $slug)
            ->published()
            ->first();
    }

    /**
     * Get related articles based on taxonomies
     */
    public function getRelated(Article $article, int $limit = 6): Collection
    {
        $taxonomyIds = $article->taxonomies->pluck('id')->toArray();

        if (empty($taxonomyIds)) {
            return $this->getLatest($limit);
        }

        return $this->model->with(['taxonomies' => function ($query) {
                $query->select(['id', 'name', 'slug', 'type', 'meta']);
            }])
            ->published()
            ->whereNull('deleted_at')
            ->withAnyTaxonomies($taxonomyIds)
            ->where('id', '!=', $article->id)
            ->select(['id', 'title', 'slug', 'excerpt', 'image', 'published_at', 'reading_time', 'view_count'])
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Search articles with taxonomy support
     */
    public function search(string $term, int $perPage = 12): LengthAwarePaginator
    {
        return $this->model->with(['taxonomies' => function ($query) {
                $query->select(['id', 'name', 'slug', 'type', 'meta']);
            }])
            ->published()
            ->whereNull('deleted_at')
            ->search($term)
            ->select(['id', 'title', 'slug', 'excerpt', 'image', 'published_at', 'reading_time', 'view_count'])
            ->latest('published_at')
            ->paginate($perPage);
    }

    /**
     * Get articles by taxonomy type
     */
    public function getByTaxonomyType(string $type, int $perPage = 12): LengthAwarePaginator
    {
        return $this->model->with(['taxonomies' => function ($query) {
                $query->select(['id', 'name', 'slug', 'type', 'meta']);
            }])
            ->published()
            ->whereNull('deleted_at')
            ->withTaxonomyType($type)
            ->select(['id', 'title', 'slug', 'excerpt', 'image', 'published_at', 'reading_time', 'view_count'])
            ->latest('published_at')
            ->paginate($perPage);
    }

    /**
     * Get articles with hierarchical taxonomy filtering
     */
    public function getByTaxonomyHierarchy(int $parentTaxonomyId, int $perPage = 12): LengthAwarePaginator
    {
        return $this->model->with(['taxonomies' => function ($query) {
                $query->select(['id', 'name', 'slug', 'type', 'meta']);
            }])
            ->published()
            ->whereNull('deleted_at')
            ->withTaxonomyHierarchy($parentTaxonomyId)
            ->select(['id', 'title', 'slug', 'excerpt', 'image', 'published_at', 'reading_time', 'view_count'])
            ->latest('published_at')
            ->paginate($perPage);
    }

    /**
     * Attach taxonomies to article
     */
    public function attachTaxonomies(Article $article, array $taxonomyIds): void
    {
        $article->attachTaxonomies($taxonomyIds);
        $this->clearCache();
    }

    /**
     * Sync taxonomies with article
     */
    public function syncTaxonomies(Article $article, array $taxonomyIds): void
    {
        $article->syncTaxonomies($taxonomyIds);
        $this->clearCache();
    }

    /**
     * Detach taxonomies from article
     */
    public function detachTaxonomies(Article $article, array $taxonomyIds = null): void
    {
        if ($taxonomyIds) {
            $article->detachTaxonomies($taxonomyIds);
        } else {
            $article->detachTaxonomies();
        }
        $this->clearCache();
    }

    /**
     * Get articles count by status
     */
    public function getCountByStatus(): array
    {
        Cache::forget('articles_count_by_status');
        return [
            'total' => $this->model->whereNull('deleted_at')->count(),
            'published' => $this->model->published()->whereNull('deleted_at')->count(),
            'draft' => $this->model->where('is_published', false)->whereNull('deleted_at')->count(),
            'featured' => $this->model->featured()->published()->whereNull('deleted_at')->count(),
        ];
    }

    /**
     * Get articles statistics
     */
    public function getStatistics(): array
    {
        Cache::forget('articles_statistics');
        return [
            'total' => $this->model->whereNull('deleted_at')->count(),
            'published' => $this->model->published()->whereNull('deleted_at')->count(),
            'draft' => $this->model->where('is_published', false)->whereNull('deleted_at')->count(),
            'featured' => $this->model->featured()->published()->whereNull('deleted_at')->count(),
            'total_views' => $this->model->whereNull('deleted_at')->sum('view_count'),
            'average_reading_time' => $this->model->whereNull('deleted_at')->avg('reading_time'),
            'most_viewed' => $this->model->published()->whereNull('deleted_at')->orderByDesc('view_count')->first(['title', 'view_count']),
            'recent_articles' => $this->model->published()->whereNull('deleted_at')->whereNotNull('published_at')->whereNotNull('updated_at')->latest('published_at')->take(5)->get(['title', 'published_at', 'updated_at']),
        ];
    }

    /**
     * Increment article view count
     */
    public function incrementViews(Article $article): bool
    {
        // Use the model's raw SQL method to avoid triggering model events
        // which cause MySQL stored function/trigger conflicts
        return $article->incrementViews();
    }

    /**
     * Get trending articles (high views in recent period)
     */
    public function getTrending(int $days = 7, int $limit = 10): Collection
    {
        $cacheKey = "trending_articles_{$days}_{$limit}";

        return Cache::remember($cacheKey, 1800, function () use ($days, $limit) {
            return $this->model->with(['tags:id,name,slug,color'])
                ->published()
                ->where('published_at', '>=', now()->subDays($days))
                ->select(['id', 'title', 'slug', 'excerpt', 'image', 'published_at', 'reading_time', 'view_count'])
                ->orderByDesc('view_count')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Clear cache
     */
    public function clearCache(): void
    {
        $cacheKeys = [
            'featured_articles',
            'latest_articles',
            'popular_articles',
            'articles_count_by_status',
            'articles_statistics',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Clear trending cache for different periods
        for ($days = 1; $days <= 30; $days++) {
            for ($limit = 5; $limit <= 20; $limit += 5) {
                Cache::forget("trending_articles_{$days}_{$limit}");
            }
        }
    }

    public function create(array $data): Article
    {
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $data['image'] = $this->uploadImage($data['image']);
        }
        $article = $this->model->create($data);
        if (isset($data['tags'])) {
            $article->tags()->sync($data['tags']);
        }
        Cache::flush();
        return $article;
    }

    public function update(Article $article, array $data): Article
    {
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $this->deleteImage($article->image);
            $data['image'] = $this->uploadImage($data['image']);
        }
        $article->update($data);
        if (isset($data['tags'])) {
            $article->tags()->sync($data['tags']);
        }
        Cache::flush();
        return $article;
    }

    public function delete(Article $article): bool
    {
        $this->deleteImage($article->image);
        Cache::flush();
        return $article->forceDelete();
    }

    private function uploadImage($image): string
    {
        return $image->store('articles', 'public');
    }

    private function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    public function bulkUpdateStatus(array $ids, string $status): void
    {
        $this->model->whereIn('id', $ids)->update([
            'status' => $status,
            'is_published' => $status === 'published',
            'published_at' => $status === 'published' ? now() : null,
        ]);
        $this->clearCache();
    }

    public function bulkUpdateFeatured(array $ids, bool $isFeatured): void
    {
        $this->model->whereIn('id', $ids)->update([
            'is_featured' => $isFeatured,
            'status' => $isFeatured ? 'published' : 'draft',
            'is_published' => $isFeatured,
            'published_at' => $isFeatured ? now() : null,
        ]);
        $this->clearCache();
    }

    public function bulkDelete(array $ids): void
    {
        $articles = $this->model->whereIn('id', $ids)->get();
        foreach ($articles as $article) {
            $article->delete();
        }
        $this->clearCache();
    }

    public function getModel()
    {
        return $this->model;
    }
} 