<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\RepositoryInterface;
use App\Traits\Caching\SmartCaching;
use App\Traits\Performance\QueryOptimization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;

/**
 * Base Repository Implementation
 * 
 * Provides common repository functionality with enterprise-level
 * performance optimization, smart caching, and query monitoring.
 * 
 * @package App\Repositories\Eloquent
 */
abstract class BaseRepository implements RepositoryInterface
{
    use SmartCaching, QueryOptimization;

    /**
     * The model instance
     *
     * @var Model
     */
    protected Model $model;

    /**
     * Query builder instance
     *
     * @var Builder|null
     */
    protected ?Builder $query = null;

    /**
     * Cache timeout in seconds
     *
     * @var int
     */
    protected int $cacheTimeout = 3600;

    /**
     * Cache key prefix
     *
     * @var string|null
     */
    protected ?string $cacheKey = null;

    /**
     * BaseRepository constructor
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->resetQuery();
    }

    /**
     * Find a model by its primary key
     *
     * @param int $id
     * @return Model|null
     */
    public function find(int $id): ?Model
    {
        $cacheKey = $this->getCacheKey("find.{$id}");
        
        return $this->smartRemember($cacheKey, function () use ($id) {
            return $this->model->find($id);
        });
    }

    /**
     * Find a model by its primary key or fail
     *
     * @param int $id
     * @return Model
     * @throws ModelNotFoundException
     */
    public function findOrFail(int $id): Model
    {
        $cacheKey = $this->getCacheKey("findOrFail.{$id}");
        
        return $this->smartRemember($cacheKey, function () use ($id) {
            return $this->model->findOrFail($id);
        });
    }

    /**
     * Get all models
     *
     * @param array $columns
     * @return Collection
     */
    public function all(array $columns = ['*']): Collection
    {
        $cacheKey = $this->getCacheKey('all.' . md5(implode(',', $columns)));
        
        return $this->smartRemember($cacheKey, function () use ($columns) {
            $query = $this->model->select($columns);
            return $this->executeWithMonitoring($this->optimizeQuery($query));
        });
    }

    /**
     * Paginate the given query
     *
     * @param int $perPage
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        $query = $this->getQuery()->select($columns);
        $optimizedQuery = $this->optimizeQuery($query);
        
        return $optimizedQuery->paginate($perPage);
    }

    /**
     * Create a new model
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        $model = $this->model->create($data);
        
        // Clear related caches
        $this->clearRelatedCaches();
        
        return $model;
    }

    /**
     * Update a model
     *
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update(int $id, array $data): Model
    {
        $model = $this->findOrFail($id);
        $model->update($data);
        
        // Clear specific and related caches
        $this->clearCache($this->getCacheKey("find.{$id}"));
        $this->clearCache($this->getCacheKey("findOrFail.{$id}"));
        $this->clearRelatedCaches();
        
        return $model->fresh();
    }

    /**
     * Delete a model
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $model = $this->findOrFail($id);
        $deleted = $model->delete();
        
        if ($deleted) {
            // Clear specific and related caches
            $this->clearCache($this->getCacheKey("find.{$id}"));
            $this->clearCache($this->getCacheKey("findOrFail.{$id}"));
            $this->clearRelatedCaches();
        }
        
        return $deleted;
    }

    /**
     * Add relationships to be eager loaded
     *
     * @param array $relations
     * @return static
     */
    public function with(array $relations): static
    {
        $this->query = $this->getQuery()->with($relations);
        return $this;
    }

    /**
     * Add a where clause to the query
     *
     * @param string $column
     * @param mixed $operator
     * @param mixed $value
     * @return static
     */
    public function where(string $column, $operator, $value = null): static
    {
        $this->query = $this->getQuery()->where($column, $operator, $value);
        return $this;
    }

    /**
     * Get the count of records
     *
     * @return int
     */
    public function count(): int
    {
        $cacheKey = $this->getCacheKey('count');
        
        return $this->smartRemember($cacheKey, function () {
            return $this->getQuery()->count();
        });
    }

    /**
     * Check if any records exist
     *
     * @return bool
     */
    public function exists(): bool
    {
        $cacheKey = $this->getCacheKey('exists');
        
        return $this->smartRemember($cacheKey, function () {
            return $this->getQuery()->exists();
        });
    }

    /**
     * Cache the results for the given number of seconds
     *
     * @param int $seconds
     * @param string|null $key
     * @return static
     */
    public function cache(int $seconds, ?string $key = null): static
    {
        $this->cacheTimeout = $seconds;
        $this->cacheKey = $key;
        return $this;
    }

