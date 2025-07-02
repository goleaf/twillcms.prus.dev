<?php

namespace App\Traits\Performance;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Query Optimization Trait
 * 
 * Implements automated N+1 detection, query optimization,
 * and performance monitoring for repository implementations.
 * 
 * @package App\Traits\Performance
 */
trait QueryOptimization
{
    /**
     * Query patterns for optimization
     *
     * @var array
     */
    protected array $queryPatterns = [];

    /**
     * Optimization rules configuration
     *
     * @var array
     */
    protected array $optimizationRules = [
        'auto_eager_loading' => true,
        'n_plus_one_detection' => true,
        'query_caching' => true,
        'index_suggestions' => true,
        'performance_logging' => true,
    ];

    /**
     * Common relationships for eager loading
     *
     * @var array
     */
    protected array $commonRelations = [
        'posts' => ['category', 'media', 'author'],
        'categories' => ['posts', 'parent', 'children'],
    ];

    /**
     * Optimize query with automatic enhancements
     *
     * @param Builder $query
     * @return Builder
     */
    public function optimizeQuery(Builder $query): Builder
    {
        // Start performance tracking
        $startTime = microtime(true);
        
        // Apply optimization techniques
        $this->detectAndAddEagerLoading($query);
        $this->optimizeIndexUsage($query);
        $this->addSelectOptimization($query);
        
        // Track optimization time
        $optimizationTime = microtime(true) - $startTime;
        $this->logOptimization($query, $optimizationTime);

        return $query;
    }

    /**
     * Detect and add automatic eager loading
     *
     * @param Builder $query
     * @return void
     */
    protected function detectAndAddEagerLoading(Builder $query): void
    {
        if (!$this->optimizationRules['auto_eager_loading']) {
            return;
        }

        $model = $query->getModel();
        $modelName = strtolower(class_basename($model));
        
        $commonRelations = $this->commonRelations[$modelName] ?? [];
        
        if (!empty($commonRelations)) {
            $existingEagerLoads = $query->getEagerLoads();
            $newRelations = array_diff($commonRelations, array_keys($existingEagerLoads));
            
            if (!empty($newRelations)) {
                $query->with($newRelations);
                $this->logEagerLoading($modelName, $newRelations);
            }
        }
    }

    /**
     * Optimize index usage in queries
     *
     * @param Builder $query
     * @return void
     */
    protected function optimizeIndexUsage(Builder $query): void
    {
        if (!$this->optimizationRules['index_suggestions']) {
            return;
        }

        // Add hints for commonly used indexes
        $model = $query->getModel();
        $tableName = $model->getTable();
        
        // Common optimization patterns
        $wheres = $query->getQuery()->wheres ?? [];
        
        foreach ($wheres as $where) {
            if (isset($where['column'])) {
                $this->suggestIndexOptimization($tableName, $where['column']);
            }
        }
    }

    /**
     * Add SELECT optimization to reduce memory usage
     *
     * @param Builder $query
     * @return void
     */
    protected function addSelectOptimization(Builder $query): void
    {
        $currentSelect = $query->getQuery()->columns;
        
        // If no specific columns selected, optimize for common use cases
        if (empty($currentSelect) || in_array('*', $currentSelect)) {
            $model = $query->getModel();
            $optimizedColumns = $this->getOptimizedColumns($model);
            
            if (!empty($optimizedColumns)) {
                $query->select($optimizedColumns);
            }
        }
    }

    /**
     * Get optimized column selection for model
     *
     * @param $model
     * @return array
     */
    protected function getOptimizedColumns($model): array
    {
        $tableName = $model->getTable();
        
        // Common optimizations by model type
        switch (class_basename($model)) {
            case 'Post':
                return [
                    "{$tableName}.id",
                    "{$tableName}.title",
                    "{$tableName}.slug", 
                    "{$tableName}.excerpt",
                    "{$tableName}.published",
                    "{$tableName}.published_at",
                    "{$tableName}.category_id",
                    "{$tableName}.created_at",
                    "{$tableName}.updated_at"
                ];
                
            case 'Category':
                return [
                    "{$tableName}.id",
                    "{$tableName}.name",
                    "{$tableName}.slug",
                    "{$tableName}.description",
                    "{$tableName}.active",
                    "{$tableName}.position",
                    "{$tableName}.parent_id"
                ];
                
            default:
                return [];
        }
    }

    /**
     * Monitor query performance and detect N+1 problems
     *
     * @param Builder $query
     * @return mixed
     */
    public function executeWithMonitoring(Builder $query): mixed
    {
        if (!$this->optimizationRules['n_plus_one_detection']) {
            return $query->get();
        }

        $startTime = microtime(true);
        $startQueries = count(DB::getQueryLog());
        
        // Enable query logging
        DB::enableQueryLog();
        
        // Execute query
        $result = $query->get();
        
        // Analyze performance
        $endTime = microtime(true);
        $endQueries = count(DB::getQueryLog());
        $queryCount = $endQueries - $startQueries;
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        
        // Detect potential N+1 problems
        if ($queryCount > 5 && $result->count() > 0) {
            $this->detectNPlusOne($queryCount, $result->count(), $executionTime);
        }
        
        // Log performance metrics
        $this->logQueryPerformance($query, $queryCount, $executionTime);
        
        return $result;
    }

