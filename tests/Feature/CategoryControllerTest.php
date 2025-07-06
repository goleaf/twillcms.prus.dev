<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_categories()
    {
        $category = Category::factory()->create([
            'published' => true,
            'title' => 'Test Category',
        ]);

        $response = $this->get('/api/v1/categories');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                ],
            ],
        ]);
    }

    public function test_can_show_individual_category()
    {
        $category = Category::factory()->create([
            'published' => true,
            'title' => 'Test Category',
            'slug' => 'test-category',
        ]);

        $response = $this->get("/api/v1/categories/{$category->slug}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'slug',
            'description',
        ]);
    }
}
