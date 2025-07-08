<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Services\ImageGenerationService;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    private static int $laravelCount = 0;
    private static int $featuredCount = 0;
    private static int $nonFeaturedCount = 0;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);
        $slug = Str::slug($title) . '-' . uniqid();
        $status = 'published';
        $is_published = true;
        $now = now();
        $publishedAt = fake()->dateTimeBetween('-1 year', $now);
        $createdAt = fake()->dateTimeBetween('-1 year', $publishedAt);
        $updatedAt = fake()->dateTimeBetween($publishedAt, $now);
        $createdAt = \Carbon\Carbon::instance($createdAt > $now ? $now : $createdAt)->format('Y-m-d H:i:s');
        $updatedAt = \Carbon\Carbon::instance($updatedAt > $now ? $now : $updatedAt)->format('Y-m-d H:i:s');
        $publishedAt = \Carbon\Carbon::instance($publishedAt > $now ? $now : $publishedAt)->format('Y-m-d H:i:s');
        $viewCount = fake()->numberBetween(0, 5000);
        $imageService = app(\App\Services\ImageGenerationService::class);
        $filename = $imageService->generatePostImage(null, $title, 'article');
        $imagePath = 'images/posts/' . $filename;
        // Inject at least 5 Laravel articles for search tests
        if (self::$laravelCount < 5) {
            self::$laravelCount++;
            $title = 'Laravel Testing Guide ' . fake()->unique()->sentence(2);
            $slug = Str::slug($title) . '-' . uniqid();
            $laravelContent = 'This article is about Laravel. Laravel is a popular PHP framework. Learn Laravel testing best practices.';
            $isFeatured = self::$featuredCount < 3 ? true : false;
            if ($isFeatured) self::$featuredCount++;
            else self::$nonFeaturedCount++;
            // Force published status and valid published_at
            $status = 'published';
            $is_published = true;
            $publishedAt = now()->subDays(rand(1, 365))->format('Y-m-d H:i:s');
            return [
                'title' => $title,
                'slug' => $slug,
                'excerpt' => 'Laravel is awesome for testing. ' . fake()->sentence(6),
                'content' => $laravelContent . ' ' . fake()->paragraphs(3, true),
                'image' => $imagePath,
                'image_caption' => fake()->sentence(6),
                'is_featured' => $isFeatured,
                'status' => $status,
                'is_published' => $is_published,
                'published_at' => $publishedAt,
                'updated_at' => $updatedAt,
                'created_at' => $createdAt,
                'view_count' => $viewCount,
                'reading_time' => fake()->numberBetween(1, 10),
            ];
        }
        // Guarantee at least 3 featured and 3 non-featured in first 10
        $isFeatured = fake()->boolean(20);
        if (self::$featuredCount < 3 && (self::$featuredCount + self::$nonFeaturedCount) < 10) {
            $isFeatured = true;
            self::$featuredCount++;
        } elseif (self::$nonFeaturedCount < 3 && (self::$featuredCount + self::$nonFeaturedCount) < 10) {
            $isFeatured = false;
            self::$nonFeaturedCount++;
        }
        return [
            'title' => $title,
            'slug' => $slug,
            'excerpt' => fake()->paragraph(2),
            'content' => fake()->paragraphs(5, true),
            'image' => $imagePath,
            'image_caption' => fake()->sentence(6),
            'is_featured' => $isFeatured,
            'status' => $status,
            'is_published' => $is_published,
            'published_at' => $publishedAt,
            'updated_at' => $updatedAt,
            'created_at' => $createdAt,
            'view_count' => $viewCount,
            'reading_time' => fake()->numberBetween(1, 10),
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
        $now = now();
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'status' => 'published',
            'is_published' => true,
            'published_at' => $now->subDays(rand(1, 365))->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Indicate that the article is published.
     */
    public function published(): static
    {
        $now = now();
        return $this->state(fn (array $attributes) => [
            'is_featured' => false,
            'status' => 'published',
            'is_published' => true,
            'published_at' => $now->subDays(rand(1, 365))->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Indicate that the article is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => false,
            'status' => 'draft',
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
