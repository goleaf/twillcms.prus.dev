<template>
  <teleport to="body">
    <transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="opacity-0 translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 translate-y-2"
    >
      <div
        v-if="hasErrors"
        class="fixed top-4 right-4 z-50 max-w-sm w-full"
      >
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg">
          <div class="flex items-start">
            <ExclamationTriangleIcon class="h-5 w-5 text-red-400 mt-0.5 mr-3 flex-shrink-0" />
            <div class="flex-1">
              <h3 class="text-sm font-medium text-red-800 mb-1">
                <!-- {{ $t('errors.server_error') }} -->
              </h3>
              <div class="text-sm text-red-700">
                <p v-for="error in errorMessages" :key="error" class="mb-1 last:mb-0">
                  {{ error }}
                </p>
              </div>
            </div>
            <button
              @click="clearAllErrors"
              class="ml-3 flex-shrink-0 text-red-400 hover:text-red-600 transition-colors"
              aria-label="Dismiss"
            >
              <XMarkIcon class="h-4 w-4" />
            </button>
          </div>
        </div>
      </div>
    </transition>
  </teleport>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useBlogStore } from '@/stores/blog';
import { useCategoryStore } from '@/stores/category';
import { useSearchStore } from '@/stores/search';
import { useSiteStore } from '@/stores/site';
import { ExclamationTriangleIcon, XMarkIcon } from '@heroicons/vue/24/outline';

const blogStore = useBlogStore();
const categoryStore = useCategoryStore();
const searchStore = useSearchStore();
const siteStore = useSiteStore();

const hasErrors = computed(() => {
  return blogStore.hasErrors || 
         !!categoryStore.error || 
         !!searchStore.error || 
         !!siteStore.error;
});

const errorMessages = computed(() => {
  const errors: string[] = [];
  
  // Blog errors
  Object.values(blogStore.errors).forEach(error => {
    if (error) errors.push(error);
  });
  
  // Category errors
  if (categoryStore.error) {
    errors.push(categoryStore.error);
  }
  
  // Search errors
  if (searchStore.error) {
    errors.push(searchStore.error);
  }
  
  // Site errors
  if (siteStore.error) {
    errors.push(siteStore.error);
  }
  
  return errors;
});

const clearAllErrors = () => {
  blogStore.clearErrors();
  categoryStore.clearError();
  searchStore.clearError();
  siteStore.clearError();
};
</script> 