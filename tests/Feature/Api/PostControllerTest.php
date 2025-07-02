<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
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
        // Create categories using proper Twill structure
        $tech = Category::factory()->create([
            'published' => true,
        ]);
        
        // Create translations for Technology category
        $tech->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Technology',
            'description' => 'Tech articles',
        ]);
        
        // Create slug for Technology category
        $tech->slugs()->create([
            'locale' => 'en',
            'slug' => 'technology',
            'active' => true,
        ]);

        $design = Category::factory()->create([
            'published' => true,
        ]);
        
        // Create translations for Design category
        $design->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Design',
            'description' => 'Design articles',
        ]);
        
        // Create slug for Design category
        $design->slugs()->create([
            'locale' => 'en',
            'slug' => 'design',
            'active' => true,
        ]);

        // Create posts using proper structure
        $post1 = Post::factory()->create([
            'published' => true,
            'published_at' => now(),
        ]);

        // Create translations for post1
        $post1->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Test Post 1',
            'description' => 'Test description 1',
            'content' => 'Test content for post 1',
        ]);

        $post2 = Post::factory()->create([
            'published' => true,
            'published_at' => now(),
        ]);

        // Create translations for post2
        $post2->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Test Post 2',
            'description' => 'Test description 2',
            'content' => 'Test content for post 2',
        ]);

        $post3 = Post::factory()->create([
            'published' => false,
        ]);

        // Create translations for post3
        $post3->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Unpublished Post',
            'description' => 'This should not appear',
            'content' => 'Unpublished content',
        ]);

        // Create slugs
        $post1->slugs()->create([
            'slug' => 'test-post-1',
            'locale' => 'en',
            'active' => true,
        ]);

        $post2->slugs()->create([
            'slug' => 'test-post-2',
            'locale' => 'en',
            'active' => true,
        ]);

        $post3->slugs()->create([
            'slug' => 'unpublished-post',
            'locale' => 'en',
            'active' => true,
        ]);

        // Attach categories
        $post1->categories()->attach([$tech->id, $design->id]);
        $post2->categories()->attach([$tech->id]);
    }

    /** @test */
    public function it_returns_paginated_posts()
    {
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

        // Should only return published posts
        $data = $response->json('data');
        $this->assertCount(2, $data);
        $this->assertEquals('Test Post 1', $data[0]['title']);
        $this->assertEquals('Test Post 2', $data[1]['title']);
    }

    /** @test */
    public function it_returns_individual_post_by_slug()
    {
        $response = $this->getJson('/api/v1/posts/test-post-1');

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

        $this->assertEquals('Test Post 1', $response->json('title'));
        $this->assertEquals('test-post-1', $response->json('slug'));
    }

    /** @test */
    public function it_returns_404_for_nonexistent_post()
    {
        $response = $this->getJson('/api/v1/posts/nonexistent-slug');

        $response->assertStatus(404);
    }

    /** @test */
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

    /** @test */
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
