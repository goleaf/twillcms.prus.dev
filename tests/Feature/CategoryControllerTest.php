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
        ]);

        $response = $this->get(route('twill.categories.index'));

        $response->assertStatus(200);
        $response->assertSee($category->name);
    }

    public function test_can_create_category()
    {
        $response = $this->post(route('twill.categories.store'), [
            'name' => ['en' => 'Test Category'],
            'published' => true,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('categories', [
            'published' => true,
        ]);
    }
}
