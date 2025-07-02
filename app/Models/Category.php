<?php

namespace App\Models;





use Illuminate\Database\Eloquent\Model;
use App\Casts\MetaCast;
use App\Casts\SettingsCast;
use App\Traits\Models\HasAdvancedScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    use HasFactory, HasAdvancedScopes;

    protected $fillable = [
        'published',
        'title',
        'description',
        'position',
        'meta',
        'settings',
        'parent_id',
        'color_code',
        'icon',
        'view_count',
        'sort_order',
    ];

    protected $casts = [
        'published' => 'boolean',
        'meta' => MetaCast::class,
        'settings' => SettingsCast::class,
        'view_count' => 'integer',
        'parent_id' => 'integer',
        'sort_order' => 'integer',
    ];

    public $slugAttributes = [
        'title',
    ];

    protected $appends = [
        'posts_count',
        'is_featured',
        'color_style',
        'breadcrumb_path',
        'child_count',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

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

    public function publishedPosts(): BelongsToMany
    {
        return $this->posts()->published();
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function publishedChildren()
    {
        return $this->children()->published();
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    // ====================
    // ACCESSORS & MUTATORS
    // ====================

    /**
     * Get published posts count
     */
    public function getPostsCountAttribute(): int
    {
        return $this->publishedPosts()->count();
    }

    /**
     * Check if category is featured
     */
    public function getIsFeaturedAttribute(): bool
    {
        return $this->settings['is_featured'] ?? false;
    }

    /**
     * Get color style for CSS
     */
    public function getColorStyleAttribute(): string
    {
        $color = $this->color_code ?? '#6366f1';
        return "color: {$color}; border-color: {$color};";
    }

    /**
     * Get breadcrumb path
     */
    public function getBreadcrumbPathAttribute(): array
    {
        $path = [];
        $current = $this;
        
        while ($current) {
            array_unshift($path, [
                'id' => $current->id,
                'title' => $current->title,
                'slug' => $current->slug,
            ]);
            $current = $current->parent;
        }
        
        return $path;
    }

    /**
     * Get count of direct children
     */
    public function getChildCountAttribute(): int
    {
        return $this->publishedChildren()->count();
    }

    /**
     * Set color code with validation
     */
    public function setColorCodeAttribute($value): void
    {
        if (is_string($value) && preg_match('/^#[a-fA-F0-9]{6}$/', $value)) {
            $this->attributes['color_code'] = $value;
        } elseif (is_string($value) && preg_match('/^[a-fA-F0-9]{6}$/', $value)) {
            $this->attributes['color_code'] = '#' . $value;
        }
    }

    // ====================
    // CUSTOM METHODS
    // ====================

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
        return \Str::slug($this->title ?? 'category-' . $this->id);
    }

    /**
     * Get all descendants (recursive children)
     */
    public function getAllDescendants()
    {
        $descendants = collect();
        
        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getAllDescendants());
        }
        
        return $descendants;
    }

    /**
     * Get all ancestors (recursive parents)
     */
    public function getAllAncestors()
    {
        $ancestors = collect();
        $current = $this->parent;
        
        while ($current) {
            $ancestors->push($current);
            $current = $current->parent;
        }
        
        return $ancestors;
    }

    /**
     * Check if this category is an ancestor of the given category
     */
    public function isAncestorOf(Category $category): bool
    {
        return $category->getAllAncestors()->contains('id', $this->id);
    }

    /**
     * Check if this category is a descendant of the given category
     */
    public function isDescendantOf(Category $category): bool
    {
        return $this->getAllAncestors()->contains('id', $category->id);
    }

    /**
     * Mark category as featured
     */
    public function markAsFeatured(bool $featured = true): void
    {
        $settings = $this->settings ?? [];
        $settings['is_featured'] = $featured;
        
        $this->update(['settings' => $settings]);
    }

    /**
     * Get popular posts from this category
     */
    public function getPopularPosts(int $limit = 10)
    {
        return $this->publishedPosts()
            ->orderBy('view_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent posts from this category
     */
    public function getRecentPosts(int $limit = 10, int $days = 30)
    {
        $date = now()->subDays($days);
        
        return $this->publishedPosts()
            ->where('published_at', '>=', $date)
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }

    // ====================
    // QUERY SCOPES
    // ====================

    /**
     * Scope for root categories (no parent)
     */
    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope for child categories
     */
    public function scopeChildren(Builder $query, $parentId = null): Builder
    {
        if ($parentId) {
            return $query->where('parent_id', $parentId);
        }
        
        return $query->whereNotNull('parent_id');
    }

    /**
     * Scope for categories with posts
     */
    public function scopeWithPosts(Builder $query): Builder
    {
        return $query->has('publishedPosts');
    }

    /**
     * Scope for categories without posts
     */
    public function scopeWithoutPosts(Builder $query): Builder
    {
        return $query->doesntHave('publishedPosts');
    }

    /**
     * Scope for navigation (published with position ordering)
     */
    public function scopeForNavigation(Builder $query): Builder
    {
        return $query->published()
            ->orderBy('sort_order')
            ->orderBy('position')
            ->orderBy('title');
    }

    /**
     * Scope by color
     */
    public function scopeByColor(Builder $query, string $color): Builder
    {
        return $query->where('color_code', $color);
    }

    /**
     * Scope for hierarchical ordering
     */
    public function scopeHierarchical(Builder $query): Builder
    {
        return $query->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('position');
    }
}
