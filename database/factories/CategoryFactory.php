<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->words(2, true);

        return [
            'published' => $this->faker->boolean(80),
            'title' => ucwords($title),
            'description' => $this->faker->paragraph(1),
            'position' => $this->faker->numberBetween(1, 100),
            'slug' => Str::slug($title),
            'color_code' => $this->faker->hexColor(),
            'view_count' => $this->faker->numberBetween(0, 500),
            'sort_order' => $this->faker->numberBetween(1, 10),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published' => true,
        ]);
    }

    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'published' => false,
        ]);
    }

    /**
     * Create category with title
     */
    public function withTitle(string $title): static
    {
        return $this->afterCreating(function (Category $category) use ($title) {
            $category->update([
                'title' => $title,
                'description' => "Description for {$title}",
            ]);
        });
    }
}
