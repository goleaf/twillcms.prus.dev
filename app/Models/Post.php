<?php

namespace App\Models;

use App\Casts\MetaCast;
use App\Casts\SettingsCast;
use App\Traits\Models\HasAdvancedScopes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasAdvancedScopes, HasFactory;

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
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'published' => 'boolean',
        'meta' => MetaCast::class,
        'settings' => SettingsCast::class,
        'view_count' => 'integer',
        'priority' => 'integer',
    ];

    protected $appends = [
        'excerpt',
        'reading_time',
        'formatted_published_at',
        'is_featured',
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
    }

    // ====================
    // RELATIONSHIPS
    // ====================

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'post_category');
    }

    public function slugs()
    {
        return $this->hasMany(PostSlug::class);
    }

    /**
     * Translation relationship (EN/LT)
     */
    public function translations()
    {
        return $this->hasMany(PostTranslation::class);
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

    /**
     * Get excerpt - use override or auto-generate
     */
    public function getExcerptAttribute(): string
    {
        $override = $this->excerpt_override;

        if (! empty($override)) {
            return $override;
        }

        $content = strip_tags($this->content ?? '');
        $words = explode(' ', $content);

        if (count($words) <= 30) {
            return $content;
        }

        return implode(' ', array_slice($words, 0, 30)).'...';
    }

    /**
     * Calculate reading time based on content
     */
    public function getReadingTimeAttribute(): int
    {
        // Check for override first
        $override = $this->settings['reading_time_override'] ?? null;

        if ($override && is_numeric($override)) {
            return (int) $override;
        }

        $wordCount = str_word_count(strip_tags($this->content ?? ''));
        $wordsPerMinute = 200; // Average reading speed

        return max(1, ceil($wordCount / $wordsPerMinute));
    }

    /**
     * Get formatted published date
     */
    public function getFormattedPublishedAtAttribute(): ?string
    {
        return $this->published_at?->format('F j, Y');
    }

    /**
     * Check if post is featured
     */
    public function getIsFeaturedAttribute(): bool
    {
        return $this->settings['is_featured'] ?? false;
    }

    /**
     * Check if post is trending
     */
    public function getIsTrendingAttribute(): bool
    {
        return $this->settings['is_trending'] ?? false;
    }

    /**
     * Check if post is breaking news
     */
    public function getIsBreakingAttribute(): bool
    {
        return $this->settings['is_breaking'] ?? false;
    }

    /**
     * Get author name with fallback
     */
    public function getAuthorNameAttribute(): string
    {
        // Check for override first
        $override = $this->settings['author_override'] ?? null;

        if (! empty($override)) {
            return $override;
        }

        return 'Anonymous'; // No user system
    }

    /**
     * Get main image URL (simplified without TwillCMS)
     */
    public function getImageUrlAttribute(): ?string
    {
        // For now, return a placeholder or implement your own image handling
        return $this->settings['image_url'] ?? null;
    }

    /**
     * Handle slug generation properly
     */
    public function setSlugAttribute($value): void
    {
        if (is_string($value)) {
            $this->attributes['slug'] = $value;
        }
    }

    /**
     * Get slug ensuring we have a fallback
     */
    public function getSlugAttribute(): string
    {
        // If we have a direct slug attribute, use it
        if (! empty($this->attributes['slug'])) {
            return $this->attributes['slug'];
        }

        // Check slugs relationship
        $slug = $this->slugs()->where('locale', app()->getLocale())->where('active', true)->first();
        if ($slug) {
            return $slug->slug;
        }

        // Generate from title as fallback
        return Str::slug($this->title ?? 'post-'.$this->id);
    }

    /**
     * Set meta data properly
     */
    public function setMetaAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['meta'] = json_encode($value);
        } elseif (is_string($value)) {
            $this->attributes['meta'] = $value;
        }
    }

    /**
     * Increment view count safely
     */
    public function incrementViews(): void
    {
        $this->increment('view_count');
    }

    /**
     * Mark post as featured
     */
    public function markAsFeatured(bool $featured = true): void
    {
        $settings = $this->settings ?? [];
        $settings['is_featured'] = $featured;
        $this->settings = $settings;
        $this->save();
    }

    /**
     * Mark post as trending
     */
    public function markAsTrending(bool $trending = true): void
    {
        $settings = $this->settings ?? [];
        $settings['is_trending'] = $trending;
        $this->settings = $settings;
        $this->save();
    }

    /**
     * Mark post as breaking news
     */
    public function markAsBreaking(bool $breaking = true): void
    {
        $settings = $this->settings ?? [];
        $settings['is_breaking'] = $breaking;
        $this->settings = $settings;
        $this->save();
    }

    /**
     * Get related posts
     */
    public function getRelatedPosts(int $limit = 5)
    {
        $categoryIds = $this->categories()->pluck('id');

        return static::published()
            ->whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('id', $categoryIds);
            })
            ->where('id', '!=', $this->id)
            ->limit($limit)
            ->get();
    }

    /**
     * Simplified tree saving for position
     */
    public static function saveTreeFromIds($ids): void
    {
        foreach ($ids as $position => $id) {
            static::where('id', $id)->update(['position' => $position]);
        }
    }

    /**
     * Scope high engagement posts
     */
    public function scopeHighEngagement(Builder $query, int $minViews = 100): Builder
    {
        return $query->where('view_count', '>=', $minViews);
    }

    /**
     * Scope posts with external URLs
     */
    public function scopeWithExternalUrl(Builder $query): Builder
    {
        return $query->whereNotNull('settings->external_url');
    }

    /**
     * Get post by slug
     */
    public function scopeForSlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }
}
