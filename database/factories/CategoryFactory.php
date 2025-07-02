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
        return [
            'published' => $this->faker->boolean(90), // 90% chance of being published
            'position' => $this->faker->numberBetween(1, 50),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Category $category) {
            $title = $this->faker->words(2, true);
            
            // Create translation data
            $category->translateOrNew('en')->title = $title;
            $category->translateOrNew('en')->description = $this->faker->sentence();
            $category->translateOrNew('en')->active = true;

            $category->translateOrNew('lt')->title = $this->faker->words(2, true);
            $category->translateOrNew('lt')->description = $this->faker->sentence();
            $category->translateOrNew('lt')->active = true;

            $category->save();
        });
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

    public function withTitle(string $title): static
    {
        return $this->afterCreating(function (Category $category) use ($title) {
            $category->translateOrNew('en')->title = $title;
            $category->translateOrNew('en')->description = $this->faker->sentence();
            $category->translateOrNew('en')->active = true;
            $category->save();
            
            // Create or update slug for English
            $category->slugs()->updateOrCreate([
                'locale' => 'en',
            ], [
                'slug' => Str::slug($title),
                'active' => true,
            ]);
        });
    }
}
