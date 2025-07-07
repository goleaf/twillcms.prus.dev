<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

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
        $response->assertViewHas('searchTerm', $searchTerm);
        $response->assertSee($matchingArticle->title);
        $response->assertDontSee($nonMatchingArticle->title);
    }

    public function test_search_returns_articles_matching_content(): void
    {
        // Create test data
        $searchTerm = 'unique content term';
        $matchingArticle = Article::factory()->create([
            'content' => "Article content with {$searchTerm}",
            'is_published' => true,
        ]);
        $nonMatchingArticle = Article::factory()->create([
            'content' => 'Unrelated content',
            'is_published' => true,
        ]);

        // Perform search
        $response = $this->get(route('articles.search', ['q' => $searchTerm]));

        // Assert response
        $response->assertStatus(200);
        $response->assertSee($matchingArticle->title);
        $response->assertDontSee($nonMatchingArticle->title);
    }

    public function test_search_returns_articles_matching_excerpt(): void
    {
        // Create test data
        $searchTerm = 'unique excerpt term';
        $matchingArticle = Article::factory()->create([
            'excerpt' => "Article excerpt with {$searchTerm}",
            'is_published' => true,
        ]);
        $nonMatchingArticle = Article::factory()->create([
            'excerpt' => 'Unrelated excerpt',
            'is_published' => true,
        ]);

        // Perform search
        $response = $this->get(route('articles.search', ['q' => $searchTerm]));

        // Assert response
        $response->assertStatus(200);
        $response->assertSee($matchingArticle->title);
        $response->assertDontSee($nonMatchingArticle->title);
    }

    public function test_search_returns_articles_matching_tags(): void
    {
        // Create test data
        $searchTerm = 'unique tag term';
        $matchingTag = Tag::factory()->create(['name' => $searchTerm]);
        $nonMatchingTag = Tag::factory()->create(['name' => 'unrelated tag']);

        $matchingArticle = Article::factory()->create(['is_published' => true]);
        $matchingArticle->tags()->attach($matchingTag);

        $nonMatchingArticle = Article::factory()->create(['is_published' => true]);
        $nonMatchingArticle->tags()->attach($nonMatchingTag);

        // Perform search
        $response = $this->get(route('articles.search', ['q' => $searchTerm]));

        // Assert response
        $response->assertStatus(200);
        $response->assertSee($matchingArticle->title);
        $response->assertDontSee($nonMatchingArticle->title);
    }

    public function test_search_only_returns_published_articles(): void
    {
        // Create test data
        $searchTerm = 'test term';
        $publishedArticle = Article::factory()->create([
            'title' => "Published article with {$searchTerm}",
            'is_published' => true,
        ]);
        $unpublishedArticle = Article::factory()->create([
            'title' => "Unpublished article with {$searchTerm}",
            'is_published' => false,
        ]);

        // Perform search
        $response = $this->get(route('articles.search', ['q' => $searchTerm]));

        // Assert response
        $response->assertStatus(200);
        $response->assertSee($publishedArticle->title);
        $response->assertDontSee($unpublishedArticle->title);
    }

    public function test_search_returns_paginated_results(): void
    {
        // Create test data
        $searchTerm = 'paginated';
        Article::factory(15)->create([
            'title' => "Article with {$searchTerm}",
            'is_published' => true,
        ]);

        // Perform search
        $response = $this->get(route('articles.search', ['q' => $searchTerm]));

        // Assert response
        $response->assertStatus(200);
        $response->assertViewHas('articles');
        $response->assertSee('Next');
    }

    public function test_empty_search_returns_all_published_articles(): void
    {
        // Create test data
        $publishedArticle = Article::factory()->create(['is_published' => true]);
        $unpublishedArticle = Article::factory()->create(['is_published' => false]);

        // Perform empty search
        $response = $this->get(route('articles.search', ['q' => '']));

        // Assert response
        $response->assertStatus(200);
        $response->assertSee($publishedArticle->title);
        $response->assertDontSee($unpublishedArticle->title);
    }

    public function test_search_is_case_insensitive(): void
    {
        // Create test data
        $searchTerm = 'UPPERCASE';
        $matchingArticle = Article::factory()->create([
            'title' => "Article with lowercase",
            'is_published' => true,
        ]);

        // Perform search
        $response = $this->get(route('articles.search', ['q' => $searchTerm]));

        // Assert response
        $response->assertStatus(200);
        $response->assertSee($matchingArticle->title);
    }
} 