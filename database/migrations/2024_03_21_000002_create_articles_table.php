<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            // Primary key and basic fields
            $table->id();
            $table->string('title', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('slug', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->text('excerpt')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->longText('content')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('image', 500)->nullable(); // Allow longer image paths
            $table->string('image_caption', 500)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->unsignedSmallInteger('reading_time')->default(1); // Minutes to read
            $table->unsignedInteger('view_count')->default(0); // Article views for popularity
            $table->string('meta_title', 255)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->text('meta_description')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->timestamps();
            $table->softDeletes();

            // Primary indexes for uniqueness
            $table->unique('slug');
            
            // Performance indexes for news portal filtering and sorting
            $table->index(['is_published', 'published_at', 'view_count']); // Published articles by date and popularity
            $table->index(['is_featured', 'is_published', 'published_at']); // Featured published articles
            $table->index(['status', 'published_at']); // Articles by status and date
            $table->index(['is_published', 'created_at']); // Recent published articles
            $table->index(['view_count', 'is_published']); // Popular published articles
            $table->index(['is_featured', 'view_count']); // Featured popular articles
            $table->index('published_at'); // Date-based filtering
            $table->index('view_count'); // Popularity sorting
            $table->index('created_at'); // Recent articles
            $table->index('deleted_at'); // Soft delete performance
            
            // Full-text search indexes for news portal search functionality
            $table->fullText(['title', 'excerpt']); // Primary search fields
            $table->fullText(['title', 'excerpt', 'content']); // Comprehensive search
            
            // MySQL specific optimizations
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
        
        // Add additional MySQL optimizations via raw SQL
        DB::statement('ALTER TABLE articles ROW_FORMAT=DYNAMIC');
        
        // Add triggers for automatic usage count updates (optional performance optimization)
        DB::statement('
            CREATE TRIGGER update_article_search_weight 
            AFTER UPDATE ON articles 
            FOR EACH ROW 
            BEGIN 
                IF NEW.view_count <> OLD.view_count THEN
                    UPDATE articles SET updated_at = NOW() WHERE id = NEW.id;
                END IF;
            END
        ');
    }

    public function down(): void
    {
        // Drop trigger first
        DB::statement('DROP TRIGGER IF EXISTS update_article_search_weight');
        Schema::dropIfExists('articles');
    }
}; 