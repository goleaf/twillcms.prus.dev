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
            'published' => $this->faker->boolean(90), // 90% chance of being published
            'title' => $title,
            'description' => $this->faker->sentence(),
            'position' => $this->faker->numberBetween(1, 50),
            'slug' => Str::slug($title),
            'view_count' => $this->faker->numberBetween(0, 500),
            'sort_order' => $this->faker->numberBetween(1, 100),
            'color_code' => $this->faker->hexColor(),
            'icon' => $this->faker->randomElement(['📰', '🏆', '💼', '🌟', '🔥', '📈', '🎯', '💡']),
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

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'settings' => ['is_featured' => true],
            'sort_order' => $this->faker->numberBetween(1, 10),
        ]);
    }

    public function withParent(Category $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
        ]);
    }

    public function withTitle(string $title): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => $title,
            'slug' => Str::slug($title),
        ]);
    }

    public function withColor(string $color): static
    {
        return $this->state(fn (array $attributes) => [
            'color_code' => $color,
        ]);
    }
}
