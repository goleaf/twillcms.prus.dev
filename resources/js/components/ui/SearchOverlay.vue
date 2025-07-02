<template>
  <teleport to="body">
    <div class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-start justify-center pt-20">
      <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4">
        <div class="p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Search Posts</h2>
            <button
              @click="$emit('close')"
              class="text-gray-400 hover:text-gray-600 transition-colors"
            >
              <XMarkIcon class="h-6 w-6" />
            </button>
          </div>
          
          <input
            type="text"
            placeholder="Search for posts..."
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            v-model="searchQuery"
            @keyup.enter="performSearch"
          />
          
          <div class="mt-4 flex justify-end">
            <button
              @click="performSearch"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
              Search
            </button>
          </div>
        </div>
      </div>
    </div>
  </teleport>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { XMarkIcon } from '@heroicons/vue/24/outline';

const emit = defineEmits<{
  close: [];
}>();

const router = useRouter();
const searchQuery = ref('');

const performSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ name: 'search', query: { q: searchQuery.value.trim() } });
    emit('close');
  }
};
</script> 