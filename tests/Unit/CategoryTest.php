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
        $category = Category::create([
            'published' => true,
            'title' => 'Technology',
            'description' => 'Latest technology news',
            'position' => 1,
        ]);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertTrue($category->published);
        $this->assertEquals('Technology', $category->title);
    }

    public function test_category_slug_generation()
    {
        $category = Category::create([
            'published' => true,
            'title' => 'Test Category Title',
            'description' => 'Test description',
            'position' => 1,
        ]);

        $this->assertNotNull($category->slug);
        $this->assertEquals('test-category-title', $category->getSlugAttribute());
    }

    public function test_category_can_have_posts()
    {
        $category = Category::create([
            'published' => true,
            'title' => 'Technology',
            'description' => 'Tech category',
            'position' => 1,
        ]);

        $post = Post::create([
            'published' => true,
            'title' => 'Test Post',
            'description' => 'Test description',
            'content' => 'Test content',
            'position' => 1,
        ]);

        $category->posts()->attach($post->id);

        $this->assertEquals(1, $category->posts()->count());
        $this->assertTrue($category->posts->contains($post));
    }

    public function test_category_position_ordering()
    {
        $categoryPos1 = Category::create([
            'published' => true,
            'title' => 'Category 1',
            'description' => 'First category',
            'position' => 1,
        ]);

        $categoryPos2 = Category::create([
            'published' => true,
            'title' => 'Category 2',
            'description' => 'Second category',
            'position' => 2,
        ]);

        $categoryPos3 = Category::create([
            'published' => true,
            'title' => 'Category 3',
            'description' => 'Third category',
            'position' => 3,
        ]);

        $categories = Category::orderBy('position')->get();

        // First category should be the one with position 1
        $this->assertEquals($categoryPos1->id, $categories->first()->id);
        // Last category should be the one with position 3
        $this->assertEquals($categoryPos3->id, $categories->last()->id);
    }

    public function test_published_categories_scope()
    {
        $publishedCategory = Category::create([
            'published' => true,
            'title' => 'Published Category',
            'description' => 'Published',
            'position' => 1,
        ]);

        $unpublishedCategory = Category::create([
            'published' => false,
            'title' => 'Unpublished Category',
            'description' => 'Not published',
            'position' => 2,
        ]);

        $this->assertTrue($publishedCategory->published);
        $this->assertFalse($unpublishedCategory->published);
    }

    public function test_category_timestamps()
    {
        $category = Category::create([
            'published' => true,
            'title' => 'Test Category',
            'description' => 'Test description',
            'position' => 1,
        ]);

        $this->assertNotNull($category->created_at);
        $this->assertNotNull($category->updated_at);
    }

    public function test_category_hierarchical_relationships()
    {
        $parentCategory = Category::create([
            'published' => true,
            'title' => 'Parent Category',
            'description' => 'Parent description',
            'position' => 1,
        ]);

        $childCategory = Category::create([
            'published' => true,
            'title' => 'Child Category',
            'description' => 'Child description',
            'position' => 2,
            'parent_id' => $parentCategory->id,
        ]);

        $this->assertEquals($parentCategory->id, $childCategory->parent_id);
        $this->assertTrue($parentCategory->children->contains($childCategory));
        $this->assertEquals($parentCategory->id, $childCategory->parent->id);
    }

    public function test_category_color_and_icon()
    {
        $category = Category::create([
            'published' => true,
            'title' => 'Colorful Category',
            'description' => 'Category with color and icon',
            'position' => 1,
            'color_code' => '#ff0000',
            'icon' => '🚀',
        ]);

        $this->assertEquals('#ff0000', $category->color_code);
        $this->assertEquals('🚀', $category->icon);
        $this->assertStringContainsString('#ff0000', $category->color_style);
    }

    public function test_category_featured_status()
    {
        $category = Category::create([
            'published' => true,
            'title' => 'Featured Category',
            'description' => 'This category is featured',
            'position' => 1,
            'settings' => ['is_featured' => true],
        ]);

        $this->assertTrue($category->is_featured);

        $category->markAsFeatured(false);
        $this->assertFalse($category->fresh()->is_featured);
    }
} 