<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasBlocks;
use A17\Twill\Models\Behaviors\HasFiles;
use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Behaviors\HasPosition;
use A17\Twill\Models\Behaviors\HasRelated;
use A17\Twill\Models\Behaviors\HasRevisions;
use A17\Twill\Models\Behaviors\HasSlug;
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
    use HasBlocks, HasFactory, HasFiles, HasMedias, HasPosition, HasRelated, HasRevisions, HasSlug, HasAdvancedScopes;

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

    public $slugAttributes = [
        'title',
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

        return $this->author?->name ?? 'Anonymous';
    }

    /**
     * Get main image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image('cover');
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
     * Handle meta attribute setting with auto-generation
     */
    public function setMetaAttribute($value): void
    {
        if (is_array($value)) {
            // Auto-generate description if not provided
            if (empty($value['description']) && !empty($this->content)) {
                $value['description'] = \Str::limit(strip_tags($this->content), 155);
            }
            
            // Auto-generate keywords if not provided
            if (empty($value['keywords']) && !empty($this->title)) {
                $value['keywords'] = collect(explode(' ', $this->title))
                    ->filter(fn($word) => strlen($word) > 3)
                    ->take(5)
                    ->implode(', ');
            }
        }
        
        $this->attributes['meta'] = json_encode($value);
    }

    /**
     * Get slug ensuring we have a fallback
     */
    public function getSlugAttribute(): string
    {
        // If we have a direct slug attribute, use it
        if (!empty($this->attributes['slug'])) {
            return $this->attributes['slug'];
        }

        // Generate from title as fallback
        return \Str::slug($this->title ?? 'post-' . $this->id);
    }

    // ====================
    // CUSTOM METHODS  
    // ====================

    /**
     * Increment view count
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
        
        $this->update(['settings' => $settings]);
    }

    /**
     * Mark post as trending
     */
    public function markAsTrending(bool $trending = true): void
    {
        $settings = $this->settings ?? [];
        $settings['is_trending'] = $trending;
        
        $this->update(['settings' => $settings]);
    }

    /**
     * Mark post as breaking news
     */
    public function markAsBreaking(bool $breaking = true): void
    {
        $settings = $this->settings ?? [];
        $settings['is_breaking'] = $breaking;
        
        $this->update(['settings' => $settings]);
    }

    /**
     * Get related posts based on categories
     */
    public function getRelatedPosts(int $limit = 5)
    {
        if ($this->categories->isEmpty()) {
            return collect();
        }

        return static::published()
            ->whereHas('categories', function ($query) {
                $query->whereIn('categories.id', $this->categories->pluck('id'));
            })
            ->where('id', '!=', $this->id)
            ->orderBy('view_count', 'desc')
            ->limit($limit)
            ->get();
    }

    // ====================
    // TREE MANIPULATION (Required by Twill)
    // ====================

    /**
     * Save tree structure from IDs array (required by Twill for drag-drop)
     */
    public static function saveTreeFromIds($ids): void
    {
        if (!is_array($ids)) {
            return;
        }

        foreach ($ids as $position => $id) {
            if (is_numeric($id)) {
                static::where('id', $id)->update(['position' => $position + 1]);
            }
        }
    }

    // ====================
    // ADDITIONAL SCOPES
    // ====================

    public function scopeByAuthor(Builder $query, $authorId): Builder
    {
        return $query->where('author_id', $authorId);
    }

    public function scopeHighEngagement(Builder $query, int $minViews = 100): Builder
    {
        return $query->where('view_count', '>=', $minViews);
    }

    public function scopeWithExternalUrl(Builder $query): Builder
    {
        return $query->whereNotNull('external_url');
    }
}
