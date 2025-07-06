<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class BlogControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithoutMiddleware;

    protected function setUp(): void
    {
        parent::setUp();
        \Cache::flush();
    }

    /** @test */
    public function it_returns_posts_from_api()
    {
        $post = Post::factory()->create([
            'title' => 'API Test Post',
            'description' => 'API test description',
            'published' => true,
        ]);

        $response = $this->getJson('/api/v1/posts');
        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'API Test Post']);
    }

    /** @test */
    public function it_returns_categories_from_api()
    {
        $category = Category::factory()->create([
            'published' => true,
            'title' => 'API Test Category',
            'slug' => 'api-test-category',
            'description' => 'API test category description',
        ]);

        $response = $this->getJson('/api/v1/categories');
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'API Test Category']);
    }

    /** @test */
    public function it_returns_individual_category_by_slug()
    {
        $category = Category::factory()->create([
            'published' => true,
            'title' => 'API Slug Category',
            'slug' => 'api-slug-category',
        ]);

        $response = $this->getJson('/api/v1/categories/api-slug-category');
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'API Slug Category']);
    }
}
