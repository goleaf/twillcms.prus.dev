<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_displays_articles(): void
    {
        // Create test data
        $articles = Article::factory(5)
            ->create(['is_published' => true]);
        $articles->each(function ($article) {
            $tag = Tag::factory()->create();
            $article->tags()->attach($tag);
            $tag->usage_count = $tag->articles()->count();
            $tag->save();
        });

        // Visit homepage
        $response = $this->get('/');

        // Assert response
        $response->assertStatus(200);
        $response->assertViewIs('articles.index');
        $response->assertViewHas('articles');

        // Assert articles are displayed
        foreach ($articles as $article) {
            $response->assertSee($article->title);
            $response->assertSee($article->excerpt);
        }
    }

    public function test_article_detail_page_displays_article(): void
    {
        // Create test data
        $article = Article::factory()
            ->create(['is_published' => true])
            ->each(function ($article) {
                $article->tags()->attach(Tag::factory()->create());
            })
            ->first();

        // Visit article page
        $response = $this->get(route('articles.show', $article));

        // Assert response
        $response->assertStatus(200);
        $response->assertViewIs('articles.show');
        $response->assertViewHas('article');

        // Assert article content is displayed
        $response->assertSee($article->title);
        $response->assertSee($article->content);
        foreach ($article->tags as $tag) {
            $response->assertSee($tag->name);
        }
    }

    public function test_tag_page_displays_tagged_articles(): void
    {
        // Create test data
        $tag = Tag::factory()->create();
        $articles = Article::factory(3)
            ->create(['is_published' => true])
            ->each(function ($article) use ($tag) {
                $article->tags()->attach($tag);
            });

        // Visit tag page
        $response = $this->get(route('articles.tag', $tag));

        // Assert response
        $response->assertStatus(200);
        $response->assertViewIs('articles.tag');
        $response->assertViewHas('tag');
        $response->assertViewHas('articles');

        // Assert tagged articles are displayed
        foreach ($articles as $article) {
            $response->assertSee($article->title);
            $response->assertSee($article->excerpt);
        }
    }

    public function test_search_returns_matching_articles(): void
    {
        // Create test data
        $searchTerm = 'unique search term';
        $matchingArticle = Article::factory()->create([
            'title' => "Article with {$searchTerm}",
            'is_published' => true,
        ]);
        $nonMatchingArticle = Article::factory()->create([
            'title' => 'Unrelated article',
            'is_published' => true,
        ]);

        // Perform search
        $response = $this->get(route('articles.search', ['q' => $searchTerm]));

        // Assert response
        $response->assertStatus(200);
        $response->assertViewIs('articles.search');
        $response->assertViewHas('articles');
        $response->assertViewHas('searchTerm');

        // Assert search results
        $response->assertSee($matchingArticle->title);
        $response->assertDontSee($nonMatchingArticle->title);
    }

    public function test_unpublished_articles_are_not_displayed(): void
    {
        // Create test data
        $publishedArticle = Article::factory()->create(['is_published' => true]);
        $unpublishedArticle = Article::factory()->create(['is_published' => false]);

        // Visit homepage
        $response = $this->get('/');

        // Assert response
        $response->assertStatus(200);
        $response->assertSee($publishedArticle->title);
        $response->assertDontSee($unpublishedArticle->title);
    }

    public function test_future_articles_are_not_displayed(): void
    {
        // Create test data
        $currentArticle = Article::factory()->create([
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);
        $futureArticle = Article::factory()->create([
            'is_published' => true,
            'published_at' => now()->addDay(),
        ]);

        // Visit homepage
        $response = $this->get('/');

        // Assert response
        $response->assertStatus(200);
        $response->assertSee($currentArticle->title);
        $response->assertDontSee($futureArticle->title);
    }

    public function test_featured_articles_are_displayed_on_homepage(): void
    {
        // Create test data
        $featuredArticle = Article::factory()->create([
            'is_published' => true,
            'is_featured' => true,
        ]);
        $regularArticle = Article::factory()->create([
            'is_published' => true,
            'is_featured' => false,
        ]);

        // Visit homepage
        $response = $this->get('/');

        // Assert response
        $response->assertStatus(200);
        $response->assertViewHas('featuredArticles');
        $response->assertSee($featuredArticle->title);
    }

    public function test_related_articles_are_displayed(): void
    {
        // Create test data
        $tag = Tag::factory()->create();
        $article = Article::factory()->create(['is_published' => true]);
        $article->tags()->attach($tag);

        $relatedArticle = Article::factory()->create(['is_published' => true]);
        $relatedArticle->tags()->attach($tag);

        $unrelatedArticle = Article::factory()->create(['is_published' => true]);

        // Visit article page
        $response = $this->get(route('articles.show', $article));

        // Assert response
        $response->assertStatus(200);
        $response->assertViewHas('relatedArticles');
        $response->assertSee($relatedArticle->title);
        $response->assertDontSee($unrelatedArticle->title);
    }

    public function test_article_reading_time_is_calculated(): void
    {
        // Create test data
        $article = Article::factory()->create([
            'is_published' => true,
            'content' => str_repeat('word ', 1000), // 1000 words
        ]);

        // Visit article page
        $response = $this->get(route('articles.show', $article));

        // Assert response
        $response->assertStatus(200);
        $response->assertSee('5 min read'); // 1000 words / 200 words per minute = 5 minutes
    }

    public function test_article_pagination_works(): void
    {
        // Create test data
        Article::factory(15)->create(['is_published' => true]);

        // Visit homepage
        $response = $this->get('/');

        // Assert response
        $response->assertStatus(200);
        $response->assertViewHas('articles');
        $response->assertSee('Next');
    }
} 