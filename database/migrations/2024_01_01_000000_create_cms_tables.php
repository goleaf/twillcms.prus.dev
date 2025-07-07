<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Categories table
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('name')->nullable();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('published')->default(true);
            $table->integer('view_count')->default(0);
            $table->integer('position')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Tags table
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // Posts table (main posts table with all required fields)
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->enum('status', ['draft', 'published', 'scheduled', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->text('description')->nullable();
            $table->integer('position')->default(0);
            $table->json('meta')->nullable();
            $table->json('settings')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->integer('priority')->default(0);
            $table->unsignedBigInteger('author_id')->nullable();
            $table->string('featured_image_caption')->nullable();
            $table->text('excerpt_override')->nullable();
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index(['published', 'published_at']);
            $table->index('user_id');
        });

        // Post-Category pivot table
        Schema::create('post_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['post_id', 'category_id']);
        });

        // Post-Tag pivot table
        Schema::create('post_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['post_id', 'tag_id']);
        });

        // Pages table
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->string('template')->default('default');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->json('meta_data')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        // Media table
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('original_name');
            $table->string('path');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Site Settings table
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->string('type')->default('string');
            $table->timestamps();
        });

        // Comments table
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->string('author_name');
            $table->string('author_email');
            $table->string('author_website')->nullable();
            $table->text('content');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();

            $table->index(['post_id', 'status']);
        });

        // Newsletter subscribers table
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('subscribed_at')->useCurrent();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('media');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('post_tag');
        Schema::dropIfExists('post_category');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('categories');
    }
};
