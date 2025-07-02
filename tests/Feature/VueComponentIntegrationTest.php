<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VueComponentIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_spa_serves_vue_application_correctly()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('<!DOCTYPE html>', false);
        $response->assertSee('<div id="app"', false);
        $response->assertSee('@vite', false);
        
        // Should include Vue app meta tags
        $response->assertSee('<meta name="viewport"', false);
        $response->assertSee('<meta charset="utf-8">', false);
    }

    /** @test */
    public function test_spa_includes_proper_seo_meta_tags()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        
        // Primary meta tags
        $response->assertSee('<title>', false);
        $response->assertSee('<meta name="description"', false);
        $response->assertSee('<meta name="keywords"', false);
        
        // Open Graph tags
        $response->assertSee('<meta property="og:type"', false);
        $response->assertSee('<meta property="og:title"', false);
        $response->assertSee('<meta property="og:description"', false);
        
        // Twitter tags
        $response->assertSee('<meta property="twitter:card"', false);
        $response->assertSee('<meta property="twitter:title"', false);
    }

    /** @test */
    public function test_spa_includes_performance_optimizations()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        
        // Preconnect links for performance
        $response->assertSee('<link rel="preconnect"', false);
        $response->assertSee('<link rel="dns-prefetch"', false);
        
        // Critical CSS
        $response->assertSee('<style>', false);
        $response->assertSee('loading-spinner', false);
        
        // Proper favicon setup
        $response->assertSee('<link rel="icon"', false);
        $response->assertSee('<link rel="apple-touch-icon"', false);
    }

    /** @test */
    public function test_spa_includes_structured_data()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('<script type="application/ld+json">', false);
        $response->assertSee('"@context": "https://schema.org"', false);
        $response->assertSee('"@type": "WebSite"', false);
    }

    /** @test */
    public function test_spa_has_proper_noscript_fallback()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('<noscript>', false);
        $response->assertSee('requires JavaScript', false);
    }

    /** @test */
    public function test_all_vue_routes_serve_same_spa_content()
    {
        $vueRoutes = [
            '/',
            '/blog',
            '/categories',
            '/search',
            '/category/technology',
            '/post/sample-post'
        ];

        $firstResponse = $this->get('/');
        $firstContent = $firstResponse->getContent();

        foreach ($vueRoutes as $route) {
            $response = $this->get($route);
            
            $response->assertStatus(200);
            
            // All Vue routes should serve the same SPA HTML
            $this->assertEquals($firstContent, $response->getContent());
        }
    }

    /** @test */
    public function test_api_endpoints_return_proper_json_for_vue()
    {
        // Test site config API
        $response = $this->get('/api/v1/site/config');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/json');
        $response->assertJsonStructure([
            'name',
            'description'
        ]);

        // Test posts API
        $response = $this->get('/api/v1/posts');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/json');
        
        // Test categories API
        $response = $this->get('/api/v1/categories');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/json');
    }

    /** @test */
    public function test_api_cors_headers_for_vue_spa()
    {
        $response = $this->get('/api/v1/site/config');
        
        $response->assertStatus(200);
        
        // Should have proper CORS headers for SPA
        $response->assertHeader('Access-Control-Allow-Origin');
    }

    /** @test */
    public function test_language_switching_api_for_vue()
    {
        $response = $this->post('/api/language/en');
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'locale' => 'en'
        ]);

        $response = $this->post('/api/language/lt');
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'locale' => 'lt'
        ]);

        // Test invalid locale
        $response = $this->post('/api/language/invalid');
        $response->assertStatus(400);
        $response->assertJson([
            'success' => false
        ]);
    }

    /** @test */
    public function test_api_search_functionality_for_vue()
    {
        $response = $this->get('/api/v1/posts/search?q=test');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/json');
        
        // Should return search results structure
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'slug'
                ]
            ]
        ]);
    }

    /** @test */
    public function test_health_check_api_for_vue_monitoring()
    {
        $response = $this->get('/api/health');
        
        $response->assertStatus(200);
        $response->assertExactJson([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'service' => 'twillcms-blog'
        ]);
    }

    /** @test */
    public function test_spa_includes_service_worker_registration()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('serviceWorker', false);
        $response->assertSee('navigator.serviceWorker.register', false);
    }

    /** @test */
    public function test_admin_routes_are_separate_from_spa()
    {
        $adminRoutes = [
            '/admin',
            '/admin/posts',
            '/admin/categories'
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->get($route);
            
            // Admin routes should not serve the Vue SPA
            // They should either redirect to login or show Twill admin
            $this->assertNotEquals(404, $response->getStatusCode());
            
            // Should not contain Vue app div
            if ($response->getStatusCode() === 200) {
                $response->assertDontSee('<div id="app"', false);
            }
        }
    }

    /** @test */
    public function test_api_error_responses_for_vue_error_handling()
    {
        // Test 404 API response
        $response = $this->get('/api/v1/nonexistent-endpoint');
        $response->assertStatus(404);
        $response->assertJson([
            'error' => 'API endpoint not found',
            'message' => 'The requested API endpoint does not exist.'
        ]);

        // Test method not allowed
        $response = $this->put('/api/health');
        $response->assertStatus(405);
    }

    /** @test */
    public function test_api_pagination_for_vue_infinite_scroll()
    {
        $response = $this->get('/api/v1/posts?page=1&per_page=10');
        $response->assertStatus(200);
        
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'current_page',
                'total',
                'per_page',
                'last_page'
            ]
        ]);
    }

    /** @test */
    public function test_vite_assets_are_properly_configured()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        
        // Should include Vite dev server or built assets
        $this->assertTrue(
            str_contains($response->getContent(), '@vite') ||
            str_contains($response->getContent(), '/build/')
        );
    }
} 