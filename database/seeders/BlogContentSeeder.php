<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Seeder;

class BlogContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Categories with correct database structure
        $categories = [
            [
                'slug' => 'technology',
                'published' => true,
                'position' => 1,
                'color_code' => '#3B82F6',
                'icon' => 'tech',
                'sort_order' => 1,
            ],
            [
                'slug' => 'business',
                'published' => true,
                'position' => 2,
                'color_code' => '#10B981',
                'icon' => 'business',
                'sort_order' => 2,
            ],
            [
                'slug' => 'design',
                'published' => true,
                'position' => 3,
                'color_code' => '#8B5CF6',
                'icon' => 'design',
                'sort_order' => 3,
            ],
            [
                'slug' => 'lifestyle',
                'published' => true,
                'position' => 4,
                'color_code' => '#F59E0B',
                'icon' => 'lifestyle',
                'sort_order' => 4,
            ],
            [
                'slug' => 'health-fitness',
                'published' => true,
                'position' => 5,
                'color_code' => '#EF4444',
                'icon' => 'health',
                'sort_order' => 5,
            ],
            [
                'slug' => 'food-cooking',
                'published' => true,
                'position' => 6,
                'color_code' => '#F97316',
                'icon' => 'food',
                'sort_order' => 6,
            ],
        ];

        // Create categories and their translations
        $categoryTranslations = [
            'technology' => [
                'title' => 'Technology',
                'description' => 'Latest technology trends and innovations',
            ],
            'business' => [
                'title' => 'Business',
                'description' => 'Business insights and entrepreneurship',
            ],
            'design' => [
                'title' => 'Design',
                'description' => 'Design principles and creative inspiration',
            ],
            'lifestyle' => [
                'title' => 'Lifestyle',
                'description' => 'Lifestyle tips and personal development',
            ],
            'health-fitness' => [
                'title' => 'Health & Fitness',
                'description' => 'Health tips and fitness guides',
            ],
            'food-cooking' => [
                'title' => 'Food & Cooking',
                'description' => 'Recipes and culinary experiences',
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::create($categoryData);

            // Create translation for the category
            $translation = $categoryTranslations[$categoryData['slug']];
            $category->translations()->create([
                'locale' => 'en',
                'active' => true,
                'title' => $translation['title'],
                'description' => $translation['description'],
            ]);

            // Create slug record
            $category->slugs()->create([
                'locale' => 'en',
                'active' => true,
                'slug' => $categoryData['slug'],
            ]);
        }

        // Create Sample Posts with correct database structure
        $postsData = [
            [
                'slug' => 'getting-started-with-laravel-11',
                'published' => true,
                'published_at' => now()->subDays(1),
                'position' => 1,
                'priority' => 100,
                'category_slug' => 'technology',
                'translation' => [
                    'title' => 'Getting Started with Laravel 11',
                    'description' => 'Learn the basics of Laravel 11 and build your first web application',
                    'content' => '<p>Laravel 11 introduces many exciting features that make web development faster and more enjoyable. In this comprehensive guide, we\'ll explore the key improvements and learn how to build a modern web application.</p><h2>What\'s New in Laravel 11</h2><p>Laravel 11 brings several improvements including better performance, enhanced security features, and a more streamlined development experience.</p>',
                ],
            ],
            [
                'slug' => 'modern-vuejs-development-practices',
                'published' => true,
                'published_at' => now()->subDays(2),
                'position' => 2,
                'priority' => 90,
                'category_slug' => 'technology',
                'translation' => [
                    'title' => 'Modern Vue.js Development Practices',
                    'description' => 'Best practices for building scalable Vue.js applications',
                    'content' => '<p>Vue.js has evolved significantly, and with it, the best practices for building maintainable and scalable applications. This article covers the modern approaches to Vue.js development.</p><h2>Composition API</h2><p>The Composition API provides better logic reuse and more flexible code organization.</p>',
                ],
            ],
            [
                'slug' => 'building-successful-startup',
                'published' => true,
                'published_at' => now()->subDays(3),
                'position' => 3,
                'priority' => 80,
                'category_slug' => 'business',
                'translation' => [
                    'title' => 'Building a Successful Startup',
                    'description' => 'Essential tips for entrepreneurs starting their journey',
                    'content' => '<p>Starting a business is an exciting but challenging journey. Here are the key principles that successful entrepreneurs follow to build thriving companies.</p><h2>Market Research</h2><p>Understanding your target market is crucial for business success.</p>',
                ],
            ],
            [
                'slug' => 'design-principles-modern-web-apps',
                'published' => true,
                'published_at' => now()->subDays(4),
                'position' => 4,
                'priority' => 70,
                'category_slug' => 'design',
                'translation' => [
                    'title' => 'Design Principles for Modern Web Applications',
                    'description' => 'Create beautiful and functional user interfaces',
                    'content' => '<p>Good design is not just about aesthetics - it\'s about creating experiences that users love. Learn the fundamental principles that guide modern web design.</p><h2>User-Centered Design</h2><p>Always put your users first when making design decisions.</p>',
                ],
            ],
            [
                'slug' => 'work-life-balance-in-tech',
                'published' => true,
                'published_at' => now()->subDays(5),
                'position' => 5,
                'priority' => 60,
                'category_slug' => 'lifestyle',
                'translation' => [
                    'title' => 'Maintaining Work-Life Balance in Tech',
                    'description' => 'Tips for developers to maintain healthy work-life balance',
                    'content' => '<p>The tech industry can be demanding, but maintaining a healthy work-life balance is essential for long-term success and happiness.</p><h2>Setting Boundaries</h2><p>Learn to separate work time from personal time effectively.</p>',
                ],
            ],
            [
                'slug' => 'ten-minute-home-workout-routine',
                'published' => true,
                'published_at' => now()->subDays(6),
                'position' => 6,
                'priority' => 50,
                'category_slug' => 'health-fitness',
                'translation' => [
                    'title' => '10 Minute Home Workout Routine',
                    'description' => 'Quick and effective exercises you can do anywhere',
                    'content' => '<p>Stay fit with this simple 10-minute workout routine that requires no equipment and can be done at home.</p><h2>Warm-up</h2><p>Start with 2 minutes of light movement to prepare your body.</p>',
                ],
            ],
            [
                'slug' => 'easy-pasta-recipes-busy-weeknights',
                'published' => true,
                'published_at' => now()->subDays(7),
                'position' => 7,
                'priority' => 40,
                'category_slug' => 'food-cooking',
                'translation' => [
                    'title' => 'Easy Pasta Recipes for Busy Weeknights',
                    'description' => 'Delicious pasta dishes ready in 30 minutes or less',
                    'content' => '<p>When you\'re short on time but want a delicious meal, these pasta recipes are perfect. Each recipe takes 30 minutes or less to prepare.</p><h2>Spaghetti Aglio e Olio</h2><p>A classic Italian dish with just a few simple ingredients.</p>',
                ],
            ],
            [
                'slug' => 'future-of-artificial-intelligence',
                'published' => true,
                'published_at' => now()->subDays(8),
                'position' => 8,
                'priority' => 30,
                'category_slug' => 'technology',
                'translation' => [
                    'title' => 'The Future of Artificial Intelligence',
                    'description' => 'Exploring AI trends and their impact on society',
                    'content' => '<p>Artificial Intelligence is rapidly evolving and transforming industries. Let\'s explore what the future holds for AI technology.</p><h2>Machine Learning Advances</h2><p>Recent breakthroughs in machine learning are opening new possibilities.</p>',
                ],
            ],
            [
                'slug' => 'remote-team-management-best-practices',
                'published' => true,
                'published_at' => now()->subDays(9),
                'position' => 9,
                'priority' => 20,
                'category_slug' => 'business',
                'translation' => [
                    'title' => 'Remote Team Management Best Practices',
                    'description' => 'Lead distributed teams effectively',
                    'content' => '<p>Managing remote teams requires different strategies than traditional office management. Here are proven techniques for success.</p><h2>Communication</h2><p>Clear and frequent communication is the foundation of remote team success.</p>',
                ],
            ],
            [
                'slug' => 'typography-in-web-design',
                'published' => true,
                'published_at' => now()->subDays(10),
                'position' => 10,
                'priority' => 10,
                'category_slug' => 'design',
                'translation' => [
                    'title' => 'Typography in Web Design',
                    'description' => 'Choose the right fonts for better user experience',
                    'content' => '<p>Typography plays a crucial role in web design, affecting both readability and user experience. Learn how to choose and use fonts effectively.</p><h2>Font Selection</h2><p>The right font can make or break your design.</p>',
                ],
            ],
        ];

        foreach ($postsData as $postData) {
            // Create the post
            $post = Post::create([
                'slug' => $postData['slug'],
                'published' => $postData['published'],
                'published_at' => $postData['published_at'],
                'position' => $postData['position'],
                'priority' => $postData['priority'],
            ]);

            // Create translation for the post
            $post->translations()->create([
                'locale' => 'en',
                'active' => true,
                'title' => $postData['translation']['title'],
                'description' => $postData['translation']['description'],
                'content' => $postData['translation']['content'],
            ]);

            // Create slug record
            $post->slugs()->create([
                'locale' => 'en',
                'active' => true,
                'slug' => $postData['slug'],
            ]);

            // Associate post with category through pivot table
            $category = Category::where('slug', $postData['category_slug'])->first();
            if ($category) {
                $post->categories()->attach($category->id);
            }
        }

        $this->command->info('✅ Created '.count($categories).' categories');
        $this->command->info('✅ Created '.count($postsData).' blog posts');
        $this->command->info('🎉 Blog content seeding completed successfully!');
    }
}
