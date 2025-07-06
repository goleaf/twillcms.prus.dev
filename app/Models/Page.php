<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'published',
        'title',
        'slug',
        'excerpt',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'position',
        'published_at',
    ];

    protected $casts = [
        'published' => 'boolean',
        'meta_keywords' => 'array',
        'published_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug on creating
        static::creating(function ($page) {
            if (empty($page->slug) && ! empty($page->title)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Scope to get published pages
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published', true)
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Get page by slug
     */
    public static function findBySlug($slug)
    {
        return static::published()
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Get the full URL for this page
     */
    public function getUrlAttribute()
    {
        return url("/{$this->slug}");
    }

    /**
     * Get formatted meta keywords
     */
    public function getMetaKeywordsStringAttribute()
    {
        return is_array($this->meta_keywords)
            ? implode(', ', $this->meta_keywords)
            : $this->meta_keywords;
    }

    /**
     * Get excerpt with fallback
     */
    public function getExcerptAttribute()
    {
        if (! empty($this->attributes['excerpt'])) {
            return $this->attributes['excerpt'];
        }

        // Generate from content
        $content = strip_tags($this->content ?? '');
        $words = explode(' ', $content);

        if (count($words) <= 30) {
            return $content;
        }

        return implode(' ', array_slice($words, 0, 30)).'...';
    }
}
