<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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
        'posts_count',
        'articles_count_for_humans',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });

        static::saving(function ($tag) {
            if ($tag->isDirty('name')) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    // ====================
    // RELATIONSHIPS
    // ====================

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tag')
            ->withTimestamps();
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class)
            ->withTimestamps();
    }

    public function publishedArticles(): BelongsToMany
    {
        return $this->articles()
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    // ====================
    // SCOPES
    // ====================

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePopular($query, int $minUsage = 5)
    {
        return $query->where('usage_count', '>=', $minUsage);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    // ====================
    // ACCESSORS
    // ====================

    public function getPostsCountAttribute(): int
    {
        return $this->posts()->count();
    }

    public function getArticlesCountForHumansAttribute(): string
    {
        return $this->articles()->count() . ' articles';
    }

    // ====================
    // METHODS
    // ====================

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function decrementUsage(): void
    {
        $this->decrement('usage_count');
    }

    public function updateUsageCount(): void
    {
        $this->update(['usage_count' => $this->posts()->count()]);
    }

    /**
     * Get published posts for this tag
     */
    public function publishedPosts(): BelongsToMany
    {
        return $this->posts()
            ->where('status', 'published')
            ->orderBy('created_at', 'desc');
    }

    public static function getPopular(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return static::withCount(['articles' => function ($query) {
                $query->where('is_published', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
            }])
            ->having('articles_count', '>', 0)
            ->orderByDesc('articles_count')
            ->limit($limit)
            ->get();
    }

    public function getRelated(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        $articleIds = $this->articles()
            ->where('is_published', true)
            ->pluck('articles.id');

        return static::whereHas('articles', function ($query) use ($articleIds) {
                $query->whereIn('articles.id', $articleIds);
            })
            ->where('id', '!=', $this->id)
            ->withCount('articles')
            ->orderByDesc('articles_count')
            ->limit($limit)
            ->get();
    }

    public function mergeWith(Tag $tag): bool
    {
        // Get all articles from the tag to merge
        $articlesToMerge = $tag->articles()->pluck('articles.id')->toArray();

        // Attach articles to this tag
        $this->articles()->syncWithoutDetaching($articlesToMerge);

        // Delete the merged tag
        return $tag->delete();
    }

    /**
     * Get the route key for the model
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}