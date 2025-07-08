<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Tag;
use App\Repositories\ArticleRepository;
use App\Repositories\TagRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NewsPortalTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected ArticleRepository $articleRepository;
    protected TagRepository $tagRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = app(ArticleRepository::class);
        $this->tagRepository = app(TagRepository::class);
    }

    /** @test */
    public function home_page_displays_correctly()
    {
        // Create test data
        $featuredArticles = Article::factory()->featured()->count(3)->create();
        $latestArticles = Article::factory()->count(6)->create();
        $tags = Tag::factory()->count(5)->create();
        // Attach tags to articles and update usage_count
        $latestArticles->each(function ($article) use ($tags) {
            $article->tags()->attach($tags->random(2));
        });
        $tags->each(function ($tag) {
            $tag->usage_count = $tag->articles()->count();
            $tag->save();
        });
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('news.home');
        $response->assertViewHas(['featuredArticles', 'latestArticles', 'popularTags']);
    }

    /** @test */
    public function article_detail_page_displays_correctly()
    {
        $article = Article::factory()->create();
        $tags = Tag::factory()->count(3)->create();
        $article->tags()->attach($tags);

        $response = $this->get("/news/{$article->slug}");

        $response->assertStatus(200);
        $response->assertViewIs('news.show');
        $response->assertViewHas(['article', 'relatedArticles']);
        $response->assertSee($article->title);
    }

    /** @test */
    public function article_view_count_increments()
    {
        $article = Article::factory()->create(['view_count' => 0]);
        
        $this->get("/news/{$article->slug}");
        
        $article->refresh();
        $this->assertEquals(1, $article->view_count);
    }

    /** @test */
    public function tags_index_page_displays_correctly()
    {
        $tags = Tag::factory()->count(10)->create();
        
        $response = $this->get('/tags');
        
        $response->assertStatus(200);
        $response->assertViewIs('news.tags.index');
        $response->assertViewHas(['tags', 'popularTags', 'featuredTags']);
    }

    /** @test */
    public function tag_show_page_displays_articles()
    {
        $tag = Tag::factory()->create();
        $articles = Article::factory()->count(5)->create();
        $tag->articles()->attach($articles);
        $tag->usage_count = $tag->articles()->count();
        $tag->save();
        $response = $this->get("/tag/{$tag->slug}");

        $response->assertStatus(200);
        $response->assertViewIs('news.tags.show');
        $response->assertViewHas(['tag', 'articles', 'relatedTags', 'popularTags']);
        $response->assertSee($tag->name);
    }

    /** @test */
    public function search_functionality_works()
    {
        $article = Article::factory()->create(['title' => 'Laravel Testing Guide']);
        
        $response = $this->get('/search?q=Laravel');
        
        $response->assertStatus(200);
        $response->assertViewIs('news.search');
        $response->assertSee('Laravel Testing Guide');
    }

    /** @test */
    public function article_repository_get_all_paginated()
    {
        Article::factory()->count(15)->create();
        
        $articles = $this->articleRepository->getAllPaginated(10);
        
        $this->assertEquals(10, $articles->count());
        $this->assertEquals(15, $articles->total());
    }

    /** @test */
    public function article_repository_get_by_tag()
    {
        $tag = Tag::factory()->create();
        $articles = Article::factory()->count(5)->create();
        $tag->articles()->attach($articles);
        
        $result = $this->articleRepository->getByTag($tag, 10);
        
        $this->assertEquals(5, $result->count());
    }

    /** @test */
    public function article_repository_get_featured()
    {
        Article::factory()->featured()->count(3)->create();
        Article::factory()->count(5)->create();
        
        $featured = $this->articleRepository->getFeatured(10);
        
        $this->assertEquals(3, $featured->count());
    }

    /** @test */
    public function article_repository_search()
    {
        Article::factory()->create(['title' => 'Laravel Best Practices']);
        Article::factory()->create(['title' => 'Vue.js Components']);
        
        $results = $this->articleRepository->search('Laravel', 10);
        
        $this->assertEquals(1, $results->count());
        $this->assertEquals('Laravel Best Practices', $results->first()->title);
    }

    /** @test */
    public function tag_repository_get_popular()
    {
        $popularTag = Tag::factory()->create(['usage_count' => 50]);
        $regularTag = Tag::factory()->create(['usage_count' => 5]);
        // Attach articles to tags to ensure usage_count is correct
        $articles = Article::factory()->count(50)->create();
        $popularTag->articles()->attach($articles);
        $popularTag->usage_count = $popularTag->articles()->count();
        $popularTag->save();
        $articles2 = Article::factory()->count(5)->create();
        $regularTag->articles()->attach($articles2);
        $regularTag->usage_count = $regularTag->articles()->count();
        $regularTag->save();
        $popular = $this->tagRepository->getPopular(10);
        $this->assertEquals($popularTag->id, $popular->first()->id);
    }

    /** @test */
    public function tag_repository_search()
    {
        Tag::factory()->create(['name' => 'Technology']);
        Tag::factory()->create(['name' => 'Business']);
        
        $results = $this->tagRepository->search('Tech', 10);
        
        $this->assertEquals(1, $results->count());
        $this->assertEquals('Technology', $results->first()->name);
    }

    /** @test */
    public function admin_dashboard_displays_correctly()
    {
        Article::factory()->count(10)->create();
        Tag::factory()->count(5)->create();
        
        $response = $this->get('/admin/dashboard');
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }

    /** @test */
    public function admin_can_create_article()
    {
        Storage::fake('public');
        $tags = Tag::factory()->count(3)->create();
        
        $response = $this->post('/admin/articles', [
            'title' => 'Test Article',
            'slug' => 'test-article',
            'excerpt' => 'This is a test excerpt',
            'content' => 'This is the test content',
            'status' => 'published',
            'is_featured' => false,
            'tags' => $tags->pluck('id')->toArray(),
            'image' => UploadedFile::fake()->image('test.jpg')
        ]);
        
        $response->assertRedirect('/admin/articles');
        $this->assertDatabaseHas('articles', [
            'title' => 'Test Article',
            'slug' => 'test-article'
        ]);
    }

    /** @test */
    public function admin_can_update_article()
    {
        $article = Article::factory()->create();
        $tags = Tag::factory()->count(2)->create();
        
        $response = $this->put("/admin/articles/{$article->id}", [
            'title' => 'Updated Article',
            'slug' => 'updated-article',
            'excerpt' => 'Updated excerpt',
            'content' => 'Updated content',
            'status' => 'published',
            'is_featured' => true,
            'tags' => $tags->pluck('id')->toArray()
        ]);
        
        $response->assertRedirect('/admin/articles');
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'Updated Article'
        ]);
    }

    /** @test */
    public function admin_can_delete_article()
    {
        $article = Article::factory()->create();
        
        $response = $this->delete("/admin/articles/{$article->id}");
        
        $response->assertRedirect('/admin/articles');
        $this->assertSoftDeleted('articles', ['id' => $article->id]);
    }

    /** @test */
    public function admin_can_create_tag()
    {
        $response = $this->post('/admin/tags', [
            'name' => 'New Technology',
            'slug' => 'new-technology',
            'description' => 'Latest technology trends',
            'color' => '#3B82F6',
            'is_featured' => false
        ]);
        
        $response->assertRedirect('/admin/tags');
        $this->assertDatabaseHas('tags', [
            'name' => 'New Technology',
            'slug' => 'new-technology'
        ]);
    }

    /** @test */
    public function admin_can_update_tag()
    {
        $tag = Tag::factory()->create();
        
        $response = $this->put("/admin/tags/{$tag->id}", [
            'name' => 'Updated Tag',
            'slug' => 'updated-tag',
            'description' => 'Updated description',
            'color' => '#EF4444',
            'is_featured' => true
        ]);
        
        $response->assertRedirect('/admin/tags');
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => 'Updated Tag'
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
    public function article_request_validation_works()
    {
        $response = $this->post('/admin/articles', []);
        
        $response->assertSessionHasErrors(['title', 'content', 'status']);
    }

    /** @test */
    public function tag_request_validation_works()
    {
        $response = $this->post('/admin/tags', []);
        
        $response->assertSessionHasErrors(['name', 'color']);
    }

    /** @test */
    public function article_slug_is_unique()
    {
        Article::factory()->create(['slug' => 'test-article']);
        
        $response = $this->post('/admin/articles', [
            'title' => 'Test Article',
            'slug' => 'test-article',
            'excerpt' => 'Test excerpt',
            'content' => 'Test content',
            'status' => 'published'
        ]);
        
        $response->assertSessionHasErrors(['slug']);
    }

    /** @test */
    public function tag_usage_count_updates_correctly()
    {
        $tag = Tag::factory()->create(['usage_count' => 0]);
        $article = Article::factory()->create();
        
        $article->tags()->attach($tag);
        $tag->increment('usage_count');
        
        $tag->refresh();
        $this->assertEquals(1, $tag->usage_count);
    }

    /** @test */
    public function related_articles_are_fetched_correctly()
    {
        $tag = Tag::factory()->create();
        $article1 = Article::factory()->create();
        $article2 = Article::factory()->create();
        $article3 = Article::factory()->create();
        
        $article1->tags()->attach($tag);
        $article2->tags()->attach($tag);
        
        $related = $this->articleRepository->getRelated($article1, 6);
        
        $this->assertEquals(1, $related->count());
        $this->assertEquals($article2->id, $related->first()->id);
    }

    /** @test */
    public function statistics_are_calculated_correctly()
    {
        Article::factory()->published()->count(10)->create();
        Article::factory()->draft()->count(5)->create();
        Article::factory()->featured()->count(3)->create();
        
        $stats = $this->articleRepository->getStatistics();
        
        $this->assertEquals(15, $stats['total']);
        $this->assertEquals(10, $stats['published']);
        $this->assertEquals(5, $stats['draft']);
        $this->assertEquals(3, $stats['featured']);
    }

    /** @test */
    public function caching_works_for_repositories()
    {
        Article::factory()->count(5)->create();
        
        // First call should hit the database
        $articles1 = $this->articleRepository->getLatest(5);
        
        // Second call should hit the cache
        $articles2 = $this->articleRepository->getLatest(5);
        
        $this->assertEquals($articles1->count(), $articles2->count());
    }
} 