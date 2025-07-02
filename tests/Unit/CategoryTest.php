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
            'position' => 1,
        ]);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertTrue($category->published);
        $this->assertEquals(1, $category->position);
    }

    public function test_category_can_have_translations()
    {
        $category = Category::create([
            'published' => true,
            'position' => 1,
        ]);

        $category->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Technology',
            'description' => 'Latest technology news and reviews',
        ]);

        $category->translations()->create([
            'locale' => 'lt',
            'active' => true,
            'title' => 'Technologijos',
            'description' => 'Naujausios technologijų naujienos ir apžvalgos',
        ]);

        $this->assertEquals(2, $category->translations()->count());

        $enTranslation = $category->translations()->where('locale', 'en')->first();
        $this->assertEquals('Technology', $enTranslation->title);

        $ltTranslation = $category->translations()->where('locale', 'lt')->first();
        $this->assertEquals('Technologijos', $ltTranslation->title);
    }

    public function test_category_can_have_posts()
    {
        $category = Category::create([
            'published' => true,
            'position' => 1,
        ]);

        $post1 = Post::create([
            'published' => true,
            'position' => 1,
        ]);

        $post2 = Post::create([
            'published' => true,
            'position' => 2,
        ]);

        $category->posts()->attach([$post1->id, $post2->id]);

        $this->assertEquals(2, $category->posts()->count());
        $this->assertTrue($category->posts->contains($post1));
        $this->assertTrue($category->posts->contains($post2));
    }

    public function test_category_position_ordering()
    {
        $categoryPos1 = Category::create([
            'published' => true,
            'position' => 1,
        ]);

        $categoryPos2 = Category::create([
            'published' => true,
            'position' => 2,
        ]);

        $categoryPos3 = Category::create([
            'published' => true,
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
            'position' => 1,
        ]);

        $unpublishedCategory = Category::create([
            'published' => false,
            'position' => 2,
        ]);

        $this->assertTrue($publishedCategory->published);
        $this->assertFalse($unpublishedCategory->published);
    }

    public function test_category_timestamps()
    {
        $category = Category::create([
            'published' => true,
            'position' => 1,
        ]);

        $this->assertNotNull($category->created_at);
        $this->assertNotNull($category->updated_at);
    }

    public function test_category_soft_deletes()
    {
        $category = Category::create([
            'published' => true,
            'position' => 1,
        ]);

        $categoryId = $category->id;

        // Soft delete
        $category->delete();

        // Should not be found in normal queries
        $this->assertNull(Category::find($categoryId));

        // Should be found with trashed
        $this->assertNotNull(Category::withTrashed()->find($categoryId));
    }

    public function test_category_translation_active_status()
    {
        $category = Category::create([
            'published' => true,
            'position' => 1,
        ]);

        $activeTranslation = $category->translations()->create([
            'locale' => 'en',
            'active' => true,
            'title' => 'Active Category',
            'description' => 'This is an active translation',
        ]);

        $inactiveTranslation = $category->translations()->create([
            'locale' => 'lt',
            'active' => false,
            'title' => 'Neaktyvi kategorija',
            'description' => 'Tai yra neaktyvi vertimas',
        ]);

        $this->assertTrue($activeTranslation->active);
        $this->assertFalse($inactiveTranslation->active);
    }
}
