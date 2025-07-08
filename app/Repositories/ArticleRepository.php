<?php

namespace App\Repositories;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ArticleRepository
{
    protected Article $model;

    public function __construct(Article $model)
    {
        $this->model = $model;
    }

    /**
     * Get all published articles with pagination
     */
    public function getAllPaginated(int $perPage = 12, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['tags:id,name,slug,color'])
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

        // Apply tag filter
        if (!empty($filters['tag'])) {
            $query->whereHas('tags', function ($q) use ($filters) {
                $q->where('slug', $filters['tag']);
            });
        }

        // Apply search filter
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // No published() scope by default for admin; only filter by status if set
        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get articles by tag with pagination
     */
    public function getByTag(Tag $tag, int $perPage = 12): LengthAwarePaginator
    {
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
     * Get featured articles
     */
    public function getFeatured(int $limit = 6): Collection
    {
        return Cache::remember('featured_articles', 3600, function () use ($limit) {
            return $this->model->with(['tags:id,name,slug,color'])
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
     * Get latest articles
     */
    public function getLatest(int $limit = 10): Collection
    {
        return Cache::remember('latest_articles', 1800, function () use ($limit) {
            return $this->model->with(['tags:id,name,slug,color'])
                ->published()
                ->whereNull('deleted_at')
                ->select(['id', 'title', 'slug', 'excerpt', 'image', 'published_at', 'reading_time', 'view_count'])
                ->latest('published_at')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get popular articles (by view count)
     */
    public function getPopular(int $limit = 10): Collection
    {
        return Cache::remember('popular_articles', 3600, function () use ($limit) {
            return $this->model->with(['tags:id,name,slug,color'])
                ->published()
                ->whereNull('deleted_at')
                ->select(['id', 'title', 'slug', 'excerpt', 'image', 'published_at', 'reading_time', 'view_count'])
                ->orderByDesc('view_count')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Find article by slug
     */
    public function findBySlug(string $slug): ?Article
    {
        return $this->model->with(['tags:id,name,slug,color'])
            ->where('slug', $slug)
            ->published()
            ->first();
    }

    /**
     * Get related articles based on tags
     */
    public function getRelated(Article $article, int $limit = 6): Collection
    {
        $tagIds = $article->tags->pluck('id')->toArray();

        if (empty($tagIds)) {
            return $this->getLatest($limit);
        }

        return $this->model->with(['tags:id,name,slug,color'])
            ->published()
            ->whereNull('deleted_at')
            ->whereHas('tags', function ($query) use ($tagIds) {
                $query->whereIn('tags.id', $tagIds);
            })
            ->where('id', '!=', $article->id)
            ->select(['id', 'title', 'slug', 'excerpt', 'image', 'published_at', 'reading_time', 'view_count'])
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Search articles
     */
    public function search(string $term, int $perPage = 12): LengthAwarePaginator
    {
        return $this->model->with(['tags:id,name,slug,color'])
            ->published()
            ->whereNull('deleted_at')
            ->search($term)
            ->select(['id', 'title', 'slug', 'excerpt', 'image', 'published_at', 'reading_time', 'view_count'])
            ->latest('published_at')
            ->paginate($perPage);
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
        return $article->increment('view_count');
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