<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div v-if="blogStore.loading.post" class="animate-pulse">
        <div class="h-8 bg-gray-200 rounded mb-4"></div>
        <div class="h-4 bg-gray-200 rounded mb-8 w-1/2"></div>
        <div class="h-64 bg-gray-200 rounded mb-8"></div>
      </div>
      
      <article v-else-if="blogStore.currentPost" class="bg-white rounded-lg shadow-sm p-8">
        <header class="mb-8">
          <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ blogStore.currentPost.title }}</h1>
          <div class="flex items-center text-gray-500 text-sm">
            <time :datetime="blogStore.currentPost.published_at">
              {{ formatDate(blogStore.currentPost.published_at) }}
            </time>
            <span v-if="blogStore.currentPost.reading_time" class="ml-4">
              {{ blogStore.currentPost.reading_time }} min read
            </span>
          </div>
        </header>
        
        <div class="prose max-w-none" v-html="blogStore.currentPost.content"></div>
      </article>
      
      <div v-else class="text-center py-12">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Post not found</h1>
        <router-link to="/blog" class="text-blue-600 hover:text-blue-800">Back to blog</router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useBlogStore } from '@/stores/blog';

const route = useRoute();
const blogStore = useBlogStore();

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const loadPost = async (slug: string) => {
  try {
    await blogStore.fetchPost(slug);
  } catch (error) {
    console.error('Failed to load post:', error);
  }
};

onMounted(() => {
  const slug = route.params.slug as string;
  if (slug) {
    loadPost(slug);
  }
});

watch(() => route.params.slug, (newSlug) => {
  if (newSlug) {
    loadPost(newSlug as string);
  }
});
</script> 