<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminRoutesTest extends TestCase
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
    public function admin_dashboard_loads_successfully()
    {
        $response = $this->get('/admin/dashboard');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertSee('Admin Dashboard');
        $response->assertSee('Statistics');
    }

    /** @test */
    public function admin_dashboard_shows_statistics()
    {
        $response = $this->get('/admin/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('Total Articles');
        $response->assertSee('Total Tags');
        $response->assertSee('Published Articles');
        $response->assertSee('Draft Articles');
    }

    /** @test */
    public function admin_dashboard_shows_recent_articles()
    {
        $recentArticle = Article::factory()->create(['created_at' => now()]);
        
        $response = $this->get('/admin/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('Recent Articles');
        $response->assertSee($recentArticle->title);
    }

    /** @test */
    public function admin_articles_index_loads_successfully()
    {
        $response = $this->get('/admin/articles');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.articles.index');
        $response->assertSee('Manage Articles');
    }

    /** @test */
    public function admin_articles_index_shows_all_articles()
    {
        $article = Article::factory()->create();
        
        $response = $this->get('/admin/articles');
        
        $response->assertStatus(200);
        $response->assertSee($article->title);
    }

    /** @test */
    public function admin_articles_index_has_pagination()
    {
        Article::factory()->count(25)->create();
        
        $response = $this->get('/admin/articles');
        
        $response->assertStatus(200);
        // Check for pagination elements
        $response->assertSeeText('articles');
    }

    /** @test */
    public function admin_articles_create_page_loads_successfully()
    {
        $response = $this->get('/admin/articles/create');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.articles.create');
        $response->assertSee('Create New Article');
    }

    /** @test */
    public function admin_articles_create_form_has_required_fields()
    {
        $response = $this->get('/admin/articles/create');
        
        $response->assertStatus(200);
        $response->assertSee('name="title"', false);
        $response->assertSee('name="content"', false);
        $response->assertSee('name="excerpt"', false);
        $response->assertSee('name="image"', false);
        $response->assertSee('name="tags[]"', false);
    }

    /** @test */
    public function admin_can_create_article()
    {
        Storage::fake('public');
        $image = UploadedFile::fake()->image('test.jpg');
        $tags = Tag::factory()->count(2)->create();
        
        $articleData = [
            'title' => 'Test Article',
            'content' => 'This is test content for the article.',
            'excerpt' => 'Test excerpt',
            'image' => $image,
            'tags' => $tags->pluck('id')->toArray(),
            'is_featured' => true,
            'status' => 'published'
        ];
        
        $response = $this->post('/admin/articles', $articleData);
        
        $response->assertRedirect('/admin/articles');
        $this->assertDatabaseHas('articles', [
            'title' => 'Test Article',
            'content' => 'This is test content for the article.',
            'excerpt' => 'Test excerpt',
            'is_featured' => true,
            'status' => 'published'
        ]);
    }

    /** @test */
    public function admin_articles_create_validation_works()
    {
        $response = $this->post('/admin/articles', []);
        
        $response->assertSessionHasErrors(['title', 'content']);
    }

    /** @test */
    public function admin_articles_show_page_loads_successfully()
    {
        $article = Article::factory()->create();
        
        $response = $this->get("/admin/articles/{$article->id}");
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.articles.show');
        $response->assertSee($article->title);
    }

    /** @test */
    public function admin_articles_edit_page_loads_successfully()
    {
        $article = Article::factory()->create();
        
        $response = $this->get("/admin/articles/{$article->id}/edit");
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.articles.edit');
        $response->assertSee('Edit Article');
        $response->assertSee($article->title);
    }

    /** @test */
    public function admin_can_update_article()
    {
        $article = Article::factory()->create();
        $tags = Tag::factory()->count(2)->create();
        
        $updateData = [
            'title' => 'Updated Article Title',
            'content' => 'Updated content',
            'excerpt' => 'Updated excerpt',
            'tags' => $tags->pluck('id')->toArray(),
            'is_featured' => false,
            'status' => 'draft'
        ];
        
        $response = $this->put("/admin/articles/{$article->id}", $updateData);
        
        $response->assertRedirect('/admin/articles');
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'Updated Article Title',
            'content' => 'Updated content',
            'excerpt' => 'Updated excerpt',
            'is_featured' => false,
            'status' => 'draft'
        ]);
    }

    /** @test */
    public function admin_can_delete_article()
    {
        $article = Article::factory()->create();
        
        $response = $this->delete("/admin/articles/{$article->id}");
        
        $response->assertRedirect('/admin/articles');
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    /** @test */
    public function admin_tags_index_loads_successfully()
    {
        $response = $this->get('/admin/tags');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.tags.index');
        $response->assertSee('Manage Tags');
    }

    /** @test */
    public function admin_tags_index_shows_all_tags()
    {
        $tag = Tag::factory()->create();
        
        $response = $this->get('/admin/tags');
        
        $response->assertStatus(200);
        $response->assertSee($tag->name);
    }

    /** @test */
    public function admin_tags_create_page_loads_successfully()
    {
        $response = $this->get('/admin/tags/create');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.tags.create');
        $response->assertSee('Create New Tag');
    }

    /** @test */
    public function admin_tags_create_form_has_required_fields()
    {
        $response = $this->get('/admin/tags/create');
        
        $response->assertStatus(200);
        $response->assertSee('name="name"', false);
        $response->assertSee('name="description"', false);
        $response->assertSee('name="color"', false);
        $response->assertSee('name="is_featured"', false);
    }

    /** @test */
    public function admin_can_create_tag()
    {
        $tagData = [
            'name' => 'Test Tag',
            'description' => 'Test description',
            'color' => '#FF5733',
            'is_featured' => true
        ];
        
        $response = $this->post('/admin/tags', $tagData);
        
        $response->assertRedirect('/admin/tags');
        $this->assertDatabaseHas('tags', [
            'name' => 'Test Tag',
            'description' => 'Test description',
            'color' => '#FF5733',
            'is_featured' => true
        ]);
    }

    /** @test */
    public function admin_tags_create_validation_works()
    {
        $response = $this->post('/admin/tags', []);
        
        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function admin_tags_show_page_loads_successfully()
    {
        $tag = Tag::factory()->create();
        
        $response = $this->get("/admin/tags/{$tag->id}");
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.tags.show');
        $response->assertSee($tag->name);
    }

    /** @test */
    public function admin_tags_edit_page_loads_successfully()
    {
        $tag = Tag::factory()->create();
        
        $response = $this->get("/admin/tags/{$tag->id}/edit");
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.tags.edit');
        $response->assertSee('Edit Tag');
        $response->assertSee($tag->name);
    }

    /** @test */
    public function admin_can_update_tag()
    {
        $tag = Tag::factory()->create();
        
        $updateData = [
            'name' => 'Updated Tag Name',
            'description' => 'Updated description',
            'color' => '#33FF57',
            'is_featured' => false
        ];
        
        $response = $this->put("/admin/tags/{$tag->id}", $updateData);
        
        $response->assertRedirect('/admin/tags');
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => 'Updated Tag Name',
            'description' => 'Updated description',
            'color' => '#33FF57',
            'is_featured' => false
        ]);
    }

    /** @test */
    public function admin_can_delete_tag()
    {
        $tag = Tag::factory()->create();
        
        $response = $this->delete("/admin/tags/{$tag->id}");
        
        $response->assertRedirect('/admin/tags');
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    /** @test */
    public function admin_statistics_page_loads_successfully()
    {
        $response = $this->get('/admin/statistics');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.statistics');
        $response->assertSee('Portal Statistics');
    }

    /** @test */
    public function admin_statistics_shows_correct_data()
    {
        $response = $this->get('/admin/statistics');
        
        $response->assertStatus(200);
        $response->assertSee('Articles by Status');
        $response->assertSee('Popular Tags');
        $response->assertSee('Recent Activity');
    }

    /** @test */
    public function admin_analytics_page_loads_successfully()
    {
        $response = $this->get('/admin/analytics');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.analytics');
        $response->assertSee('Analytics Dashboard');
    }

    /** @test */
    public function admin_analytics_shows_metrics()
    {
        $response = $this->get('/admin/analytics');
        
        $response->assertStatus(200);
        $response->assertSee('Views Analytics');
        $response->assertSee('Content Performance');
        $response->assertSee('Popular Articles');
    }

    /** @test */
    public function admin_articles_search_functionality_works()
    {
        $searchableArticle = Article::factory()->create(['title' => 'Laravel Testing Guide']);
        $otherArticle = Article::factory()->create(['title' => 'Vue.js Components']);
        
        $response = $this->get('/admin/articles?search=Laravel');
        
        $response->assertStatus(200);
        $response->assertSee($searchableArticle->title);
        $response->assertDontSee($otherArticle->title);
    }

    /** @test */
    public function admin_articles_filter_by_status_works()
    {
        $publishedArticle = Article::factory()->create(['status' => 'published']);
        $draftArticle = Article::factory()->create(['status' => 'draft']);
        
        $response = $this->get('/admin/articles?status=published');
        
        $response->assertStatus(200);
        $response->assertSee($publishedArticle->title);
        $response->assertDontSee($draftArticle->title);
    }

    /** @test */
    public function admin_articles_filter_by_featured_works()
    {
        $featuredArticle = Article::factory()->featured()->create();
        $regularArticle = Article::factory()->create(['is_featured' => false]);
        
        $response = $this->get('/admin/articles?featured=1');
        
        $response->assertStatus(200);
        $response->assertSee($featuredArticle->title);
        $response->assertDontSee($regularArticle->title);
    }

    /** @test */
    public function admin_tags_search_functionality_works()
    {
        $searchableTag = Tag::factory()->create(['name' => 'Technology']);
        $otherTag = Tag::factory()->create(['name' => 'Sports']);
        
        $response = $this->get('/admin/tags?search=Tech');
        
        $response->assertStatus(200);
        $response->assertSee($searchableTag->name);
        $response->assertDontSee($otherTag->name);
    }

    /** @test */
    public function admin_tags_filter_by_featured_works()
    {
        $featuredTag = Tag::factory()->featured()->create();
        $regularTag = Tag::factory()->create(['is_featured' => false]);
        
        $response = $this->get('/admin/tags?featured=1');
        
        $response->assertStatus(200);
        $response->assertSee($featuredTag->name);
        $response->assertDontSee($regularTag->name);
    }

    /** @test */
    public function admin_bulk_actions_work_for_articles()
    {
        $articles = Article::factory()->count(3)->create();
        
        $response = $this->post('/admin/articles/bulk-action', [
            'action' => 'publish',
            'articles' => $articles->pluck('id')->toArray()
        ]);
        
        $response->assertRedirect('/admin/articles');
        
        foreach ($articles as $article) {
            $this->assertDatabaseHas('articles', [
                'id' => $article->id,
                'status' => 'published'
            ]);
        }
    }

    /** @test */
    public function admin_bulk_actions_work_for_tags()
    {
        $tags = Tag::factory()->count(3)->create();
        
        $response = $this->post('/admin/tags/bulk-action', [
            'action' => 'feature',
            'tags' => $tags->pluck('id')->toArray()
        ]);
        
        $response->assertRedirect('/admin/tags');
        
        foreach ($tags as $tag) {
            $this->assertDatabaseHas('tags', [
                'id' => $tag->id,
                'is_featured' => true
            ]);
        }
    }

    /** @test */
    public function admin_navigation_links_work()
    {
        $response = $this->get('/admin/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('href="/admin/articles"', false);
        $response->assertSee('href="/admin/tags"', false);
        $response->assertSee('href="/admin/statistics"', false);
        $response->assertSee('href="/admin/analytics"', false);
    }

    /** @test */
    public function admin_breadcrumbs_work()
    {
        $response = $this->get('/admin/articles');
        
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('Articles');
    }

    /** @test */
    public function admin_quick_actions_work()
    {
        $response = $this->get('/admin/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('Create Article');
        $response->assertSee('Create Tag');
        $response->assertSee('View Statistics');
    }

    /** @test */
    public function admin_responsive_design_classes_exist()
    {
        $response = $this->get('/admin/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('sm:');
        $response->assertSee('md:');
        $response->assertSee('lg:');
    }

    /** @test */
    public function admin_dark_mode_classes_exist()
    {
        $response = $this->get('/admin/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('dark:bg-gray-900');
        $response->assertSee('dark:text-white');
    }

    /** @test */
    public function admin_no_external_cdn_links_used()
    {
        $response = $this->get('/admin/dashboard');
        
        $response->assertStatus(200);
        $response->assertDontSee('cdn.jsdelivr.net');
        $response->assertDontSee('unpkg.com');
        $response->assertDontSee('cdnjs.cloudflare.com');
    }
} 