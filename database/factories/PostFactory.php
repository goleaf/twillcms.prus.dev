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
            'position' => $this->faker->numberBetween(1, 100),
            'slug' => Str::slug($title),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Post $post) {
            // Create translation data
            $post->translateOrNew('en')->title = $this->faker->sentence(4);
            $post->translateOrNew('en')->description = $this->faker->paragraph(2);
            $post->translateOrNew('en')->content = $this->faker->paragraphs(5, true);
            $post->translateOrNew('en')->active = true;

            $post->translateOrNew('lt')->title = $this->faker->sentence(4);
            $post->translateOrNew('lt')->description = $this->faker->paragraph(2);
            $post->translateOrNew('lt')->content = $this->faker->paragraphs(5, true);
            $post->translateOrNew('lt')->active = true;

            $post->save();
        });
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
        ]);
    }

    public function withTitle(string $title): static
    {
        return $this->state(fn (array $attributes) => [
            'slug' => Str::slug($title),
        ])->afterCreating(function (Post $post) use ($title) {
            $post->translateOrNew('en')->title = $title;
            $post->save();
        });
    }
}
