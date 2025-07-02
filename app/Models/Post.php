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
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model implements Sortable
{
    use HasBlocks, HasFactory, HasFiles, HasMedias, HasPosition, HasRelated, HasRevisions, HasSlug, HasTranslation;

    protected $fillable = [
        'published',
        'published_at',
        'title',
        'description',
        'content',
        'position',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'published' => 'boolean',
    ];

    public $translatedAttributes = [
        'title',
        'description',
        'content',
        'active',
    ];

    // Disable automatic slug generation to avoid array conversion issues
    public $slugAttributes = [];

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
     * Get the image URL attribute
     */
    public function getImageUrlAttribute(): ?string
    {
        return null; // Placeholder for now
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'post_category');
    }

    /**
     * Calculate estimated reading time based on content
     */
    public function readingTime(): int
    {
        $wordCount = str_word_count(strip_tags($this->content ?? ''));
        $wordsPerMinute = 200; // Average reading speed

        return max(1, ceil($wordCount / $wordsPerMinute));
    }

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

    /**
     * Scope for published posts - Override Twill's default
     */
    public function scopePublished(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('published', true)
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    /**
     * Scope for recent posts
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('published_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to find post by slug
     */
    public function scopeForSlug($query, $slug)
    {
        return $query->whereHas('slugs', function ($q) use ($slug) {
            $q->where('slug', $slug)->where('active', true);
        });
    }

    /**
     * Get the published at date formatted for display
     */
    public function getFormattedPublishedAtAttribute(): ?string
    {
        return $this->published_at?->format('F j, Y');
    }
}
