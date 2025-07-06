<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('post_translations')) {
            Schema::create('post_translations', function (Blueprint $table) {
                $table->id();
                $table->string('locale');
                $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
                $table->boolean('active')->default(true);
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->longText('content')->nullable();
                $table->text('excerpt_override')->nullable();
                $table->string('featured_image_caption')->nullable();
                $table->timestamps();

                $table->unique(['post_id', 'locale']);
                $table->index(['locale', 'active']);
            });
        }

        if (! Schema::hasTable('category_translations')) {
            Schema::create('category_translations', function (Blueprint $table) {
                $table->id();
                $table->string('locale');
                $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
                $table->boolean('active')->default(true);
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();

                $table->unique(['category_id', 'locale']);
                $table->index(['locale', 'active']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_translations');
        Schema::dropIfExists('category_translations');
    }
};
