<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function article_can_be_created(): void
    {
        $article = Article::factory()->create();
        $this->assertModelExists($article);
    }

    #[Test]
    public function article_can_have_tags(): void
    {
        // Use fresh instances and clear any cache
        \Cache::flush();
        
        $article = Article::factory()->create();
        $tag = Tag::factory()->create();

        $article->tags()->attach($tag);

        $this->assertTrue($article->tags->contains($tag));
        $this->assertEquals(1, $article->tags->count());
    }

    #[Test]
    public function published_articles_scope(): void
    {
        Article::factory()->create(['is_published' => true]);
        Article::factory()->create(['is_published' => false]);

        $publishedCount = Article::published()->count();
        $this->assertEquals(1, $publishedCount);
    }

    #[Test]
    public function featured_articles_scope(): void
    {
        Article::factory()->create(['is_featured' => true]);
        Article::factory()->create(['is_featured' => false]);

        $featuredCount = Article::featured()->count();
        $this->assertEquals(1, $featuredCount);
    }

    #[Test]
    public function article_timestamps(): void
    {
        $article = Article::factory()->create();

        $this->assertNotNull($article->created_at);
        $this->assertNotNull($article->updated_at);
    }

    #[Test]
    public function article_basic_attributes(): void
    {
        $data = [
            'title' => 'Test Article',
            'slug' => 'test-article',
            'excerpt' => 'Test excerpt',
            'content' => 'Test content',
            'is_published' => true,
            'is_featured' => false,
            'reading_time' => 5,
        ];

        $article = Article::factory()->create($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $article->$key);
        }
    }

    #[Test]
    public function article_can_be_searched(): void
    {
        $article = Article::factory()->create([
            'title' => 'Test Article',
            'content' => 'Test content',
            'is_published' => true,
        ]);

        $searchResults = Article::search('Test')->get();

        // Debug output
        $this->assertTrue($searchResults->count() > 0, 'Search results should not be empty');
        $this->assertTrue($searchResults->contains($article), 'Search results should contain the article');
    }

    #[Test]
    public function article_can_be_filtered_by_tag(): void
    {
        $tag = Tag::factory()->create();
        $article = Article::factory()->create(['is_published' => true]);
        $article->tags()->attach($tag);

        $filteredArticles = Article::withAnyTags([$tag->id])->get();

        $this->assertTrue($filteredArticles->contains($article));
    }

    #[Test]
    public function article_can_get_related(): void
    {
        $tag = Tag::factory()->create();
        $article = Article::factory()->create(['is_published' => true]);
        
        // Create 6 related articles to match the test expectation
        $relatedArticles = Article::factory()->count(6)->create(['is_published' => true]);
        
        // Attach the tag to the main article
        $article->tags()->attach($tag);
        
        // Attach the tag to all related articles
        foreach ($relatedArticles as $relatedArticle) {
            $relatedArticle->tags()->attach($tag);
        }

        $related = $article->getRelated();
        $this->assertCount(6, $related);

        // Check that all related articles are in the results
        foreach ($relatedArticles as $relatedArticle) {
            $this->assertTrue($related->contains($relatedArticle));
        }
        
        // Check that the main article is not in the results
        $this->assertFalse($related->contains($article));
    }

    #[Test]
    public function article_reading_time_is_calculated(): void
    {
        // Create exactly 1000 words for the test
        $words = array_fill(0, 1000, 'word');
        $content = implode(' ', $words); // This creates exactly 1000 words
        
        $article = Article::factory()->create([
            'content' => $content,
        ]);

        $this->assertEquals(5, $article->reading_time); // 1000 words / 200 words per minute = 5 minutes
    }

    #[Test]
    public function article_excerpt_is_truncated(): void
    {
        $longText = str_repeat('Lorem ipsum dolor sit amet. ', 20);
        $article = Article::factory()->create([
            'excerpt' => $longText,
        ]);

        $this->assertTrue(strlen($article->excerpt) <= 500);
    }

    #[Test]
    public function article_can_be_published(): void
    {
        $article = Article::factory()->create(['is_published' => false]);
        $article->publish();

        $this->assertTrue($article->is_published);
        $this->assertNotNull($article->published_at);
    }

    #[Test]
    public function article_can_be_unpublished(): void
    {
        $article = Article::factory()->create(['is_published' => true]);
        $article->unpublish();

        $this->assertFalse($article->is_published);
        $this->assertNull($article->published_at);
    }

    #[Test]
    public function article_can_be_featured(): void
    {
        $article = Article::factory()->create(['is_featured' => false]);
        $article->feature();

        $this->assertTrue($article->is_featured);
    }

    #[Test]
    public function article_can_be_unfeatured(): void
    {
        $article = Article::factory()->create(['is_featured' => true]);
        $article->unfeature();

        $this->assertFalse($article->is_featured);
    }
} 