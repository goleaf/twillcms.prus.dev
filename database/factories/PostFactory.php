<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        return [
            'published' => $this->faker->boolean(80), // 80% chance published
            'published_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'position' => $this->faker->numberBetween(1, 100),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Create a published post
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published' => true,
            'published_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    /**
     * Create post with specific title and slug
     */
    public function withTitle(string $title): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => $title,
            'slug' => Str::slug($title),
        ]);
    }

    /**
     * Create post with categories
     */
    public function withCategories(array $categoryIds): static
    {
        return $this->afterCreating(function (Post $post) use ($categoryIds) {
            $post->categories()->sync($categoryIds);
        });
    }

    /**
     * Create post with content
     */
    public function withContent(?string $title = null, ?string $content = null): static
    {
        return $this->afterCreating(function (Post $post) use ($title, $content) {
            $postTitle = $title ?: $this->faker->sentence(4);
            $postContent = $content ?: $this->faker->paragraphs(5, true);
            $postDescription = $this->faker->paragraph(1);

            // Set content directly on the post
            $post->update([
                'title' => $postTitle,
                'content' => $postContent,
                'description' => $postDescription,
            ]);
        });
    }

    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'published' => false,
            'published_at' => null,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'settings' => ['is_featured' => true],
            'priority' => $this->faker->numberBetween(8, 10),
        ]);
    }

    public function trending(): static
    {
        return $this->state(fn (array $attributes) => [
            'settings' => ['is_trending' => true],
            'view_count' => $this->faker->numberBetween(500, 5000),
        ]);
    }

    public function breaking(): static
    {
        return $this->state(fn (array $attributes) => [
            'settings' => ['is_breaking' => true],
            'priority' => 10,
        ]);
    }
}
