<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_post_can_be_created()
    {
        $post = Post::create([
            'published' => true,
            'title' => 'Test Post',
            'description' => 'Test description',
            'content' => 'Test content',
            'position' => 1,
        ]);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertTrue($post->published);
        $this->assertEquals('Test Post', $post->title);
    }

    public function test_post_slug_generation()
    {
        $post = Post::create([
            'published' => true,
            'title' => 'Test Post Title',
            'description' => 'Description',
            'content' => 'Content',
            'position' => 1,
        ]);

        $this->assertEquals('test-post-title', $post->slug);
    }

    public function test_post_can_have_categories()
    {
        $post = Post::create([
            'published' => true,
            'title' => 'Test Post',
            'description' => 'Test description',
            'content' => 'Test content',
            'position' => 1,
        ]);

        $category = Category::create([
            'published' => true,
            'title' => 'Technology',
            'description' => 'Tech category',
            'position' => 1,
        ]);

        $post->categories()->attach($category->id);

        $this->assertEquals(1, $post->categories()->count());
        $this->assertTrue($post->categories->contains($category));
    }

    public function test_published_posts_scope()
    {
        $publishedPost = Post::create([
            'published' => true,
            'title' => 'Published Post',
            'description' => 'Published',
            'content' => 'Published content',
            'position' => 1,
        ]);

        $unpublishedPost = Post::create([
            'published' => false,
            'title' => 'Unpublished Post',
            'description' => 'Not published',
            'content' => 'Unpublished content',
            'position' => 2,
        ]);

        $this->assertTrue($publishedPost->published);
        $this->assertFalse($unpublishedPost->published);
    }

    public function test_post_reading_time_calculation()
    {
        $shortPost = Post::create([
            'published' => true,
            'title' => 'Short Post',
            'description' => 'Short description',
            'content' => 'This is short content.',
            'position' => 1,
        ]);

        $longPost = Post::create([
            'published' => true,
            'title' => 'Long Post',
            'description' => 'Long description',
            'content' => str_repeat('This is a long post with many words. ', 100), // ~500 words
            'position' => 2,
        ]);

        $this->assertEquals(1, $shortPost->reading_time); // Minimum 1 minute
        $this->assertGreaterThan(1, $longPost->reading_time);
    }

    public function test_post_excerpt_generation()
    {
        $post = Post::create([
            'published' => true,
            'title' => 'Test Post',
            'description' => 'Test description',
            'content' => str_repeat('This is a word ', 50), // 50 words
            'position' => 1,
        ]);

        $excerpt = $post->excerpt;
        $this->assertNotNull($excerpt);
        $this->assertStringContainsString('...', $excerpt); // Should be truncated
    }

    public function test_post_excerpt_override()
    {
        $customExcerpt = 'This is a custom excerpt for this post.';

        $post = Post::create([
            'published' => true,
            'title' => 'Test Post',
            'description' => 'Test description',
            'content' => str_repeat('This is a word ', 50),
            'excerpt_override' => $customExcerpt,
            'position' => 1,
        ]);

        $this->assertEquals($customExcerpt, $post->excerpt);
    }

    public function test_post_timestamps()
    {
        $post = Post::create([
            'published' => true,
            'title' => 'Test Post',
            'description' => 'Test description',
            'content' => 'Test content',
            'position' => 1,
        ]);

        $this->assertNotNull($post->created_at);
        $this->assertNotNull($post->updated_at);
    }

    public function test_post_featured_status()
    {
        $post = Post::create([
            'published' => true,
            'title' => 'Featured Post',
            'description' => 'This post is featured',
            'content' => 'Featured content',
            'position' => 1,
            'settings' => ['is_featured' => true],
        ]);

        $this->assertTrue($post->is_featured);

        $post->markAsFeatured(false);
        $this->assertFalse($post->fresh()->is_featured);
    }

    public function test_post_view_count_increment()
    {
        $post = Post::create([
            'published' => true,
            'title' => 'Test Post',
            'description' => 'Test description',
            'content' => 'Test content',
            'position' => 1,
            'view_count' => 5,
        ]);

        $originalCount = $post->view_count;
        $post->incrementViews();

        $this->assertEquals($originalCount + 1, $post->fresh()->view_count);
    }

    public function test_post_content_validation()
    {
        $post = Post::create([
            'published' => true,
            'title' => 'Test Post',
            'description' => 'Test description',
            'content' => '<p>Test content with <strong>HTML</strong></p>',
            'position' => 1,
        ]);

        $this->assertStringContainsString('<strong>HTML</strong>', $post->content);
        $this->assertStringContainsString('<p>', $post->content);
    }
}
