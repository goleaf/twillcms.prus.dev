<?php

namespace App\Repositories;

use Aliziodev\LaravelTaxonomy\Models\Taxonomy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TaxonomyRepository
{
    protected $model;
    protected $cacheTimeout = 3600; // 1 hour

    public function __construct(Taxonomy $model)
    {
        $this->model = $model;
    }

    /**
     * Get taxonomies by type with caching
     */
    public function getByType(string $type, bool $withModels = false): Collection
    {
        $cacheKey = "taxonomy.type.{$type}" . ($withModels ? '.with_models' : '');
        
        return Cache::remember($cacheKey, $this->cacheTimeout, function () use ($type, $withModels) {
            $query = $this->model->where('type', $type)->orderBy('name');
            
            if ($withModels) {
                $query->withCount(['models as usage_count']);
            }
            
            return $query->get();
        });
    }

    /**
     * Get hierarchical taxonomy tree with caching
     */
    public function getTree(string $type): Collection
    {
        $cacheKey = "taxonomy.tree.{$type}";
        
        return Cache::remember($cacheKey, $this->cacheTimeout, function () use ($type) {
            return $this->model->tree($type);
        });
    }

    /**
     * Get flat tree with depth information
     */
    public function getFlatTree(string $type): Collection
    {
        $cacheKey = "taxonomy.flat_tree.{$type}";
        
        return Cache::remember($cacheKey, $this->cacheTimeout, function () use ($type) {
            return $this->model->flatTree($type);
        });
    }

    /**
     * Get popular taxonomies by usage count
     */
    public function getPopular(string $type, int $limit = 10): Collection
    {
        $cacheKey = "taxonomy.popular.{$type}.{$limit}";
        
        return Cache::remember($cacheKey, $this->cacheTimeout, function () use ($type, $limit) {
            return $this->model
                ->where('type', $type)
                ->withCount(['models as usage_count'])
                ->orderByDesc('usage_count')
                ->orderBy('name')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get featured taxonomies
     */
    public function getFeatured(string $type, int $limit = 5): Collection
    {
        $cacheKey = "taxonomy.featured.{$type}.{$limit}";
        
        return Cache::remember($cacheKey, $this->cacheTimeout, function () use ($type, $limit) {
            return $this->model
                ->where('type', $type)
                ->whereJsonContains('meta->is_featured', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get taxonomy by slug and type
     */
    public function getBySlug(string $slug, string $type = null): ?Taxonomy
    {
        $cacheKey = "taxonomy.slug.{$slug}" . ($type ? ".{$type}" : '');
        
        return Cache::remember($cacheKey, $this->cacheTimeout, function () use ($slug, $type) {
            $query = $this->model->where('slug', $slug);
            
            if ($type) {
                $query->where('type', $type);
            }
            
            return $query->first();
        });
    }

    /**
     * Search taxonomies
     */
    public function search(string $term, string $type = null, int $limit = 20): Collection
    {
        $query = $this->model
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            });

        if ($type) {
            $query->where('type', $type);
        }

        return $query
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    /**
     * Get taxonomies with pagination
     */
    public function getPaginated(string $type = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->withCount(['models as usage_count']);

        if ($type) {
            $query->where('type', $type);
        }

        return $query
            ->orderBy('type')
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Create new taxonomy
     */
    public function create(array $data): Taxonomy
    {
        $taxonomy = $this->model->create($data);
        
        // Clear relevant caches
        $this->clearCacheByType($taxonomy->type);
        
        return $taxonomy;
    }

    /**
     * Update taxonomy
     */
    public function update(int $id, array $data): bool
    {
        $taxonomy = $this->model->find($id);
        
        if (!$taxonomy) {
            return false;
        }
        
        $updated = $taxonomy->update($data);
        
        if ($updated) {
            // Clear relevant caches
            $this->clearCacheByType($taxonomy->type);
            $this->clearCacheBySlug($taxonomy->slug);
        }
        
        return $updated;
    }

    /**
     * Delete taxonomy
     */
    public function delete(int $id): bool
    {
        $taxonomy = $this->model->find($id);
        
        if (!$taxonomy) {
            return false;
        }
        
        $type = $taxonomy->type;
        $slug = $taxonomy->slug;
        
        $deleted = $taxonomy->delete();
        
        if ($deleted) {
            // Clear relevant caches
            $this->clearCacheByType($type);
            $this->clearCacheBySlug($slug);
        }
        
        return $deleted;
    }

    /**
     * Bulk attach taxonomies to models
     */
    public function bulkAttach(Collection $models, array $taxonomyIds): void
    {
        $data = [];
        $timestamp = now();

        foreach ($models as $model) {
            foreach ($taxonomyIds as $taxonomyId) {
                $data[] = [
                    'taxonomy_id' => $taxonomyId,
                    'taxonomable_id' => $model->id,
                    'taxonomable_type' => get_class($model),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }
        }

        DB::table('taxonomables')->insert($data);
        
        // Clear taxonomy caches
        $this->clearAllCaches();
    }

    /**
     * Get taxonomy statistics
     */
    public function getStatistics(): array
    {
        $cacheKey = 'taxonomy.statistics';
        
        return Cache::remember($cacheKey, $this->cacheTimeout, function () {
            return [
                'total_taxonomies' => $this->model->count(),
                'by_type' => $this->model
                    ->select('type', DB::raw('count(*) as count'))
                    ->groupBy('type')
                    ->pluck('count', 'type')
                    ->toArray(),
                'total_associations' => DB::table('taxonomables')->count(),
                'hierarchical_categories' => $this->model
                    ->where('type', 'category')
                    ->whereNotNull('parent_id')
                    ->count(),
            ];
        });
    }

    /**
     * Rebuild nested set for hierarchical taxonomies
     */
    public function rebuildNestedSet(string $type): void
    {
        $this->model->rebuildNestedSet($type);
        
        // Clear tree caches
        $this->clearCacheByType($type);
    }

    /**
     * Clear cache by type
     */
    protected function clearCacheByType(string $type): void
    {
        $keys = [
            "taxonomy.type.{$type}",
            "taxonomy.type.{$type}.with_models",
            "taxonomy.tree.{$type}",
            "taxonomy.flat_tree.{$type}",
            "taxonomy.popular.{$type}.*",
            "taxonomy.featured.{$type}.*",
        ];

        foreach ($keys as $key) {
            if (str_contains($key, '*')) {
                // For wildcard keys, we'll need to flush all cache or use tags
                Cache::flush();
                break;
            } else {
                Cache::forget($key);
            }
        }
    }

    /**
     * Clear cache by slug
     */
    protected function clearCacheBySlug(string $slug): void
    {
        // This would ideally use cache tags in Redis/Memcached
        Cache::forget("taxonomy.slug.{$slug}");
    }

    /**
     * Clear all taxonomy caches
     */
    protected function clearAllCaches(): void
    {
        // In production, you'd want to use cache tags for more granular control
        Cache::flush();
    }

    /**
     * Warm up commonly used caches
     */
    public function warmCache(): void
    {
        $types = ['tag', 'category'];
        
        foreach ($types as $type) {
            $this->getByType($type);
            $this->getTree($type);
            $this->getPopular($type);
            $this->getFeatured($type);
        }
    }
} 