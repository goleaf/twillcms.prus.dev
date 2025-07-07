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
        $title = fake()->words(2, true);

        return [
            'title' => ucwords($title),
            'name' => ucwords($title),
            'slug' => Str::slug($title),
            'description' => fake()->paragraph(),
            'image' => null,
            'is_active' => true,
            'published' => fake()->boolean(80),
            'view_count' => fake()->numberBetween(0, 500),
            'position' => fake()->numberBetween(1, 100),
            'sort_order' => fake()->numberBetween(1, 10),
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

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
