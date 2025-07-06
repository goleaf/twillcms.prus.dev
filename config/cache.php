<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache connection that gets used while
    | using this caching library. This connection is used when another is
    | not explicitly specified when executing a given caching function.
    |
    */

    'default' => env('CACHE_DRIVER', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the cache "stores" for your application as
    | well as their drivers. You may even define multiple stores for the
    | same cache driver to group types of items stored in your caches.
    |
    | Supported drivers: "apc", "array", "database", "file",
    |            "memcached", "redis", "dynamodb", "octane", "null"
    |
    */

    'stores' => [

        'apc' => [
            'driver' => 'apc',
        ],

        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'cache',
            'connection' => null,
            'lock_connection' => null,
        ],

        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
            'lock_path' => storage_path('framework/cache/data'),
        ],

        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => env('MEMCACHED_PERSISTENT_ID'),
            'sasl' => [
                env('MEMCACHED_USERNAME'),
                env('MEMCACHED_PASSWORD'),
            ],
            'options' => [
                // Memcached::OPT_CONNECT_TIMEOUT => 2000,
            ],
            'servers' => [
                [
                    'host' => env('MEMCACHED_HOST', '127.0.0.1'),
                    'port' => env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'lock_connection' => 'default',
        ],

        'dynamodb' => [
            'driver' => 'dynamodb',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'table' => env('DYNAMODB_CACHE_TABLE', 'cache'),
            'endpoint' => env('DYNAMODB_ENDPOINT'),
        ],

        'octane' => [
            'driver' => 'octane',
        ],

        // Performance-optimized cache stores
        'api' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/api'),
            'lock_path' => storage_path('framework/cache/api'),
        ],

        'posts' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/posts'),
            'lock_path' => storage_path('framework/cache/posts'),
        ],

        'categories' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/categories'),
            'lock_path' => storage_path('framework/cache/categories'),
        ],

        'views' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/views'),
            'lock_path' => storage_path('framework/cache/views'),
        ],

        'long_term' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/long_term'),
            'lock_path' => storage_path('framework/cache/long_term'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | When utilizing the APC, database, memcached, Redis, or DynamoDB cache
    | stores, there might be other applications using the same cache. For
    | that reason, you may prefix every cache key to avoid collisions.
    |
    */

    'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_'),

    /*
    |--------------------------------------------------------------------------
    | Cache Tags
    |--------------------------------------------------------------------------
    |
    | Cache tags allow you to tag related pieces of data in the cache so that
    | you can flush all tagged cache entries. This is useful for cache
    | invalidation strategies and grouping related cache data.
    |
    */

    'tags' => [
        'posts' => ['posts', 'content'],
        'categories' => ['categories', 'content'],
        'site' => ['site', 'config'],
        'api' => ['api', 'responses'],
        'views' => ['views', 'templates'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache TTL Settings
    |--------------------------------------------------------------------------
    |
    | Define default Time To Live (TTL) values for different types of cached
    | data. This helps with cache invalidation and ensures fresh content.
    |
    */

    'ttl' => [
        // API responses
        'api' => [
            'posts' => env('CACHE_TTL_POSTS', 1800), // 30 minutes
            'categories' => env('CACHE_TTL_CATEGORIES', 3600), // 1 hour
            'site_config' => env('CACHE_TTL_SITE_CONFIG', 86400), // 24 hours
            'search' => env('CACHE_TTL_SEARCH', 900), // 15 minutes
        ],

        // View caches
        'views' => [
            'partial' => env('CACHE_TTL_PARTIAL_VIEWS', 3600), // 1 hour
            'full' => env('CACHE_TTL_FULL_VIEWS', 1800), // 30 minutes
        ],

        // Long-term caches
        'long_term' => [
            'assets' => env('CACHE_TTL_ASSETS', 604800), // 7 days
            'static' => env('CACHE_TTL_STATIC', 2592000), // 30 days
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Performance Settings
    |--------------------------------------------------------------------------
    |
    | Performance-related cache configurations for optimization.
    |
    */

    'performance' => [
        // Enable cache compression for large objects
        'compress' => env('CACHE_COMPRESS', true),

        // Cache size limits (in bytes)
        'size_limits' => [
            'small' => 1024 * 10,    // 10KB
            'medium' => 1024 * 100,  // 100KB
            'large' => 1024 * 1024,  // 1MB
        ],

        // Enable cache warming
        'warm_cache' => env('CACHE_WARM_ENABLED', true),

        // Cache invalidation strategies
        'invalidation' => [
            'strategy' => env('CACHE_INVALIDATION_STRATEGY', 'tag'), // tag, key, time
            'cascade' => env('CACHE_INVALIDATION_CASCADE', true),
        ],

        // Cache monitoring
        'monitoring' => [
            'enabled' => env('CACHE_MONITORING_ENABLED', true),
            'log_hits' => env('CACHE_LOG_HITS', false),
            'log_misses' => env('CACHE_LOG_MISSES', true),
        ],
    ],

];
