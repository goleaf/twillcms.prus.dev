<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true) . ' ' . uniqid();
        $slug = Str::slug($name);
        return [
            'name' => ucfirst($name),
            'slug' => $slug,
            'description' => fake()->sentence(8),
            'color' => fake()->hexColor(),
            'is_featured' => fake()->boolean(10),
            'usage_count' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * Indicate that the tag is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the tag is popular (high usage count).
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'usage_count' => fake()->numberBetween(50, 200),
        ]);
    }

    /**
     * Create tag with specific color scheme
     */
    public function withColor(string $color): static
    {
        return $this->state(fn (array $attributes) => [
            'color' => $color,
        ]);
    }

    /**
     * Create tag with specific category
     */
    public function category(string $category): static
    {
        $categoryTags = [
            'technology' => ['Technology', 'AI', 'Programming', 'Web Development', 'Mobile Apps', 'Cybersecurity', 'Data Science'],
            'business' => ['Business', 'Entrepreneurship', 'Marketing', 'Finance', 'Leadership', 'Startups', 'Economics'],
            'science' => ['Science', 'Research', 'Innovation', 'Space', 'Climate', 'Environment', 'Medicine'],
            'health' => ['Health', 'Wellness', 'Fitness', 'Nutrition', 'Mental Health', 'Healthcare', 'Medicine'],
            'sports' => ['Sports', 'Football', 'Basketball', 'Tennis', 'Olympics', 'Fitness', 'Athletics'],
            'entertainment' => ['Entertainment', 'Movies', 'Music', 'TV Shows', 'Celebrity', 'Gaming', 'Books'],
            'lifestyle' => ['Lifestyle', 'Travel', 'Food', 'Fashion', 'Home', 'Relationships', 'Parenting'],
        ];

        $tags = $categoryTags[$category] ?? $categoryTags['technology'];
        $tagName = $tags[array_rand($tags)] . ' ' . uniqid();
        $slug = Str::slug($tagName);
        return $this->state(fn (array $attributes) => [
            'name' => $tagName,
            'slug' => $slug,
        ]);
    }

    public function configure()
    {
        return $this->afterCreating(function (\App\Models\Tag $tag) {
            $publishedArticle = \App\Models\Article::where('is_published', true)->inRandomOrder()->first();
            if ($publishedArticle) {
                $tag->articles()->attach($publishedArticle->id);
                $tag->usage_count = $tag->articles()->count();
                $tag->save();
            }
        });
    }
}
