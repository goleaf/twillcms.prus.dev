<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Article::truncate();
        Tag::truncate();
        DB::table('article_tag')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Creating tags...');
        
        // Create comprehensive tag categories for news portal
        $tagCategories = [
            'Technology' => ['#3B82F6', 15],
            'Business' => ['#EF4444', 12], 
            'Politics' => ['#10B981', 10],
            'Health' => ['#F59E0B', 8],
            'Sports' => ['#8B5CF6', 12],
            'Entertainment' => ['#EC4899', 10],
            'Science' => ['#14B8A6', 8],
            'World News' => ['#F97316', 15],
            'Economy' => ['#6366F1', 8],
            'Environment' => ['#84CC16', 8],
            'Education' => ['#06B6D4', 6],
            'Food' => ['#F43F5E', 6],
            'Travel' => ['#8B5A2B', 6],
            'Lifestyle' => ['#374151', 8],
            'Art' => ['#7C2D12', 5],
        ];

        $allTags = collect();

        foreach ($tagCategories as $category => $data) {
            [$color, $count] = $data;
            
            // Create main category tag
            $mainTag = Tag::create([
                'name' => $category,
                'slug' => \Illuminate\Support\Str::slug($category),
                'color' => $color,
                'description' => "Articles related to {$category}",
                'is_featured' => in_array($category, ['Technology', 'Business', 'Politics', 'World News', 'Sports']),
                'usage_count' => 0,
            ]);
            
            $allTags->push($mainTag);
            
            // Create subcategory tags
            $subcategories = $this->getSubcategories($category);
            
            foreach (array_slice($subcategories, 0, $count - 1) as $subcategory) {
                $subTag = Tag::create([
                    'name' => $subcategory,
                    'slug' => \Illuminate\Support\Str::slug($subcategory),
                    'color' => $this->adjustColor($color),
                    'description' => "News and articles about {$subcategory}",
                    'is_featured' => false,
                    'usage_count' => 0,
                ]);
                
                $allTags->push($subTag);
            }
        }

        $this->command->info("Created {$allTags->count()} tags");
        $this->command->info('Creating 300 articles with random content and images...');

        // Create articles in batches for better performance
        $batchSize = 50;
        $totalArticles = 300;
        $batches = ceil($totalArticles / $batchSize);

        for ($batch = 0; $batch < $batches; $batch++) {
            $articlesInBatch = min($batchSize, $totalArticles - ($batch * $batchSize));
            
            $this->command->info("Creating batch " . ($batch + 1) . " of {$batches} ({$articlesInBatch} articles)");
            
            $articles = Article::factory()
                ->count($articlesInBatch)
                ->create();

            // Attach random tags to each article
            foreach ($articles as $article) {
                $randomTags = $allTags->random(rand(1, 5));
                $article->tags()->attach($randomTags->pluck('id'));
                
                // Update article with better content
                $article->update([
                    'content' => $this->generateRichContent($article->title, $randomTags),
                    'image' => $this->generateRandomImageUrl(),
                    'reading_time' => Article::calculateReadingTime($article->content),
                ]);
            }
        }

        // Update tag usage counts
        $this->command->info('Updating tag usage counts...');
        foreach ($allTags as $tag) {
            $tag->updateUsageCount();
        }

        // Create some recent popular articles
        $this->command->info('Creating recent popular articles...');
        Article::factory()
            ->count(20)
            ->recent()
            ->popular()
            ->create()
            ->each(function ($article) use ($allTags) {
                $randomTags = $allTags->random(rand(2, 4));
                $article->tags()->attach($randomTags->pluck('id'));
                $article->update([
                    'content' => $this->generateRichContent($article->title, $randomTags),
                    'image' => $this->generateRandomImageUrl(),
                ]);
            });

        // Create featured articles
        $this->command->info('Creating featured articles...');
        Article::factory()
            ->count(10)
            ->featured()
            ->create()
            ->each(function ($article) use ($allTags) {
                $randomTags = $allTags->random(rand(2, 3));
                $article->tags()->attach($randomTags->pluck('id'));
                $article->update([
                    'content' => $this->generateRichContent($article->title, $randomTags),
                    'image' => $this->generateRandomImageUrl(),
                ]);
            });

        // Update all tag usage counts again
        foreach ($allTags as $tag) {
            $tag->updateUsageCount();
        }

        $totalArticles = Article::count();
        $publishedArticles = Article::published()->count();
        $featuredArticles = Article::featured()->count();
        $totalTags = Tag::count();

        $this->command->info('Database seeding completed!');
        $this->command->table(['Metric', 'Count'], [
            ['Total Articles', $totalArticles],
            ['Published Articles', $publishedArticles],
            ['Featured Articles', $featuredArticles],
            ['Total Tags', $totalTags],
        ]);
    }

    /**
     * Get subcategories for a main category
     */
    private function getSubcategories(string $category): array
    {
        $subcategories = [
            'Technology' => [
                'Artificial Intelligence', 'Machine Learning', 'Software Development', 'Cybersecurity',
                'Mobile Apps', 'Web Development', 'Cloud Computing', 'Data Science', 'Blockchain',
                'IoT', 'Robotics', 'Virtual Reality', 'Gaming Tech', 'Tech Startups', 'Social Media'
            ],
            'Business' => [
                'Entrepreneurship', 'Marketing', 'Finance', 'Investment', 'Real Estate',
                'E-commerce', 'Leadership', 'Management', 'Industry News', 'Mergers', 'IPOs'
            ],
            'Politics' => [
                'Elections', 'Government', 'Policy', 'International Relations', 'Democracy',
                'Law', 'Congress', 'Senate', 'Local Politics', 'Campaigns'
            ],
            'Health' => [
                'Mental Health', 'Fitness', 'Nutrition', 'Medical Research', 'Healthcare',
                'Wellness', 'Disease Prevention'
            ],
            'Sports' => [
                'Football', 'Basketball', 'Baseball', 'Soccer', 'Tennis', 'Olympics',
                'Hockey', 'Golf', 'Boxing', 'MMA', 'Athletics', 'Swimming'
            ],
            'Entertainment' => [
                'Movies', 'TV Shows', 'Music', 'Celebrities', 'Awards', 'Theater',
                'Streaming', 'Books', 'Comics', 'Festivals'
            ],
            'Science' => [
                'Space', 'Physics', 'Chemistry', 'Biology', 'Climate Research', 'Scientific Discovery',
                'Innovation', 'Laboratory Research'
            ],
            'World News' => [
                'International News', 'Asia Pacific', 'Europe Today', 'Americas Report', 'Africa News', 'Middle East',
                'Breaking News', 'Global Events', 'Diplomacy', 'International Trade', 'Global Conflict', 
                'Peace Treaties', 'Human Rights', 'Immigration', 'Natural Disasters'
            ],
            'Economy' => [
                'Stock Markets', 'Banking', 'Currency Exchange', 'Economic Policy', 'Employment', 'GDP Growth', 'Inflation Rate', 'Economic Forecast'
            ],
            'Environment' => [
                'Climate Change', 'Sustainability', 'Renewable Energy', 'Conservation',
                'Pollution Control', 'Wildlife Protection', 'Green Technology', 'Recycling'
            ],
            'Education' => [
                'Schools', 'Universities', 'Online Learning', 'Student Life', 'Educational Research', 'Teaching Methods'
            ],
            'Food' => [
                'Restaurants', 'Recipes', 'Cooking', 'Food Nutrition', 'Food Safety', 'Dining Experience'
            ],
            'Travel' => [
                'Destinations', 'Tourism', 'Adventure Travel', 'Culture Tours', 'Hotels', 'Airlines'
            ],
            'Lifestyle' => [
                'Fashion', 'Beauty', 'Home Decor', 'Family Life', 'Relationships', 'Personal Growth',
                'Hobbies', 'DIY Projects'
            ],
            'Art' => [
                'Painting', 'Sculpture', 'Photography', 'Design', 'Museums'
            ],
        ];

        return $subcategories[$category] ?? [];
    }

    /**
     * Adjust color brightness
     */
    private function adjustColor(string $hexColor): string
    {
        $colors = [
            '#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6',
            '#EC4899', '#14B8A6', '#F97316', '#6366F1', '#84CC16',
            '#06B6D4', '#F43F5E', '#8B5A2B', '#374151', '#7C2D12',
            '#DC2626', '#7C3AED', '#059669', '#D97706', '#DB2777'
        ];

        return $colors[array_rand($colors)];
    }

    /**
     * Generate rich content for articles
     */
    private function generateRichContent(string $title, $tags): string
    {
        $tagNames = $tags->pluck('name')->toArray();
        $mainTag = $tagNames[0] ?? 'News';
        
        $intros = [
            "In today's rapidly evolving world of {$mainTag}, ",
            "Recent developments in {$mainTag} have shown that ",
            "As the {$mainTag} industry continues to grow, ",
            "Experts in {$mainTag} are increasingly focused on ",
            "The latest trends in {$mainTag} reveal that ",
        ];

        $intro = $intros[array_rand($intros)];
        
        $paragraphs = [
            $intro . fake()->paragraph(4),
            fake()->paragraph(6),
            fake()->paragraph(5),
            "According to industry experts, " . fake()->paragraph(4),
            fake()->paragraph(6),
            "Looking ahead, " . fake()->paragraph(3),
        ];

        // Add some tag-specific content
        if (count($tagNames) > 1) {
            $paragraphs[] = "This development also impacts " . implode(', ', array_slice($tagNames, 1)) . ". " . fake()->paragraph(4);
        }

        return implode("\n\n", $paragraphs);
    }

    /**
     * Generate random image URL
     */
    private function generateRandomImageUrl(): string
    {
        $width = rand(800, 1200);
        $height = rand(500, 800);
        $seed = rand(1, 10000);
        
        return "https://picsum.photos/{$width}/{$height}?random={$seed}";
    }
}
