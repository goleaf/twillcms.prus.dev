<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasPosition;
use A17\Twill\Models\Behaviors\HasRevisions;
use A17\Twill\Models\Behaviors\HasSlug;
use A17\Twill\Models\Behaviors\HasTranslation;
use A17\Twill\Models\Behaviors\Sortable;
use A17\Twill\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model implements Sortable
{
    use HasFactory, HasPosition, HasRevisions, HasSlug, HasTranslation;

    protected $fillable = [
        'published',
        'title',
        'description',
        'position',
    ];

    protected $casts = [
        'published' => 'boolean',
    ];

    public $translatedAttributes = [
        'title',
        'description',
        'active',
    ];

    public $slugAttributes = [
        'title',
    ];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_category');
    }

    /**
     * Scope for published categories - Override Twill's default
     */
    public function scopePublished(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('published', true);
    }

    /**
     * Scope to find category by slug
     */
    public function scopeForSlug($query, $slug)
    {
        return $query->whereHas('slugs', function ($q) use ($slug) {
            $q->where('slug', $slug)->where('active', true);
        });
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

        return $slug ? $slug->slug : 'category-'.$this->id;
    }
}
