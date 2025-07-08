<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            // Primary key and basic fields
            $table->id();
            $table->string('name', 100)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('slug', 100)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->text('description')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('color', 7)->default('#3B82F6'); // Hex color code for visual categorization
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('usage_count')->default(0); // For popularity sorting
            $table->timestamps();
            $table->softDeletes();

            // Primary indexes for uniqueness
            $table->unique('name');
            $table->unique('slug');
            
            // Performance indexes for news portal filtering and sorting
            $table->index(['is_featured', 'usage_count', 'name']); // Featured tags with popularity
            $table->index(['usage_count', 'name']); // Popular tags sorting
            $table->index(['created_at', 'is_featured']); // Recent featured tags
            $table->index(['name', 'is_featured']); // Search by name with featured priority
            $table->index('slug'); // URL routing performance
            $table->index('deleted_at'); // Soft delete performance
            
            // MySQL specific optimizations
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
        
        // Add additional MySQL optimizations via raw SQL
        DB::statement('ALTER TABLE tags ROW_FORMAT=DYNAMIC');
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
}; 