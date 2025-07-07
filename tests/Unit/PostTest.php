<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_can_be_created()
    {
        $post = Post::factory()->create([
            'title' => 'Test Post',
            'status' => 'published',
        ]);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals('published', $post->status);
        $this->assertEquals('Test Post', $post->title);
    }

    public function test_post_can_have_categories()
    {
        $post = Post::factory()->create();
        $category = Category::factory()->create([
            'title' => 'Technology',
        ]);

        $post->categories()->attach($category->id);

        $this->assertEquals(1, $post->categories()->count());
        $this->assertTrue($post->categories->contains($category));
    }

    public function test_published_posts_scope()
    {
        $publishedPost = Post::factory()->published()->create();
        $draftPost = Post::factory()->draft()->create();

        $this->assertEquals('published', $publishedPost->status);
        $this->assertEquals('draft', $draftPost->status);
    }

    public function test_post_timestamps()
    {
        $post = Post::factory()->create();

        $this->assertNotNull($post->created_at);
        $this->assertNotNull($post->updated_at);
    }
}
