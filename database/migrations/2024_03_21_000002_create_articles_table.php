<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt');
            $table->longText('content');
            $table->string('image')->nullable();
            $table->string('image_caption')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->integer('reading_time')->default(1);
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamps();

            // Indexes for performance optimization
            $table->index('is_featured');
            $table->index('is_published');
            $table->index('published_at');
            $table->index('view_count');
            $table->index('created_at');
            $table->index('updated_at');
            
            // Composite indexes for common queries
            $table->index(['is_published', 'published_at']);
            $table->index(['is_featured', 'is_published', 'published_at']);
            $table->index(['is_published', 'view_count']);
            $table->index(['is_featured', 'view_count']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
}; 