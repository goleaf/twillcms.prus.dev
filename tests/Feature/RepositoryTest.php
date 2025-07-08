<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Tag;
use App\Repositories\ArticleRepository;
use App\Repositories\TagRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ArticleRepository $articleRepository;
    private TagRepository $tagRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = new ArticleRepository(new Article());
        $this->tagRepository = new TagRepository(new Tag());
    }

    public function test_article_repository_get_all_paginated(): void
    {
        // Create test data
        Article::factory(15)->create(['is_published' => true]);

        // Get paginated articles
        $articles = $this->articleRepository->getAllPaginated();

        // Assert response
        $this->assertEquals(12, $articles->count()); // Default per page is 12
        $this->assertTrue($articles->hasMorePages());
    }

    public function test_article_repository_get_by_tag(): void
    {
        // Create test data
        $tag = Tag::factory()->create();
        $articles = Article::factory(3)
            ->create(['is_published' => true]);
        $articles->each(function ($article) use ($tag) {
            $article->tags()->attach($tag);
        });
        $tag->usage_count = $tag->articles()->count();
        $tag->save();
        // Get articles by tag
        $articlesResult = $this->articleRepository->getByTag($tag->slug);
        // Assert response
        $this->assertEquals(3, $articlesResult->total());
    }

    public function test_article_repository_get_featured(): void
    {
        // Create test data
        Article::factory(3)->create([
            'is_published' => true,
            'is_featured' => true,
        ]);

        // Get featured articles
        $articles = $this->articleRepository->getFeatured();

        // Assert response
        $this->assertEquals(3, $articles->count());
        $this->assertTrue($articles->every(fn ($article) => $article->is_featured));
    }

    public function test_article_repository_find_by_slug(): void
    {
        // Create test data
        $article = Article::factory()->create(['is_published' => true]);

        // Find article by slug
        $foundArticle = $this->articleRepository->findBySlug($article->slug);

        // Assert response
        $this->assertEquals($article->id, $foundArticle->id);
    }

    public function test_article_repository_search(): void
    {
        // Create test data
        $searchTerm = 'unique search term';
        Article::factory()->create([
            'title' => "Article with {$searchTerm}",
            'is_published' => true,
        ]);

        // Search articles
        $articles = $this->articleRepository->search($searchTerm);

        // Assert response
        $this->assertEquals(1, $articles->total());
    }

    public function test_article_repository_get_related(): void
    {
        // Create test data
        $tag = Tag::factory()->create();
        $article = Article::factory()->create(['is_published' => true]);
        $article->tags()->attach($tag);

        Article::factory(3)
            ->create(['is_published' => true])
            ->each(function ($article) use ($tag) {
                $article->tags()->attach($tag);
            });

        // Get related articles
        $relatedArticles = $this->articleRepository->getRelated($article);

        // Assert response
        $this->assertEquals(3, $relatedArticles->count());
    }

    public function test_tag_repository_get_all(): void
    {
        // Create test data
        Tag::factory(5)->create();

        // Get all tags
        $tags = $this->tagRepository->getAll();

        // Assert response
        $this->assertEquals(5, $tags->count());
    }

    public function test_tag_repository_find_by_slug(): void
    {
        // Create test data
        $tag = Tag::factory()->create();

        // Find tag by slug
        $foundTag = $this->tagRepository->findBySlug($tag->slug);

        // Assert response
        $this->assertEquals($tag->id, $foundTag->id);
    }

    public function test_tag_repository_get_popular(): void
    {
        // Create test data
        $popularTag = Tag::factory()->create();
        $unpopularTag = Tag::factory()->create();
        $articles = Article::factory(5)
            ->create(['is_published' => true]);
        $articles->each(function ($article) use ($popularTag) {
            $article->tags()->attach($popularTag);
        });
        $popularTag->usage_count = $popularTag->articles()->count();
        $popularTag->save();
        $article = Article::factory()->create(['is_published' => true]);
        $article->tags()->attach($unpopularTag);
        $unpopularTag->usage_count = $unpopularTag->articles()->count();
        $unpopularTag->save();
        // Get popular tags
        $popularTags = $this->tagRepository->getPopular(1);
        // Assert response
        $this->assertEquals(1, $popularTags->count());
        $this->assertEquals($popularTag->id, $popularTags->first()->id);
    }

    public function test_tag_repository_search(): void
    {
        // Create test data
        $searchTerm = 'unique tag';
        Tag::factory()->create(['name' => "Tag with {$searchTerm}"]);

        // Search tags
        $tags = $this->tagRepository->search($searchTerm);

        // Assert response
        $this->assertEquals(1, $tags->count());
    }
} 