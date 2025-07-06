<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->timestamps();
            $table->boolean('published')->default(false);
            $table->integer('position')->nullable();
            $table->string('slug')->nullable();
        });

        Schema::create('post_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->string('locale')->index();
            $table->boolean('active')->default(false);
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['post_id', 'locale']);
        });

        Schema::create('post_slugs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->string('locale')->index();
            $table->boolean('active')->default(false);
            $table->string('slug');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['post_id', 'locale']);
        });

        Schema::create('post_revisions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id')->index();
            $table->text('payload');
            $table->timestamps();

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_revisions');
        Schema::dropIfExists('post_slugs');
        Schema::dropIfExists('post_translations');
        Schema::dropIfExists('posts');
    }
};
