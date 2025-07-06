<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test as TestMethod;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->createTestPosts();
    }

    protected function createTestPosts(): void
    {
        // Create categories using single-language system
        $tech = Category::factory()->create([
            'published' => true,
            'title' => 'Technology',
            'description' => 'Tech articles',
            'slug' => 'technology',
        ]);

        $design = Category::factory()->create([
            'published' => true,
            'title' => 'Design',
            'description' => 'Design articles',
            'slug' => 'design',
        ]);

        // Create posts using single-language system (ordered by published_at)
        $post1 = Post::create([
            'published' => true,
            'published_at' => now()->subDays(2), // Earlier post, definitely in the past
            'title' => 'Test Post 1',
            'description' => 'Test description 1',
            'content' => 'Test content for post 1',
            'slug' => 'test-post-1',
        ]);

        $post2 = Post::create([
            'published' => true,
            'published_at' => now()->subDay(), // More recent post, still in the past
            'title' => 'Test Post 2',
            'description' => 'Test description 2',
            'content' => 'Test content for post 2',
            'slug' => 'test-post-2',
        ]);

        $post3 = Post::create([
            'published' => false,
            'title' => 'Unpublished Post',
            'description' => 'This should not appear',
            'content' => 'Unpublished content',
            'slug' => 'unpublished-post',
        ]);

        // Attach categories
        $post1->categories()->attach([$tech->id, $design->id]);
        $post2->categories()->attach([$tech->id]);
    }

    #[TestMethod]
    public function it_returns_paginated_posts()
    {
        // Debug: Check how many published posts are in the database
        $publishedCount = Post::published()->count();
        echo 'Published posts in database: '.$publishedCount."\n";

        $response = $this->getJson('/api/v1/posts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'slug',
                        'description',
                        'published_at',
                        'created_at',
                        'meta' => [
                            'url',
                            'api_url',
                            'reading_time',
                        ],
                        'categories',
                        'excerpt',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
            ]);

        // Should only return published posts (ordered by published_at desc)
        $data = $response->json('data');
        $this->assertCount(2, $data);
        $this->assertEquals('Test Post 2', $data[0]['title']); // Most recent first
        $this->assertEquals('Test Post 1', $data[1]['title']); // Older second
    }

    #[TestMethod]
    public function it_returns_individual_post_by_slug()
    {
        // Get an actual post from the database
        $post = Post::published()->first();
        $this->assertNotNull($post, 'Should have at least one published post');

        $response = $this->getJson("/api/v1/posts/{$post->slug}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'title',
                'slug',
                'description',
                'content',
                'published',
                'published_at',
                'created_at',
                'updated_at',
                'meta' => [
                    'title',
                    'description',
                    'canonical_url',
                    'api_url',
                ],
                'categories',
                'reading_time',
                'related_posts',
            ]);

        $this->assertEquals($post->title, $response->json('title'));
        $this->assertEquals($post->slug, $response->json('slug'));
    }

    #[TestMethod]
    public function it_returns_404_for_nonexistent_post()
    {
        $response = $this->getJson('/api/v1/posts/nonexistent-slug');

        $response->assertStatus(404);
    }

    #[TestMethod]
    public function it_searches_posts_by_query()
    {
        $response = $this->getJson('/api/v1/posts/search?q=Test');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => [
                    'query',
                    'current_page',
                    'total',
                ],
                'links',
            ]);

        $data = $response->json('data');
        $this->assertCount(2, $data);
        $this->assertEquals('Test', $response->json('meta.query'));
    }

    #[TestMethod]
    public function it_caches_responses()
    {
        Cache::flush();

        // First request
        $response1 = $this->getJson('/api/v1/posts');
        $response1->assertStatus(200);

        // Second request should use cache
        $response2 = $this->getJson('/api/v1/posts');
        $response2->assertStatus(200);

        // Responses should be identical
        $this->assertEquals($response1->json(), $response2->json());
    }
}
