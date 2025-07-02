<?php

namespace Tests\Unit\Resources;

use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class PostResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_transforms_post_correctly()
    {
        $category = Category::factory()->withTitle('Technology')->published()->create();

        $post = Post::factory()->create([
            'title' => 'Test Post',
            'slug' => 'test-post',
            'description' => 'Test description',
            'content' => 'Test content with multiple words for reading time calculation.',
            'published' => true,
        ]);

        $post->categories()->attach($category);

        $request = Request::create('/api/v1/posts/test-post');
        $resource = new PostResource($post);
        $data = $resource->toArray($request);

        $this->assertEquals($post->id, $data['id']);
        $this->assertEquals('Test Post', $data['title']);
        $this->assertEquals('test-post', $data['slug']);
        $this->assertEquals('Test description', $data['description']);
        $this->assertEquals(true, $data['published']);

        // Check meta data
        $this->assertArrayHasKey('meta', $data);
        $this->assertEquals('Test Post', $data['meta']['title']);
        $this->assertEquals('Test description', $data['meta']['description']);
        $this->assertStringContains('/blog/test-post', $data['meta']['canonical_url']);
        $this->assertStringContains('/api/v1/posts/test-post', $data['meta']['api_url']);

        // Check reading time
        $this->assertArrayHasKey('reading_time', $data);
        $this->assertIsInt($data['reading_time']);
        $this->assertGreaterThan(0, $data['reading_time']);
    }

    /** @test */
    public function it_includes_categories_when_loaded()
    {
        $category = Category::factory()->withTitle('Technology')->published()->create();

        $post = Post::factory()->create([
            'published' => true,
            'published_at' => now()->subDay(),
        ]);
        $post->categories()->attach($category);
        $post->load('categories');

        $request = Request::create('/api/v1/posts/test-post');
        $resource = new PostResource($post);
        $data = $resource->toArray($request);

        $this->assertArrayHasKey('categories', $data);
        $this->assertCount(1, $data['categories']);
        $this->assertEquals('Technology', $data['categories'][0]['name']);
    }

    /** @test */
    public function it_includes_translations_when_requested()
    {
        $post = Post::factory()->create();

        $request = Request::create('/api/v1/posts/test-post?include_translations=1');
        $resource = new PostResource($post);
        $data = $resource->toArray($request);

        $this->assertArrayHasKey('translations', $data);
    }

    /** @test */
    public function it_handles_null_content_for_reading_time()
    {
        $post = Post::factory()->create(['content' => null]);

        $request = Request::create('/api/v1/posts/test-post');
        $resource = new PostResource($post);
        $data = $resource->toArray($request);

        $this->assertEquals(1, $data['reading_time']); // Minimum 1 minute
    }

    /** @test */
    public function it_includes_related_posts_when_loaded()
    {
        $post = Post::factory()->create();
        $relatedPost = Post::factory()->create();

        $post->setRelation('relatedPosts', collect([$relatedPost]));

        $request = Request::create('/api/v1/posts/test-post');
        $resource = new PostResource($post);
        $data = $resource->toArray($request);

        $this->assertArrayHasKey('related_posts', $data);
        $this->assertCount(1, $data['related_posts']);
    }
}
