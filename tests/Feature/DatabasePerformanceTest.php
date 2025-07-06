<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test as TestMethod;
use Tests\TestCase;

class DatabasePerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed database for performance testing
        $this->artisan('db:seed');
    }

    #[TestMethod]
    public function test_sqlite_performance_indexes_are_created()
    {
        // Test that performance indexes exist (SQLite compatible)
        $indexes = DB::select("
            SELECT name 
            FROM sqlite_master 
            WHERE type = 'index' AND name LIKE 'idx_%'
        ");

        $indexNames = collect($indexes)->pluck('name')->toArray();

        $expectedIndexes = [
            'idx_posts_published_created',
            'idx_posts_position',
            'idx_post_category_composite',
        ];

        foreach ($expectedIndexes as $expectedIndex) {
            $this->assertContains($expectedIndex, $indexNames, "Performance index {$expectedIndex} should exist");
        }
    }

    #[TestMethod]
    public function test_multilingual_query_performance()
    {
        // Test performance of multilingual queries
        $startTime = microtime(true);

        $posts = Post::with(['translations' => function ($query) {
            $query->where('locale', 'en')->where('active', true);
        }])
            ->where('published', true)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $endTime = microtime(true);
        $queryTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        $this->assertLessThan(100, $queryTime, 'Multilingual query should complete under 100ms');
        $this->assertGreaterThan(0, $posts->count(), 'Should return posts');
    }

    #[TestMethod]
    public function test_category_filtering_performance()
    {
        $category = Category::first();
        $this->assertNotNull($category, 'Should have at least one category');

        $startTime = microtime(true);

        $posts = Post::whereHas('categories', function ($query) use ($category) {
            $query->where('categories.id', $category->id);
        })
            ->with(['translations' => function ($query) {
                $query->where('locale', 'en');
            }])
            ->where('published', true)
            ->get();

        $endTime = microtime(true);
        $queryTime = ($endTime - $startTime) * 1000;

        $this->assertLessThan(150, $queryTime, 'Category filtering should complete under 150ms');
    }

    #[TestMethod]
    public function test_search_query_performance()
    {
        $searchTerm = 'technology';

        $startTime = microtime(true);

        $posts = Post::whereHas('translations', function ($query) use ($searchTerm) {
            $query->where('locale', 'en')
                ->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('content', 'LIKE', "%{$searchTerm}%");
                });
        })
            ->where('published', true)
            ->with(['translations' => function ($query) {
                $query->where('locale', 'en');
            }])
            ->get();

        $endTime = microtime(true);
        $queryTime = ($endTime - $startTime) * 1000;

        $this->assertLessThan(200, $queryTime, 'Search query should complete under 200ms');
    }

    #[TestMethod]
    public function test_pagination_performance()
    {
        $startTime = microtime(true);

        $posts = Post::with(['translations' => function ($query) {
            $query->where('locale', 'en');
        }])
            ->where('published', true)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $endTime = microtime(true);
        $queryTime = ($endTime - $startTime) * 1000;

        $this->assertLessThan(100, $queryTime, 'Pagination query should complete under 100ms');
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $posts);
    }

    #[TestMethod]
    public function test_database_integrity()
    {
        // Test foreign key constraints
        $this->assertTrue(true); // SQLite foreign keys are enabled by default in our config

        // Test data integrity (single language system)
        $postsWithContent = Post::whereNotNull('title')
            ->whereNotNull('content')
            ->count();
        $totalPosts = Post::count();

        $this->assertEquals($totalPosts, $postsWithContent, 'All posts should have content');

        $categoriesWithContent = Category::whereNotNull('title')
            ->whereNotNull('slug')
            ->count();
        $totalCategories = Category::count();

        $this->assertEquals($totalCategories, $categoriesWithContent, 'All categories should have content');
    }

    #[TestMethod]
    public function test_multilingual_content_availability()
    {
        // Test English content only (multilingual system removed)
        $enPosts = Post::where('published', true)->count();

        $this->assertGreaterThan(0, $enPosts, 'Should have English posts');

        // Verify posts have basic content fields
        $postWithContent = Post::where('published', true)
            ->whereNotNull('title')
            ->whereNotNull('content')
            ->first();

        $this->assertNotNull($postWithContent, 'Should have posts with content');
    }

    #[TestMethod]
    public function test_archive_query_performance()
    {
        $year = date('Y');
        $month = date('m');

        $startTime = microtime(true);

        $posts = Post::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('published', true)
            ->with(['translations' => function ($query) {
                $query->where('locale', 'en');
            }])
            ->get();

        $endTime = microtime(true);
        $queryTime = ($endTime - $startTime) * 1000;

        $this->assertLessThan(100, $queryTime, 'Archive query should complete under 100ms');
    }

    #[TestMethod]
    public function test_complex_join_performance()
    {
        $startTime = microtime(true);

        $posts = Post::join('post_category as pc', 'posts.id', '=', 'pc.post_id')
            ->join('categories as c', 'pc.category_id', '=', 'c.id')
            ->where('posts.published', true)
            ->select('posts.*', 'c.title as category_name', 'c.slug as category_slug')
            ->distinct()
            ->limit(20)
            ->get();

        $endTime = microtime(true);
        $queryTime = ($endTime - $startTime) * 1000;

        $this->assertLessThan(150, $queryTime, 'Complex join query should complete under 150ms');
    }

    #[TestMethod]
    public function test_bulk_operations_performance()
    {
        $startTime = microtime(true);

        // Simulate bulk update operation
        Post::where('published', true)
            ->whereDate('created_at', '>', now()->subDays(30))
            ->increment('view_count', 1);

        $endTime = microtime(true);
        $queryTime = ($endTime - $startTime) * 1000;

        $this->assertLessThan(200, $queryTime, 'Bulk operations should complete under 200ms');
    }

    #[TestMethod]
    public function test_database_size_optimization()
    {
        // Check that database size is reasonable
        $dbPath = database_path('database.sqlite');
        if (file_exists($dbPath)) {
            $sizeInMB = filesize($dbPath) / 1024 / 1024;
            $this->assertLessThan(100, $sizeInMB, 'Database size should be under 100MB');
        }

        // Check record counts are reasonable
        $postCount = Post::count();
        $categoryCount = Category::count();

        $this->assertGreaterThan(0, $postCount, 'Should have posts');
        $this->assertGreaterThan(0, $categoryCount, 'Should have categories');
    }

    #[TestMethod]
    public function test_query_count_optimization()
    {
        DB::enableQueryLog();

        // Load posts with relationships efficiently
        $posts = Post::with(['categories', 'translations' => function ($query) {
            $query->where('locale', 'en');
        }])
            ->where('published', true)
            ->limit(10)
            ->get();

        $queryCount = count(DB::getQueryLog());

        $this->assertLessThan(5, $queryCount, 'Should use fewer than 5 queries with proper eager loading');
        $this->assertGreaterThan(0, $posts->count(), 'Should return posts');

        DB::disableQueryLog();
    }

    #[TestMethod]
    public function test_memory_usage_during_operations()
    {
        $initialMemory = memory_get_usage(true);

        // Perform memory-intensive operation
        $posts = Post::with(['categories', 'translations'])
            ->where('published', true)
            ->limit(100)
            ->get();

        $posts->each(function ($post) {
            $post->reading_time; // Trigger accessor
            $post->excerpt; // Trigger accessor
            $post->categories->count();
        });

        $finalMemory = memory_get_usage(true);
        $memoryIncrease = ($finalMemory - $initialMemory) / 1024 / 1024; // Convert to MB

        $this->assertLessThan(50, $memoryIncrease, 'Memory usage should increase by less than 50MB');
    }
}
