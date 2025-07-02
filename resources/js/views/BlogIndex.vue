<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Page Header -->
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Blog</h1>
        <p class="text-lg text-gray-600">Discover our latest articles and insights</p>
      </div>

      <!-- Loading State -->
      <div v-if="blogStore.loading.posts" class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        <div v-for="i in 6" :key="i" class="animate-pulse">
          <div class="bg-gray-200 h-48 rounded-lg mb-4"></div>
          <div class="h-4 bg-gray-200 rounded mb-2"></div>
          <div class="h-4 bg-gray-200 rounded w-3/4"></div>
        </div>
      </div>

      <!-- Posts Grid -->
      <div v-else-if="blogStore.posts.length > 0" class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        <article
          v-for="post in blogStore.posts"
          :key="post.id"
          class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden"
        >
          <!-- Featured Image -->
          <div v-if="post.featured_image" class="aspect-w-16 aspect-h-9">
            <img
              :src="post.featured_image.thumb || post.featured_image.url"
              :alt="post.featured_image.alt"
              class="w-full h-48 object-cover"
              loading="lazy"
            />
          </div>
          
          <div class="p-6">
            <!-- Categories -->
            <div v-if="post.categories && post.categories.length > 0" class="flex flex-wrap gap-2 mb-3">
              <span
                v-for="category in post.categories.slice(0, 2)"
                :key="category.id"
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                :style="{ 
                  backgroundColor: category.color + '20', 
                  color: category.color 
                }"
              >
                {{ category.name }}
              </span>
            </div>

            <!-- Title -->
            <h2 class="text-xl font-semibold text-gray-900 mb-3">
              <router-link
                :to="`/blog/${post.slug}`"
                class="hover:text-blue-600 transition-colors"
              >
                {{ post.title }}
              </router-link>
            </h2>

            <!-- Excerpt -->
            <p v-if="post.excerpt" class="text-gray-600 mb-4 line-clamp-3">
              {{ post.excerpt }}
            </p>

            <!-- Meta -->
            <div class="flex items-center justify-between text-sm text-gray-500">
              <time :datetime="post.published_at">
                {{ formatDate(post.published_at) }}
              </time>
              <span v-if="post.meta.reading_time">
                {{ post.meta.reading_time }} min read
              </span>
            </div>
          </div>
        </article>
      </div>

      <!-- No Posts -->
      <div v-else class="text-center py-12">
        <p class="text-gray-500 text-lg">No posts available yet.</p>
      </div>

      <!-- Pagination -->
      <div v-if="blogStore.pagination && blogStore.pagination.last_page > 1" class="mt-12">
        <nav class="flex items-center justify-center">
          <button
            v-if="blogStore.pagination.links.prev"
            @click="loadPage(blogStore.currentPage - 1)"
            class="mr-3 px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Previous
          </button>
          
          <span class="px-4 py-2 text-sm text-gray-700">
            Page {{ blogStore.currentPage }} of {{ blogStore.lastPage }}
          </span>
          
          <button
            v-if="blogStore.pagination.links.next"
            @click="loadPage(blogStore.currentPage + 1)"
            class="ml-3 px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Next
          </button>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { useBlogStore } from '@/stores/blog';

const blogStore = useBlogStore();

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const loadPage = async (page: number) => {
  await blogStore.fetchPosts({ page });
};

onMounted(async () => {
  if (blogStore.posts.length === 0) {
    await blogStore.fetchPosts();
  }
});
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.aspect-w-16 {
  position: relative;
  padding-bottom: 56.25%; /* 16:9 aspect ratio */
}

.aspect-w-16 img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}
</style> 