<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Performance indexes for posts table
        DB::statement('CREATE INDEX idx_posts_published_created ON posts(published, created_at DESC)');
        DB::statement('CREATE INDEX idx_posts_position ON posts(position)');
        DB::statement('CREATE INDEX idx_posts_published_position ON posts(published, position)');

        // Performance indexes for categories table
        DB::statement('CREATE INDEX idx_categories_published_position ON categories(published, position)');

        // Performance indexes for post_category pivot table
        DB::statement('CREATE INDEX idx_post_category_post ON post_category(post_id)');
        DB::statement('CREATE INDEX idx_post_category_category ON post_category(category_id)');
        DB::statement('CREATE INDEX idx_post_category_composite ON post_category(post_id, category_id)');

        // Date-based indexes for archive functionality (MySQL compatible)
        // Note: Functional indexes not supported in older MariaDB versions
        DB::statement('CREATE INDEX idx_posts_created_at ON posts(created_at DESC)');
        DB::statement('CREATE INDEX idx_posts_updated_at ON posts(updated_at DESC)');

        // Performance indexes for twill-specific columns
        if (Schema::hasColumn('posts', 'publish_start_date')) {
            DB::statement('CREATE INDEX idx_posts_publish_dates ON posts(publish_start_date, publish_end_date)');
        }

        if (Schema::hasColumn('posts', 'featured')) {
            DB::statement('CREATE INDEX idx_posts_featured ON posts(featured, published)');
        }

        // Search indexes for direct content fields (single language)
        if (Schema::hasColumn('posts', 'title') && Schema::hasColumn('posts', 'description')) {
            DB::statement('CREATE INDEX idx_posts_search ON posts(title, description)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop performance indexes using MySQL syntax
        $indexes = [
            'posts' => [
                'idx_posts_published_created',
                'idx_posts_position', 
                'idx_posts_published_position',
                'idx_posts_created_at',
                'idx_posts_updated_at',
                'idx_posts_publish_dates',
                'idx_posts_featured',
                'idx_posts_search'
            ],
            'categories' => [
                'idx_categories_published_position'
            ],
            'post_category' => [
                'idx_post_category_post',
                'idx_post_category_category',
                'idx_post_category_composite'
            ]
        ];

        foreach ($indexes as $table => $tableIndexes) {
            foreach ($tableIndexes as $index) {
                try {
                    DB::statement("DROP INDEX {$index} ON {$table}");
                } catch (\Exception $e) {
                    // Index may not exist, continue
                }
            }
        }
    }
};
