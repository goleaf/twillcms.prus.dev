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
        $title = $this->faker->sentence(4);
        $isPublished = $this->faker->boolean(80); // 80% chance of being published

        return [
            'published' => $isPublished,
            'published_at' => $isPublished ? $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s') : null,
            'title' => $title,
            'description' => $this->faker->paragraph(2),
            'content' => $this->faker->paragraphs(5, true),
            'position' => $this->faker->numberBetween(1, 100),
            'slug' => Str::slug($title),
            'view_count' => $this->faker->numberBetween(0, 1000),
            'priority' => $this->faker->numberBetween(0, 10),
            'excerpt_override' => $this->faker->boolean(30) ? $this->faker->paragraph(1) : null,
            'featured_image_caption' => $this->faker->boolean(50) ? $this->faker->sentence() : null,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published' => true,
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
        ]);
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

    public function withTitle(string $title): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => $title,
            'slug' => Str::slug($title),
        ]);
    }

    public function withContent(string $content): static
    {
        return $this->state(fn (array $attributes) => [
            'content' => $content,
        ]);
    }
}
