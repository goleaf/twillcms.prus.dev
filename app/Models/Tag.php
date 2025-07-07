<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'usage_count',
        'is_featured',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'usage_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'posts_count',
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
    }

    // ====================
    // RELATIONSHIPS
    // ====================

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tag')
            ->withTimestamps();
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

    // ====================
    // ACCESSORS
    // ====================

    public function getPostsCountAttribute(): int
    {
        return $this->posts()->count();
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

    /**
     * Get the route key for the model
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}