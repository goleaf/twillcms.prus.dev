<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Aliziodev\LaravelTaxonomy\Models\Taxonomy;
use App\Models\Article;
use App\Models\Tag;
use App\Models\Category;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Migrate existing tags to taxonomy system
        $this->migrateTags();
        
        // Step 2: Migrate existing categories to taxonomy system
        $this->migrateCategories();
        
        // Step 3: Migrate article-tag relationships
        $this->migrateArticleTagRelationships();
        
        // Step 4: Migrate article-category relationships
        $this->migrateArticleCategoryRelationships();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove taxonomy data (but keep the structure)
        DB::table('taxonomables')->where('taxonomable_type', 'App\Models\Article')->delete();
        DB::table('taxonomies')->whereIn('type', ['tag', 'category'])->delete();
    }
    
    /**
     * Migrate existing tags to taxonomy system
     */
    private function migrateTags(): void
    {
        $tags = DB::table('tags')->get();
        
        foreach ($tags as $tag) {
            $taxonomyData = [
                'name' => $tag->name,
                'slug' => $tag->slug,
                'type' => 'tag',
                'description' => $tag->description ?? null,
                'sort_order' => 0,
                'meta' => json_encode([
                    'color' => $tag->color ?? '#6c757d',
                    'usage_count' => $tag->usage_count ?? 0,
                    'is_featured' => $tag->is_featured ?? false,
                    'original_tag_id' => $tag->id,
                ]),
                'created_at' => $tag->created_at ?? now(),
                'updated_at' => $tag->updated_at ?? now(),
            ];
            
            DB::table('taxonomies')->insert($taxonomyData);
        }
    }
    
    /**
     * Migrate existing categories to taxonomy system
     */
    private function migrateCategories(): void
    {
        $categories = DB::table('categories')->orderBy('parent_id')->get();
        $categoryMap = [];
        
        foreach ($categories as $category) {
            $parentTaxonomyId = null;
            
            // If this category has a parent, find the corresponding taxonomy ID
            if ($category->parent_id) {
                $parentTaxonomyId = $categoryMap[$category->parent_id] ?? null;
            }
            
            $taxonomyData = [
                'name' => $category->name,
                'slug' => $category->slug,
                'type' => 'category',
                'description' => $category->description ?? null,
                'parent_id' => $parentTaxonomyId,
                'sort_order' => $category->sort_order ?? 0,
                'meta' => json_encode([
                    'color' => $category->color ?? '#007bff',
                    'title' => $category->title ?? $category->name,
                    'is_active' => $category->is_active ?? true,
                    'original_category_id' => $category->id,
                ]),
                'created_at' => $category->created_at ?? now(),
                'updated_at' => $category->updated_at ?? now(),
            ];
            
            $taxonomyId = DB::table('taxonomies')->insertGetId($taxonomyData);
            $categoryMap[$category->id] = $taxonomyId;
        }
        
        // Update nested set values for hierarchical categories
        $this->updateNestedSetValues();
    }
    
    /**
     * Migrate article-tag relationships
     */
    private function migrateArticleTagRelationships(): void
    {
        $relationships = DB::table('article_tag')->get();
        
        foreach ($relationships as $relation) {
            // Find the corresponding taxonomy for this tag
            $tagTaxonomy = DB::table('taxonomies')
                ->where('type', 'tag')
                ->whereJsonContains('meta->original_tag_id', $relation->tag_id)
                ->first();
                
            if ($tagTaxonomy) {
                DB::table('taxonomables')->insert([
                    'taxonomy_id' => $tagTaxonomy->id,
                    'taxonomable_id' => $relation->article_id,
                    'taxonomable_type' => 'App\Models\Article',
                    'created_at' => $relation->created_at ?? now(),
                    'updated_at' => $relation->updated_at ?? now(),
                ]);
            }
        }
    }
    
    /**
     * Migrate article-category relationships
     */
    private function migrateArticleCategoryRelationships(): void
    {
        $relationships = DB::table('article_category')->get();
        
        foreach ($relationships as $relation) {
            // Find the corresponding taxonomy for this category
            $categoryTaxonomy = DB::table('taxonomies')
                ->where('type', 'category')
                ->whereJsonContains('meta->original_category_id', $relation->category_id)
                ->first();
                
            if ($categoryTaxonomy) {
                DB::table('taxonomables')->insert([
                    'taxonomy_id' => $categoryTaxonomy->id,
                    'taxonomable_id' => $relation->article_id,
                    'taxonomable_type' => 'App\Models\Article',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
    
    /**
     * Update nested set values for hierarchical categories
     */
    private function updateNestedSetValues(): void
    {
        // This will be handled by the taxonomy package's built-in methods
        // We'll rebuild the nested set after migration
        try {
            \Aliziodev\LaravelTaxonomy\Models\Taxonomy::rebuildNestedSet('category');
        } catch (Exception $e) {
            // Handle any potential errors during nested set rebuild
            \Log::warning('Could not rebuild nested set for categories: ' . $e->getMessage());
        }
    }
};
