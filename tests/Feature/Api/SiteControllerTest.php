<?php

namespace Tests\Feature\Api;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SiteControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestData();
    }

    protected function createTestData(): void
    {
        // Create posts for different years/months
        $post1 = Post::factory()->create([
            'published' => true,
            'created_at' => '2023-01-15',
        ]);

        $post2 = Post::factory()->create([
            'published' => true,
            'created_at' => '2023-02-20',
        ]);

        $post3 = Post::factory()->create([
            'published' => true,
            'created_at' => '2024-01-10',
        ]);

        // Create slugs
        $post1->slugs()->create(['slug' => 'post-2023-01', 'locale' => 'en', 'active' => true]);
        $post2->slugs()->create(['slug' => 'post-2023-02', 'locale' => 'en', 'active' => true]);
        $post3->slugs()->create(['slug' => 'post-2024-01', 'locale' => 'en', 'active' => true]);
    }

    /** @test */
    public function it_returns_health_status()
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
                'timestamp' => now()->toISOString(),
                'service' => 'TwillCMS API',
                'version' => '1.0.0',
            ]);
    }

    /** @test */
    public function it_returns_site_configuration()
    {
        $response = $this->getJson('/api/v1/site/config');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'site' => [
                    'name',
                    'description',
                    'url',
                    'locale',
                    'available_locales',
                    'timezone',
                ],
                'meta' => [
                    'generator',
                    'version',
                    'api_version',
                ],
                'social' => [
                    'twitter',
                    'github',
                ],
                'features' => [
                    'search',
                    'categories',
                    'archives',
                    'translations',
                    'rss',
                ],
            ]);

        $site = $response->json('site');
        $this->assertEquals('Laravel', $site['name']);
        $this->assertEquals('A modern blog built with TwillCMS and Vue.js', $site['description']);
        $this->assertEquals('en', $site['locale']);
        $this->assertContains('en', $site['available_locales']);
    }

    /** @test */
    public function it_returns_translations()
    {
        $response = $this->getJson('/api/v1/site/translations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'locale',
                'translations' => [
                    'common',
                    'navigation',
                    'blog',
                    'search',
                    'categories',
                    'pagination',
                    'meta',
                    'archive',
                    'errors',
                    'accessibility',
                ],
            ]);

        $this->assertEquals('en', $response->json('locale'));
    }

    /** @test */
    public function it_returns_translations_for_specific_locale()
    {
        $response = $this->getJson('/api/v1/site/translations/lt');

        $response->assertStatus(200);
        $this->assertEquals('lt', $response->json('locale'));
    }

    /** @test */
    public function it_returns_404_for_unsupported_locale()
    {
        $response = $this->getJson('/api/v1/site/translations/fr');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_archives_by_year()
    {
        $response = $this->getJson('/api/v1/site/archives');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'year',
                    'posts_count',
                    'months' => [
                        '*' => [
                            'month',
                            'month_name',
                            'posts_count',
                        ],
                    ],
                ],
            ]);

        $data = $response->json();
        $this->assertGreaterThan(0, count($data));

        // Check 2023 data
        $year2023 = collect($data)->firstWhere('year', 2023);
        $this->assertNotNull($year2023);
        $this->assertEquals(2, $year2023['posts_count']);
        $this->assertCount(2, $year2023['months']); // January and February
    }

    /** @test */
    public function it_caches_site_configuration()
    {
        Cache::flush();

        // First request
        $response1 = $this->getJson('/api/v1/site/config');
        $response1->assertStatus(200);

        // Second request should use cache
        $response2 = $this->getJson('/api/v1/site/config');
        $response2->assertStatus(200);

        // Responses should be identical
        $this->assertEquals($response1->json(), $response2->json());
    }

    /** @test */
    public function it_includes_proper_cache_headers_for_config()
    {
        $response = $this->getJson('/api/v1/site/config');

        $response->assertStatus(200)
            ->assertHeader('Cache-Control', 'public, max-age=3600'); // 1 hour
    }

    /** @test */
    public function it_includes_proper_cache_headers_for_translations()
    {
        $response = $this->getJson('/api/v1/site/translations');

        $response->assertStatus(200)
            ->assertHeader('Cache-Control', 'public, max-age=3600'); // 1 hour
    }

    /** @test */
    public function it_includes_proper_cache_headers_for_archives()
    {
        $response = $this->getJson('/api/v1/site/archives');

        $response->assertStatus(200)
            ->assertHeader('Cache-Control', 'public, max-age=1800'); // 30 minutes
    }

    /** @test */
    public function archives_are_ordered_by_year_desc()
    {
        $response = $this->getJson('/api/v1/site/archives');

        $response->assertStatus(200);

        $data = $response->json();
        $years = collect($data)->pluck('year');

        // Should be in descending order
        $this->assertEquals($years->sort()->reverse()->values(), $years->values());
    }

    /** @test */
    public function archive_months_are_ordered_by_month_desc()
    {
        $response = $this->getJson('/api/v1/site/archives');

        $response->assertStatus(200);

        $data = $response->json();
        $year2023 = collect($data)->firstWhere('year', 2023);

        if ($year2023) {
            $months = collect($year2023['months'])->pluck('month');
            // February (2) should come before January (1) in descending order
            $this->assertEquals([2, 1], $months->values()->toArray());
        }
    }
}
