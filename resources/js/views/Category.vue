<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div v-if="categoryStore.loading" class="text-center py-12">
        <div class="animate-pulse">
          <div class="h-8 bg-gray-200 rounded mb-4 w-1/3 mx-auto"></div>
          <div class="h-4 bg-gray-200 rounded mb-8 w-1/2 mx-auto"></div>
        </div>
      </div>
      
      <div v-else-if="categoryStore.currentCategory">
        <div class="text-center mb-12">
          <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ categoryStore.currentCategory.name }}</h1>
          <p v-if="categoryStore.currentCategory.description" class="text-lg text-gray-600">
            {{ categoryStore.currentCategory.description }}
          </p>
        </div>
        
        <div class="text-center py-12">
          <p class="text-gray-500">Category posts will be loaded here</p>
        </div>
      </div>
      
      <div v-else class="text-center py-12">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Category not found</h1>
        <router-link to="/categories" class="text-blue-600 hover:text-blue-800">Back to categories</router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useCategoryStore } from '@/stores/category';

const route = useRoute();
const categoryStore = useCategoryStore();

onMounted(async () => {
  const slug = route.params.slug as string;
  if (slug) {
    try {
      await categoryStore.fetchCategory(slug);
    } catch (error) {
      console.error('Failed to load category:', error);
    }
  }
});
</script> 