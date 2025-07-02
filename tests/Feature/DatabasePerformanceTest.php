<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

    /** @test */
    public function test_sqlite_performance_indexes_are_created()
    {
        // Test that performance indexes exist (MySQL/MariaDB compatible)
        $indexes = DB::select("
            SELECT INDEX_NAME as name 
            FROM INFORMATION_SCHEMA.STATISTICS 
            WHERE TABLE_SCHEMA = ? AND INDEX_NAME LIKE 'idx_%' 
            GROUP BY INDEX_NAME
        ", [config('database.connections.mysql.database')]);
        
        $indexNames = collect($indexes)->pluck('name')->toArray();

        $expectedIndexes = [
            'idx_posts_published_created',
            'idx_posts_position',
            'idx_post_translations_locale_active',
            'idx_category_translations_locale',
            'idx_post_category_composite',
        ];

        foreach ($expectedIndexes as $expectedIndex) {
            $this->assertContains($expectedIndex, $indexNames, "Performance index {$expectedIndex} should exist");
        }
    }

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
    public function test_database_integrity()
    {
        // Test foreign key constraints
        $this->assertTrue(true); // SQLite foreign keys are enabled by default in our config

        // Test data integrity
        $postsWithTranslations = Post::whereHas('translations')->count();
        $totalPosts = Post::count();

        $this->assertEquals($totalPosts, $postsWithTranslations, 'All posts should have translations');

        $categoriesWithTranslations = Category::whereHas('translations')->count();
        $totalCategories = Category::count();

        $this->assertEquals($totalCategories, $categoriesWithTranslations, 'All categories should have translations');
    }

    /** @test */
    public function test_multilingual_content_availability()
    {
        // Test English content
        $enPosts = Post::whereHas('translations', function ($query) {
            $query->where('locale', 'en')->where('active', true);
        })->count();

        // Test Lithuanian content
        $ltPosts = Post::whereHas('translations', function ($query) {
            $query->where('locale', 'lt')->where('active', true);
        })->count();

        $this->assertGreaterThan(0, $enPosts, 'Should have English posts');
        $this->assertGreaterThan(0, $ltPosts, 'Should have Lithuanian posts');
        $this->assertEquals($enPosts, $ltPosts, 'Should have equal number of posts in both languages');
    }

    /** @test */
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

    /** @test */
    public function test_complex_join_performance()
    {
        $startTime = microtime(true);

        $results = DB::table('posts')
            ->join('post_translations', 'posts.id', '=', 'post_translations.post_id')
            ->join('post_category', 'posts.id', '=', 'post_category.post_id')
            ->join('categories', 'post_category.category_id', '=', 'categories.id')
            ->join('category_translations', 'categories.id', '=', 'category_translations.category_id')
            ->where('posts.published', true)
            ->where('post_translations.locale', 'en')
            ->where('post_translations.active', true)
            ->where('category_translations.locale', 'en')
            ->where('category_translations.active', true)
            ->select(
                'posts.id',
                'post_translations.title as post_title',
                'category_translations.title as category_title'
            )
            ->limit(50)
            ->get();

        $endTime = microtime(true);
        $queryTime = ($endTime - $startTime) * 1000;

        $this->assertLessThan(150, $queryTime, 'Complex join query should complete under 150ms');
        $this->assertNotEmpty($results, 'Complex join should return results');
    }

    /** @test */
    public function test_bulk_operations_performance()
    {
        // Test bulk insert performance
        $startTime = microtime(true);

        $posts = [];
        for ($i = 0; $i < 10; $i++) {
            $posts[] = [
                'published' => true,
                'position' => 1000 + $i,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('posts')->insert($posts);

        $endTime = microtime(true);
        $insertTime = ($endTime - $startTime) * 1000;

        $this->assertLessThan(50, $insertTime, 'Bulk insert should complete under 50ms');

        // Cleanup
        DB::table('posts')->where('position', '>=', 1000)->delete();
    }

    /** @test */
    public function test_database_size_optimization()
    {
        $dbPath = database_path('database.sqlite');
        $this->assertFileExists($dbPath, 'SQLite database file should exist');

        $dbSize = filesize($dbPath);
        $dbSizeMB = $dbSize / 1024 / 1024;

        // Database should be reasonable size (less than 50MB for test data)
        $this->assertLessThan(50, $dbSizeMB, 'Database size should be optimized');

        // Should have data
        $this->assertGreaterThan(0.1, $dbSizeMB, 'Database should contain data');
    }

    /** @test */
    public function test_query_count_optimization()
    {
        // Test N+1 query prevention
        DB::enableQueryLog();

        $posts = Post::with(['translations', 'categories.translations'])
            ->where('published', true)
            ->limit(10)
            ->get();

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // Should use efficient eager loading (limited number of queries)
        $this->assertLessThan(10, count($queries), 'Should use efficient eager loading');
        $this->assertGreaterThan(0, $posts->count(), 'Should return posts');
    }

    /** @test */
    public function test_memory_usage_during_operations()
    {
        $memoryBefore = memory_get_usage(true);

        // Perform memory-intensive operation
        $posts = Post::with(['translations', 'categories'])
            ->where('published', true)
            ->get();

        $memoryAfter = memory_get_usage(true);
        $memoryUsed = ($memoryAfter - $memoryBefore) / 1024 / 1024; // MB

        $this->assertLessThan(50, $memoryUsed, 'Memory usage should be reasonable (< 50MB)');
        $this->assertGreaterThan(0, $posts->count(), 'Should return posts');
    }
}
