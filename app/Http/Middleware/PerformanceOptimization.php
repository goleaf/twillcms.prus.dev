<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class PerformanceOptimization
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $startTime = microtime(true);

        // Check if request should be cached
        if ($this->shouldCache($request)) {
            $cacheKey = $this->generateCacheKey($request);

            // Try to get cached response
            $cachedResponse = Cache::store('api')->get($cacheKey);

            if ($cachedResponse && $this->isCacheValid($cachedResponse)) {
                $response = $this->createResponseFromCache($cachedResponse);
                $this->addPerformanceHeaders($response, $startTime, true);

                return $response;
            }
        }

        // Process the request
        $response = $next($request);

        // Apply optimizations to the response
        $this->optimizeResponse($response, $request);

        // Cache the response if appropriate
        if ($this->shouldCache($request) && $response->getStatusCode() === 200) {
            $this->cacheResponse($request, $response);
        }

        // Add performance headers
        $this->addPerformanceHeaders($response, $startTime, false);

        return $response;
    }

    /**
     * Determine if the request should be cached.
     */
    protected function shouldCache(Request $request): bool
    {
        // Don't cache non-GET requests
        if (! $request->isMethod('GET')) {
            return false;
        }

        // Don't cache authenticated requests (if any)
        if ($request->user()) {
            return false;
        }

        // Cache API routes
        if ($request->is('api/*')) {
            return true;
        }

        // Cache specific routes
        $cacheableRoutes = [
            'api/v1/posts*',
            'api/v1/categories*',
            'api/v1/site/*',
        ];

        foreach ($cacheableRoutes as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate a cache key for the request.
     */
    protected function generateCacheKey(Request $request): string
    {
        $key = 'api_response:'.md5($request->fullUrl());

        // Add query parameters to cache key
        if ($request->query()) {
            $queryString = http_build_query($request->query());
            $key .= ':'.md5($queryString);
        }

        // Add language/locale to cache key if applicable
        if ($request->header('Accept-Language')) {
            $key .= ':'.md5($request->header('Accept-Language'));
        }

        return $key;
    }

    /**
     * Check if cached response is still valid.
     */
    protected function isCacheValid(array $cachedResponse): bool
    {
        if (! isset($cachedResponse['expires_at'])) {
            return false;
        }

        return now()->timestamp < $cachedResponse['expires_at'];
    }

    /**
     * Create a response from cached data.
     */
    protected function createResponseFromCache(array $cachedResponse): Response
    {
        $response = new Response(
            $cachedResponse['content'],
            $cachedResponse['status'],
            $cachedResponse['headers']
        );

        // Add cache hit header
        $response->headers->set('X-Cache', 'HIT');

        return $response;
    }

    /**
     * Cache the response.
     */
    protected function cacheResponse(Request $request, Response $response): void
    {
        $cacheKey = $this->generateCacheKey($request);
        $ttl = $this->getCacheTtl($request);

        $cacheData = [
            'content' => $response->getContent(),
            'status' => $response->getStatusCode(),
            'headers' => $response->headers->all(),
            'expires_at' => now()->addSeconds($ttl)->timestamp,
            'created_at' => now()->timestamp,
        ];

        try {
            Cache::store('api')->put($cacheKey, $cacheData, $ttl);
        } catch (\Exception $e) {
            Log::warning('Failed to cache response', [
                'error' => $e->getMessage(),
                'cache_key' => $cacheKey,
                'url' => $request->fullUrl(),
            ]);
        }
    }

    /**
     * Get cache TTL for the request.
     */
    protected function getCacheTtl(Request $request): int
    {
        $defaultTtl = 1800; // 30 minutes

        $ttlMap = [
            'api/v1/posts' => config('cache.ttl.api.posts', 1800),
            'api/v1/categories' => config('cache.ttl.api.categories', 3600),
            'api/v1/site/config' => config('cache.ttl.api.site_config', 86400),
            'api/v1/search' => config('cache.ttl.api.search', 900),
        ];

        foreach ($ttlMap as $pattern => $ttl) {
            if ($request->is($pattern.'*')) {
                return $ttl;
            }
        }

        return $defaultTtl;
    }

    /**
     * Optimize the response.
     */
    protected function optimizeResponse(Response $response, Request $request): void
    {
        // Enable compression if supported
        if ($this->supportsCompression($request)) {
            $this->enableCompression($response);
        }

        // Set caching headers
        $this->setCacheHeaders($response, $request);

        // Set security headers
        $this->setSecurityHeaders($response);

        // Optimize JSON responses
        if ($this->isJsonResponse($response)) {
            $this->optimizeJsonResponse($response);
        }

        // Set CORS headers for API routes
        if ($request->is('api/*')) {
            $this->setCorsHeaders($response);
        }
    }

    /**
     * Check if client supports compression.
     */
    protected function supportsCompression(Request $request): bool
    {
        $acceptEncoding = $request->header('Accept-Encoding', '');

        return strpos($acceptEncoding, 'gzip') !== false;
    }

    /**
     * Enable response compression.
     */
    protected function enableCompression(Response $response): void
    {
        $content = $response->getContent();

        // Only compress if content is large enough
        if (strlen($content) > 1024) {
            $compressed = gzencode($content, 6);

            if ($compressed !== false) {
                $response->setContent($compressed);
                $response->headers->set('Content-Encoding', 'gzip');
                $response->headers->set('Content-Length', strlen($compressed));
            }
        }
    }

    /**
     * Set cache headers.
     */
    protected function setCacheHeaders(Response $response, Request $request): void
    {
        if ($request->is('api/*')) {
            $ttl = $this->getCacheTtl($request);

            $response->headers->set('Cache-Control', 'public, max-age='.$ttl);
            $response->headers->set('Expires', now()->addSeconds($ttl)->toRfc7231String());
            $response->headers->set('Last-Modified', now()->toRfc7231String());

            // Set ETag for better caching
            $etag = md5($response->getContent());
            $response->headers->set('ETag', '"'.$etag.'"');

            // Check if client has valid cached version
            if ($request->header('If-None-Match') === '"'.$etag.'"') {
                $response->setStatusCode(304);
                $response->setContent('');
            }
        }
    }

    /**
     * Set security headers.
     */
    protected function setSecurityHeaders(Response $response): void
    {
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // CSP for API responses
        $response->headers->set('Content-Security-Policy', "default-src 'none'");
    }

    /**
     * Check if response is JSON.
     */
    protected function isJsonResponse(Response $response): bool
    {
        return strpos($response->headers->get('Content-Type', ''), 'application/json') !== false;
    }

    /**
     * Optimize JSON response.
     */
    protected function optimizeJsonResponse(Response $response): void
    {
        $content = $response->getContent();

        if (! empty($content)) {
            try {
                $data = json_decode($content, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    // Re-encode with optimized flags
                    $optimized = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    $response->setContent($optimized);
                }
            } catch (\Exception $e) {
                // If optimization fails, keep original content
                Log::debug('JSON optimization failed', ['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Set CORS headers.
     */
    protected function setCorsHeaders(Response $response): void
    {
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        $response->headers->set('Access-Control-Max-Age', '3600');
    }

    /**
     * Add performance headers.
     */
    protected function addPerformanceHeaders(Response $response, float $startTime, bool $fromCache): void
    {
        $executionTime = round((microtime(true) - $startTime) * 1000, 2);

        $response->headers->set('X-Response-Time', $executionTime.'ms');
        $response->headers->set('X-Cache', $fromCache ? 'HIT' : 'MISS');
        $response->headers->set('X-Powered-By', 'TwillCMS-Performance-Optimized');

        // Add memory usage in development
        if (config('app.debug')) {
            $memoryUsage = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
            $response->headers->set('X-Memory-Usage', $memoryUsage.'MB');
        }
    }
}
