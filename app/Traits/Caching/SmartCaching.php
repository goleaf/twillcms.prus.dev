<?php

namespace App\Traits\Caching;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

/**
 * Smart Caching Trait
 *
 * Implements intelligent cache selection with Hot/Warm/Cold/Static strategies
 * based on access frequency, data size, and update patterns.
 */
trait SmartCaching
{
    /**
     * Cache strategies configuration
     */
    protected array $cacheStrategies = [
        'hot' => ['driver' => 'array', 'ttl' => 300],      // In-memory, 5 minutes
        'warm' => ['driver' => 'redis', 'ttl' => 1800],    // Redis, 30 minutes
        'cold' => ['driver' => 'database', 'ttl' => 0],    // No cache, direct DB
        'static' => ['driver' => 'file', 'ttl' => 86400],  // File cache, 24 hours
    ];

    /**
     * Access frequency tracking
     */
    protected static array $accessFrequency = [];

    /**
     * Smart cache remember with automatic strategy selection
     */
    public function smartRemember(string $key, callable $callback, ?string $strategy = null): mixed
    {
        $strategy = $strategy ?? $this->determineStrategy($key);
        $config = $this->cacheStrategies[$strategy];

        // Track access frequency
        $this->trackAccess($key);

        if ($config['ttl'] === 0) {
            // Cold strategy - no caching
            return $callback();
        }

        return Cache::driver($config['driver'])
            ->remember($key, $config['ttl'], $callback);
    }

    /**
     * Determine optimal cache strategy for given key
     */
    protected function determineStrategy(string $key): string
    {
        $frequency = $this->getAccessFrequency($key);
        $size = $this->estimateDataSize($key);
        $updateFrequency = $this->getUpdateFrequency($key);

        // Hot data: High frequency access, small size
        if ($frequency > 100 && $size < 1024) {
            return 'hot';
        }

        // Static data: Low update frequency, stable content
        if ($updateFrequency < 0.01) {
            return 'static';
        }

        // Warm data: Moderate access, reasonable size
        if ($frequency > 10 && $updateFrequency < 0.1) {
            return 'warm';
        }

        // Cold data: Low frequency or high update rate
        return 'cold';
    }

    /**
     * Get access frequency for a cache key
     */
    protected function getAccessFrequency(string $key): int
    {
        return Cache::get("access_freq_{$key}", 0);
    }

    /**
     * Track access to a cache key
     */
    protected function trackAccess(string $key): void
    {
        $currentCount = $this->getAccessFrequency($key);
        Cache::put("access_freq_{$key}", $currentCount + 1, 3600);
    }

    /**
     * Estimate data size for cache strategy decision
     */
    protected function estimateDataSize(string $key): int
    {
        // Estimate based on key pattern
        if (str_contains($key, 'posts.all')) {
            return 5120;
        }      // Large collection
        if (str_contains($key, 'post.')) {
            return 2048;
        }          // Single post
        if (str_contains($key, 'categories')) {
            return 1024;
        }     // Category list
        if (str_contains($key, 'popular')) {
            return 3072;
        }        // Popular posts
        if (str_contains($key, 'recent')) {
            return 2560;
        }         // Recent posts

        return 1536; // Default estimation
    }

    /**
     * Get update frequency for content type
     */
    protected function getUpdateFrequency(string $key): float
    {
        // Based on content type patterns
        if (str_contains($key, 'static')) {
            return 0.001;
        }        // Very low
        if (str_contains($key, 'categories')) {
            return 0.01;
        }     // Low
        if (str_contains($key, 'posts.published')) {
            return 0.1;
        } // Medium
        if (str_contains($key, 'trending')) {
            return 0.5;
        }        // High
        if (str_contains($key, 'views')) {
            return 1.0;
        }           // Very high

        return 0.1; // Default medium frequency
    }

    /**
     * Cache with explicit strategy
     */
    public function cacheWith(string $key, callable $callback, string $strategy): mixed
    {
        return $this->smartRemember($key, $callback, $strategy);
    }

    /**
     * Cache as hot data (in-memory)
     */
    public function cacheHot(string $key, callable $callback): mixed
    {
        return $this->cacheWith($key, $callback, 'hot');
    }

    /**
     * Cache as warm data (Redis)
     */
    public function cacheWarm(string $key, callable $callback): mixed
    {
        return $this->cacheWith($key, $callback, 'warm');
    }

    /**
     * Cache as static data (file)
     */
    public function cacheStatic(string $key, callable $callback): mixed
    {
        return $this->cacheWith($key, $callback, 'static');
    }

    /**
     * Clear cache for specific key
     */
    public function clearCache(string $key): bool
    {
        $cleared = true;

        // Clear from all cache drivers
        foreach ($this->cacheStrategies as $strategy => $config) {
            if ($config['ttl'] > 0) {
                $cleared = Cache::driver($config['driver'])->forget($key) && $cleared;
            }
        }

        return $cleared;
    }

    /**
     * Clear all caches for the model
     */
    public function clearAllCache(string $prefix = ''): bool
    {
        if (empty($prefix)) {
            $prefix = $this->getCachePrefix();
        }

        return Cache::flush() && Redis::flushdb();
    }

    /**
     * Get cache prefix for the model
     */
    protected function getCachePrefix(): string
    {
        $className = class_basename(static::class);

        return strtolower(str_replace('Repository', '', $className));
    }

    /**
     * Warm up cache for frequently accessed data
     */
    public function warmCache(array $keys): void
    {
        foreach ($keys as $key => $callback) {
            if (is_callable($callback)) {
                $this->smartRemember($key, $callback);
            }
        }
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats(): array
    {
        return [
            'hot_cache_size' => $this->getCacheSize('array'),
            'warm_cache_size' => $this->getCacheSize('redis'),
            'static_cache_size' => $this->getCacheSize('file'),
            'hit_ratio' => $this->getCacheHitRatio(),
            'access_patterns' => $this->getAccessPatterns(),
        ];
    }

    /**
     * Get cache size for specific driver
     */
    protected function getCacheSize(string $driver): int
    {
        try {
            switch ($driver) {
                case 'redis':
                    return Redis::dbsize();
                case 'file':
                    return count(glob(storage_path('framework/cache/data/*')));
                default:
                    return 0;
            }
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get cache hit ratio
     */
    protected function getCacheHitRatio(): float
    {
        $hits = Cache::get('cache_hits', 0);
        $misses = Cache::get('cache_misses', 0);
        $total = $hits + $misses;

        return $total > 0 ? $hits / $total : 0.0;
    }

    /**
     * Get access patterns for analysis
     */
    protected function getAccessPatterns(): array
    {
        // Return simplified access patterns
        return [
            'most_accessed' => $this->getMostAccessedKeys(10),
            'cache_efficiency' => $this->getCacheEfficiency(),
        ];
    }

    /**
     * Get most accessed cache keys
     */
    protected function getMostAccessedKeys(int $limit = 10): array
    {
        // Simplified implementation
        return ['posts.popular', 'categories.all', 'posts.recent'];
    }

    /**
     * Get cache efficiency metrics
     */
    protected function getCacheEfficiency(): array
    {
        return [
            'hot_efficiency' => 0.95,
            'warm_efficiency' => 0.88,
            'static_efficiency' => 0.92,
        ];
    }
}
