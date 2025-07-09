<?php

namespace App\Models;

use Aliziodev\LaravelTaxonomy\Traits\HasTaxonomy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes, HasTaxonomy;

    protected $fillable = [
        'name',
        'slug', 
        'title',
        'description',
        'color',
        'is_active',
        'sort_order',
        'parent_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'parent_id' => 'integer'
    ];

    /**
     * Get articles belonging to this category
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_category');
    }

    /**
     * Get published articles only
     */
    public function publishedArticles(): BelongsToMany
    {
        return $this->articles()->where('is_published', true);
    }

    /**
     * Get posts (alias for articles for backward compatibility)
     */
    public function posts(): BelongsToMany
    {
        return $this->articles();
    }

    /**
     * Parent category relationship
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Child categories relationship
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get posts count attribute
     */
    public function getPostsCountAttribute()
    {
        return $this->articles()->where('is_published', true)->count();
    }

    /**
     * Get articles count attribute
     */
    public function getArticlesCountAttribute()
    {
        return $this->articles()->where('is_published', true)->count();
    }
}
