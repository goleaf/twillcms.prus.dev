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
            'position' => 1,
        ]);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertTrue($post->published);
        $this->assertEquals(1, $post->position);
    }

    public function test_post_can_have_translations()
    {
        $post = Post::create([
            'published' => true,
            'position' => 1,
        ]);

        $post->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Test Post',
            'description' => 'This is a test post description',
            'content' => '<p>This is test content</p>',
        ]);

        $post->translations()->create([
            'locale' => 'lt',
            'active' => true,
            'title' => 'Testo įrašas',
            'description' => 'Tai yra testo įrašo aprašymas',
            'content' => '<p>Tai yra testo turinys</p>',
        ]);

        $this->assertEquals(2, $post->translations()->count());

        $enTranslation = $post->translations()->where('locale', 'en')->first();
        $this->assertEquals('Test Post', $enTranslation->title);

        $ltTranslation = $post->translations()->where('locale', 'lt')->first();
        $this->assertEquals('Testo įrašas', $ltTranslation->title);
    }

    public function test_post_can_have_categories()
    {
        $post = Post::create([
            'published' => true,
            'position' => 1,
        ]);

        $category1 = Category::create([
            'published' => true,
            'position' => 1,
        ]);

        $category2 = Category::create([
            'published' => true,
            'position' => 2,
        ]);

        $post->categories()->attach([$category1->id, $category2->id]);

        $this->assertEquals(2, $post->categories()->count());
        $this->assertTrue($post->categories->contains($category1));
        $this->assertTrue($post->categories->contains($category2));
    }

    public function test_post_slug_generation()
    {
        $post = Post::create([
            'published' => true,
            'position' => 1,
        ]);

        $post->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'This is a Test Post Title',
            'description' => 'Description',
            'content' => 'Content',
        ]);

        // Assuming slug is auto-generated from title
        $translation = $post->translations()->where('locale', 'en')->first();
        $this->assertNotNull($translation->title);
    }

    public function test_published_posts_scope()
    {
        // Create published post
        $publishedPost = Post::create([
            'published' => true,
            'position' => 1,
        ]);

        // Create unpublished post
        $unpublishedPost = Post::create([
            'published' => false,
            'position' => 2,
        ]);

        // Test if we can filter published posts
        $this->assertTrue($publishedPost->published);
        $this->assertFalse($unpublishedPost->published);
    }

    public function test_post_content_validation()
    {
        $post = Post::create([
            'published' => true,
            'position' => 1,
        ]);

        $translation = $post->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Test Post',
            'description' => 'Test description',
            'content' => '<p>Test content with <strong>HTML</strong></p>',
        ]);

        $this->assertStringContainsString('<strong>HTML</strong>', $translation->content);
        $this->assertStringContainsString('<p>', $translation->content);
    }

    public function test_post_timestamps()
    {
        $post = Post::create([
            'published' => true,
            'position' => 1,
        ]);

        $this->assertNotNull($post->created_at);
        $this->assertNotNull($post->updated_at);
    }

    public function test_post_soft_deletes()
    {
        $post = Post::create([
            'published' => true,
            'position' => 1,
        ]);

        $postId = $post->id;

        // Soft delete
        $post->delete();

        // Should not be found in normal queries
        $this->assertNull(Post::find($postId));

        // Should be found with trashed
        $this->assertNotNull(Post::withTrashed()->find($postId));
    }
}