    /**
     * Clear the repository cache
     *
     * @param string|null $key
     * @return bool
     */
    public function clearCache(?string $key = null): bool
    {
        if ($key) {
            return $this->clearCacheByKey($key);
        }
        
        return $this->clearAllCache();
    }

    /**
     * Clear cache for a specific key using SmartCaching trait
     *
     * @param string $key
     * @return bool
     */
    private function clearCacheByKey(string $key): bool
    {
        $cleared = true;

        // Clear from all cache drivers using SmartCaching logic
        foreach ($this->cacheStrategies as $strategy => $config) {
            if (isset($config['driver'])) {
                $driver = Cache::store($config['driver']);
                $cleared = $cleared && $driver->forget($key);
            }
        }

        return $cleared;
    }

    /**
     * Get the current query builder
     *
     * @return Builder
     */
    protected function getQuery(): Builder
    {
        if ($this->query === null) {
            $this->resetQuery();
        }
        
        return $this->query;
    }

    /**
     * Reset the query builder
     *
     * @return void
     */
    protected function resetQuery(): void
    {
        $this->query = $this->model->newQuery();
    }

    /**
     * Generate cache key for the repository
     *
     * @param string $suffix
     * @return string
     */
    protected function getCacheKey(string $suffix): string
    {
        if ($this->cacheKey) {
            return $this->cacheKey;
        }
        
        $modelName = strtolower(class_basename($this->model));
        return "{$modelName}.{$suffix}";
    }

    /**
     * Clear related caches that might be affected
     *
     * @return void
     */
    protected function clearRelatedCaches(): void
    {
        $modelName = strtolower(class_basename($this->model));
        
        // Clear common cache patterns
        $this->clearCache($this->getCacheKey('all'));
        $this->clearCache($this->getCacheKey('count'));
        $this->clearCache($this->getCacheKey('exists'));
        
        // Clear model-specific patterns
        $this->clearModelSpecificCaches($modelName);
    }

    /**
     * Clear model-specific cache patterns
     *
     * @param string $modelName
     * @return void
     */
    protected function clearModelSpecificCaches(string $modelName): void
    {
        // Common patterns that should be cleared
        $patterns = [
            "{$modelName}.published",
            "{$modelName}.popular",
            "{$modelName}.recent", 
            "{$modelName}.featured",
            "{$modelName}.trending",
            "{$modelName}.statistics"
        ];
        
        foreach ($patterns as $pattern) {
            $this->clearCache($pattern);
        }
    }

    /**
     * Execute query with performance optimization
     *
     * @param Builder $query
     * @return Collection
     */
    protected function executeOptimizedQuery(Builder $query): Collection
    {
        $optimizedQuery = $this->optimizeQuery($query);
        return $this->executeWithMonitoring($optimizedQuery);
    }

    /**
     * Get repository statistics
     *
     * @return array
     */
    public function getRepositoryStats(): array
    {
        return [
            'model' => get_class($this->model),
            'cache_stats' => $this->getCacheStats(),
            'performance_stats' => $this->getPerformanceStats(),
            'memory_usage' => [
                'current_mb' => memory_get_usage(true) / 1024 / 1024,
                'peak_mb' => memory_get_peak_usage(true) / 1024 / 1024,
            ]
        ];
    }

    /**
     * Warm up frequently accessed caches
     *
     * @return void
     */
    public function warmupCache(): void
    {
        // Warm up common queries - to be implemented in child classes
        $this->warmCache([]);
    }

    /**
     * Get fresh model instance
     *
     * @return Model
     */
    protected function getFreshModel(): Model
    {
        return $this->model->newInstance();
    }

    /**
     * Apply common scopes to query
     *
     * @param Builder $query
     * @param array $scopes
     * @return Builder
     */
    protected function applyScopes(Builder $query, array $scopes): Builder
    {
        foreach ($scopes as $scope => $parameters) {
            if (is_string($scope)) {
                $query = $query->$scope(...$parameters);
            } else {
                $query = $query->$parameters();
            }
        }
        
        return $query;
    }

    /**
     * Batch process records with memory optimization
     *
     * @param callable $callback
     * @param int $chunkSize
     * @return void
     */
    public function batchProcess(callable $callback, int $chunkSize = 1000): void
    {
        $this->model->chunk($chunkSize, $callback);
    }

    /**
     * Clean up resources
     *
     * @return void
     */
    public function __destruct()
    {
        $this->query = null;
    }
} 