<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Unique constraint to prevent duplicate relationships
            $table->unique(['article_id', 'tag_id']);

            // Indexes for faster lookups
            $table->index('article_id');
            $table->index('tag_id');
            $table->index(['tag_id', 'article_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_tag');
    }
}; 