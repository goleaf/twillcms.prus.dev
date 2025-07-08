<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    public function test_tag_list_is_displayed_on_homepage(): void
    {
        // Create test data
        $tags = Tag::factory(5)->create();
        $article = Article::factory()->create(['is_published' => true]);
        $article->tags()->attach($tags->first());
        $tags->first()->usage_count = $tags->first()->articles()->count();
        $tags->first()->save();
        // Visit homepage
        $response = $this->get('/');
        // Assert response
        $response->assertStatus(200);
        $response->assertViewHas('tags');
        $response->assertSee($tags->first()->name);
    }

    public function test_tag_page_shows_correct_tag_info(): void
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
        $response->assertSee($tag->name);
        $response->assertSee($tag->description);
    }

    public function test_tag_page_shows_only_published_articles(): void
    {
        // Create test data
        $tag = Tag::factory()->create();
        $publishedArticle = Article::factory()->create(['is_published' => true]);
        $unpublishedArticle = Article::factory()->create(['is_published' => false]);
        $publishedArticle->tags()->attach($tag);
        $unpublishedArticle->tags()->attach($tag);

        // Visit tag page
        $response = $this->get(route('articles.tag', $tag));

        // Assert response
        $response->assertStatus(200);
        $response->assertSee($publishedArticle->title);
        $response->assertDontSee($unpublishedArticle->title);
    }

    public function test_tag_page_shows_articles_in_correct_order(): void
    {
        // Create test data
        $tag = Tag::factory()->create();
        $olderArticle = Article::factory()->create([
            'is_published' => true,
            'published_at' => now()->subDays(2),
        ]);
        $newerArticle = Article::factory()->create([
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);
        $olderArticle->tags()->attach($tag);
        $newerArticle->tags()->attach($tag);

        // Visit tag page
        $response = $this->get(route('articles.tag', $tag));

        // Assert response
        $response->assertStatus(200);
        $response->assertSeeInOrder([
            $newerArticle->title,
            $olderArticle->title,
        ]);
    }

    public function test_tag_page_pagination_works(): void
    {
        // Create test data
        $tag = Tag::factory()->create();
        Article::factory(15)
            ->create(['is_published' => true])
            ->each(function ($article) use ($tag) {
                $article->tags()->attach($tag);
            });

        // Visit tag page
        $response = $this->get(route('articles.tag', $tag));

        // Assert response
        $response->assertStatus(200);
        $response->assertViewHas('articles');
        $response->assertSee('Next');
    }

    public function test_popular_tags_are_displayed(): void
    {
        // Create test data
        $popularTag = Tag::factory()->create();
        $unpopularTag = Tag::factory()->create();

        // Create 5 articles for popular tag
        Article::factory(5)
            ->create(['is_published' => true])
            ->each(function ($article) use ($popularTag) {
                $article->tags()->attach($popularTag);
            });

        // Create 1 article for unpopular tag
        Article::factory()
            ->create(['is_published' => true])
            ->tags()
            ->attach($unpopularTag);

        // Visit homepage
        $response = $this->get('/');

        // Assert response
        $response->assertStatus(200);
        $response->assertViewHas('popularTags');
        $response->assertSee($popularTag->name);
    }

    public function test_tag_article_count_is_correct(): void
    {
        // Create test data
        $tag = Tag::factory()->create();
        Article::factory(3)
            ->create(['is_published' => true])
            ->each(function ($article) use ($tag) {
                $article->tags()->attach($tag);
            });

        // Visit tag page
        $response = $this->get(route('articles.tag', $tag));

        // Assert response
        $response->assertStatus(200);
        $response->assertSee('3 articles');
    }
} 