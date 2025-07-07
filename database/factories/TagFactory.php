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
        $name = fake()->unique()->randomElement([
            'Technology', 'Business', 'Science', 'Health', 'Sports', 'Entertainment',
            'Politics', 'World News', 'Education', 'Travel', 'Food', 'Fashion',
            'Art', 'Music', 'Movies', 'Books', 'Gaming', 'Photography',
            'Environment', 'Climate', 'Energy', 'Transportation', 'Space',
            'Medicine', 'Psychology', 'Economics', 'Finance', 'Marketing',
            'Design', 'Architecture', 'History', 'Culture', 'Society',
            'Innovation', 'Startups', 'Entrepreneurship', 'Leadership',
            'Productivity', 'Lifestyle', 'Wellness', 'Fitness', 'Nutrition',
            'Relationships', 'Parenting', 'Career', 'Personal Development',
            'Artificial Intelligence', 'Machine Learning', 'Blockchain',
            'Cryptocurrency', 'Cybersecurity', 'Data Science', 'Programming',
            'Web Development', 'Mobile Apps', 'Cloud Computing', 'IoT',
            'Renewable Energy', 'Sustainability', 'Green Technology',
            'Social Media', 'Digital Marketing', 'Content Creation',
            'Journalism', 'Communication', 'Public Relations', 'Branding',
            'E-commerce', 'Retail', 'Manufacturing', 'Agriculture',
            'Real Estate', 'Construction', 'Automotive', 'Aviation',
            'Healthcare', 'Biotechnology', 'Pharmaceuticals', 'Telemedicine',
            'Mental Health', 'Mindfulness', 'Meditation', 'Yoga',
            'Adventure', 'Outdoor Activities', 'Hiking', 'Camping',
            'Cooking', 'Recipes', 'Restaurants', 'Wine', 'Coffee',
            'Pets', 'Animals', 'Nature', 'Wildlife', 'Conservation',
            'DIY', 'Crafts', 'Home Improvement', 'Gardening', 'Interior Design',
            'Gadgets', 'Reviews', 'Tutorials', 'How-to', 'Tips',
            'News Analysis', 'Opinion', 'Editorial', 'Interview', 'Profile',
            'Breaking News', 'Local News', 'International', 'Global',
            'Emerging Markets', 'Developing Countries', 'Urban Planning',
            'Smart Cities', 'Future Trends', 'Predictions', 'Research'
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(rand(1, 3)),
            'color' => fake()->hexColor(),
            'is_featured' => fake()->boolean(20), // 20% chance of being featured
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
        $tagName = $tags[array_rand($tags)];

        return $this->state(fn (array $attributes) => [
            'name' => $tagName,
            'slug' => Str::slug($tagName),
        ]);
    }
}
