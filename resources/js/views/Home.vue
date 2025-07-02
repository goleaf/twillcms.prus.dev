<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">
    <!-- Hero Section -->
    <section class="relative bg-white overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 opacity-5"></div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
        <div class="text-center">
          <h1 class="text-5xl lg:text-7xl font-extrabold text-gray-900 mb-6 leading-tight">
            {{ siteStore.siteName }}
          </h1>
          <p class="text-xl lg:text-2xl text-gray-600 mb-10 max-w-4xl mx-auto leading-relaxed">
            {{ siteStore.siteDescription || t('blog.site_description') }}
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <router-link
              to="/blog"
              class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-semibold rounded-xl text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1"
            >
              {{ t('blog.latest_posts') }}
              <ArrowRightIcon class="ml-2 h-5 w-5" />
            </router-link>
            <router-link
              to="/categories"
              class="inline-flex items-center px-8 py-4 border-2 border-gray-300 text-lg font-semibold rounded-xl text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-md hover:shadow-lg"
            >
              {{ t('categories.browse_categories') }}
              <FolderIcon class="ml-2 h-5 w-5" />
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Posts Section -->
    <section class="py-16 lg:py-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
            {{ t('blog.latest_posts') }}
          </h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            {{ t('blog.blog_description') }}
          </p>
        </div>

        <!-- Loading State -->
        <div v-if="blogStore.loading.posts" class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
          <div v-for="i in 6" :key="i" class="animate-pulse">
            <div class="bg-gray-200 h-64 rounded-2xl mb-6"></div>
            <div class="h-6 bg-gray-200 rounded-lg mb-3"></div>
            <div class="h-4 bg-gray-200 rounded-lg mb-2"></div>
            <div class="h-4 bg-gray-200 rounded-lg w-3/4"></div>
          </div>
        </div>

        <!-- Posts Grid -->
        <div v-else-if="blogStore.recentPosts.length > 0" class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
          <article
            v-for="post in blogStore.recentPosts"
            :key="post.id"
            class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-gray-200 transform hover:-translate-y-2"
          >
            <!-- Featured Image -->
            <div class="relative aspect-w-16 aspect-h-9 overflow-hidden">
              <img
                v-if="post.featured_image"
                :src="post.featured_image.thumb || post.featured_image.url"
                :alt="post.featured_image.alt"
                class="w-full h-56 object-cover group-hover:scale-105 transition-transform duration-300"
                loading="lazy"
              />
              <div v-else class="w-full h-56 bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center">
                <DocumentTextIcon class="h-16 w-16 text-gray-400" />
              </div>
              
              <!-- Category Badge -->
              <div v-if="post.categories && post.categories.length > 0" class="absolute top-4 left-4">
                <span
                  class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold backdrop-blur-sm bg-white/90 shadow-sm"
                  :style="{ color: post.categories[0].color }"
                >
                  {{ post.categories[0].name }}
                </span>
              </div>
            </div>
            
            <div class="p-6">
              <!-- Title -->
              <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors line-clamp-2">
                <router-link :to="`/blog/${post.slug}`">
                  {{ post.title }}
                </router-link>
              </h3>

              <!-- Excerpt -->
              <p v-if="post.excerpt" class="text-gray-600 mb-4 line-clamp-3 leading-relaxed">
                {{ post.excerpt }}
              </p>

              <!-- Meta -->
              <div class="flex items-center justify-between text-sm text-gray-500 pt-4 border-t border-gray-100">
                <time :datetime="post.published_at" class="flex items-center">
                  <CalendarIcon class="h-4 w-4 mr-1" />
                  {{ formatDate(post.published_at) }}
                </time>
                <span v-if="post.meta.reading_time" class="flex items-center">
                  <ClockIcon class="h-4 w-4 mr-1" />
                  {{ post.meta.reading_time }} {{ t('blog.reading_time') }}
                </span>
              </div>
            </div>
          </article>
        </div>

        <!-- No Posts -->
        <div v-else class="text-center py-16">
          <DocumentTextIcon class="h-16 w-16 text-gray-300 mx-auto mb-4" />
          <p class="text-gray-500 text-xl">{{ t('blog.no_posts') }}</p>
        </div>

        <!-- View All Posts Button -->
        <div v-if="blogStore.recentPosts.length > 0" class="text-center mt-16">
          <router-link
            to="/blog"
            class="inline-flex items-center px-8 py-4 border-2 border-gray-300 text-lg font-semibold rounded-xl text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-md hover:shadow-lg"
          >
            {{ t('blog.view_all_posts') }}
            <ArrowRightIcon class="ml-2 h-5 w-5" />
          </router-link>
        </div>
      </div>
    </section>

    <!-- Categories Section -->
    <section class="bg-white py-16 lg:py-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
            {{ t('categories.popular_categories') }}
          </h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            {{ t('blog.categories_description') }}
          </p>
        </div>

        <!-- Categories Grid -->
        <div v-if="categoryStore.popularCategories.length > 0" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
          <router-link
            v-for="category in categoryStore.popularCategories"
            :key="category.id"
            :to="`/category/${category.slug}`"
            class="group p-6 bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl hover:from-blue-50 hover:to-purple-50 transition-all duration-300 border border-gray-200 hover:border-blue-200 hover:shadow-lg transform hover:-translate-y-1"
          >
            <div class="flex items-center mb-4">
              <div
                class="w-6 h-6 rounded-full mr-3 shadow-sm"
                :style="{ backgroundColor: category.color }"
              ></div>
              <h3 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                {{ category.name }}
              </h3>
            </div>
            <p class="text-sm text-gray-600 flex items-center">
              <DocumentTextIcon class="h-4 w-4 mr-1" />
              {{ t('blog.posts_count', { count: category.posts_count }) }}
            </p>
          </router-link>
        </div>

        <!-- View All Categories Button -->
        <div v-if="categoryStore.popularCategories.length > 0" class="text-center mt-16">
          <router-link
            to="/categories"
            class="inline-flex items-center px-8 py-4 border-2 border-gray-300 text-lg font-semibold rounded-xl text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-md hover:shadow-lg"
          >
            {{ t('categories.view_all_categories') }}
            <FolderIcon class="ml-2 h-5 w-5" />
          </router-link>
        </div>
      </div>
    </section>

    <!-- Newsletter Section -->
    <section class="bg-gradient-to-r from-blue-600 to-purple-600 py-16">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl lg:text-4xl font-bold text-white mb-6">
          {{ t('blog.site_tagline') }}
        </h2>
        <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
          {{ t('blog.site_description') }}
        </p>
        <router-link
          to="/blog"
          class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-xl hover:bg-gray-50 transition-colors shadow-lg hover:shadow-xl transform hover:-translate-y-1"
        >
          {{ t('common.read_more') }}
          <ArrowRightIcon class="ml-2 h-5 w-5" />
        </router-link>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { useSiteStore } from '@/stores/site';
import { useBlogStore } from '@/stores/blog';
import { useCategoryStore } from '@/stores/category';
import { useTranslations } from '@/composables/useTranslations';
import { 
  ArrowRightIcon, 
  FolderIcon, 
  DocumentTextIcon, 
  CalendarIcon, 
  ClockIcon 
} from '@heroicons/vue/24/outline';

const siteStore = useSiteStore();
const blogStore = useBlogStore();
const categoryStore = useCategoryStore();
const { t } = useTranslations();

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString(undefined, {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

onMounted(async () => {
  // Fetch recent posts and popular categories if not already loaded
  if (blogStore.recentPosts.length === 0) {
    await blogStore.fetchRecentPosts(6);
  }
  
  if (categoryStore.popularCategories.length === 0) {
    await categoryStore.fetchPopularCategories(8);
  }
});
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

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

.aspect-w-16 > * {
  position: absolute;
  height: 100%;
  width: 100%;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}
</style>
