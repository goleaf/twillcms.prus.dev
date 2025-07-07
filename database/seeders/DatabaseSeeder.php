<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Disable foreign key checks for faster seeding
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing data
        DB::table('article_tag')->truncate();
        Article::truncate();
        Tag::truncate();

        $this->command->info('Creating tags...');
        
        // Create 50 diverse tags
        $tags = collect([
            // Technology
            ['name' => 'Technology', 'color' => '#3B82F6', 'is_featured' => true],
            ['name' => 'Artificial Intelligence', 'color' => '#8B5CF6', 'is_featured' => true],
            ['name' => 'Programming', 'color' => '#10B981', 'is_featured' => false],
            ['name' => 'Web Development', 'color' => '#F59E0B', 'is_featured' => false],
            ['name' => 'Mobile Apps', 'color' => '#EF4444', 'is_featured' => false],
            ['name' => 'Cybersecurity', 'color' => '#DC2626', 'is_featured' => true],
            ['name' => 'Data Science', 'color' => '#7C3AED', 'is_featured' => false],
            ['name' => 'Blockchain', 'color' => '#059669', 'is_featured' => false],
            ['name' => 'Cloud Computing', 'color' => '#0EA5E9', 'is_featured' => false],
            ['name' => 'IoT', 'color' => '#84CC16', 'is_featured' => false],
            
            // Business
            ['name' => 'Business', 'color' => '#1F2937', 'is_featured' => true],
            ['name' => 'Entrepreneurship', 'color' => '#374151', 'is_featured' => true],
            ['name' => 'Marketing', 'color' => '#F97316', 'is_featured' => false],
            ['name' => 'Finance', 'color' => '#065F46', 'is_featured' => false],
            ['name' => 'Investment', 'color' => '#047857', 'is_featured' => false],
            ['name' => 'Startups', 'color' => '#7C2D12', 'is_featured' => false],
            ['name' => 'Leadership', 'color' => '#92400E', 'is_featured' => false],
            ['name' => 'Economics', 'color' => '#451A03', 'is_featured' => false],
            
            // Science & Health
            ['name' => 'Science', 'color' => '#1E40AF', 'is_featured' => true],
            ['name' => 'Health', 'color' => '#DC2626', 'is_featured' => true],
            ['name' => 'Medicine', 'color' => '#B91C1C', 'is_featured' => false],
            ['name' => 'Research', 'color' => '#1D4ED8', 'is_featured' => false],
            ['name' => 'Climate Change', 'color' => '#059669', 'is_featured' => true],
            ['name' => 'Environment', 'color' => '#047857', 'is_featured' => false],
            ['name' => 'Space', 'color' => '#1E1B4B', 'is_featured' => false],
            ['name' => 'Psychology', 'color' => '#7C3AED', 'is_featured' => false],
            ['name' => 'Mental Health', 'color' => '#A21CAF', 'is_featured' => false],
            
            // Lifestyle
            ['name' => 'Lifestyle', 'color' => '#EC4899', 'is_featured' => true],
            ['name' => 'Travel', 'color' => '#06B6D4', 'is_featured' => false],
            ['name' => 'Food', 'color' => '#F59E0B', 'is_featured' => false],
            ['name' => 'Fashion', 'color' => '#EC4899', 'is_featured' => false],
            ['name' => 'Fitness', 'color' => '#EF4444', 'is_featured' => false],
            ['name' => 'Nutrition', 'color' => '#84CC16', 'is_featured' => false],
            ['name' => 'Wellness', 'color' => '#10B981', 'is_featured' => false],
            
            // Entertainment & Culture
            ['name' => 'Entertainment', 'color' => '#F59E0B', 'is_featured' => true],
            ['name' => 'Sports', 'color' => '#EF4444', 'is_featured' => true],
            ['name' => 'Movies', 'color' => '#7C3AED', 'is_featured' => false],
            ['name' => 'Music', 'color' => '#EC4899', 'is_featured' => false],
            ['name' => 'Gaming', 'color' => '#8B5CF6', 'is_featured' => false],
            ['name' => 'Books', 'color' => '#92400E', 'is_featured' => false],
            ['name' => 'Art', 'color' => '#BE185D', 'is_featured' => false],
            ['name' => 'Photography', 'color' => '#1F2937', 'is_featured' => false],
            
            // News & Politics
            ['name' => 'News', 'color' => '#1F2937', 'is_featured' => true],
            ['name' => 'Politics', 'color' => '#374151', 'is_featured' => false],
            ['name' => 'World News', 'color' => '#4B5563', 'is_featured' => false],
            ['name' => 'Local News', 'color' => '#6B7280', 'is_featured' => false],
            ['name' => 'Breaking News', 'color' => '#DC2626', 'is_featured' => false],
            
            // Education & Career
            ['name' => 'Education', 'color' => '#1D4ED8', 'is_featured' => false],
            ['name' => 'Career', 'color' => '#059669', 'is_featured' => false],
            ['name' => 'Personal Development', 'color' => '#7C3AED', 'is_featured' => false],
            ['name' => 'Productivity', 'color' => '#F59E0B', 'is_featured' => false],
        ])->map(function ($tag) {
            return Tag::create([
                'name' => $tag['name'],
                'slug' => \Illuminate\Support\Str::slug($tag['name']),
                'color' => $tag['color'],
                'is_featured' => $tag['is_featured'],
                'description' => "Articles related to {$tag['name']} and related topics.",
                'usage_count' => 0,
            ]);
        });

        $this->command->info('Created ' . $tags->count() . ' tags.');
        $this->command->info('Creating articles...');

        // Create 300 articles with varied content
        $articles = collect();
        
        // Create 50 featured articles
        for ($i = 1; $i <= 50; $i++) {
            $article = Article::factory()
                ->featured()
                ->published()
                ->popular()
                ->create();
            $articles->push($article);
            
            if ($i % 10 === 0) {
                $this->command->info("Created {$i}/50 featured articles...");
            }
        }

        // Create 200 regular published articles
        for ($i = 1; $i <= 200; $i++) {
            $article = Article::factory()
                ->published()
                ->create();
            $articles->push($article);
            
            if ($i % 50 === 0) {
                $this->command->info("Created {$i}/200 regular articles...");
            }
        }

        // Create 30 recent articles
        for ($i = 1; $i <= 30; $i++) {
            $article = Article::factory()
                ->published()
                ->recent()
                ->create();
            $articles->push($article);
            
            if ($i % 10 === 0) {
                $this->command->info("Created {$i}/30 recent articles...");
            }
        }

        // Create 20 draft articles
        for ($i = 1; $i <= 20; $i++) {
            $article = Article::factory()
                ->draft()
                ->create();
            $articles->push($article);
            
            if ($i % 10 === 0) {
                $this->command->info("Created {$i}/20 draft articles...");
            }
        }

        $this->command->info('Assigning tags to articles...');

        // Assign random tags to articles
        $articles->each(function ($article, $index) use ($tags) {
            // Each article gets 1-5 random tags
            $randomTags = $tags->random(rand(1, 5));
            $article->tags()->attach($randomTags->pluck('id'));
            
            // Update tag usage counts
            $randomTags->each(function ($tag) {
                $tag->increment('usage_count');
            });
            
            if (($index + 1) % 50 === 0) {
                $this->command->info("Assigned tags to " . ($index + 1) . "/300 articles...");
            }
        });

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Database seeding completed successfully!');
        $this->command->info('Created:');
        $this->command->info('- ' . $tags->count() . ' tags');
        $this->command->info('- ' . $articles->count() . ' articles');
        $this->command->info('- 50 featured articles');
        $this->command->info('- 200 regular articles');
        $this->command->info('- 30 recent articles');
        $this->command->info('- 20 draft articles');
    }
}
