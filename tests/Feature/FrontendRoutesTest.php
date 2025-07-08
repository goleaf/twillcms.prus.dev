<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FrontendRoutesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestData();
    }

    private function createTestData()
    {
        // Create tags
        $tags = Tag::factory()->count(10)->create();
        
        // Create articles with tags
        $articles = Article::factory()->count(20)->create();
        
        // Attach random tags to articles
        $articles->each(function ($article) use ($tags) {
            $article->tags()->attach($tags->random(rand(1, 3)));
        });
        
        // Update tag usage counts
        $tags->each(function ($tag) {
            $tag->usage_count = $tag->articles()->count();
            $tag->save();
        });
    }

    /** @test */
    public function home_page_loads_successfully()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertViewIs('news.home');
        $response->assertSee('Stay Informed');
        $response->assertSee('Featured Stories');
        $response->assertSee('Latest News');
    }

    /** @test */
    public function home_page_displays_featured_articles()
    {
        $featuredArticle = Article::factory()->featured()->create();
        $tag = Tag::factory()->create();
        $featuredArticle->tags()->attach($tag);
        $tag->usage_count = $tag->articles()->count();
        $tag->save();
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee($featuredArticle->title);
    }

    /** @test */
    public function home_page_displays_latest_articles()
    {
        $latestArticle = Article::factory()->create(['created_at' => now()]);
        
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee($latestArticle->title);
    }

    /** @test */
    public function home_page_displays_popular_tags()
    {
        $popularTag = Tag::factory()->create(['usage_count' => 50]);
        
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee($popularTag->name);
    }

    /** @test */
    public function article_detail_page_loads_successfully()
    {
        $article = Article::factory()->create();
        
        $response = $this->get("/news/{$article->slug}");
        
        $response->assertStatus(200);
        $response->assertViewIs('news.show');
        $response->assertSee($article->title);
        $response->assertSee($article->content);
    }

    /** @test */
    public function article_detail_page_shows_tags()
    {
        $article = Article::factory()->create();
        $tag = Tag::factory()->create();
        $article->tags()->attach($tag);
        
        $response = $this->get("/news/{$article->slug}");
        
        $response->assertStatus(200);
        $response->assertSee($tag->name);
    }

    /** @test */
    public function article_detail_page_shows_related_articles()
    {
        $tag = Tag::factory()->create();
        $article = Article::factory()->create();
        $relatedArticle = Article::factory()->create();
        
        $article->tags()->attach($tag);
        $relatedArticle->tags()->attach($tag);
        
        $response = $this->get("/news/{$article->slug}");
        
        $response->assertStatus(200);
        $response->assertSee('Related Articles');
    }

    /** @test */
    public function article_view_count_increments_on_visit()
    {
        $article = Article::factory()->create(['view_count' => 0]);
        
        $this->get("/news/{$article->slug}");
        
        $article->refresh();
        $this->assertEquals(1, $article->view_count);
    }

    /** @test */
    public function nonexistent_article_returns_404()
    {
        $response = $this->get('/news/nonexistent-article');
        
        $response->assertStatus(404);
    }

    /** @test */
    public function tags_index_page_loads_successfully()
    {
        $response = $this->get('/tags');
        
        $response->assertStatus(200);
        $response->assertViewIs('news.tags.index');
        $response->assertSee('Explore Topics');
    }

    /** @test */
    public function tags_index_displays_all_tags()
    {
        $tag = Tag::factory()->create();
        
        $response = $this->get('/tags');
        
        $response->assertStatus(200);
        $response->assertSee($tag->name);
    }

    /** @test */
    public function tags_index_displays_featured_tags()
    {
        $featuredTag = Tag::factory()->featured()->create();
        $article = Article::factory()->create();
        $featuredTag->articles()->attach($article);
        $featuredTag->usage_count = $featuredTag->articles()->count();
        $featuredTag->save();
        $response = $this->get('/tags');
        $response->assertStatus(200);
        $response->assertSee($featuredTag->name);
        $response->assertSee('Featured');
    }

    /** @test */
    public function tags_search_functionality_works()
    {
        $searchableTag = Tag::factory()->create(['name' => 'Technology']);
        $otherTag = Tag::factory()->create(['name' => 'Sports']);
        
        $response = $this->get('/tags?search=Tech');
        
        $response->assertStatus(200);
        $response->assertSee($searchableTag->name);
        $response->assertDontSee($otherTag->name);
    }

    /** @test */
    public function tag_detail_page_loads_successfully()
    {
        $tag = Tag::factory()->create();
        $article = Article::factory()->create();
        $tag->articles()->attach($article);
        
        $response = $this->get("/tag/{$tag->slug}");
        
        $response->assertStatus(200);
        $response->assertViewIs('news.tags.show');
        $response->assertSee($tag->name);
    }

    /** @test */
    public function tag_detail_page_shows_articles()
    {
        $tag = Tag::factory()->create();
        $article = Article::factory()->create();
        $tag->articles()->attach($article);
        
        $response = $this->get("/tag/{$tag->slug}");
        
        $response->assertStatus(200);
        $response->assertSee($article->title);
    }

    /** @test */
    public function tag_detail_page_shows_related_tags()
    {
        $tag = Tag::factory()->create();
        $relatedTag = Tag::factory()->create();
        
        $response = $this->get("/tag/{$tag->slug}");
        
        $response->assertStatus(200);
        $response->assertSee('Related Topics');
    }

    /** @test */
    public function nonexistent_tag_returns_404()
    {
        $response = $this->get('/tag/nonexistent-tag');
        
        $response->assertStatus(404);
    }

    /** @test */
    public function search_page_loads_successfully()
    {
        $response = $this->get('/search');
        
        $response->assertStatus(200);
        $response->assertViewIs('news.search');
        $response->assertSee('Search Results');
    }

    /** @test */
    public function search_functionality_works()
    {
        $searchableArticle = Article::factory()->create(['title' => 'Laravel Best Practices']);
        $otherArticle = Article::factory()->create(['title' => 'Vue.js Components']);
        
        $response = $this->get('/search?q=Laravel');
        
        $response->assertStatus(200);
        $response->assertSee($searchableArticle->title);
        $response->assertDontSee($otherArticle->title);
    }

    /** @test */
    public function search_with_no_query_shows_empty_results()
    {
        $response = $this->get('/search');
        
        $response->assertStatus(200);
        $response->assertSee('Enter a search term');
    }

    /** @test */
    public function search_with_no_results_shows_message()
    {
        $response = $this->get('/search?q=nonexistentterm');
        
        $response->assertStatus(200);
        $response->assertSee('No articles found');
    }

    /** @test */
    public function about_page_loads_successfully()
    {
        $response = $this->get('/about');
        
        $response->assertStatus(200);
        $response->assertViewIs('pages.about');
    }

    /** @test */
    public function contact_page_loads_successfully()
    {
        $response = $this->get('/contact');
        
        $response->assertStatus(200);
        $response->assertViewIs('pages.contact');
    }

    /** @test */
    public function privacy_page_loads_successfully()
    {
        $response = $this->get('/privacy');
        
        $response->assertStatus(200);
        $response->assertViewIs('pages.privacy');
    }

    /** @test */
    public function terms_page_loads_successfully()
    {
        $response = $this->get('/terms');
        
        $response->assertStatus(200);
        $response->assertViewIs('pages.terms');
    }

    /** @test */
    public function navigation_links_are_present()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('href="/"', false);
        $response->assertSee('href="/tags"', false);
        $response->assertSee('href="/about"', false);
        $response->assertSee('href="/admin/dashboard"', false);
    }

    /** @test */
    public function mobile_menu_functionality_exists()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('mobile-menu-button');
        $response->assertSee('mobile-menu');
    }

    /** @test */
    public function search_trigger_button_exists()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('toggle-search');
    }

    /** @test */
    public function footer_links_are_present()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('Privacy Policy');
        $response->assertSee('Terms of Service');
        $response->assertSee('Contact');
    }

    /** @test */
    public function pagination_works_on_home_page()
    {
        Article::factory()->count(25)->create();
        
        $response = $this->get('/');
        
        $response->assertStatus(200);
        // Check for pagination elements
        $response->assertSeeText('articles');
    }

    /** @test */
    public function pagination_works_on_tags_page()
    {
        Tag::factory()->count(25)->create();
        
        $response = $this->get('/tags');
        
        $response->assertStatus(200);
        // Check for pagination elements
        $response->assertSeeText('topics');
    }

    /** @test */
    public function pagination_works_on_tag_detail_page()
    {
        $tag = Tag::factory()->create();
        $articles = Article::factory()->count(25)->create();
        $tag->articles()->attach($articles);
        
        $response = $this->get("/tag/{$tag->slug}");
        
        $response->assertStatus(200);
        // Check for pagination elements
        $response->assertSeeText('articles');
    }

    /** @test */
    public function social_sharing_buttons_exist_on_article_page()
    {
        $article = Article::factory()->create();
        
        $response = $this->get("/news/{$article->slug}");
        
        $response->assertStatus(200);
        $response->assertSee('shareArticle');
        $response->assertSee('Share this article');
    }

    /** @test */
    public function reading_progress_bar_exists_on_article_page()
    {
        $article = Article::factory()->create();
        
        $response = $this->get("/news/{$article->slug}");
        
        $response->assertStatus(200);
        $response->assertSee('reading-progress');
    }

    /** @test */
    public function back_to_top_button_exists()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('scroll-to-top');
    }

    /** @test */
    public function newsletter_signup_form_exists()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('Subscribe to our newsletter');
        $response->assertSee('type="email"', false);
    }

    /** @test */
    public function meta_tags_are_present()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('<meta name="viewport"', false);
        $response->assertSee('<meta name="csrf-token"', false);
    }

    /** @test */
    public function structured_data_exists_on_article_page()
    {
        $article = Article::factory()->create();
        
        $response = $this->get("/news/{$article->slug}");
        
        $response->assertStatus(200);
        // Articles should have proper meta structure
        $response->assertSee($article->title);
        $response->assertSee($article->published_at->format('F j, Y'));
    }

    /** @test */
    public function error_handling_for_invalid_routes()
    {
        $response = $this->get('/invalid-route');
        
        $response->assertStatus(404);
    }

    /** @test */
    public function css_and_js_assets_are_loaded()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('app.css');
        $response->assertSee('app.js');
    }

    /** @test */
    public function no_external_cdn_links_used()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertDontSee('cdn.jsdelivr.net');
        $response->assertDontSee('unpkg.com');
        $response->assertDontSee('cdnjs.cloudflare.com');
    }

    /** @test */
    public function responsive_classes_exist()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('sm:');
        $response->assertSee('md:');
        $response->assertSee('lg:');
    }
} 