<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Services\ContentGenerationService;
use App\Services\ImageGenerationService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EnhancedDatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    private $contentService;

    private $imageService;

    public function __construct()
    {
        $this->contentService = new ContentGenerationService;
        $this->imageService = new ImageGenerationService;
    }

    /**
     * Seed the application's database with enhanced performance
     */
    public function run(): void
    {
        echo "🚀 Starting enhanced database seeding with performance optimization...\n";

        // Disable foreign key checks for better performance
        DB::statement('PRAGMA foreign_keys=OFF');

        try {
            // Create storage directories
            $this->createStorageDirectories();

            // Create categories with optimized approach
            echo "📁 Creating enhanced categories...\n";
            $categories = $this->createEnhancedCategories();

            // Create posts in batches for performance
            echo "📝 Creating enhanced blog posts with images...\n";
            $this->createEnhancedPosts($categories);

            // Re-enable foreign keys
            DB::statement('PRAGMA foreign_keys=ON');

            echo "✅ Enhanced database seeding completed successfully!\n";
            $this->displayStatistics();

        } catch (\Exception $e) {
            DB::statement('PRAGMA foreign_keys=ON');
            echo '❌ Seeding failed: '.$e->getMessage()."\n";
            throw $e;
        }
    }

    /**
     * Create storage directories for images
     */
    private function createStorageDirectories(): void
    {
        $directories = [
            'public/images',
            'public/images/posts',
            'public/images/categories',
            'public/images/thumbnails',
        ];

        foreach ($directories as $dir) {
            if (! Storage::exists($dir)) {
                Storage::makeDirectory($dir);
            }
        }
    }

    /**
     * Create enhanced categories with realistic content
     */
    private function createEnhancedCategories(): array
    {
        $categoryData = [
            'technology' => [
                'en' => ['title' => 'Technology', 'description' => 'Latest technology news, reviews, and innovative solutions'],
                'lt' => ['title' => 'Technologijos', 'description' => 'Naujausios technologijų naujienos, apžvalgos ir novatoriški sprendimai'],
            ],
            'design' => [
                'en' => ['title' => 'Design', 'description' => 'Creative design inspiration, tutorials, and industry insights'],
                'lt' => ['title' => 'Dizainas', 'description' => 'Kūrybinio dizaino įkvėpimas, mokymai ir pramonės įžvalgos'],
            ],
            'lifestyle' => [
                'en' => ['title' => 'Lifestyle', 'description' => 'Tips and insights for better living, wellness, and personal growth'],
                'lt' => ['title' => 'Gyvenimo būdas', 'description' => 'Patarimai geresniam gyvenimui, sveikatai ir asmeniniam tobulėjimui'],
            ],
            'business' => [
                'en' => ['title' => 'Business', 'description' => 'Entrepreneurship, marketing strategies, and business innovation'],
                'lt' => ['title' => 'Verslas', 'description' => 'Verslininkystė, rinkodaros strategijos ir verslo inovacijos'],
            ],
            'travel' => [
                'en' => ['title' => 'Travel', 'description' => 'Travel guides, destination reviews, and adventure stories'],
                'lt' => ['title' => 'Kelionės', 'description' => 'Kelionių vadovai, vietų apžvalgos ir nuotykių istorijos'],
            ],
            'food' => [
                'en' => ['title' => 'Food & Cooking', 'description' => 'Delicious recipes, cooking techniques, and culinary adventures'],
                'lt' => ['title' => 'Maistas ir gaminimas', 'description' => 'Skanūs receptai, gaminimo metodai ir kulinariniai nuotykiai'],
            ],
            'health' => [
                'en' => ['title' => 'Health & Fitness', 'description' => 'Workout routines, nutrition advice, and wellness strategies'],
                'lt' => ['title' => 'Sveikata ir sportas', 'description' => 'Treniruočių programos, mitybos patarimai ir sveikatos strategijos'],
            ],
            'photography' => [
                'en' => ['title' => 'Photography', 'description' => 'Photography techniques, equipment reviews, and creative inspiration'],
                'lt' => ['title' => 'Fotografija', 'description' => 'Fotografavimo metodai, įrangos apžvalgos ir kūrybinis įkvėpimas'],
            ],
        ];

        $categories = [];
        $position = 1;

        // Use transaction for better performance
        DB::transaction(function () use ($categoryData, &$categories, &$position) {
            foreach ($categoryData as $key => $data) {
                echo "Creating category: {$data['en']['title']}...\n";

                $category = Category::create([
                    'published' => true,
                    'position' => $position++,
                ]);

                // Create translations in batch
                $translations = [];
                foreach (['en', 'lt'] as $locale) {
                    $translations[] = [
                        'category_id' => $category->id,
                        'locale' => $locale,
                        'active' => true,
                        'title' => $data[$locale]['title'],
                        'description' => $data[$locale]['description'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                DB::table('category_translations')->insert($translations);

                // Generate category image
                $this->imageService->generateCategoryImage($category, $data['en']['title']);

                $categories[$key] = $category;
            }
        });

        return $categories;
    }

    /**
     * Create enhanced posts with batch processing
     */
    private function createEnhancedPosts(array $categories): void
    {
        $totalPosts = 150; // Increased for better testing
        $batchSize = 25; // Process in batches for memory efficiency
        $batches = ceil($totalPosts / $batchSize);

        for ($batch = 0; $batch < $batches; $batch++) {
            $startIndex = $batch * $batchSize;
            $endIndex = min($startIndex + $batchSize, $totalPosts);
            $batchCount = $endIndex - $startIndex;

            echo 'Processing batch '.($batch + 1)."/{$batches} ({$batchCount} posts)...\n";

            $this->createPostBatch($categories, $startIndex, $batchCount);

            // Force garbage collection to manage memory
            if (function_exists('gc_collect_cycles')) {
                gc_collect_cycles();
            }
        }
    }

    /**
     * Create a batch of posts efficiently
     */
    private function createPostBatch(array $categories, int $startIndex, int $count): void
    {
        DB::transaction(function () use ($categories, $startIndex, $count) {
            for ($i = 0; $i < $count; $i++) {
                $position = $startIndex + $i + 1;

                // Select random category for content generation
                $categoryKeys = array_keys($categories);
                $categoryKey = $categoryKeys[array_rand($categoryKeys)];
                $category = $categories[$categoryKey];

                // Create post
                $post = Post::create([
                    'published' => rand(1, 100) <= 85, // 85% published
                    'position' => $position,
                    'created_at' => now()->subDays(rand(0, 730)), // Random date within 2 years
                ]);

                // Generate content using service
                $enContent = $this->contentService->generateEnglishContent($categoryKey);
                $ltContent = $this->contentService->generateLithuanianContent($categoryKey);

                // Create translations
                $translations = [
                    [
                        'post_id' => $post->id,
                        'locale' => 'en',
                        'active' => true,
                        'title' => $enContent['title'],
                        'description' => $enContent['description'],
                        'content' => $enContent['content'],
                        'created_at' => $post->created_at,
                        'updated_at' => $post->created_at,
                    ],
                    [
                        'post_id' => $post->id,
                        'locale' => 'lt',
                        'active' => true,
                        'title' => $ltContent['title'],
                        'description' => $ltContent['description'],
                        'content' => $ltContent['content'],
                        'created_at' => $post->created_at,
                        'updated_at' => $post->created_at,
                    ],
                ];

                DB::table('post_translations')->insert($translations);

                // Assign random categories (1-3 per post)
                $postCategories = array_rand($categories, rand(1, min(3, count($categories))));
                if (! is_array($postCategories)) {
                    $postCategories = [$postCategories];
                }

                $categoryIds = [];
                foreach ($postCategories as $categoryIndex) {
                    $categoryKey = $categoryKeys[$categoryIndex];
                    $categoryIds[] = $categories[$categoryKey]->id;
                }

                $post->categories()->attach($categoryIds);

                // Generate post image
                $this->imageService->generatePostImage($post, $enContent['title'], $categoryKey);

                // Update post timestamp for realistic distribution
                $post->update([
                    'updated_at' => now()->subDays(rand(0, 30)),
                ]);

                if (($position % 10) === 0) {
                    echo "  Created {$position} posts...\r";
                }
            }
        });
    }

    /**
     * Display seeding statistics
     */
    private function displayStatistics(): void
    {
        $stats = [
            'Categories' => Category::count(),
            'Posts' => Post::count(),
            'Published Posts' => Post::where('published', true)->count(),
            'Category Translations' => DB::table('category_translations')->count(),
            'Post Translations' => DB::table('post_translations')->count(),
            'Category Relationships' => DB::table('post_category')->count(),
        ];

        echo "\n📊 **SEEDING STATISTICS**\n";
        echo "========================\n";

        foreach ($stats as $label => $count) {
            echo sprintf("%-25s: %d\n", $label, $count);
        }

        echo "\n🎯 **PERFORMANCE METRICS**\n";
        echo "==========================\n";

        // Check index usage
        $indexes = DB::select("SELECT name FROM sqlite_master WHERE type='index' AND name LIKE 'idx_%'");
        echo sprintf("%-25s: %d\n", 'Performance Indexes', count($indexes));

        // Database size
        $dbPath = database_path('database.sqlite');
        $dbSize = file_exists($dbPath) ? filesize($dbPath) : 0;
        echo sprintf("%-25s: %.2f MB\n", 'Database Size', $dbSize / 1024 / 1024);

        // Storage directory info
        $imageCount = 0;
        if (Storage::exists('public/images/posts')) {
            $imageCount += count(Storage::files('public/images/posts'));
        }
        if (Storage::exists('public/images/categories')) {
            $imageCount += count(Storage::files('public/images/categories'));
        }
        echo sprintf("%-25s: %d\n", 'Generated Images', $imageCount);

        echo "\n✅ All systems optimized and ready!\n";
    }
}
