<?php

namespace App\Models;

use App\Casts\MetaCast;
use App\Casts\SettingsCast;
use App\Observers\PostObserver;
use App\Traits\HasSlug;
use App\Traits\Models\HasAdvancedScopes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory, HasSlug, HasAdvancedScopes;

    protected $fillable = [
        'published',
        'published_at',
        'title',
        'description',
        'content',
        'position',
        'meta',
        'settings',
        'view_count',
        'priority',
        'author_id',
        'featured_image_caption',
        'excerpt_override',
        'slug',
        'user_id',
        'featured_image',
        'status',
        'is_featured',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'published' => 'boolean',
        'meta' => MetaCast::class,
        'settings' => SettingsCast::class,
        'view_count' => 'integer',
        'priority' => 'integer',
        'is_featured' => 'boolean',
    ];

    protected $appends = [
        'excerpt',
        'reading_time',
        'formatted_published_at',
        'is_trending',
        'is_breaking',
        'author_name',
        'image_url',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug on creating
        static::creating(function ($post) {
            if (empty($post->slug) && ! empty($post->title)) {
                $post->slug = Str::slug($post->title);
            }
        });

        // Auto-increment view count on model retrieval
        static::retrieved(function ($post) {
            if (request()->route() && ! request()->route()->action['uses'] instanceof \Closure) {
                $post->increment('view_count');
            }
        });

        static::observe(PostObserver::class);
    }

    // ====================
    // RELATIONSHIPS
    // ====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'post_category')
            ->withTimestamps();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag')
            ->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments()
    {
        return $this->hasMany(Comment::class)->where('status', 'approved');
    }

    public function slugs()
    {
        return $this->hasMany(PostSlug::class);
    }

    public function analytics()
    {
        return $this->morphMany(Analytics::class, 'trackable');
    }

    /**
     * Translation relationship (EN/LT)
     */
    public function translations()
    {
        return $this->hasMany(PostTranslation::class);
    }

    // ====================
    // SCOPES
    // ====================

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeHighEngagement(Builder $query, int $minViews = 100): Builder
    {
        return $query->where('view_count', '>=', $minViews);
    }

    public function scopeWithExternalUrl(Builder $query): Builder
    {
        return $query->whereNotNull('external_url');
    }

    public function scopeForSlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    // ====================
    // ACCESSORS & MUTATORS
    // ====================

    /**
     * Get the post title from translations
     */
    public function getTitleAttribute(): ?string
    {
        // Check if title is set directly on the model first
        if (isset($this->attributes['title']) && $this->attributes['title']) {
            return $this->attributes['title'];
        }

        // Get from translations
        $translation = $this->translations()->where('locale', app()->getLocale())->where('active', true)->first();

        return $translation?->title;
    }

    /**
     * Get the post description from translations
     */
    public function getDescriptionAttribute(): ?string
    {
        // Check if description is set directly on the model first
        if (isset($this->attributes['description']) && $this->attributes['description']) {
            return $this->attributes['description'];
        }

        // Get from translations
        $translation = $this->translations()->where('locale', app()->getLocale())->where('active', true)->first();

        return $translation?->description;
    }

    /**
     * Get the post content from translations
     */
    public function getContentAttribute(): ?string
    {
        // Check if content is set directly on the model first
        if (isset($this->attributes['content']) && $this->attributes['content']) {
            return $this->attributes['content'];
        }

        // Get from translations
        $translation = $this->translations()->where('locale', app()->getLocale())->where('active', true)->first();

        return $translation?->content;
    }

    public function getExcerptAttribute(): string
    {
        if ($this->excerpt_override) {
            return $this->excerpt_override;
        }

        return Str::limit(strip_tags($this->content), 150);
    }

    public function getReadingTimeAttribute(): int
    {
        $words = Str::wordCount(strip_tags($this->content));
        $minutes = ceil($words / 200);

        return (int) max(1, $minutes);
    }

    public function getFormattedPublishedAtAttribute(): ?string
    {
        return $this->published_at?->format('M d, Y');
    }

    public function getIsFeaturedAttribute(): bool
    {
        return (bool) $this->priority === 1;
    }

    public function getIsTrendingAttribute(): bool
    {
        return (bool) $this->priority === 2;
    }

    public function getIsBreakingAttribute(): bool
    {
        return (bool) $this->priority === 3;
    }

    public function getAuthorNameAttribute(): string
    {
        return $this->user->name ?? 'Unknown Author';
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->featured_image ? asset('storage/' . $this->featured_image) : null;
    }

    public function setSlugAttribute($value): void
    {
        $this->attributes['slug'] = Str::slug($value);
    }

    public function getSlugAttribute(): string
    {
        return $this->attributes['slug'] ?? '';
    }

    public function setMetaAttribute($value): void
    {
        $this->attributes['meta'] = json_encode($value);
    }

    // ====================
    // METHODS
    // ====================

    public function incrementViews(): void
    {
        $this->increment('view_count');
    }

    public function markAsFeatured(bool $featured = true): void
    {
        $this->priority = $featured ? 1 : 0;
        $this->save();
    }

    public function markAsTrending(bool $trending = true): void
    {
        $this->priority = $trending ? 2 : 0;
        $this->save();
    }

    public function markAsBreaking(bool $breaking = true): void
    {
        $this->priority = $breaking ? 3 : 0;
        $this->save();
    }

    public function getRelatedPosts(int $limit = 5)
    {
        return $this->categories()->first()?->posts()
            ->where('id', '!=', $this->id)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    public static function saveTreeFromIds($ids): void
    {
        // Logic to reorder or save based on given IDs
    }

    /**
     * Get the route key for the model
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
