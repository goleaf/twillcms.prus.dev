<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

abstract class BaseRepository
{
    protected Model $model;
    protected int $cacheTime = 3600; // 1 hour default cache
    protected string $cachePrefix;

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->cachePrefix = strtolower(class_basename($model));
    }

    /**
     * Get all records with optional caching
     */
    public function all(bool $useCache = true): Collection
    {
        if (!$useCache) {
            return $this->model->all();
        }

        return Cache::remember(
            $this->getCacheKey('all'),
            $this->cacheTime,
            fn() => $this->model->all()
        );
    }

    /**
     * Find by ID with caching
     */
    public function find(int $id, bool $useCache = true): ?Model
    {
        if (!$useCache) {
            return $this->model->find($id);
        }

        return Cache::remember(
            $this->getCacheKey("find.{$id}"),
            $this->cacheTime,
            fn() => $this->model->find($id)
        );
    }

    /**
     * Find by slug with caching
     */
    public function findBySlug(string $slug, bool $useCache = true): ?Model
    {
        if (!$useCache) {
            return $this->model->where('slug', $slug)->first();
        }

        return Cache::remember(
            $this->getCacheKey("slug.{$slug}"),
            $this->cacheTime,
            fn() => $this->model->where('slug', $slug)->first()
        );
    }

    /**
     * Create new record and clear cache
     */
    public function create(array $data): Model
    {
        $model = $this->model->create($data);
        $this->clearCache();
        return $model;
    }

    /**
     * Update record and clear cache
     */
    public function update(int $id, array $data): bool
    {
        $model = $this->find($id, false);
        if (!$model) {
            return false;
        }

        $result = $model->update($data);
        if ($result) {
            $this->clearCache();
        }
        return $result;
    }

    /**
     * Delete record and clear cache
     */
    public function delete(int $id): bool
    {
        $model = $this->find($id, false);
        if (!$model) {
            return false;
        }

        $result = $model->delete();
        if ($result) {
            $this->clearCache();
        }
        return $result;
    }

    /**
     * Paginate results with caching key based on page and parameters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $page = request('page', 1);
        $cacheKey = $this->getCacheKey("paginate.{$perPage}.{$page}." . md5(serialize($filters)));

        return Cache::remember(
            $cacheKey,
            $this->cacheTime,
            function() use ($perPage, $filters) {
                $query = $this->model->query();
                $query = $this->applyFilters($query, $filters);
                return $query->paginate($perPage);
            }
        );
    }

    /**
     * Get query builder instance
     */
    public function query(): Builder
    {
        return $this->model->query();
    }

    /**
     * Count records with caching
     */
    public function count(array $filters = []): int
    {
        $cacheKey = $this->getCacheKey('count.' . md5(serialize($filters)));

        return Cache::remember(
            $cacheKey,
            $this->cacheTime,
            function() use ($filters) {
                $query = $this->model->query();
                $query = $this->applyFilters($query, $filters);
                return $query->count();
            }
        );
    }

    /**
     * Get records with where conditions
     */
    public function where(array $conditions): Collection
    {
        $query = $this->model->query();
        foreach ($conditions as $field => $value) {
            $query->where($field, $value);
        }
        return $query->get();
    }

    /**
     * Bulk insert for performance
     */
    public function bulkInsert(array $data): bool
    {
        $result = DB::table($this->model->getTable())->insert($data);
        if ($result) {
            $this->clearCache();
        }
        return $result;
    }

    /**
     * Apply filters to query - override in child repositories
     */
    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query;
    }

    /**
     * Generate cache key
     */
    protected function getCacheKey(string $suffix): string
    {
        return "{$this->cachePrefix}.{$suffix}";
    }

    /**
     * Clear all cache for this repository
     */
    public function clearCache(): void
    {
        $pattern = $this->cachePrefix . '.*';
        $keys = Cache::getRedis()->keys($pattern);
        if (!empty($keys)) {
            Cache::getRedis()->del($keys);
        }
    }

    /**
     * Get the underlying model
     */
    public function getModel(): Model
    {
        return $this->model;
    }
}
