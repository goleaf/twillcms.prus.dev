<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

        // Process the request
        $response = $next($request);

        // Apply optimizations to the response
        $this->optimizeResponse($response, $request);

        // Add performance headers
        $this->addPerformanceHeaders($response, $startTime);

        return $response;
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

        // Set security headers
        $this->setSecurityHeaders($response);

        // Optimize JSON responses
        if ($this->isJsonResponse($response)) {
            $this->optimizeJsonResponse($response);
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
     * Set security headers.
     */
    protected function setSecurityHeaders(Response $response): void
    {
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
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
     * Add performance headers.
     */
    protected function addPerformanceHeaders(Response $response, float $startTime): void
    {
        $executionTime = round((microtime(true) - $startTime) * 1000, 2);

        $response->headers->set('X-Response-Time', $executionTime.'ms');
        $response->headers->set('X-Powered-By', 'TwillCMS-Performance-Optimized');

        // Add memory usage in development
        if (config('app.debug')) {
            $memoryUsage = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
            $response->headers->set('X-Memory-Usage', $memoryUsage.'MB');
        }
    }
}
