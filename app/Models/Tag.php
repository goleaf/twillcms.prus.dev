<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class Tag extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'usage_count',
        'is_featured',
        'description',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'usage_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'articles_count_text',
        'color_with_alpha',
    ];

    // Performance optimization: define which relationships to always load
    protected $with = [];

    /*
    |--------------------------------------------------------------------------
    | Model Events
    |--------------------------------------------------------------------------
    */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = static::generateUniqueSlug($tag->name);
            }

            // Set default color if not provided
            if (empty($tag->color)) {
                $tag->color = static::getRandomColor();
            }
        });

        static::updating(function ($tag) {
            if ($tag->isDirty('name')) {
                $tag->slug = static::generateUniqueSlug($tag->name, $tag->id);
            }
        });

        static::saved(function ($tag) {
            // Clear relevant caches when tag is saved
            Cache::flush();
        });

        static::deleted(function ($tag) {
            // Clear caches when tag is deleted
            Cache::flush();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Tag belongs to many articles
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_tag')
            ->withTimestamps()
            ->orderBy('published_at', 'desc');
    }

    /**
     * Only published articles for this tag
     */
    public function publishedArticles(): BelongsToMany
    {
        return $this->articles()
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Featured articles for this tag
     */
    public function featuredArticles(): BelongsToMany
    {
        return $this->publishedArticles()
            ->where('is_featured', true);
    }

    /**
     * Recent articles for this tag
     */
    public function recentArticles(): BelongsToMany
    {
        return $this->publishedArticles()
            ->orderBy('published_at', 'desc');
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopePopular(Builder $query, int $minUsage = 1): Builder
    {
        return $query->where('usage_count', '>=', $minUsage)
            ->orderBy('usage_count', 'desc');
    }

    public function scopeWithArticleCount(Builder $query): Builder
    {
        return $query->withCount(['articles as published_articles_count' => function ($query) {
            $query->where('is_published', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now());
        }]);
    }

    public function scopeHasArticles(Builder $query): Builder
    {
        return $query->whereHas('articles', function ($query) {
            $query->where('is_published', true)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now());
        });
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function scopeByUsage(Builder $query, string $direction = 'desc'): Builder
    {
        return $query->orderBy('usage_count', $direction);
    }

    public function scopeAlphabetical(Builder $query): Builder
    {
        return $query->orderBy('name', 'asc');
    }

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get popular tags with article counts
     */
    public static function getPopular(int $limit = 10): Collection
    {
        $cacheKey = "tags.popular.{$limit}";
        
        return Cache::remember($cacheKey, 3600, function () use ($limit) {
            return static::withArticleCount()
                ->hasArticles()
                ->orderByDesc('published_articles_count')
                ->orderBy('name')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get featured tags
     */
    public static function getFeatured(int $limit = 5): Collection
    {
        $cacheKey = "tags.featured.{$limit}";
        
        return Cache::remember($cacheKey, 3600, function () use ($limit) {
            return static::featured()
                ->withArticleCount()
                ->hasArticles()
                ->orderByDesc('published_articles_count')
                ->orderBy('name')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get all tags for tag cloud
     */
    public static function getForCloud(int $limit = 50): Collection
    {
        $cacheKey = "tags.cloud.{$limit}";
        
        return Cache::remember($cacheKey, 3600, function () use ($limit) {
            return static::withArticleCount()
                ->hasArticles()
                ->orderByDesc('published_articles_count')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Search tags with caching
     */
    public static function searchTags(string $term, int $limit = 20): Collection
    {
        $cacheKey = "tags.search." . md5($term) . ".{$limit}";
        
        return Cache::remember($cacheKey, 1800, function () use ($term, $limit) {
            return static::search($term)
                ->withArticleCount()
                ->hasArticles()
                ->orderByDesc('published_articles_count')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Generate unique slug
     */
    public static function generateUniqueSlug(string $name, ?int $id = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        $query = static::where('slug', $slug);
        if ($id) {
            $query->where('id', '!=', $id);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
            $query = static::where('slug', $slug);
            if ($id) {
                $query->where('id', '!=', $id);
            }
        }

        return $slug;
    }

    /**
     * Get random color for new tags
     */
    public static function getRandomColor(): string
    {
        $colors = [
            '#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6',
            '#EC4899', '#14B8A6', '#F97316', '#6366F1', '#84CC16',
            '#06B6D4', '#F43F5E', '#8B5A2B', '#374151', '#7C2D12',
        ];

        return $colors[array_rand($colors)];
    }

    /*
    |--------------------------------------------------------------------------
    | Instance Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get related tags based on shared articles
     */
    public function getRelated(int $limit = 5): Collection
    {
        $cacheKey = "tag.{$this->id}.related.{$limit}";
        
        return Cache::remember($cacheKey, 3600, function () use ($limit) {
            $articleIds = $this->publishedArticles()->pluck('articles.id');

            if ($articleIds->isEmpty()) {
                return collect();
            }

            return static::whereHas('articles', function ($query) use ($articleIds) {
                    $query->whereIn('articles.id', $articleIds);
                })
                ->where('id', '!=', $this->id)
                ->withArticleCount()
                ->orderByDesc('published_articles_count')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get articles for this tag with pagination
     */
    public function getArticlesPaginated(int $perPage = 12, array $filters = [])
    {
        $query = $this->publishedArticles();

        // Apply filters
        if (isset($filters['featured']) && $filters['featured']) {
            $query->where('is_featured', true);
        }

        if (isset($filters['sort'])) {
            switch ($filters['sort']) {
                case 'popular':
                    $query->orderBy('view_count', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('published_at', 'asc');
                    break;
                default: // 'newest'
                    $query->orderBy('published_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('published_at', 'desc');
        }

        return $query->paginate($perPage);
    }

    /**
     * Merge this tag with another tag
     */
    public function mergeWith(Tag $tag): bool
    {
        // Get all articles from the tag to merge
        $articlesToMerge = $tag->articles()->pluck('articles.id')->toArray();

        // Attach articles to this tag (without duplicates)
        $this->articles()->syncWithoutDetaching($articlesToMerge);

        // Update usage count
        $this->updateUsageCount();

        // Delete the merged tag
        return $tag->delete();
    }

    /**
     * Update usage count based on actual article relationships
     */
    public function updateUsageCount(): void
    {
        $count = $this->publishedArticles()->count();
        $this->update(['usage_count' => $count]);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getArticlesCountTextAttribute(): string
    {
        $count = $this->usage_count;
        
        if ($count === 0) {
            return 'No articles';
        } elseif ($count === 1) {
            return '1 article';
        } else {
            return number_format($count) . ' articles';
        }
    }

    public function getColorWithAlphaAttribute(): string
    {
        // Convert hex color to rgba with 0.1 alpha for backgrounds
        $hex = ltrim($this->color, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        return "rgba({$r}, {$g}, {$b}, 0.1)";
    }

    public function getUrlAttribute(): string
    {
        return route('tags.show', $this->slug);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if tag has any published articles
     */
    public function hasPublishedArticles(): bool
    {
        return $this->usage_count > 0;
    }

    /**
     * Get tag size for tag cloud (1-5 scale)
     */
    public function getCloudSize(int $maxUsage): int
    {
        if ($maxUsage === 0) {
            return 1;
        }

        $percentage = ($this->usage_count / $maxUsage) * 100;
        
        if ($percentage >= 80) return 5;
        if ($percentage >= 60) return 4;
        if ($percentage >= 40) return 3;
        if ($percentage >= 20) return 2;
        
        return 1;
    }
}