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
        // Check if columns exist before creating indexes
        if (Schema::hasColumn('posts', 'status')) {
            DB::statement('CREATE INDEX idx_posts_status_created ON posts(status, created_at DESC)');
        }

        if (Schema::hasColumn('posts', 'published_at')) {
            DB::statement('CREATE INDEX idx_posts_published_at ON posts(published_at DESC)');
        }

        // Basic date indexes
        DB::statement('CREATE INDEX idx_posts_created_at ON posts(created_at DESC)');
        DB::statement('CREATE INDEX idx_posts_updated_at ON posts(updated_at DESC)');

        // Performance indexes for categories table (only if they exist)
        if (Schema::hasTable('categories')) {
            if (Schema::hasColumn('categories', 'published') && Schema::hasColumn('categories', 'position')) {
                DB::statement('CREATE INDEX idx_categories_published_position ON categories(published, position)');
            }
        }

        // Performance indexes for post_category pivot table (only if it exists)
        if (Schema::hasTable('post_category')) {
            DB::statement('CREATE INDEX idx_post_category_post ON post_category(post_id)');
            DB::statement('CREATE INDEX idx_post_category_category ON post_category(category_id)');
            DB::statement('CREATE INDEX idx_post_category_composite ON post_category(post_id, category_id)');
        }

        // Search indexes for content fields (only if they exist)
        if (Schema::hasColumn('posts', 'title')) {
            DB::statement('CREATE INDEX idx_posts_title ON posts(title)');
        }

        if (Schema::hasColumn('posts', 'slug')) {
            DB::statement('CREATE INDEX idx_posts_slug ON posts(slug)');
        }

        // User relationship index
        if (Schema::hasColumn('posts', 'user_id')) {
            DB::statement('CREATE INDEX idx_posts_user_id ON posts(user_id)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop performance indexes for SQLite
        $indexes = [
            'idx_posts_status_created',
            'idx_posts_published_at',
            'idx_posts_created_at',
            'idx_posts_updated_at',
            'idx_categories_published_position',
            'idx_post_category_post',
            'idx_post_category_category',
            'idx_post_category_composite',
            'idx_posts_title',
            'idx_posts_slug',
            'idx_posts_user_id',
        ];

        foreach ($indexes as $index) {
            try {
                DB::statement("DROP INDEX IF EXISTS {$index}");
            } catch (\Exception $e) {
                // Index may not exist, continue
            }
        }
    }
};
