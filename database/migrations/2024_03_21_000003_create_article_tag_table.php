<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_tag', function (Blueprint $table) {
            // Use bigInt to match parent tables
            $table->id();
            $table->unsignedBigInteger('article_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();

            // Foreign key constraints with proper cascade behavior
            $table->foreign('article_id')
                  ->references('id')
                  ->on('articles')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
                  
            $table->foreign('tag_id')
                  ->references('id')
                  ->on('tags')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            // Unique constraint to prevent duplicate relationships
            $table->unique(['article_id', 'tag_id'], 'article_tag_unique');

            // Performance indexes for news portal tag filtering
            $table->index('article_id', 'idx_article_tag_article'); // Find tags for article
            $table->index('tag_id', 'idx_article_tag_tag'); // Find articles for tag
            $table->index(['tag_id', 'article_id'], 'idx_tag_article_lookup'); // Tag-based article lookup
            $table->index(['article_id', 'tag_id'], 'idx_article_tag_lookup'); // Article-based tag lookup
            $table->index('created_at', 'idx_article_tag_created'); // Recent tag associations
            
            // MySQL specific optimizations
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
        
        // Add additional MySQL optimizations via raw SQL
        DB::statement('ALTER TABLE article_tag ROW_FORMAT=DYNAMIC');
        
        // Add trigger to update tag usage_count automatically
        DB::statement('
            CREATE TRIGGER increment_tag_usage 
            AFTER INSERT ON article_tag 
            FOR EACH ROW 
            BEGIN 
                UPDATE tags SET usage_count = (
                    SELECT COUNT(*) FROM article_tag WHERE tag_id = NEW.tag_id
                ) WHERE id = NEW.tag_id;
            END
        ');
        
        DB::statement('
            CREATE TRIGGER decrement_tag_usage 
            AFTER DELETE ON article_tag 
            FOR EACH ROW 
            BEGIN 
                UPDATE tags SET usage_count = (
                    SELECT COUNT(*) FROM article_tag WHERE tag_id = OLD.tag_id
                ) WHERE id = OLD.tag_id;
            END
        ');
    }

    public function down(): void
    {
        // Drop triggers first
        DB::statement('DROP TRIGGER IF EXISTS increment_tag_usage');
        DB::statement('DROP TRIGGER IF EXISTS decrement_tag_usage');
        Schema::dropIfExists('article_tag');
    }
}; 