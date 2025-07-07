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
    protected $model = Tag::class;

    protected $tags = [
        'World News', 'Politics', 'Technology', 'Science', 'Health',
        'Business', 'Entertainment', 'Sports', 'Environment', 'Education',
        'Arts & Culture', 'Lifestyle', 'Opinion', 'Travel', 'Food',
        'Fashion', 'Automotive', 'Real Estate', 'Innovation', 'Social Media',
        'Gaming', 'Music', 'Movies', 'Books', 'Television',
        'Cryptocurrency', 'AI & ML', 'Climate Change', 'Space Exploration', 'Cybersecurity'
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement($this->tags);
        
        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
