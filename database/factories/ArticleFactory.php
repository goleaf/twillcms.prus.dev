<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(rand(6, 12));
        $content = collect(range(1, rand(4, 8)))
            ->map(fn() => "<p>" . $this->faker->paragraph(rand(8, 15)) . "</p>")
            ->join("\n\n");
        
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => $this->faker->text(200),
            'content' => $content,
            'image' => 'articles/' . $this->faker->image('public/storage/articles', 1200, 800, null, false),
            'image_caption' => $this->faker->sentence(),
            'view_count' => $this->faker->numberBetween(0, 10000),
            'reading_time' => ceil(str_word_count(strip_tags($content)) / 200),
            'is_featured' => $this->faker->boolean(10),
            'is_published' => $this->faker->boolean(90),
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function unpublished(): self
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }

    public function featured(): self
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
