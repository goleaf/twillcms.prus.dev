<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'published',
        'title',
        'slug',
        'description',
        'position',
        'parent_id',
        'color_code',
        'icon',
        'view_count',
        'sort_order',
    ];

    protected $casts = [
        'published' => 'boolean',
        'view_count' => 'integer',
        'parent_id' => 'integer',
        'sort_order' => 'integer',
        'position' => 'integer',
    ];

    protected $attributes = [
        'published' => true,
        'view_count' => 0,
        'position' => 0,
        'sort_order' => 0,
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug on creating
        static::creating(function ($category) {
            if (empty($category->slug) && ! empty($category->title)) {
                $category->slug = Str::slug($category->title);
            }
        });

        // Auto-increment view count on category access
        static::retrieved(function ($category) {
            if (request()->route() && str_contains(request()->route()->getName() ?? '', 'category')) {
                $category->increment('view_count');
            }
        });
    }

    // ====================
    // RELATIONSHIPS
    // ====================

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_category');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // ====================
    // SCOPES
    // ====================

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published', true);
    }

    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeChildren(Builder $query, $parentId = null): Builder
    {
        if ($parentId) {
            return $query->where('parent_id', $parentId);
        }
        return $query->whereNotNull('parent_id');
    }

    public function scopeWithPosts(Builder $query): Builder
    {
        return $query->has('posts');
    }

    public function scopeForSlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }

    // ====================
    // ACCESSORS & MUTATORS
    // ====================

    public function getSlugAttribute(): string
    {
        if (! empty($this->attributes['slug'])) {
            return $this->attributes['slug'];
        }
        return Str::slug($this->title ?? 'category-'.$this->id);
    }

    public function setSlugAttribute($value): void
    {
        $this->attributes['slug'] = Str::slug($value);
    }

    public function setColorCodeAttribute($value): void
    {
        if (is_string($value) && preg_match('/^#[a-fA-F0-9]{6}$/', $value)) {
            $this->attributes['color_code'] = $value;
        } elseif (is_string($value) && preg_match('/^[a-fA-F0-9]{6}$/', $value)) {
            $this->attributes['color_code'] = '#'.$value;
        }
    }

    // ====================
    // METHODS
    // ====================

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function incrementViews(): void
    {
        $this->increment('view_count');
    }
}
