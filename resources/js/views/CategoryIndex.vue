<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Categories</h1>
        <p class="text-lg text-gray-600">Browse all blog categories</p>
      </div>
      
      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <p class="mt-4 text-gray-600">Loading categories...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center py-12">
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 max-w-md mx-auto">
          <p class="text-red-600">{{ error }}</p>
          <button 
            @click="retryFetch"
            class="mt-4 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
          >
            Try Again
          </button>
        </div>
      </div>

      <!-- Categories Grid -->
      <div v-else-if="categories.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div
          v-for="category in categories"
          :key="category.id"
          class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden group"
        >
          <router-link
            :to="`/categories/${category.slug}`"
            class="block h-full"
          >
            <!-- Category Header -->
            <div 
              :style="{ backgroundColor: category.color || '#3B82F6' }"
              class="h-24 flex items-center justify-center relative"
            >
              <div class="absolute inset-0 bg-gradient-to-br from-transparent to-black/20"></div>
              <h3 class="text-white text-xl font-semibold z-10">
                {{ category.name || category.slug }}
              </h3>
            </div>

            <!-- Category Content -->
            <div class="p-6">
              <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                {{ category.description }}
              </p>
              
              <!-- Post Count Badge -->
              <div class="flex items-center justify-between">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  {{ category.posts_count || 0 }} 
                  {{ (category.posts_count || 0) === 1 ? 'post' : 'posts' }}
                </span>
                
                <!-- Arrow Icon -->
                <svg 
                  class="w-5 h-5 text-gray-400 group-hover:text-blue-600 group-hover:translate-x-1 transition-all duration-200" 
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </div>
            </div>
          </router-link>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <div class="max-w-md mx-auto">
          <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No Categories Found</h3>
          <p class="text-gray-500">Check back later for new categories.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { useCategoryStore } from '@/stores/category';
import { storeToRefs } from 'pinia';

const categoryStore = useCategoryStore();
const { categories, loading, error } = storeToRefs(categoryStore);

const retryFetch = async () => {
  await categoryStore.fetchCategories();
};

onMounted(async () => {
  // Fetch categories when component mounts
  await categoryStore.fetchCategories();
});
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style> 