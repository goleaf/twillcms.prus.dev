<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test as TestMethod;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithoutMiddleware;

    protected function setUp(): void
    {
        parent::setUp();
        \Cache::flush();
        $this->createTestData();
    }

    protected function createTestData(): void
    {
        // Create categories with direct field assignment
        $tech = Category::factory()->create([
            'published' => true,
            'position' => 1,
            'title' => 'Technology',
            'slug' => 'technology',
            'description' => 'Tech articles',
        ]);

        $design = Category::factory()->create([
            'published' => true,
            'position' => 2,
            'title' => 'Design',
            'slug' => 'design',
            'description' => 'Design articles',
        ]);

        $unpublished = Category::factory()->create([
            'published' => false,
            'title' => 'Unpublished',
            'slug' => 'unpublished',
            'description' => 'Unpublished category',
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

    #[TestMethod]
    public function it_returns_all_published_categories()
    {
        $response = $this->getJson('/api/v1/categories');

        if ($response->getStatusCode() !== 200) {
            dump('Response status: '.$response->getStatusCode());
            dump('Response content: '.$response->getContent());
        }

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
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
                ],
            ]);

        $data = $response->json('data');
        $this->assertGreaterThan(0, count($data)); // Should have categories

        // Check that all returned categories are published
        foreach ($data as $category) {
            $this->assertTrue($category['published']);
        }
    }

    #[TestMethod]
    public function it_includes_posts_count()
    {
        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200);

        $data = $response->json('data');
        $techCategory = collect($data)->firstWhere('slug', 'technology');
        $designCategory = collect($data)->firstWhere('slug', 'design');

        $this->assertEquals('Technology', $techCategory['name']);
        $this->assertEquals('Design', $designCategory['name']);
        $this->assertEquals(2, $techCategory['posts_count']); // 2 published posts
        $this->assertEquals(1, $designCategory['posts_count']); // 1 published post
    }

    #[TestMethod]
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

    #[TestMethod]
    public function it_returns_404_for_nonexistent_category()
    {
        $response = $this->getJson('/api/v1/categories/nonexistent');

        $response->assertStatus(404);
    }

    #[TestMethod]
    public function it_returns_404_for_unpublished_category()
    {
        $response = $this->getJson('/api/v1/categories/unpublished');

        $response->assertStatus(404);
    }

    #[TestMethod]
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

    #[TestMethod]
    public function it_respects_limit_for_popular_categories()
    {
        $response = $this->getJson('/api/v1/categories/popular?limit=1');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertCount(1, $data);
    }

    #[TestMethod]
    public function it_enforces_maximum_limit()
    {
        $response = $this->getJson('/api/v1/categories/popular?limit=100');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertLessThanOrEqual(20, count($data)); // Max 20
    }

    #[TestMethod]
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

    #[TestMethod]
    public function it_includes_proper_cache_headers()
    {
        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200)
            ->assertHeader('Cache-Control', 'max-age=1800, public'); // 30 minutes
    }

    #[TestMethod]
    public function it_includes_translations_when_requested()
    {
        $response = $this->getJson('/api/v1/categories/technology?include_translations=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'translations',
            ]);
    }

    #[TestMethod]
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
