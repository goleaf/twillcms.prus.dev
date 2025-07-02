<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AllRoutesTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function test_health_check_route()
    {
        $response = $this->get('/api/health');
        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);
    }

    /** @test */
    public function test_language_switch_api_route()
    {
        // In single-language mode, all requests should return 'en' as the active locale
        $response = $this->post('/api/language/en');
        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'locale' => 'en']);
        // Single-language mode: all requests return English

        // Even requesting Lithuanian should return English (single-language mode)
        $response = $this->post('/api/language/lt');
        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'locale' => 'en']); // Always returns 'en'

        // Invalid locales should still return error
        $response = $this->post('/api/language/invalid');
        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }

    /** @test */
    public function test_site_config_api_route()
    {
        $response = $this->get('/api/v1/site/config');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/json');
    }

    /** @test */
    public function test_posts_index_api_route()
    {
        $response = $this->get('/api/v1/posts');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/json');
    }

    /** @test */
    public function test_categories_index_api_route()
    {
        $response = $this->get('/api/v1/categories');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/json');
    }

    /** @test */
    public function test_spa_routes_serve_vue_app()
    {
        $vueRoutes = [
            '/',
            '/blog',
            '/categories',
            '/search',
            '/category/technology',
            '/post/sample-post',
            '/about',
            '/contact'
        ];

        foreach ($vueRoutes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
            
            // All routes should serve the Vue SPA
            $response->assertSee('<!DOCTYPE html>', false);
            $response->assertSee('id="app"', false);
            // Note: @vite is processed during build, not visible in output
            
            // Should not contain Laravel Blade content (except the SPA template)
            $response->assertDontSee('@yield', false);
            $response->assertDontSee('@extends', false);
        }
    }

    /** @test */
    public function test_admin_routes_exist_separately()
    {
        // Test that admin routes exist (will require authentication but should not be 404)
        $adminRoutes = [
            '/admin',
            '/admin/posts',
            '/admin/categories'
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->get($route);
            // Should not be 404, could be 302 (redirect to login) or 200
            $this->assertNotEquals(404, $response->getStatusCode(), "Route {$route} should exist");
            
            // Admin routes should NOT serve the Vue SPA
            if ($response->getStatusCode() === 200) {
                $response->assertDontSee('<div id="app"', false);
            }
        }
    }

    /** @test */
    public function test_api_fallback_route()
    {
        $response = $this->get('/api/v1/nonexistent-endpoint');
        $response->assertStatus(404);
        $response->assertJson([
            'error' => 'API endpoint not found',
            'message' => 'The requested API endpoint does not exist.'
        ]);
    }

    /** @test */
    public function test_route_names_are_properly_defined()
    {
        // Test that all our named routes work
        $namedRoutes = [
            'api.health' => '/api/health',
            'api.language.switch' => '/api/language/en',
            'api.site.config' => '/api/v1/site/config',
            'api.posts.index' => '/api/v1/posts',
            'api.categories.index' => '/api/v1/categories'
        ];

        foreach ($namedRoutes as $name => $expectedPath) {
            if ($name === 'api.language.switch') {
                $url = route($name, ['locale' => 'en']);
                $this->assertStringContainsString('/api/language/en', $url, "Route name {$name} should generate correct URL");
            } else {
                $url = route($name);
                $this->assertStringContainsString($expectedPath, $url, "Route name {$name} should generate correct URL");
            }
        }
    }

    /** @test */
    public function test_vue_spa_consistency()
    {
        // Test that all Vue routes serve identical HTML (SPA architecture)
        $response1 = $this->get('/');
        $response2 = $this->get('/blog');
        $response3 = $this->get('/categories');
        $response4 = $this->get('/search');

        // All should return the same SPA HTML
        $this->assertEquals($response1->getContent(), $response2->getContent());
        $this->assertEquals($response1->getContent(), $response3->getContent());
        $this->assertEquals($response1->getContent(), $response4->getContent());

        // Should contain Vue app structure
        $response1->assertSee('<div id="app"', false); // Changed to partial match since v-cloak attribute is present
        $response1->assertSee('v-cloak', false);
    }

    /** @test */
    public function test_all_api_v1_routes_exist()
    {
        $apiRoutes = [
            '/api/v1/site/config',
            '/api/v1/site/stats',
            '/api/v1/site/archives',
            '/api/v1/posts',
            '/api/v1/posts/popular',
            '/api/v1/posts/recent',
            '/api/v1/posts/search',
            '/api/v1/categories',
            '/api/v1/categories/navigation',
            '/api/v1/categories/popular'
        ];

        foreach ($apiRoutes as $route) {
            $response = $this->get($route);
            $this->assertLessThan(500, $response->getStatusCode(), "Route {$route} should not return 500 error");
            $this->assertNotEquals(404, $response->getStatusCode(), "Route {$route} should exist");
            
            // All API routes should return JSON
            if ($response->getStatusCode() === 200) {
                $response->assertHeader('content-type', 'application/json');
            }
        }
    }

    /** @test */
    public function test_twill_admin_routes_exist()
    {
        $twillRoutes = [
            '/admin',
            '/admin/posts',
            '/admin/categories',
            '/admin/users',
            '/admin/media-library/medias',
            '/admin/file-library/files'
        ];

        foreach ($twillRoutes as $route) {
            $response = $this->get($route);
            // Should not be 404, authentication/authorization issues are OK
            $this->assertNotEquals(404, $response->getStatusCode(), "Twill route {$route} should exist");
            
            // Twill routes should NOT serve Vue SPA
            if ($response->getStatusCode() === 200) {
                $response->assertDontSee('<div id="app"', false);
                $response->assertDontSee('v-cloak', false);
            }
        }
    }

    /** @test */
    public function test_storage_routes_work()
    {
        // Test storage route (will be 404 for non-existent files but route should exist)
        $response = $this->get('/storage/test-file.jpg');
        $response->assertStatus(404); // File doesn't exist but route works

        // Test image processing route (Glide)
        $response = $this->get('/img/test.jpg');
        // Should be handled by Glide, not return 404 route not found
        $this->assertNotEquals(404, $response->getStatusCode());
    }

    /** @test */
    public function test_no_blade_frontend_routes_exist()
    {
        // Ensure no old Blade routes are still accessible
        $oldBladeRoutes = [
            '/home',
            '/welcome',
            '/blog/index',
            '/categories/index'
        ];

        foreach ($oldBladeRoutes as $route) {
            $response = $this->get($route);
            
            // These routes should either not exist (404) or redirect to SPA
            if ($response->getStatusCode() === 200) {
                // If they exist, they should serve the Vue SPA, not Blade content
                $response->assertSee('<div id="app"', false);
                $response->assertDontSee('@yield', false);
                $response->assertDontSee('@extends', false);
            }
        }
    }

    /** @test */
    public function test_api_endpoints_support_cors_for_spa()
    {
        $response = $this->get('/api/v1/site/config');
        $response->assertStatus(200);
        
        // Should have CORS headers for SPA
        $response->assertHeader('Access-Control-Allow-Origin');
    }

    /** @test */
    public function test_vue_specific_api_endpoints()
    {
        // Test endpoints specifically designed for Vue.js frontend
        $vueApiRoutes = [
            '/api/v1/posts/search' => ['q' => 'test'],
            '/api/v1/categories/navigation' => [],
            '/api/v1/posts/popular' => [],
            '/api/v1/posts/recent' => []
        ];

        foreach ($vueApiRoutes as $route => $params) {
            $response = $this->get($route . ($params ? '?' . http_build_query($params) : ''));
            $response->assertStatus(200);
            $response->assertHeader('content-type', 'application/json');
        }
    }
} 