    /**
     * Detect N+1 query problems
     *
     * @param int $queryCount
     * @param int $resultCount
     * @param float $executionTime
     * @return void
     */
    protected function detectNPlusOne(int $queryCount, int $resultCount, float $executionTime): void
    {
        $queryRatio = $queryCount / max($resultCount, 1);
        
        // Potential N+1 if query count is close to result count
        if ($queryRatio > 0.8 && $queryCount > 10) {
            $this->logNPlusOneDetection($queryCount, $resultCount, $executionTime);
            $this->suggestEagerLoadingSolution($queryCount, $resultCount);
        }
    }

    /**
     * Log N+1 query detection
     *
     * @param int $queryCount
     * @param int $resultCount
     * @param float $executionTime
     * @return void
     */
    protected function logNPlusOneDetection(int $queryCount, int $resultCount, float $executionTime): void
    {
        Log::warning('Potential N+1 Query Detected', [
            'query_count' => $queryCount,
            'result_count' => $resultCount,
            'execution_time_ms' => $executionTime,
            'query_ratio' => $queryCount / max($resultCount, 1),
            'repository' => static::class,
            'suggested_solution' => 'Add eager loading for relationships'
        ]);
    }

    /**
     * Suggest eager loading solution
     *
     * @param int $queryCount
     * @param int $resultCount
     * @return void
     */
    protected function suggestEagerLoadingSolution(int $queryCount, int $resultCount): void
    {
        $suggestions = [
            'detected_issue' => 'N+1 Query Problem',
            'query_count' => $queryCount,
            'result_count' => $resultCount,
            'suggestion' => 'Use eager loading: ->with([\'relation1\', \'relation2\'])',
            'example' => 'Post::with([\'category\', \'media\'])->get()'
        ];
        
        Log::info('Query Optimization Suggestion', $suggestions);
    }

    /**
     * Log query performance metrics
     *
     * @param Builder $query
     * @param int $queryCount
     * @param float $executionTime
     * @return void
     */
    protected function logQueryPerformance(Builder $query, int $queryCount, float $executionTime): void
    {
        if (!$this->optimizationRules['performance_logging']) {
            return;
        }

        $performanceData = [
            'model' => get_class($query->getModel()),
            'query_count' => $queryCount,
            'execution_time_ms' => $executionTime,
            'memory_usage_mb' => memory_get_usage(true) / 1024 / 1024,
            'timestamp' => now()->toDateTimeString()
        ];

        // Log slow queries
        if ($executionTime > 100) { // Slower than 100ms
            Log::warning('Slow Query Detected', $performanceData);
        } else {
            Log::debug('Query Performance', $performanceData);
        }
    }

    /**
     * Log optimization details
     *
     * @param Builder $query
     * @param float $optimizationTime
     * @return void
     */
    protected function logOptimization(Builder $query, float $optimizationTime): void
    {
        if ($optimizationTime > 0.010) { // Optimization took more than 10ms
            Log::debug('Query Optimization Applied', [
                'model' => get_class($query->getModel()),
                'optimization_time_ms' => $optimizationTime * 1000,
                'eager_loads' => array_keys($query->getEagerLoads()),
            ]);
        }
    }

    /**
     * Log eager loading additions
     *
     * @param string $modelName
     * @param array $relations
     * @return void
     */
    protected function logEagerLoading(string $modelName, array $relations): void
    {
        Log::debug('Auto Eager Loading Applied', [
            'model' => $modelName,
            'relations' => $relations,
            'reason' => 'Common access pattern detected'
        ]);
    }

    /**
     * Suggest index optimization
     *
     * @param string $tableName
     * @param string $column
     * @return void
     */
    protected function suggestIndexOptimization(string $tableName, string $column): void
    {
        $indexSuggestions = [
            'table' => $tableName,
            'column' => $column,
            'suggestion' => "Consider adding index on {$tableName}.{$column}",
            'sql' => "CREATE INDEX idx_{$tableName}_{$column} ON {$tableName} ({$column});"
        ];
        
        Log::debug('Index Optimization Suggestion', $indexSuggestions);
    }

    /**
     * Get query performance statistics
     *
     * @return array
     */
    public function getPerformanceStats(): array
    {
        return [
            'optimizations_enabled' => array_filter($this->optimizationRules),
            'common_relations' => $this->commonRelations,
            'memory_usage_mb' => memory_get_usage(true) / 1024 / 1024,
            'peak_memory_mb' => memory_get_peak_usage(true) / 1024 / 1024,
        ];
    }

    /**
     * Configure optimization rules
     *
     * @param array $rules
     * @return void
     */
    public function configureOptimization(array $rules): void
    {
        $this->optimizationRules = array_merge($this->optimizationRules, $rules);
    }

    /**
     * Add common relations for a model
     *
     * @param string $model
     * @param array $relations
     * @return void
     */
    public function addCommonRelations(string $model, array $relations): void
    {
        $modelKey = strtolower($model);
        $this->commonRelations[$modelKey] = array_merge(
            $this->commonRelations[$modelKey] ?? [],
            $relations
        );
    }

    /**
     * Clear optimization cache and patterns
     *
     * @return void
     */
    public function clearOptimizationCache(): void
    {
        $this->queryPatterns = [];
        Log::info('Query optimization cache cleared');
    }
} 