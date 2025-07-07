<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ComprehensiveRoutesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $post;
    protected $category;
    protected $tag;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestData();
    }

    private function createTestData()
    {
        // Create a user
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create categories
        $this->category = Category::create([
            'title' => 'Technology',
            'slug' => 'technology',
            'description' => 'Technology news and updates',
            'is_active' => true,
            'position' => 1,
        ]);

        Category::create([
            'title' => 'Sports',
            'slug' => 'sports',
            'description' => 'Sports news and updates',
            'is_active' => true,
            'position' => 2,
        ]);

        // Create tags
        $this->tag = Tag::create([
            'name' => 'Laravel',
            'slug' => 'laravel',
        ]);

        Tag::create([
            'name' => 'PHP',
            'slug' => 'php',
        ]);

        Tag::create([
            'name' => 'JavaScript',
            'slug' => 'javascript',
        ]);

        // Create posts
        $this->post = Post::create([
            'title' => 'Laravel News Update',
            'slug' => 'laravel-news-update',
            'content' => 'This is a comprehensive article about Laravel updates and new features.',
            'excerpt' => 'Laravel framework receives major updates',
            'status' => 'published',
            'user_id' => $this->user->id,
            'is_featured' => true,
            'view_count' => 0,
        ]);

        // Attach relationships
        $this->post->categories()->attach($this->category->id);
        $this->post->tags()->attach($this->tag->id);

        // Create additional posts
        Post::create([
            'title' => 'PHP 8.3 Features',
            'slug' => 'php-8-3-features',
            'content' => 'Exploring the new features in PHP 8.3 release.',
            'excerpt' => 'PHP 8.3 brings exciting new features',
            'status' => 'published',
            'user_id' => $this->user->id,
            'is_featured' => false,
        ]);

        Post::create([
            'title' => 'JavaScript Trends 2024',
            'slug' => 'javascript-trends-2024',
            'content' => 'The latest trends in JavaScript development for 2024.',
            'excerpt' => 'JavaScript trends and developments',
            'status' => 'published',
            'user_id' => $this->user->id,
            'is_featured' => true,
        ]);
    }

    // ====================
    // FRONTEND PUBLIC ROUTES
    // ====================

    #[Test]
    public function homepage_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    #[Test]
    public function news_index_loads_successfully(): void
    {
        $response = $this->get('/news');
        $response->assertStatus(200);
    }

    #[Test]
    public function individual_news_article_loads(): void
    {
        $response = $this->get('/news/test-article');
        $response->assertStatus(200);
    }

    #[Test]
    public function tags_index_loads_successfully(): void
    {
        $response = $this->get('/tags');
        $response->assertStatus(200);
    }

    #[Test]
    public function individual_tag_page_loads(): void
    {
        $response = $this->get('/tags/test-tag');
        $response->assertStatus(200);
    }

    #[Test]
    public function search_functionality_works(): void
    {
        $response = $this->get('/search?q=test');
        $response->assertStatus(200);
    }

    #[Test]
    public function search_with_empty_query_redirects(): void
    {
        $response = $this->get('/search');
        $response->assertRedirect('/');
    }

    /** @test */
    public function categories_index_loads_successfully()
    {
        $response = $this->get('/categories');

        $response->assertStatus(200);
        $response->assertSee('Technology');
        $response->assertViewIs('news.categories.index');
    }

    /** @test */
    public function individual_category_page_loads()
    {
        $response = $this->get('/categories/technology');

        $response->assertStatus(200);
        $response->assertSee('Technology');
        $response->assertSee('Laravel News Update');
        $response->assertViewIs('news.categories.show');
    }

    #[Test]
    public function returns_404_for_nonexistent_news_article(): void
    {
        $response = $this->get('/news/nonexistent-article');
        $response->assertStatus(404);
    }

    #[Test]
    public function returns_404_for_nonexistent_tag(): void
    {
        $response = $this->get('/tags/nonexistent-tag');
        $response->assertStatus(404);
    }

    #[Test]
    public function returns_404_for_nonexistent_category()
    {
        $response = $this->get('/categories/nonexistent-category');

        $response->assertStatus(404);
    }

    // ====================
    // ADMIN ROUTES
    // ====================

    /** @test */
    public function admin_dashboard_loads()
    {
        $response = $this->get('/admin');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }

    /** @test */
    public function admin_posts_index_loads()
    {
        $response = $this->get('/admin/posts');

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_posts_create_loads()
    {
        $response = $this->get('/admin/posts/create');

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_posts_show_loads()
    {
        $response = $this->get("/admin/posts/{$this->post->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_posts_edit_loads()
    {
        $response = $this->get("/admin/posts/{$this->post->id}/edit");

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_categories_index_loads()
    {
        $response = $this->get('/admin/categories');

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_categories_create_loads()
    {
        $response = $this->get('/admin/categories/create');

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_categories_show_loads()
    {
        $response = $this->get("/admin/categories/{$this->category->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_categories_edit_loads()
    {
        $response = $this->get("/admin/categories/{$this->category->id}/edit");

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_tags_index_loads()
    {
        $response = $this->get('/admin/tags');

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_tags_create_loads()
    {
        $response = $this->get('/admin/tags/create');

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_tags_show_loads()
    {
        $response = $this->get("/admin/tags/{$this->tag->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_tags_edit_loads()
    {
        $response = $this->get("/admin/tags/{$this->tag->id}/edit");

        $response->assertStatus(200);
    }

    // ====================
    // FUNCTIONALITY TESTS
    // ====================

    #[Test]
    public function tag_filtering_works_correctly(): void
    {
        $response = $this->get('/tags/test-tag');
        $response->assertStatus(200);
        $response->assertViewHas('articles');
    }

    #[Test]
    public function search_finds_relevant_articles(): void
    {
        $response = $this->get('/search?q=test');
        $response->assertStatus(200);
        $response->assertViewHas('articles');
    }

    #[Test]
    public function pagination_works_on_news_index(): void
    {
        $response = $this->get('/news?page=1');
        $response->assertStatus(200);
        $response->assertViewHas('articles');
    }

    #[Test]
    public function featured_articles_are_prioritized(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewHas('featuredArticles');
    }

    #[Test]
    public function mobile_responsive_elements_present(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('mobile-menu', false);
    }

    #[Test]
    public function meta_tags_are_present(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('<meta name="description"', false);
    }
} 