<?php

namespace App\Models;

use Aliziodev\LaravelTaxonomy\Traits\HasTaxonomy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class Article extends Model
{
    use HasFactory, SoftDeletes, HasTaxonomy;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
        'image_caption',
        'is_featured',
        'is_published',
        'published_at',
        'reading_time',
        'view_count',
        'status',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'reading_time' => 'integer',
        'view_count' => 'integer',
    ];

    protected $dates = [
        'published_at',
        'deleted_at',
    ];

    // Performance optimization: define which relationships to always load
    protected $with = [];

    // Performance optimization: define which attributes to always select
    protected $hidden = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = static::generateUniqueSlug($article->title);
            }

            if (empty($article->reading_time)) {
                $article->reading_time = static::calculateReadingTime($article->content);
            }

            if (empty($article->excerpt)) {
                $article->excerpt = static::generateExcerpt($article->content);
            }

            if ($article->is_published && empty($article->published_at)) {
                $article->published_at = now();
            }

            // Set status based on published state
            if ($article->is_published) {
                $article->status = 'published';
            }
        });

        static::updating(function ($article) {
            if ($article->isDirty('title')) {
                $article->slug = static::generateUniqueSlug($article->title, $article->id);
            }

            if ($article->isDirty('content')) {
                $article->reading_time = static::calculateReadingTime($article->content);
                if (empty($article->excerpt)) {
                    $article->excerpt = static::generateExcerpt($article->content);
                }
            }

            if ($article->isDirty('is_published')) {
                $article->published_at = $article->is_published ? now() : null;
                $article->status = $article->is_published ? 'published' : 'draft';
            }
        });

        static::saved(function ($article) {
            // Clear relevant caches when article is saved
            Cache::flush();
        });

        static::deleted(function ($article) {
            // Clear caches when article is deleted
            Cache::flush();
        });
    }

    /**
     * Relationship: Article belongs to many tags
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'article_tag')
            ->withTimestamps()
            ->orderBy('usage_count', 'desc');
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes for Performance
    |--------------------------------------------------------------------------
    */

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('published_at', 'desc');
    }

    public function scopePopular(Builder $query): Builder
    {
        return $query->orderBy('view_count', 'desc');
    }

    public function scopeWithTags(Builder $query): Builder
    {
        return $query->with(['tags:id,name,slug,color']);
    }

    public function scopeBasicInfo(Builder $query): Builder
    {
        return $query->select([
            'id', 'title', 'slug', 'excerpt', 'image', 
            'is_featured', 'published_at', 'reading_time', 'view_count'
        ]);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($query) use ($term) {
            $query->where('title', 'like', "%{$term}%")
                ->orWhere('excerpt', 'like', "%{$term}%")
                ->orWhere('content', 'like', "%{$term}%")
                ->orWhereHas('tags', function ($query) use ($term) {
                    $query->where('name', 'like', "%{$term}%");
                });
        });
    }

    public function scopeWithAnyTags(Builder $query, array $tagIds): Builder
    {
        return $query->whereHas('tags', function ($query) use ($tagIds) {
            $query->whereIn('tags.id', $tagIds);
        });
    }

    public function scopeWithAllTags(Builder $query, array $tagIds): Builder
    {
        foreach ($tagIds as $tagId) {
            $query->whereHas('tags', function ($q) use ($tagId) {
                $q->where('tags.id', $tagId);
            });
        }
        return $query;
    }

    public function scopeExcept(Builder $query, int $articleId): Builder
    {
        return $query->where('id', '!=', $articleId);
    }

    /*
    |--------------------------------------------------------------------------
    | Static Methods for Testing
    |--------------------------------------------------------------------------
    */

    /**
     * Static search method for tests
     */
    public static function search(string $term)
    {
        return static::query()->search($term);
    }

    /*
    |--------------------------------------------------------------------------
    | Instance Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get related articles based on shared tags with caching
     */
    public function getRelated(int $limit = 6): Collection
    {
        if ($this->tags->isEmpty()) {
            return collect();
        }

        $cacheKey = "article.{$this->id}.related.{$limit}";
        
        return Cache::remember($cacheKey, 3600, function () use ($limit) {
            return static::published()
                ->withTags()
                ->whereHas('tags', function ($query) {
                    $query->whereIn('tags.id', $this->tags->pluck('id'));
                })
                ->except($this->id)
                ->popular()
                ->recent()
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get popular articles in the same categories (tags)
     */
    public function getSimilarPopular(int $limit = 3): Collection
    {
        if ($this->tags->isEmpty()) {
            return collect();
        }

        return static::published()
            ->withTags()
            ->withAnyTags($this->tags->pluck('id')->toArray())
            ->except($this->id)
            ->popular()
            ->limit($limit)
            ->get();
    }

    /**
     * Increment view count efficiently
     * Uses raw SQL to avoid model events and MySQL trigger conflicts
     */
    public function incrementViews(): bool
    {
        // Use raw SQL with DB::update for maximum performance and to avoid
        // any model events that could trigger MySQL stored function conflicts
        return (bool) DB::update(
            'UPDATE articles SET view_count = view_count + 1 WHERE id = ? AND deleted_at IS NULL',
            [$this->id]
        );
    }

    /**
     * Publish article
     */
    public function publish(): bool
    {
        return $this->update([
            'is_published' => true,
            'published_at' => now(),
            'status' => 'published',
        ]);
    }

    /**
     * Unpublish article
     */
    public function unpublish(): bool
    {
        return $this->update([
            'is_published' => false,
            'published_at' => null,
            'status' => 'draft',
        ]);
    }

    /**
     * Feature article
     */
    public function feature(): bool
    {
        return $this->update(['is_featured' => true]);
    }

    /**
     * Unfeature article
     */
    public function unfeature(): bool
    {
        return $this->update(['is_featured' => false]);
    }

    /*
    |--------------------------------------------------------------------------
    | Static Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate reading time based on content
     */
    public static function calculateReadingTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        $wordsPerMinute = 200; // Average reading speed
        
        return max(1, (int) ceil($wordCount / $wordsPerMinute));
    }

    /**
     * Generate excerpt from content
     */
    public static function generateExcerpt(string $content, int $length = 500): string
    {
        $cleanContent = strip_tags($content);
        return Str::limit($cleanContent, $length);
    }

    /**
     * Generate unique slug
     */
    public static function generateUniqueSlug(string $title, ?int $id = null): string
    {
        $slug = Str::slug($title);
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

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getImageUrlAttribute(): string
    {
        if (!$this->image) {
            return asset('images/placeholder-article.jpg');
        }

        // If it's already a full URL, return as-is
        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }

    public function getReadingTimeForHumansAttribute(): string
    {
        return $this->reading_time . ' min read';
    }

    public function getPublishedAtForHumansAttribute(): string
    {
        return $this->published_at?->diffForHumans() ?? '';
    }

    public function getPublishedAtFormattedAttribute(): string
    {
        return $this->published_at?->format('M j, Y') ?? '';
    }

    public function getExcerptShortAttribute(): string
    {
        return Str::limit($this->excerpt, 150);
    }

    public function getMetaTitleAttribute($value): string
    {
        return $value ?: $this->title;
    }

    public function getMetaDescriptionAttribute($value): string
    {
        return $value ?: $this->excerpt_short;
    }

    /*
    |--------------------------------------------------------------------------
    | Performance Optimizations
    |--------------------------------------------------------------------------
    */

    /**
     * Get the attributes that should be cast to native types.
     */
    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'reading_time' => 'integer',
            'view_count' => 'integer',
        ];
    }
}
