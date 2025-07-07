<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Services\ImageGenerationService;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(4);
        $content = fake()->paragraphs(5, true);
        $imageService = app(ImageGenerationService::class);
        $filename = $imageService->generatePostImage(null, $title, 'post');
        $imagePath = 'images/posts/' . $filename;
        $status = fake()->randomElement(['published', 'draft']);
        $publishedAt = $status === 'published' ? fake()->dateTimeBetween('-3 months', 'now') : now();
        $updatedAt = fake()->dateTimeBetween($publishedAt, 'now');

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $content,
            'excerpt' => fake()->paragraph(),
            'featured_image' => $imagePath,
            'status' => $status,
            'user_id' => User::factory(),
            'published_at' => $publishedAt,
            'updated_at' => $updatedAt,
        ];
    }

    /**
     * Create a published post
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ]);
    }

    /**
     * Create a draft post
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    /**
     * Create an archived post
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
            'published_at' => null,
        ]);
    }
}
