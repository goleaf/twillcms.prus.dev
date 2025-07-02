<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasBlocks;
use A17\Twill\Models\Behaviors\HasFiles;
use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Behaviors\HasPosition;
use A17\Twill\Models\Behaviors\HasRelated;
use A17\Twill\Models\Behaviors\HasRevisions;
use A17\Twill\Models\Behaviors\HasSlug;
use A17\Twill\Models\Behaviors\HasTranslation;
use A17\Twill\Models\Behaviors\Sortable;
use A17\Twill\Models\Model;
use App\Casts\MetaCast;
use App\Casts\SettingsCast;
use App\Traits\Models\HasAdvancedScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Post extends Model implements Sortable
{
    use HasBlocks, HasFactory, HasFiles, HasMedias, HasPosition, HasRelated, HasRevisions, HasSlug, HasTranslation, HasAdvancedScopes;

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
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'published' => 'boolean',
        'meta' => MetaCast::class,
        'settings' => SettingsCast::class,
        'view_count' => 'integer',
        'priority' => 'integer',
    ];

    public $translatedAttributes = [
        'title',
        'description',
        'content',
        'active',
        'excerpt_override',
        'featured_image_caption',
    ];

    // Disable automatic slug generation to avoid array conversion issues
    public $slugAttributes = [];

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

    public $mediasParams = [
        'cover' => [
            'default' => [
                [
                    'name' => 'default',
                    'ratio' => 16 / 9,
                ],
            ],
        ],
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-increment view count on model retrieval
        static::retrieved(function ($post) {
            if (request()->route() && !request()->route()->action['uses'] instanceof \Closure) {
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

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // ====================
    // ACCESSORS & MUTATORS
    // ====================

    /**
     * Get excerpt - use override or auto-generate
     */
    public function getExcerptAttribute(): string
    {
        $override = $this->excerpt_override;
        
        if (!empty($override)) {
            return $override;
        }

        $content = strip_tags($this->content ?? '');
        $words = explode(' ', $content);
        
        if (count($words) <= 30) {
            return $content;
        }
        
        return implode(' ', array_slice($words, 0, 30)) . '...';
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
        
        if (!empty($override)) {
            return $override;
        }

        return $this->author->name ?? 'Anonymous';
    }

    /**
     * Get the image URL attribute
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image('cover', 'default');
    }

    /**
     * Set slug attribute with validation
     */
    public function setSlugAttribute($value): void
    {
        if (is_string($value) && !empty($value)) {
            $this->attributes['slug'] = \Str::slug($value);
        }
    }

    /**
     * Auto-generate meta description if not provided
     */
    public function setMetaAttribute($value): void
    {
        if (is_array($value)) {
            // Auto-generate meta description if not provided
            if (empty($value['description'])) {
                $value['description'] = $this->excerpt;
            }
            
            // Auto-generate keywords from categories and content
            if (empty($value['keywords'])) {
                $keywords = $this->categories->pluck('title')->toArray();
                $value['keywords'] = implode(', ', $keywords);
            }
        }
        
        $this->attributes['meta'] = is_array($value) ? json_encode($value) : $value;
    }

    // ====================
    // CUSTOM METHODS
    // ====================

    /**
     * Get the slug for the current locale
     */
    public function getSlugAttribute(): string
    {
        // First try to get slug for current locale
        $slug = $this->slugs
            ->where('locale', app()->getLocale())
            ->where('active', true)
            ->first();

        // Fallback to any active slug
        if (! $slug) {
            $slug = $this->slugs->where('active', true)->first();
        }

        return $slug ? $slug->slug : 'post-'.$this->id;
    }

    /**
     * Increment view count safely
     */
    public function incrementViews(): void
    {
        $this->increment('view_count');
    }

    /**
     * Mark as featured
     */
    public function markAsFeatured(bool $featured = true): void
    {
        $settings = $this->settings;
        $settings['is_featured'] = $featured;
        $this->update(['settings' => $settings]);
    }

    /**
     * Mark as trending
     */
    public function markAsTrending(bool $trending = true): void
    {
        $settings = $this->settings;
        $settings['is_trending'] = $trending;
        $this->update(['settings' => $settings]);
    }

    /**
     * Mark as breaking news
     */
    public function markAsBreaking(bool $breaking = true): void
    {
        $settings = $this->settings;
        $settings['is_breaking'] = $breaking;
        $this->update(['settings' => $settings]);
    }

    /**
     * Get related posts based on categories
     */
    public function getRelatedPosts(int $limit = 5)
    {
        $categoryIds = $this->categories->pluck('id');
        
        return static::published()
            ->where('id', '!=', $this->id)
            ->whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            })
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Static method to handle tree reordering (required for Twill nesting functionality)
     *
     * @param  array  $ids
     */
    public static function saveTreeFromIds($ids): void
    {
        if (! is_array($ids)) {
            return;
        }

        $position = 1;
        foreach ($ids as $id) {
            static::where('id', $id)->update(['position' => $position]);
            $position++;
        }
    }

    // ====================
    // QUERY SCOPES (Additional)
    // ====================

    /**
     * Scope for posts by specific author
     */
    public function scopeByAuthor(Builder $query, $authorId): Builder
    {
        return $query->where('author_id', $authorId);
    }

    /**
     * Scope for posts with high engagement
     */
    public function scopeHighEngagement(Builder $query, int $minViews = 100): Builder
    {
        return $query->where('view_count', '>=', $minViews);
    }

    /**
     * Scope for posts with external links
     */
    public function scopeWithExternalUrl(Builder $query): Builder
    {
        return $query->whereJsonContains('settings->external_url', function ($value) {
            return !empty($value);
        });
    }
}
