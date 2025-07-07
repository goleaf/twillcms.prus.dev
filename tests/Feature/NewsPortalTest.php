<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Article;
use PHPUnit\Framework\Attributes\Test;

class NewsPortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->createTestData();
    }

    private function createTestData()
    {
        // Create categories
        $category = Category::create([
            'title' => 'Technology',
            'slug' => 'technology',
            'description' => 'Latest tech news',
            'is_active' => true,
        ]);

        // Create tags
        $tag1 = Tag::create(['name' => 'Laravel', 'slug' => 'laravel']);
        $tag2 = Tag::create(['name' => 'PHP', 'slug' => 'php']);
        $tag3 = Tag::create(['name' => 'JavaScript', 'slug' => 'javascript']);

        // Create posts
        $post1 = Post::create([
            'title' => 'Laravel News Update',
            'slug' => 'laravel-news-update',
            'content' => 'This is a test article about Laravel framework updates.',
            'excerpt' => 'Latest Laravel framework updates and features.',
            'status' => 'published',
            'is_featured' => true,
            'user_id' => 1,
        ]);

        $post2 = Post::create([
            'title' => 'PHP 8.3 Features',
            'slug' => 'php-83-features',
            'content' => 'New features in PHP 8.3 release.',
            'excerpt' => 'Overview of PHP 8.3 new features.',
            'status' => 'published',
            'is_featured' => false,
            'user_id' => 1,
        ]);

        $post3 = Post::create([
            'title' => 'JavaScript Trends 2024',
            'slug' => 'javascript-trends-2024',
            'content' => 'Latest JavaScript trends for 2024.',
            'excerpt' => 'JavaScript development trends.',
            'status' => 'published',
            'is_featured' => true,
            'user_id' => 1,
        ]);

        // Attach relationships
        $post1->categories()->attach($category->id);
        $post1->tags()->attach([$tag1->id, $tag2->id]);

        $post2->categories()->attach($category->id);
        $post2->tags()->attach([$tag2->id]);

        $post3->categories()->attach($category->id);
        $post3->tags()->attach([$tag3->id]);
    }

    /** @test */
    public function homepage_displays_correctly()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('News Portal');
        $response->assertSee('Featured News');
        $response->assertSee('Latest News');
        $response->assertSee('Laravel News Update');
        $response->assertSee('JavaScript Trends 2024');
    }

    /** @test */
    public function news_index_page_works()
    {
        $response = $this->get('/news');

        $response->assertStatus(200);
        $response->assertSee('Featured News');
        $response->assertSee('Latest News');
    }

    /** @test */
    public function individual_news_article_displays()
    {
        $response = $this->get('/news/laravel-news-update');

        $response->assertStatus(200);
        $response->assertSee('Laravel News Update');
        $response->assertSee('This is a test article about Laravel framework updates.');
        $response->assertSee('Related Topics');
        $response->assertSee('Laravel');
        $response->assertSee('PHP');
    }

    /** @test */
    public function tags_index_page_works()
    {
        $response = $this->get('/tags');

        $response->assertStatus(200);
        $response->assertSee('All Topics');
        $response->assertSee('Topic Cloud');
        $response->assertSee('Laravel');
        $response->assertSee('PHP');
        $response->assertSee('JavaScript');
    }

    /** @test */
    public function individual_tag_page_displays_articles()
    {
        $response = $this->get('/tags/laravel');

        $response->assertStatus(200);
        $response->assertSee('#Laravel');
        $response->assertSee('Laravel News Update');
        $response->assertSee('Related Topics');
    }

    /** @test */
    public function categories_index_page_works()
    {
        $response = $this->get('/categories');

        $response->assertStatus(200);
        $response->assertSee('Technology');
    }

    /** @test */
    public function individual_category_page_displays_articles()
    {
        $response = $this->get('/categories/technology');

        $response->assertStatus(200);
        $response->assertSee('Technology');
        $response->assertSee('Laravel News Update');
        $response->assertSee('PHP 8.3 Features');
        $response->assertSee('JavaScript Trends 2024');
    }

    /** @test */
    public function search_functionality_works()
    {
        $response = $this->get('/search?q=Laravel');

        $response->assertStatus(200);
        $response->assertSee('Search Results');
        $response->assertSee('Laravel News Update');
        $response->assertSee('Found 1 result');
    }

    /** @test */
    public function search_with_no_results()
    {
        $response = $this->get('/search?q=nonexistent');

        $response->assertStatus(200);
        $response->assertSee('No articles found');
        $response->assertSee('Search Suggestions');
    }

    /** @test */
    public function empty_search_redirects_to_home()
    {
        $response = $this->get('/search?q=');

        $response->assertRedirect('/');
    }

    /** @test */
    public function featured_articles_are_displayed()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Laravel News Update');
        $response->assertSee('JavaScript Trends 2024');
    }

    /** @test */
    public function article_view_count_increments()
    {
        $post = Post::where('slug', 'laravel-news-update')->first();
        $initialViews = $post->view_count;

        $this->get('/news/laravel-news-update');

        $post->refresh();
        $this->assertEquals($initialViews + 1, $post->view_count);
    }

    /** @test */
    public function related_articles_are_shown()
    {
        $response = $this->get('/news/laravel-news-update');

        $response->assertStatus(200);
        $response->assertSee('Related Articles');
        // Should show PHP article since it shares the PHP tag
        $response->assertSee('PHP 8.3 Features');
    }

    /** @test */
    public function navigation_links_work()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Home');
        $response->assertSee('Latest News');
        $response->assertSee('Topics');
        $response->assertSee('Sections');
    }

    /** @test */
    public function pagination_works_on_tag_pages()
    {
        // Create more posts for pagination
        for ($i = 1; $i <= 15; $i++) {
            $post = Post::create([
                'title' => "Test Article {$i}",
                'slug' => "test-article-{$i}",
                'content' => "Content for test article {$i}",
                'excerpt' => "Excerpt for test article {$i}",
                'status' => 'published',
                'user_id' => 1,
            ]);

            $tag = Tag::where('slug', 'laravel')->first();
            $post->tags()->attach($tag->id);
        }

        $response = $this->get('/tags/laravel');

        $response->assertStatus(200);
        // Should show pagination if more than 12 posts
        $response->assertSee('Test Article');
    }

    /** @test */
    public function social_sharing_meta_tags_present()
    {
        $response = $this->get('/news/laravel-news-update');

        $response->assertStatus(200);
        $response->assertSee('og:title', false);
        $response->assertSee('og:description', false);
        $response->assertSee('twitter:card', false);
    }

    /** @test */
    public function breadcrumbs_are_displayed()
    {
        $response = $this->get('/tags/laravel');

        $response->assertStatus(200);
        $response->assertSee('Home');
        $response->assertSee('Topics');
        $response->assertSee('Laravel');
    }

    /** @test */
    public function tag_search_functionality_works()
    {
        $response = $this->get('/tags');

        $response->assertStatus(200);
        $response->assertSee('Search topics...');
        $response->assertSee('tag-search');
    }

    /** @test */
    public function popular_tags_are_displayed()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Popular Topics');
        $response->assertSee('Laravel');
        $response->assertSee('PHP');
        $response->assertSee('JavaScript');
    }

    /** @test */
    public function mobile_responsive_navigation()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('mobile-menu-button');
        $response->assertSee('mobile-menu');
    }

    /** @test */
    public function returns_404_for_nonexistent_article()
    {
        $response = $this->get('/news/nonexistent-article');

        $response->assertStatus(404);
    }

    /** @test */
    public function returns_404_for_nonexistent_tag()
    {
        $response = $this->get('/tags/nonexistent-tag');

        $response->assertStatus(404);
    }

    /** @test */
    public function returns_404_for_nonexistent_category()
    {
        $response = $this->get('/categories/nonexistent-category');

        $response->assertStatus(404);
    }

    #[Test]
    public function test_home_page_displays_correctly()
    {
        $tag = Tag::factory()->create();
        $articles = Article::factory()
            ->count(3)
            ->create()
            ->each(function ($article) use ($tag) {
                $article->tags()->attach($tag);
            });

        $response = $this->get(route('home'));

        $response->assertStatus(200)
                ->assertViewIs('news.index')
                ->assertViewHas('articles')
                ->assertViewHas('featuredArticles')
                ->assertViewHas('tags')
                ->assertSee($articles->first()->title)
                ->assertSee($tag->name);
    }

    #[Test]
    public function test_article_detail_page_displays_correctly()
    {
        $article = Article::factory()->create(['is_published' => true]);
        $tags = Tag::factory()->count(2)->create();
        $article->tags()->attach($tags);

        $response = $this->get(route('article.show', $article));

        $response->assertStatus(200)
                ->assertViewIs('news.show')
                ->assertViewHas('article')
                ->assertViewHas('relatedArticles')
                ->assertSee($article->title)
                ->assertSee($tags->first()->name);
    }

    #[Test]
    public function test_unpublished_article_returns_404()
    {
        $article = Article::factory()->create(['is_published' => false]);

        $response = $this->get(route('article.show', $article));

        $response->assertStatus(404);
    }

    #[Test]
    public function test_tag_page_displays_correctly()
    {
        $tag = Tag::factory()->create();
        $articles = Article::factory()
            ->count(3)
            ->create(['is_published' => true])
            ->each(function ($article) use ($tag) {
                $article->tags()->attach($tag);
            });

        $response = $this->get(route('tag.show', $tag));

        $response->assertStatus(200)
                ->assertViewIs('news.tag')
                ->assertViewHas('tag')
                ->assertViewHas('articles')
                ->assertSee($tag->name)
                ->assertSee($articles->first()->title);
    }

    #[Test]
    public function test_search_functionality_works()
    {
        $searchTerm = 'unique search term';
        $matchingArticle = Article::factory()->create([
            'title' => "Article with {$searchTerm}",
            'is_published' => true
        ]);
        $nonMatchingArticle = Article::factory()->create([
            'title' => 'Different article',
            'is_published' => true
        ]);

        $response = $this->get(route('home', ['search' => $searchTerm]));

        $response->assertStatus(200)
                ->assertViewIs('news.index')
                ->assertSee($matchingArticle->title)
                ->assertDontSee($nonMatchingArticle->title);
    }

    #[Test]
    public function test_tag_filtering_works()
    {
        $tag = Tag::factory()->create();
        $matchingArticle = Article::factory()->create(['is_published' => true]);
        $nonMatchingArticle = Article::factory()->create(['is_published' => true]);
        
        $matchingArticle->tags()->attach($tag);

        $response = $this->get(route('home', ['tag' => $tag->slug]));

        $response->assertStatus(200)
                ->assertViewIs('news.index')
                ->assertSee($matchingArticle->title)
                ->assertDontSee($nonMatchingArticle->title);
    }

    #[Test]
    public function test_pagination_works()
    {
        $articles = Article::factory()
            ->count(15) // More than our per-page limit
            ->create(['is_published' => true]);

        $response = $this->get(route('home'));

        $response->assertStatus(200)
                ->assertViewIs('news.index')
                ->assertSee('Next');
    }

    #[Test]
    public function test_view_count_increments()
    {
        $article = Article::factory()->create(['is_published' => true]);
        $initialViewCount = $article->view_count;

        $response = $this->get(route('article.show', $article));

        $this->assertEquals($initialViewCount + 1, $article->fresh()->view_count);
    }

    #[Test]
    public function test_related_articles_are_shown()
    {
        $tag = Tag::factory()->create();
        $article = Article::factory()->create(['is_published' => true]);
        $relatedArticle = Article::factory()->create(['is_published' => true]);
        $unrelatedArticle = Article::factory()->create(['is_published' => true]);

        $article->tags()->attach($tag);
        $relatedArticle->tags()->attach($tag);

        $response = $this->get(route('article.show', $article));

        $response->assertStatus(200)
                ->assertViewIs('news.show')
                ->assertSee($relatedArticle->title)
                ->assertDontSee($unrelatedArticle->title);
    }
} 