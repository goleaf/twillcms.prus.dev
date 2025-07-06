<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Add content fields that were removed with translation tables
     */
    public function up(): void
    {
        // Add content fields to posts table
        Schema::table('posts', function (Blueprint $table) {
            // Core content fields that TwillCMS expects
            $table->string('title')->nullable()->after('published');
            $table->text('description')->nullable()->after('title');
            $table->longText('content')->nullable()->after('description');

            // Published at field for proper sorting (if not exists)
            if (! Schema::hasColumn('posts', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('published');
            }

            // Add indexes for performance
            $table->index('title', 'idx_posts_title');
            $table->index(['published', 'title'], 'idx_posts_published_title');
        });

        // Add content fields to categories table
        Schema::table('categories', function (Blueprint $table) {
            // Core content fields that TwillCMS expects
            if (! Schema::hasColumn('categories', 'title')) {
                $table->string('title')->nullable()->after('published');
            }
            if (! Schema::hasColumn('categories', 'description')) {
                $table->text('description')->nullable()->after('title');
            }

            // Add indexes for performance
            $table->index('title', 'idx_categories_title');
            $table->index(['published', 'title'], 'idx_categories_published_title');
        });
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_posts_title');
            $table->dropIndex('idx_posts_published_title');

            // Drop columns
            $table->dropColumn(['title', 'description', 'content']);
        });

        Schema::table('categories', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_categories_title');
            $table->dropIndex('idx_categories_published_title');

            // Drop columns (only if we added them)
            if (Schema::hasColumn('categories', 'title')) {
                $table->dropColumn('title');
            }
            if (Schema::hasColumn('categories', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
