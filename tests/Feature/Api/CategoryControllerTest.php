<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestData();
    }

    protected function createTestData(): void
    {
        // Create categories using proper Twill structure
        $tech = Category::factory()->withTitle('Technology')->create([
            'published' => true,
            'position' => 1,
        ]);

        $design = Category::factory()->withTitle('Design')->create([
            'published' => true,
            'position' => 2,
        ]);

        $unpublished = Category::factory()->withTitle('Unpublished')->create([
            'published' => false,
        ]);

        // Create posts with content directly (single-language mode)
        $post1 = Post::factory()->create([
            'published' => true, 
            'published_at' => now(),
            'title' => 'Post 1',
            'description' => 'Description 1',
            'content' => 'Content 1',
        ]);

        $post2 = Post::factory()->create([
            'published' => true, 
            'published_at' => now(),
            'title' => 'Post 2',
            'description' => 'Description 2',
            'content' => 'Content 2',
        ]);

        $post3 = Post::factory()->create([
            'published' => false,
            'title' => 'Post 3',
            'description' => 'Description 3',
            'content' => 'Content 3',
        ]);

        // Create slugs for posts
        $post1->slugs()->create(['slug' => 'post-1', 'locale' => 'en', 'active' => true]);
        $post2->slugs()->create(['slug' => 'post-2', 'locale' => 'en', 'active' => true]);
        $post3->slugs()->create(['slug' => 'post-3', 'locale' => 'en', 'active' => true]);

        // Attach posts to categories
        $tech->posts()->attach([$post1->id, $post2->id, $post3->id]);
        $design->posts()->attach([$post1->id]);
    }

    /** @test */
    public function it_returns_all_published_categories()
    {
        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'color',
                    'position',
                    'published',
                    'created_at',
                    'updated_at',
                    'meta' => [
                        'title',
                        'description',
                        'canonical_url',
                    ],
                    'posts_count',
                ],
            ]);

        $data = $response->json();
        $this->assertCount(2, $data); // Only published categories

        // Should be ordered by position
        $this->assertEquals('Technology', $data[0]['name']);
        $this->assertEquals('Design', $data[1]['name']);
    }

    /** @test */
    public function it_includes_posts_count()
    {
        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200);

        $data = $response->json();
        $techCategory = collect($data)->firstWhere('slug', 'technology');
        $designCategory = collect($data)->firstWhere('slug', 'design');

        $this->assertEquals(2, $techCategory['posts_count']); // 2 published posts
        $this->assertEquals(1, $designCategory['posts_count']); // 1 published post
    }

    /** @test */
    public function it_returns_individual_category_by_slug()
    {
        $response = $this->getJson('/api/v1/categories/technology');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'slug',
                'description',
                'color',
                'position',
                'published',
                'created_at',
                'updated_at',
                'meta',
                'posts_count',
                'posts' => [
                    '*' => [
                        'id',
                        'title',
                        'slug',
                        'description',
                        'meta',
                        'categories',
                    ],
                ],
            ]);

        $this->assertEquals('Technology', $response->json('name'));
        $this->assertEquals('technology', $response->json('slug'));
        $this->assertCount(2, $response->json('posts')); // Only published posts
    }

    /** @test */
    public function it_returns_404_for_nonexistent_category()
    {
        $response = $this->getJson('/api/v1/categories/nonexistent');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_404_for_unpublished_category()
    {
        $response = $this->getJson('/api/v1/categories/unpublished');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_popular_categories()
    {
        $response = $this->getJson('/api/v1/categories/popular');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'posts_count',
                ],
            ]);

        $data = $response->json();
        $this->assertCount(2, $data);

        // Should be ordered by posts count (descending)
        $this->assertEquals('Technology', $data[0]['name']);
        $this->assertEquals(2, $data[0]['posts_count']);
    }

    /** @test */
    public function it_respects_limit_for_popular_categories()
    {
        $response = $this->getJson('/api/v1/categories/popular?limit=1');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertCount(1, $data);
    }

    /** @test */
    public function it_enforces_maximum_limit()
    {
        $response = $this->getJson('/api/v1/categories/popular?limit=100');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertLessThanOrEqual(20, count($data)); // Max 20
    }

    /** @test */
    public function it_caches_responses()
    {
        Cache::flush();

        // First request
        $response1 = $this->getJson('/api/v1/categories');
        $response1->assertStatus(200);

        // Second request should use cache
        $response2 = $this->getJson('/api/v1/categories');
        $response2->assertStatus(200);

        // Responses should be identical
        $this->assertEquals($response1->json(), $response2->json());
    }

    /** @test */
    public function it_includes_proper_cache_headers()
    {
        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200)
            ->assertHeader('Cache-Control', 'public, max-age=1800'); // 30 minutes
    }

    /** @test */
    public function it_includes_translations_when_requested()
    {
        $response = $this->getJson('/api/v1/categories/technology?include_translations=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'translations',
            ]);
    }

    /** @test */
    public function category_posts_are_paginated()
    {
        // Create many posts for technology category
        $tech = Category::where('slug', 'technology')->first();
        for ($i = 0; $i < 15; $i++) {
            $post = Post::factory()->create(['published' => true]);
            $post->slugs()->create([
                'slug' => "test-post-{$i}",
                'locale' => 'en',
                'active' => true,
            ]);
            $tech->posts()->attach($post->id);
        }

        $response = $this->getJson('/api/v1/categories/technology?per_page=10');

        $response->assertStatus(200);

        $posts = $response->json('posts');
        $this->assertCount(10, $posts); // Should be limited to 10 per page
    }
}
