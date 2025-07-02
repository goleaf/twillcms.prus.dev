<?php

namespace Tests\Feature\Admin;

use A17\Twill\Models\User;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a Twill admin user for admin access
        $this->user = User::create([
            'email' => 'admin@test.com',
            'name' => 'Test Admin',
            'password' => bcrypt('password'),
            'published' => true,
            'role' => 'SUPERADMIN',
            'registered_at' => now(),
        ]);

        // Set up test locale
        app()->setLocale('en');
    }

    public function test_admin_can_view_posts_index()
    {
        $this->actingAs($this->user, 'twill_users');

        Post::factory()->count(5)->create();

        $response = $this->get('/admin/posts');

        $response->assertStatus(200);
    }

    public function test_admin_can_view_post_create_form()
    {
        $this->actingAs($this->user, 'twill_users');

        $response = $this->get('/admin/posts/create');

        // Twill redirects create form to index with openCreate parameter
        if ($response->getStatusCode() === 302) {
            $location = $response->headers->get('Location');
            $this->assertStringContainsString('/admin/posts', $location);
            $this->assertStringContainsString('openCreate=1', $location);
        } else {
            $response->assertStatus(200);
        }
    }

    public function test_admin_can_create_post()
    {
        $this->actingAs($this->user, 'twill_users');

        $category = Category::factory()->create();

        $postData = [
            'title' => ['en' => 'Test Post Title'],
            'description' => ['en' => 'Test post description'],
            'content' => ['en' => 'Test post content'],
            'published' => true,
            'languages' => [
                'en' => ['published' => true, 'active' => true],
            ],
            'slugs' => [
                'en' => [
                    'slug' => 'test-post-title',
                    'locale' => 'en',
                    'active' => true,
                ],
            ],
        ];

        $response = $this->post('/admin/posts', $postData);

        // Twill returns JSON response with redirect URL for AJAX requests
        if ($response->getStatusCode() === 200) {
            $responseData = json_decode($response->getContent(), true);
            $this->assertArrayHasKey('redirect', $responseData);
            $this->assertStringContainsString('/admin/posts/', $responseData['redirect']);
        } else {
            $response->assertRedirect();
        }
        $this->assertDatabaseHas('posts', ['published' => true]);
        $this->assertDatabaseHas('post_translations', [
            'title' => 'Test Post Title',
            'description' => 'Test post description',
            'content' => 'Test post content',
        ]);
    }

    public function test_admin_can_view_post_edit_form()
    {
        $this->actingAs($this->user, 'twill_users');

        $post = Post::factory()->create();

        $response = $this->get("/admin/posts/{$post->id}/edit");

        $response->assertStatus(200);
    }

    public function test_admin_can_update_post()
    {
        $this->actingAs($this->user, 'twill_users');

        $post = Post::factory()->create();

        $updateData = [
            'title' => ['en' => 'Updated Post Title'],
            'description' => ['en' => 'Updated description'],
            'content' => ['en' => 'Updated content'],
            'published' => true,
            'languages' => [
                'en' => ['published' => true, 'active' => true],
            ],
            'slugs' => [
                'en' => [
                    'slug' => 'updated-post-title',
                    'locale' => 'en',
                    'active' => true,
                ],
            ],
        ];

        $response = $this->put("/admin/posts/{$post->id}", $updateData);

        // Twill returns JSON response - either redirect for create or success message for update
        if ($response->getStatusCode() === 200) {
            $responseData = json_decode($response->getContent(), true);
            // Check if it's a redirect response (create) or success message (update)
            if (isset($responseData['redirect'])) {
                $this->assertStringContainsString('/admin/posts/', $responseData['redirect']);
            } elseif (isset($responseData['message'])) {
                $this->assertStringContainsString('saved', strtolower($responseData['message']));
            } else {
                // Update was successful (status 200)
                $this->assertTrue(true);
            }
        } else {
            $response->assertRedirect();
        }
        $this->assertDatabaseHas('post_translations', [
            'title' => 'Updated Post Title',
            'description' => 'Updated description',
            'content' => 'Updated content',
        ]);
    }

    public function test_admin_can_delete_post()
    {
        $this->actingAs($this->user, 'twill_users');

        $post = Post::factory()->create();

        $response = $this->delete("/admin/posts/{$post->id}");

        $response->assertRedirect();
        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    public function test_post_creation_validates_required_fields()
    {
        $this->actingAs($this->user, 'twill_users');

        $response = $this->post('/admin/posts', []);

        $response->assertSessionHasErrors(['title']);
    }

    public function test_post_creation_validates_title_length()
    {
        $this->actingAs($this->user, 'twill_users');

        $response = $this->post('/admin/posts', [
            'title' => ['en' => str_repeat('a', 201)],
        ]);

        $response->assertSessionHasErrors(['title.en']);
    }

    public function test_admin_can_publish_unpublish_post()
    {
        $this->actingAs($this->user, 'twill_users');

        $post = Post::factory()->create(['published' => false]);

        // Publish
        $response = $this->put('/admin/posts/publish', [
            'ids' => (string) $post->id,
            'publish' => true,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'published' => true]);

        // Unpublish
        $response = $this->put('/admin/posts/publish', [
            'ids' => (string) $post->id,
            'publish' => false,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'published' => false]);
    }

    public function test_admin_can_reorder_posts()
    {
        $this->actingAs($this->user, 'twill_users');

        $post1 = Post::factory()->create(['position' => 1]);
        $post2 = Post::factory()->create(['position' => 2]);

        $response = $this->post('/admin/posts/reorder', [
            'ids' => implode(',', [$post2->id, $post1->id]),
        ]);

        $response->assertStatus(200);
    }

    public function test_admin_can_bulk_delete_posts()
    {
        $this->actingAs($this->user, 'twill_users');

        $posts = Post::factory()->count(3)->create();
        $postIds = $posts->pluck('id')->toArray();

        $response = $this->post('/admin/posts/bulkDelete', [
            'ids' => implode(',', $postIds),
        ]);

        $response->assertStatus(200);

        foreach ($postIds as $id) {
            $this->assertSoftDeleted('posts', ['id' => $id]);
        }
    }

    public function test_admin_can_restore_deleted_post()
    {
        $this->actingAs($this->user, 'twill_users');

        $post = Post::factory()->create();
        $post->delete();

        $response = $this->put('/admin/posts/restore', [
            'ids' => (string) $post->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'deleted_at' => null]);
    }

    public function test_guest_cannot_access_admin_posts()
    {
        $response = $this->get('/admin/posts');

        $response->assertRedirect('/admin/login');
    }
}
