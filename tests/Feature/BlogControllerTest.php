<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BlogControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Set default locale
        app()->setLocale('en');
    }

    public function test_blog_index_page_loads_successfully()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('blog.index');
        $response->assertViewHas('posts');
        $response->assertViewHas('categories');
    }

    public function test_blog_index_displays_published_posts_only()
    {
        // Create published post
        $publishedPost = Post::create([
            'published' => true,
            'published_at' => now()->subDays(1),
            'position' => 1,
        ]);

        $publishedPost->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Published Post',
            'description' => 'This post is published',
            'content' => '<p>Published content</p>',
        ]);
        
        $publishedPost->slugs()->create([
            'locale' => 'en',
            'slug' => 'published-post',
            'active' => true,
        ]);

        // Create unpublished post
        $unpublishedPost = Post::create([
            'published' => false,
            'position' => 2,
        ]);

        $unpublishedPost->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Unpublished Post',
            'description' => 'This post is not published',
            'content' => '<p>Unpublished content</p>',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Published Post');
        $response->assertDontSee('Unpublished Post');
    }

    public function test_blog_post_detail_page_loads_successfully()
    {
        $post = Post::create([
            'published' => true,
            'published_at' => now()->subDay(),
            'position' => 1,
        ]);

        $translation = $post->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Test Post Title',
            'description' => 'Test post description',
            'content' => '<p>Test post content</p>',
        ]);

        // Create slug for the post
        $post->slugs()->create([
            'locale' => 'en',
            'slug' => 'test-post-title',
            'active' => true,
        ]);

        $response = $this->get('/blog/test-post-title');

        $response->assertStatus(200);
        $response->assertViewIs('blog.show');
        $response->assertViewHas('post');
        $response->assertSee('Test Post Title');
        $response->assertSee('Test post content');
    }

    public function test_blog_post_detail_404_for_unpublished_post()
    {
        $post = Post::create([
            'published' => false,
            'position' => 1,
        ]);

        $post->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Unpublished Post',
            'description' => 'This should not be visible',
            'content' => '<p>Hidden content</p>',
        ]);

        // Create slug for the post
        $post->slugs()->create([
            'locale' => 'en',
            'slug' => 'unpublished-post',
            'active' => true,
        ]);

        $response = $this->get('/blog/unpublished-post');

        $response->assertStatus(404);
    }

    public function test_blog_category_page_loads_successfully()
    {
        $category = Category::create([
            'published' => true,
            'position' => 1,
        ]);

        $category->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Technology',
            'description' => 'Tech category',
        ]);

        // Create slug for the category
        $category->slugs()->create([
            'locale' => 'en',
            'slug' => 'technology',
            'active' => true,
        ]);

        // Create posts in this category
        $post = Post::create([
            'published' => true,
            'published_at' => now()->subDay(),
            'position' => 1,
        ]);

        $post->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Tech Post',
            'description' => 'A technology post',
            'content' => '<p>Tech content</p>',
        ]);

        $post->categories()->attach($category->id);

        $response = $this->get('/blog/category/technology');

        $response->assertStatus(200);
        $response->assertViewIs('blog.category');
        $response->assertViewHas('posts');
        $response->assertViewHas('category');
        $response->assertSee('Technology');
        $response->assertSee('Tech Post');
    }

    public function test_blog_search_functionality()
    {
        $post = Post::create([
            'published' => true,
            'published_at' => now()->subDay(),
            'position' => 1,
        ]);

        $post->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Laravel Framework Tutorial',
            'description' => 'Learn Laravel basics',
            'content' => '<p>Laravel is a PHP framework</p>',
        ]);

        $response = $this->get('/?search=Laravel');

        $response->assertStatus(200);
        $response->assertSee('Laravel Framework Tutorial');
    }

    public function test_blog_search_no_results()
    {
        $response = $this->get('/?search=NonexistentTerm');

        $response->assertStatus(200);
        $response->assertSee(__('blog.no_posts_found'));
    }

    public function test_language_switching_works()
    {
        $post = Post::create([
            'published' => true,
            'published_at' => now()->subDay(),
            'position' => 1,
        ]);

        // English translation
        $post->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'English Title',
            'description' => 'English description',
            'content' => '<p>English content</p>',
        ]);

        // Lithuanian translation
        $post->translations()->create([
            'locale' => 'lt',
            'active' => true,
            'title' => 'Lietuviškas pavadinimas',
            'description' => 'Lietuviškas aprašymas',
            'content' => '<p>Lietuviškas turinys</p>',
        ]);

        // Test English version
        $response = $this->withSession(['locale' => 'en'])->get('/');
        $response->assertStatus(200);
        $response->assertSee('English Title');

        // Test Lithuanian version
        $response = $this->withSession(['locale' => 'lt'])->get('/');
        $response->assertStatus(200);
        $response->assertSee('Lietuviškas pavadinimas');
    }

    public function test_pagination_works()
    {
        // Create 25 posts to test pagination
        for ($i = 1; $i <= 25; $i++) {
            $post = Post::create([
                'published' => true,
                'published_at' => now()->subDays($i),
                'position' => $i,
            ]);

            $post->translations()->create([
                'locale' => 'en',
                'active' => true,
                'title' => "Post Title {$i}",
                'description' => "Description {$i}",
                'content' => "<p>Content {$i}</p>",
            ]);
        }

        $response = $this->get('/');
        $response->assertStatus(200);

        // Should see pagination links
        $response->assertSee('Next');

        // Test second page
        $response = $this->get('/?page=2');
        $response->assertStatus(200);
    }

    public function test_category_filter_works()
    {
        $techCategory = Category::create([
            'published' => true,
            'position' => 1,
        ]);

        $techCategory->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Technology',
            'description' => 'Tech posts',
        ]);

        // Create slug for tech category
        $techCategory->slugs()->create([
            'locale' => 'en',
            'slug' => 'technology',
            'active' => true,
        ]);

        $designCategory = Category::create([
            'published' => true,
            'position' => 2,
        ]);

        $designCategory->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Design',
            'description' => 'Design posts',
        ]);

        // Create slug for design category
        $designCategory->slugs()->create([
            'locale' => 'en',
            'slug' => 'design',
            'active' => true,
        ]);

        // Create tech post
        $techPost = Post::create([
            'published' => true,
            'position' => 1,
        ]);

        $techPost->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Tech Post',
            'description' => 'About technology',
            'content' => '<p>Tech content</p>',
        ]);

        $techPost->categories()->attach($techCategory->id);

        // Create design post
        $designPost = Post::create([
            'published' => true,
            'position' => 2,
        ]);

        $designPost->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Design Post',
            'description' => 'About design',
            'content' => '<p>Design content</p>',
        ]);

        $designPost->categories()->attach($designCategory->id);

        // Test technology category filter
        $response = $this->get('/blog/category/technology');
        $response->assertStatus(200);
        $response->assertSee('Tech Post');
        $response->assertDontSee('Design Post');
    }

    public function test_error_handling_for_database_issues()
    {
        // This test would require mocking database failures
        // For now, we'll test that the controller handles empty results gracefully

        $response = $this->get('/');
        $response->assertStatus(200);

        // Should not crash even with no posts
        $response->assertViewHas('posts');
        $response->assertViewHas('categories');
    }

    public function test_seo_meta_tags_present()
    {
        $post = Post::create([
            'published' => true,
            'position' => 1,
        ]);

        $post->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'SEO Test Post',
            'description' => 'This is for SEO testing',
            'content' => '<p>SEO content</p>',
        ]);

        // Create slug for the post
        $post->slugs()->create([
            'locale' => 'en',
            'slug' => 'seo-test-post',
            'active' => true,
        ]);

        $response = $this->get('/blog/seo-test-post');

        $response->assertStatus(200);
        $response->assertSee('<meta name="description"', false);
        $response->assertSee('SEO Test Post');
    }

    public function test_responsive_design_classes_present()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('responsive');
        $response->assertSee('mobile');
    }
}
