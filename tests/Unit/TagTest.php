<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TagTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function tag_can_be_created(): void
    {
        $tag = Tag::factory()->create();
        $this->assertModelExists($tag);
    }

    #[Test]
    public function tag_can_have_articles(): void
    {
        $tag = Tag::factory()->create();
        $article = Article::factory()->create();

        $tag->articles()->attach($article);

        $this->assertTrue($tag->articles->contains($article));
    }

    #[Test]
    public function tag_slug_generation(): void
    {
        $tag = Tag::factory()->create(['name' => 'Test Tag']);
        $this->assertEquals('test-tag', $tag->slug);
    }

    #[Test]
    public function tag_timestamps(): void
    {
        $tag = Tag::factory()->create();

        $this->assertNotNull($tag->created_at);
        $this->assertNotNull($tag->updated_at);
    }

    #[Test]
    public function tag_basic_attributes(): void
    {
        $data = [
            'name' => 'Test Tag',
            'slug' => 'test-tag',
            'description' => 'Test description',
        ];

        $tag = Tag::factory()->create($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $tag->$key);
        }
    }

    #[Test]
    public function tag_can_be_searched(): void
    {
        $tag = Tag::factory()->create(['name' => 'Test Tag']);

        $searchResults = Tag::search('Test')->get();

        $this->assertTrue($searchResults->contains($tag));
    }

    #[Test]
    public function tag_article_count_is_accurate(): void
    {
        $tag = Tag::factory()->create();
        Article::factory(3)
            ->create(['is_published' => true])
            ->each(function ($article) use ($tag) {
                $article->tags()->attach($tag);
            });

        $this->assertEquals(3, $tag->articles()->count());
    }

    #[Test]
    public function tag_only_counts_published_articles(): void
    {
        $tag = Tag::factory()->create();
        Article::factory()->create(['is_published' => true])->tags()->attach($tag);
        Article::factory()->create(['is_published' => false])->tags()->attach($tag);

        $this->assertEquals(1, $tag->publishedArticles()->count());
    }

    #[Test]
    public function tag_can_get_popular(): void
    {
        $popularTag = Tag::factory()->create();
        $unpopularTag = Tag::factory()->create();

        Article::factory(5)
            ->create(['is_published' => true])
            ->each(function ($article) use ($popularTag) {
                $article->tags()->attach($popularTag);
            });

        Article::factory()
            ->create(['is_published' => true])
            ->tags()
            ->attach($unpopularTag);

        $popularTags = Tag::getPopular(1);

        $this->assertEquals(1, $popularTags->count());
        $this->assertEquals($popularTag->id, $popularTags->first()->id);
    }

    #[Test]
    public function tag_can_get_related(): void
    {
        $tag = Tag::factory()->create();
        $article = Article::factory()->create(['is_published' => true]);
        $relatedTag = Tag::factory()->create();

        $article->tags()->attach([$tag->id, $relatedTag->id]);

        $related = $tag->getRelated();

        $this->assertTrue($related->contains($relatedTag));
        $this->assertFalse($related->contains($tag));
    }

    #[Test]
    public function tag_name_is_unique(): void
    {
        Tag::factory()->create(['name' => 'Test Tag']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        Tag::factory()->create(['name' => 'Test Tag']);
    }

    #[Test]
    public function tag_slug_is_unique(): void
    {
        Tag::factory()->create(['slug' => 'test-tag']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        Tag::factory()->create(['slug' => 'test-tag']);
    }

    #[Test]
    public function tag_can_merge_with_another_tag(): void
    {
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();

        $article1 = Article::factory()->create(['is_published' => true]);
        $article2 = Article::factory()->create(['is_published' => true]);

        $article1->tags()->attach($tag1);
        $article2->tags()->attach($tag2);

        $tag1->mergeWith($tag2);

        $this->assertEquals(2, $tag1->articles()->count());
        $this->assertFalse(Tag::where('id', $tag2->id)->exists());
    }

    #[Test]
    public function tag_can_be_soft_deleted(): void
    {
        $tag = Tag::factory()->create();
        $tag->delete();

        $this->assertSoftDeleted($tag);
    }

    #[Test]
    public function tag_articles_are_preserved_after_soft_delete(): void
    {
        $tag = Tag::factory()->create();
        $article = Article::factory()->create(['is_published' => true]);
        $tag->articles()->attach($article);

        $tag->delete();

        $this->assertTrue($article->tags()->withTrashed()->get()->contains($tag));
    }
} 