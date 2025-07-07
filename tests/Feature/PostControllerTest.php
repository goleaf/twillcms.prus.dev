<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_posts()
    {
        $post = Post::factory()->published()->create([
            'title' => 'Test Post',
        ]);

        $response = $this->get('/api/v1/posts');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'slug',
                ],
            ],
        ]);
    }

    public function test_can_show_individual_post()
    {
        $post = Post::factory()->published()->create([
            'title' => 'Test Post',
            'slug' => 'test-post',
        ]);

        $response = $this->get("/api/v1/posts/{$post->slug}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'title',
            'slug',
            'content',
        ]);
    }
}
