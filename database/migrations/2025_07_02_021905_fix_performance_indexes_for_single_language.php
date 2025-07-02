<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Fix performance indexes for single language operation
     */
    public function up(): void
    {
        // Remove any existing translation table indexes if they somehow still exist
        $translationIndexes = [
            'post_translations' => [
                'idx_post_translations_locale_active',
                'idx_post_translations_post_locale', 
                'idx_post_translations_title',
                'idx_post_translations_content_search'
            ],
            'category_translations' => [
                'idx_category_translations_locale',
                'idx_category_translations_category_locale'
            ]
        ];

        foreach ($translationIndexes as $table => $indexes) {
            if (Schema::hasTable($table)) {
                foreach ($indexes as $index) {
                    try {
                        DB::statement("DROP INDEX {$index} ON {$table}");
                    } catch (\Exception $e) {
                        // Index may not exist, continue
                    }
                }
            }
        }

        // Add enhanced indexes for single language operation on main tables
        if (Schema::hasTable('posts')) {
            // Enhanced content search for single language
            try {
                DB::statement('CREATE INDEX idx_posts_title_desc ON posts(title, description)');
            } catch (\Exception $e) {
                // Index may already exist
            }
            
            // Enhanced published content search
            try {
                DB::statement('CREATE INDEX idx_posts_published_title ON posts(published, title)');
            } catch (\Exception $e) {
                // Index may already exist
            }
        }

        if (Schema::hasTable('categories')) {
            // Enhanced category search for single language
            try {
                DB::statement('CREATE INDEX idx_categories_title_published ON categories(title, published)');
            } catch (\Exception $e) {
                // Index may already exist
            }
        }
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        // Remove the single language indexes we added
        $singleLanguageIndexes = [
            'posts' => [
                'idx_posts_title_desc',
                'idx_posts_published_title'
            ],
            'categories' => [
                'idx_categories_title_published'
            ]
        ];

        foreach ($singleLanguageIndexes as $table => $indexes) {
            if (Schema::hasTable($table)) {
                foreach ($indexes as $index) {
                    try {
                        DB::statement("DROP INDEX {$index} ON {$table}");
                    } catch (\Exception $e) {
                        // Index may not exist, continue
                    }
                }
            }
        }
    }
};
