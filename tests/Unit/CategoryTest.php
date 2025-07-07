<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_category_can_be_created()
    {
        $category = Category::factory()->create([
            'title' => 'Technology',
        ]);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals('Technology', $category->title);
        $this->assertNotNull($category->slug);
    }

    public function test_category_slug_generation()
    {
        $category = Category::factory()->create([
            'title' => 'Test Category Title',
        ]);

        $this->assertNotNull($category->slug);
        $this->assertStringContainsString('test-category-title', $category->slug);
    }

    public function test_category_can_have_posts()
    {
        $category = Category::factory()->create();
        $post = Post::factory()->create();

        $category->posts()->attach($post->id);

        $this->assertEquals(1, $category->posts()->count());
        $this->assertTrue($category->posts->contains($post));
    }

    public function test_category_timestamps()
    {
        $category = Category::factory()->create();

        $this->assertNotNull($category->created_at);
        $this->assertNotNull($category->updated_at);
    }

    public function test_category_basic_attributes()
    {
        $category = Category::factory()->create([
            'title' => 'Test Category',
        ]);

        $this->assertEquals('Test Category', $category->title);
        $this->assertNotNull($category->slug);
    }
}
