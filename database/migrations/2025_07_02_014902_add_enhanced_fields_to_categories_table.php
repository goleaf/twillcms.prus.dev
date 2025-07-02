<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // JSON fields for enhanced functionality
            $table->json('meta')->nullable()->comment('SEO and metadata information');
            $table->json('settings')->nullable()->comment('Advanced category settings');
            
            // Hierarchical structure
            $table->unsignedBigInteger('parent_id')->nullable()->comment('Parent category for hierarchy');
            
            // Visual and branding fields
            $table->string('color_code', 7)->nullable()->comment('Category color (hex code)');
            $table->string('icon', 50)->nullable()->comment('Category icon identifier');
            
            // Performance and analytics fields
            $table->unsignedBigInteger('view_count')->default(0)->comment('Category view counter');
            $table->unsignedInteger('sort_order')->default(0)->comment('Custom sort order');
            
            // Add indexes for performance
            $table->index('parent_id', 'idx_categories_parent_id');
            $table->index('view_count', 'idx_categories_view_count');
            $table->index('sort_order', 'idx_categories_sort_order');
            $table->index(['published', 'sort_order'], 'idx_categories_published_sort');
            
            // Add foreign key for hierarchical structure
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
        });
        
        // Add indexes for JSON fields (MySQL 5.7+ and modern databases)
        if (config('database.default') === 'mysql') {
            try {
                DB::statement('CREATE INDEX idx_categories_meta_description ON categories ((JSON_EXTRACT(meta, "$.description"(100))))');
                DB::statement('CREATE INDEX idx_categories_settings_featured ON categories ((JSON_EXTRACT(settings, "$.is_featured")))');
            } catch (Exception $e) {
                // Ignore if the database doesn't support JSON indexes
                Log::info('JSON indexes not created for categories table: ' . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['parent_id']);
            
            // Drop indexes
            $table->dropIndex('idx_categories_parent_id');
            $table->dropIndex('idx_categories_view_count');
            $table->dropIndex('idx_categories_sort_order');
            $table->dropIndex('idx_categories_published_sort');
            
            // Drop columns
            $table->dropColumn([
                'meta',
                'settings',
                'parent_id',
                'color_code',
                'icon',
                'view_count',
                'sort_order'
            ]);
        });
        
        // Drop JSON indexes if they exist
        if (config('database.default') === 'mysql') {
            try {
                DB::statement('DROP INDEX IF EXISTS idx_categories_meta_description ON categories');
                DB::statement('DROP INDEX IF EXISTS idx_categories_settings_featured ON categories');
            } catch (Exception $e) {
                // Ignore if indexes don't exist
            }
        }
    }
};
