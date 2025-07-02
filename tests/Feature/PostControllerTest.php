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
        $post = Post::factory()->create([
            'published' => true,
        ]);

        $response = $this->get(route('twill.posts.index'));

        $response->assertStatus(200);
        $response->assertSee($post->title);
    }

    public function test_can_create_post()
    {
        $response = $this->post(route('twill.posts.store'), [
            'title' => ['en' => 'Test Post'],
            'published' => true,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('posts', [
            'published' => true,
        ]);
    }
}
