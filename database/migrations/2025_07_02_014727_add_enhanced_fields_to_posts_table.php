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
        Schema::table('posts', function (Blueprint $table) {
            // JSON fields for enhanced functionality
            $table->json('meta')->nullable()->comment('SEO and metadata information');
            $table->json('settings')->nullable()->comment('Advanced post settings');
            
            // Performance and analytics fields
            $table->unsignedBigInteger('view_count')->default(0)->comment('Post view counter');
            $table->unsignedInteger('priority')->default(0)->comment('Display priority');
            
            // Additional content fields
            $table->text('excerpt_override')->nullable()->comment('Custom excerpt override');
            $table->string('featured_image_caption')->nullable()->comment('Featured image caption');
            
            // Author override
            $table->unsignedBigInteger('author_id')->nullable()->comment('Post author reference');
            
            // Add indexes for performance
            $table->index('view_count', 'idx_posts_view_count');
            $table->index('priority', 'idx_posts_priority');
            $table->index('author_id', 'idx_posts_author_id');
            $table->index(['published', 'published_at'], 'idx_posts_published_date');
            
            // Add foreign key for author if users table exists
            if (Schema::hasTable('users')) {
                $table->foreign('author_id')->references('id')->on('users')->onDelete('set null');
            }
        });
        
        // Add indexes for JSON fields (MySQL 5.7+ and modern databases)
        if (config('database.default') === 'mysql') {
            try {
                DB::statement('CREATE INDEX idx_posts_meta_description ON posts ((JSON_EXTRACT(meta, "$.description"(100))))');
                DB::statement('CREATE INDEX idx_posts_settings_featured ON posts ((JSON_EXTRACT(settings, "$.is_featured")))');
                DB::statement('CREATE INDEX idx_posts_settings_trending ON posts ((JSON_EXTRACT(settings, "$.is_trending")))');
            } catch (Exception $e) {
                // Ignore if the database doesn't support JSON indexes
                Log::info('JSON indexes not created for posts table: ' . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Drop foreign keys first
            if (Schema::hasTable('users')) {
                $table->dropForeign(['author_id']);
            }
            
            // Drop indexes
            $table->dropIndex('idx_posts_view_count');
            $table->dropIndex('idx_posts_priority');
            $table->dropIndex('idx_posts_author_id');
            $table->dropIndex('idx_posts_published_date');
            
            // Drop columns
            $table->dropColumn([
                'meta',
                'settings', 
                'view_count',
                'priority',
                'excerpt_override',
                'featured_image_caption',
                'author_id'
            ]);
        });
        
        // Drop JSON indexes if they exist
        if (config('database.default') === 'mysql') {
            try {
                DB::statement('DROP INDEX IF EXISTS idx_posts_meta_description ON posts');
                DB::statement('DROP INDEX IF EXISTS idx_posts_settings_featured ON posts');
                DB::statement('DROP INDEX IF EXISTS idx_posts_settings_trending ON posts');
            } catch (Exception $e) {
                // Ignore if indexes don't exist
            }
        }
    }
};
