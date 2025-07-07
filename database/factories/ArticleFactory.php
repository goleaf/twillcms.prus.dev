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
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(rand(3, 8));
        $content = $this->generateContent();
        $publishedAt = fake()->dateTimeBetween('-1 year', 'now');
        
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->paragraph(rand(2, 4)),
            'content' => $content,
            'image' => $this->generateImagePath(),
            'image_caption' => fake()->sentence(rand(3, 6)),
            'is_featured' => fake()->boolean(15), // 15% chance of being featured
            'is_published' => fake()->boolean(85), // 85% chance of being published
            'published_at' => $publishedAt,
            'reading_time' => Article::calculateReadingTime($content),
            'view_count' => fake()->numberBetween(0, 5000),
            'created_at' => $publishedAt,
            'updated_at' => fake()->dateTimeBetween($publishedAt, 'now'),
        ];
    }

    /**
     * Generate article content with random paragraphs
     */
    private function generateContent(): string
    {
        $paragraphs = [];
        $paragraphCount = rand(3, 12);
        
        for ($i = 0; $i < $paragraphCount; $i++) {
            $sentences = [];
            $sentenceCount = rand(3, 8);
            
            for ($j = 0; $j < $sentenceCount; $j++) {
                $sentences[] = fake()->sentence(rand(8, 20));
            }
            
            $paragraphs[] = implode(' ', $sentences);
        }
        
        return implode("\n\n", $paragraphs);
    }

    /**
     * Generate random image path
     */
    private function generateImagePath(): string
    {
        $categories = ['technology', 'business', 'science', 'health', 'sports', 'entertainment', 'politics', 'world'];
        $category = $categories[array_rand($categories)];
        $width = rand(800, 1200);
        $height = rand(400, 800);
        
        return "https://picsum.photos/{$width}/{$height}?random=" . rand(1, 1000);
    }

    /**
     * Indicate that the article is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the article is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    /**
     * Indicate that the article is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }

    /**
     * Indicate that the article is recent.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the article is popular (high view count).
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'view_count' => fake()->numberBetween(1000, 10000),
        ]);
    }

    /**
     * Create article with specific category-like content
     */
    public function withCategory(string $category): static
    {
        $categoryTitles = [
            'technology' => [
                'The Future of Artificial Intelligence',
                'Revolutionary Blockchain Technology',
                'Quantum Computing Breakthrough',
                'Cybersecurity in the Digital Age',
                'The Rise of IoT Devices',
            ],
            'business' => [
                'Market Trends and Analysis',
                'Startup Success Stories',
                'Economic Forecast for Next Year',
                'Investment Strategies That Work',
                'Corporate Leadership Insights',
            ],
            'science' => [
                'Climate Change Research Updates',
                'Space Exploration Achievements',
                'Medical Breakthrough Discoveries',
                'Environmental Conservation Efforts',
                'Scientific Innovation in Healthcare',
            ],
            'health' => [
                'Mental Health Awareness',
                'Nutrition and Wellness Tips',
                'Exercise and Fitness Trends',
                'Healthcare Technology Advances',
                'Disease Prevention Strategies',
            ],
            'sports' => [
                'Championship Game Highlights',
                'Athlete Performance Analysis',
                'Sports Technology Innovations',
                'Team Strategy Breakdowns',
                'Olympic Games Coverage',
            ],
        ];

        $titles = $categoryTitles[$category] ?? $categoryTitles['technology'];
        $title = $titles[array_rand($titles)] . ' - ' . fake()->sentence(rand(2, 4));

        return $this->state(fn (array $attributes) => [
            'title' => $title,
            'slug' => Str::slug($title),
        ]);
    }
}
