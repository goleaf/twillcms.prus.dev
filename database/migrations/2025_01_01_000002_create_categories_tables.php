<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->timestamps();
            $table->boolean('published')->default(false);
            $table->integer('position')->nullable();
            $table->string('slug')->nullable();
        });

        Schema::create('category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('locale')->index();
            $table->boolean('active')->default(false);
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['category_id', 'locale']);
        });

        Schema::create('category_slugs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('locale')->index();
            $table->boolean('active')->default(false);
            $table->string('slug');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['category_id', 'locale']);
        });

        Schema::create('category_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('payload');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_revisions');
        Schema::dropIfExists('category_slugs');
        Schema::dropIfExists('category_translations');
        Schema::dropIfExists('categories');
    }
};
