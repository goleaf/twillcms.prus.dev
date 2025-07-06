<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Drop translation tables for single language operation
     */
    public function up(): void
    {
        // Drop translation tables since we're moving to single language (English only)
        Schema::dropIfExists('post_translations');
        Schema::dropIfExists('category_translations');

        // Note: We keep the main posts and categories tables as they now contain
        // the English content directly without needing translation relationships
    }

    /**
     * Reverse the migrations - Recreate translation tables if needed
     */
    public function down(): void
    {
        // Recreate post_translations table
        Schema::create('post_translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale');
            $table->unsignedBigInteger('post_id');
            $table->boolean('active')->default(true);
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->text('excerpt_override')->nullable();
            $table->string('featured_image_caption')->nullable();
            $table->timestamps();

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->unique(['post_id', 'locale']);
            $table->index(['locale', 'active']);
        });

        // Recreate category_translations table
        Schema::create('category_translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale');
            $table->unsignedBigInteger('category_id');
            $table->boolean('active')->default(true);
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->unique(['category_id', 'locale']);
            $table->index(['locale', 'active']);
        });
    }
};
