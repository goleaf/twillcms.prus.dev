<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test as TestMethod;
use Tests\TestCase;

class AllRoutesTest extends TestCase
{
    use RefreshDatabase;

    #[TestMethod]
    public function test_health_check_route()
    {
        $response = $this->get('/api/health');
        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);
    }

    #[TestMethod]
    public function test_language_switch_api_route()
    {
        // Language switching API has been removed in single-language mode
        $response = $this->post('/api/language/en');
        $response->assertStatus(405); // Method not allowed

        $response = $this->post('/api/language/lt');
        $response->assertStatus(405); // Method not allowed

        $response = $this->post('/api/language/invalid');
        $response->assertStatus(405); // Method not allowed
    }

    #[TestMethod]
    public function test_site_config_api_route()
    {
        $response = $this->get('/api/v1/site/config');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/json');
    }

    #[TestMethod]
    public function test_posts_index_api_route()
    {
        $response = $this->get('/api/v1/posts');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/json');
    }

    #[TestMethod]
    public function test_categories_index_api_route()
    {
        $response = $this->get('/api/v1/categories');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/json');
    }

    #[TestMethod]
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
            '/contact',
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

    #[TestMethod]
    public function test_admin_routes_exist_separately()
    {
        // Test that admin routes exist (will require authentication but should not be 404)
        $adminRoutes = [
            '/admin',
            '/admin/posts',
            '/admin/categories',
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

    #[TestMethod]
    public function test_api_fallback_route()
    {
        $response = $this->get('/api/v1/nonexistent-endpoint');
        $response->assertStatus(404);
        $response->assertJson([
            'error' => 'API endpoint not found',
            'message' => 'The requested API endpoint does not exist.',
        ]);
    }

    #[TestMethod]
    public function test_route_names_are_properly_defined()
    {
        // Test that all our named routes work
        $namedRoutes = [
            'api.health' => '/api/health',
            'api.site.config' => '/api/v1/site/config',
            'api.posts.index' => '/api/v1/posts',
            'api.categories.index' => '/api/v1/categories',
        ];

        foreach ($namedRoutes as $name => $expectedPath) {
            $url = route($name);
            $this->assertStringContainsString($expectedPath, $url, "Route name {$name} should generate correct URL");
        }
    }

    #[TestMethod]
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

    #[TestMethod]
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
            '/api/v1/categories/popular',
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

    #[TestMethod]
    public function test_storage_routes_work()
    {
        // Test storage route - should be handled by storage system
        $response = $this->get('/storage/test-file.jpg');
        // Storage route should exist and be handled (could be 404 for missing file or 200 with placeholder)
        $this->assertNotEquals(500, $response->getStatusCode(), 'Storage route should not return 500 error');

        // Test image processing route (Glide)
        $response = $this->get('/img/test.jpg');
        // Should be handled by image processing system, not return 404 route not found
        $this->assertNotEquals(404, $response->getStatusCode());
        $this->assertNotEquals(500, $response->getStatusCode());
    }

    #[TestMethod]
    public function test_no_blade_frontend_routes_exist()
    {
        // Ensure no old Blade routes are still accessible
        $oldBladeRoutes = [
            '/home',
            '/welcome',
            '/blog/index',
            '/categories/index',
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

    #[TestMethod]
    public function test_api_endpoints_support_cors_for_spa()
    {
        $response = $this->get('/api/v1/site/config');
        $response->assertStatus(200);

        // Should have CORS headers for SPA
        $response->assertHeader('Access-Control-Allow-Origin');
    }

    #[TestMethod]
    public function test_vue_specific_api_endpoints()
    {
        // Test endpoints specifically designed for Vue.js frontend
        $vueApiRoutes = [
            '/api/v1/posts/search' => ['q' => 'test'],
            '/api/v1/categories/navigation' => [],
            '/api/v1/posts/popular' => [],
            '/api/v1/posts/recent' => [],
        ];

        foreach ($vueApiRoutes as $route => $params) {
            $response = $this->get($route.($params ? '?'.http_build_query($params) : ''));
            $response->assertStatus(200);
            $response->assertHeader('content-type', 'application/json');
        }
    }
}
