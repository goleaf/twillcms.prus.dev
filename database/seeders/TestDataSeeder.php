<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Seed the application's database with test data.
     */
    public function run(): void
    {
        // Create test categories if they don't exist
        $techCategory = Category::firstOrCreate(
            ['slug' => 'technology'],
            ['title' => 'Technology', 'description' => 'Latest technology news', 'published' => true, 'position' => 1]
        );

        $designCategory = Category::firstOrCreate(
            ['slug' => 'design'],
            ['title' => 'Design', 'description' => 'Creative design inspiration', 'published' => true, 'position' => 2]
        );

        // Create test posts with specific slugs
        $testPost = Post::firstOrCreate(
            ['slug' => 'test-blog-post'],
            [
                'title' => 'Test Blog Post',
                'description' => 'This is a test blog post for API testing.',
                'content' => '<p>This is the content of the test blog post.</p>',
                'published' => true,
                'published_at' => now(),
                'position' => 1,
                'created_at' => now()->subDays(10),
                'updated_at' => now(),
            ]
        );

        // Attach categories to the test post
        $testPost->categories()->sync([$techCategory->id, $designCategory->id]);

        // Create additional test posts for other scenarios
        $seoPost = Post::firstOrCreate(
            ['slug' => 'seo-test-post'],
            [
                'title' => 'SEO Test Post',
                'description' => 'This is a test post for SEO meta tags.',
                'content' => '<p>Content for SEO testing.</p>',
                'published' => true,
                'published_at' => now(),
                'position' => 2,
                'created_at' => now()->subDays(5),
                'updated_at' => now(),
            ]
        );

        $seoPost->categories()->sync([$techCategory->id]);

        echo "✅ Test data seeded successfully!\n";
        echo "- Created or updated post: Test Blog Post (slug: test-blog-post)\n";
        echo "- Created or updated post: SEO Test Post (slug: seo-test-post)\n";
        echo "- Created or updated categories: Technology and Design\n";
    }
}
