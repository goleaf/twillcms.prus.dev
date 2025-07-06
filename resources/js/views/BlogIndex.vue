<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Page Header with Search -->
      <div class="text-center mb-12">
        <h1 class="text-5xl font-bold text-gray-900 mb-6">Discover Amazing Stories</h1>
        <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">Explore our latest articles, insights, and stories crafted with passion and expertise</p>
        
        <!-- Search Bar -->
        <div class="max-w-2xl mx-auto">
          <div class="relative">
            <input
              v-model="searchQuery"
              @input="performSearch"
              type="text"
              placeholder="Search articles..."
              class="w-full px-6 py-4 pr-12 text-lg border border-gray-300 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm"
            />
            <MagnifyingGlassIcon class="absolute right-4 top-1/2 transform -translate-y-1/2 h-6 w-6 text-gray-400" />
          </div>
        </div>

        <!-- Category Filter -->
        <div v-if="availableCategories.length > 0" class="flex flex-wrap justify-center gap-3 mt-6">
          <button
            @click="filterByCategory(null)"
            :class="[
              'px-4 py-2 rounded-full text-sm font-medium transition-all duration-200',
              !selectedCategory ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200'
            ]"
          >
            All Posts
          </button>
          <button
            v-for="category in availableCategories"
            :key="category.id"
            @click="filterByCategory(category)"
            :class="[
              'px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 border',
              selectedCategory?.id === category.id 
                ? 'text-white shadow-md' 
                : 'bg-white text-gray-700 hover:bg-gray-100 border-gray-200'
            ]"
            :style="selectedCategory?.id === category.id ? { backgroundColor: category.color } : {}"
          >
            {{ category.name }}
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="blogStore.loading.posts" class="space-y-8">
        <!-- Featured Post Skeleton -->
        <div class="animate-pulse">
          <div class="bg-gray-200 h-96 rounded-2xl mb-8"></div>
        </div>
        <!-- Grid Skeletons -->
        <div class="masonry-grid gap-8">
          <div v-for="i in 6" :key="i" class="animate-pulse">
            <div class="bg-gray-200 h-64 rounded-xl mb-4"></div>
            <div class="h-4 bg-gray-200 rounded mb-2"></div>
            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
          </div>
        </div>
      </div>

      <!-- Featured Post (Large) -->
      <div v-else-if="filteredPosts.length > 0" class="mb-16">
        <article
          class="group relative bg-white rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 overflow-hidden lg:flex lg:items-center min-h-[500px]"
        >
          <!-- Featured Image -->
          <div class="lg:w-1/2 h-64 lg:h-full relative overflow-hidden">
            <img
              v-if="filteredPosts[0].featured_image"
              :src="filteredPosts[0].featured_image.large || filteredPosts[0].featured_image.url"
              :alt="filteredPosts[0].featured_image.alt || filteredPosts[0].title"
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
            />
            <div v-else class="w-full h-full bg-gradient-to-br from-blue-500 via-purple-600 to-pink-500 flex items-center justify-center">
              <DocumentTextIcon class="h-20 w-20 text-white opacity-50" />
            </div>
            
            <!-- Featured Badge -->
            <div class="absolute top-6 left-6">
              <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-amber-400 text-amber-900 shadow-lg">
                <StarIcon class="h-4 w-4 mr-2" />
                Featured Story
              </div>
            </div>
          </div>
          
          <!-- Featured Content -->
          <div class="p-8 lg:w-1/2 lg:p-16 flex flex-col justify-center">
            <!-- Categories -->
            <div v-if="filteredPosts[0].categories?.length" class="flex flex-wrap gap-2 mb-6">
              <span
                v-for="category in filteredPosts[0].categories.slice(0, 2)"
                :key="category.id"
                class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold shadow-sm"
                :style="{ 
                  backgroundColor: category.color + '20', 
                  color: category.color,
                  borderColor: category.color + '40'
                }"
              >
                {{ category.name }}
              </span>
            </div>

            <!-- Title -->
            <h2 class="text-3xl lg:text-5xl font-bold text-gray-900 mb-6 leading-tight">
              <router-link
                :to="`/blog/${filteredPosts[0].slug}`"
                class="hover:text-blue-600 transition-colors duration-300"
              >
                {{ filteredPosts[0].title }}
              </router-link>
            </h2>

            <!-- Excerpt -->
            <p v-if="filteredPosts[0].excerpt" class="text-lg lg:text-xl text-gray-600 mb-8 line-clamp-3 leading-relaxed">
              {{ filteredPosts[0].excerpt }}
            </p>

            <!-- Meta & CTA -->
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-6 text-gray-500">
                <time :datetime="filteredPosts[0].published_at" class="flex items-center text-sm">
                  <CalendarIcon class="h-5 w-5 mr-2" />
                  {{ formatDate(filteredPosts[0].published_at) }}
                </time>
                <span v-if="filteredPosts[0].meta.reading_time" class="flex items-center text-sm">
                  <ClockIcon class="h-5 w-5 mr-2" />
                  {{ filteredPosts[0].meta.reading_time }} min read
                </span>
              </div>
              <router-link
                :to="`/blog/${filteredPosts[0].slug}`"
                class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-2xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 text-lg font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1"
              >
                Read Story
                <ArrowRightIcon class="h-5 w-5 ml-3" />
              </router-link>
            </div>
          </div>
        </article>
      </div>

      <!-- Masonry Grid for Remaining Posts -->
      <div v-if="filteredPosts.length > 1" class="masonry-grid gap-8 mb-16">
        <article
          v-for="(post, index) in filteredPosts.slice(1)"
          :key="post.id"
          :class="[
            'group bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden transform hover:-translate-y-2',
            getMasonryClass(index)
          ]"
        >
          <!-- Post Image -->
          <div class="relative overflow-hidden">
            <div :class="getImageHeight(index)">
              <img
                v-if="post.featured_image"
                :src="post.featured_image.medium || post.featured_image.url"
                :alt="post.featured_image.alt || post.title"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                loading="lazy"
              />
              <div v-else class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-300 flex items-center justify-center">
                <DocumentTextIcon class="h-12 w-12 text-gray-400" />
              </div>
            </div>
            
            <!-- Category Badge -->
            <div v-if="post.categories?.length" class="absolute top-4 left-4">
              <span
                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold backdrop-blur-sm bg-white/90 shadow-sm"
                :style="{ color: post.categories[0].color }"
              >
                {{ post.categories[0].name }}
              </span>
            </div>
          </div>
          
          <!-- Post Content -->
          <div class="p-6">
            <!-- Title -->
            <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors line-clamp-2 leading-tight">
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
                {{ post.meta.reading_time }} min
              </span>
            </div>
          </div>
        </article>
      </div>

      <!-- No Posts -->
      <div v-else-if="!blogStore.loading.posts" class="text-center py-20">
        <DocumentTextIcon class="h-20 w-20 text-gray-300 mx-auto mb-6" />
        <h3 class="text-2xl font-semibold text-gray-900 mb-4">No articles found</h3>
        <p class="text-gray-600 text-lg mb-8">
          {{ searchQuery ? `No results for "${searchQuery}"` : 'No posts available yet.' }}
        </p>
        <button
          v-if="searchQuery || selectedCategory"
          @click="clearFilters"
          class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-medium"
        >
          Clear Filters
        </button>
      </div>

      <!-- Enhanced Pagination -->
      <div v-if="blogStore.pagination && blogStore.pagination.last_page > 1" class="mt-16">
        <nav class="flex items-center justify-center space-x-2">
          <button
            v-if="blogStore.pagination.links.prev"
            @click="loadPage(blogStore.currentPage - 1)"
            class="inline-flex items-center px-6 py-3 border border-gray-300 text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 transition-colors shadow-sm"
          >
            <ArrowLeftIcon class="h-4 w-4 mr-2" />
            Previous
          </button>
          
          <!-- Page Numbers -->
          <div class="flex items-center space-x-1">
            <button
              v-for="page in visiblePages"
              :key="page"
              @click="loadPage(page)"
              :class="[
                'px-4 py-2 text-sm font-medium rounded-lg transition-colors',
                page === blogStore.currentPage
                  ? 'bg-blue-600 text-white shadow-md'
                  : 'text-gray-700 hover:bg-gray-100'
              ]"
            >
              {{ page }}
            </button>
          </div>
          
          <button
            v-if="blogStore.pagination.links.next"
            @click="loadPage(blogStore.currentPage + 1)"
            class="inline-flex items-center px-6 py-3 border border-gray-300 text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 transition-colors shadow-sm"
          >
            Next
            <ArrowRightIcon class="h-4 w-4 ml-2" />
          </button>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { useBlogStore } from '@/stores/blog';
import { useCategoryStore } from '@/stores/category';
import { 
  MagnifyingGlassIcon,
  CalendarIcon,
  ClockIcon,
  DocumentTextIcon,
  StarIcon,
  ArrowRightIcon,
  ArrowLeftIcon
} from '@heroicons/vue/24/outline';

const blogStore = useBlogStore();
const categoryStore = useCategoryStore();

// Reactive state
const searchQuery = ref('');
const selectedCategory = ref<any>(null);
const searchTimeout = ref<any>(null);

// Computed properties
const availableCategories = computed(() => categoryStore.categories);

const filteredPosts = computed(() => {
  let posts = blogStore.posts;
  
  // Filter by category
  if (selectedCategory.value) {
    posts = posts.filter(post => 
      post.categories?.some(cat => cat.id === selectedCategory.value.id)
    );
  }
  
  // Filter by search
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase();
    posts = posts.filter(post => 
      post.title.toLowerCase().includes(query) ||
      post.excerpt?.toLowerCase().includes(query) ||
      post.content?.toLowerCase().includes(query) ||
      post.categories?.some(cat => cat.name.toLowerCase().includes(query))
    );
  }
  
  return posts;
});

const visiblePages = computed(() => {
  const current = blogStore.currentPage;
  const total = blogStore.lastPage;
  const delta = 2; // Show 2 pages on each side of current page
  
  let start = Math.max(1, current - delta);
  let end = Math.min(total, current + delta);
  
  // Adjust if we're near the beginning or end
  if (current <= delta) {
    end = Math.min(total, delta * 2 + 1);
  }
  if (current > total - delta) {
    start = Math.max(1, total - delta * 2);
  }
  
  const pages = [];
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  
  return pages;
});

// Methods
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const getMasonryClass = (index: number) => {
  // Create varying heights for masonry effect
  const patterns = [
    'masonry-item-tall',
    'masonry-item-medium',
    'masonry-item-short',
    'masonry-item-medium',
    'masonry-item-tall',
    'masonry-item-short'
  ];
  return patterns[index % patterns.length];
};

const getImageHeight = (index: number) => {
  // Varying image heights for Pinterest-style layout
  const heights = [
    'h-48',  // 192px
    'h-40',  // 160px
    'h-32',  // 128px
    'h-44',  // 176px
    'h-52',  // 208px
    'h-36'   // 144px
  ];
  return heights[index % heights.length];
};

const performSearch = () => {
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value);
  }
  
  searchTimeout.value = setTimeout(() => {
    // Trigger search after 300ms delay for better UX
    if (searchQuery.value.trim()) {
      // Could implement API search here for better performance
      console.log('Searching for:', searchQuery.value);
    }
  }, 300);
};

const filterByCategory = (category: any) => {
  selectedCategory.value = category;
  // Reset to first page when filtering
  if (blogStore.currentPage > 1) {
    loadPage(1);
  }
};

const clearFilters = () => {
  searchQuery.value = '';
  selectedCategory.value = null;
  if (blogStore.currentPage > 1) {
    loadPage(1);
  }
};

const loadPage = async (page: number) => {
  const params: any = { page };
  
  // Add category filter if selected
  if (selectedCategory.value) {
    params.category = selectedCategory.value.slug;
  }
  
  // Add search query if present
  if (searchQuery.value.trim()) {
    params.search = searchQuery.value;
  }
  
  await blogStore.fetchPosts(params);
};

// Lifecycle
onMounted(async () => {
  // Load posts if not already loaded
  if (blogStore.posts.length === 0) {
    await blogStore.fetchPosts();
  }
  
  // Load categories for filtering
  if (categoryStore.categories.length === 0) {
    await categoryStore.fetchCategories();
  }
});

// Watchers
watch(selectedCategory, () => {
  // Could trigger analytics or other side effects
  console.log('Category filter changed:', selectedCategory.value?.name || 'All');
});

watch(searchQuery, () => {
  // Could trigger analytics for search queries
  if (searchQuery.value.trim()) {
    console.log('Search query:', searchQuery.value);
  }
});
</script>

<style scoped>
/* Masonry Grid Layout */
.masonry-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  grid-gap: 2rem;
  grid-auto-rows: max-content;
}

@media (max-width: 640px) {
  .masonry-grid {
    grid-template-columns: 1fr;
    grid-gap: 1.5rem;
  }
}

@media (min-width: 1024px) {
  .masonry-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (min-width: 1280px) {
  .masonry-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

/* Masonry Item Heights */
.masonry-item-short {
  grid-row-end: span 1;
}

.masonry-item-medium {
  grid-row-end: span 2;
}

.masonry-item-tall {
  grid-row-end: span 3;
}

/* Text Clamping */
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

/* Enhanced Animations */
.group:hover .group-hover\:scale-105 {
  transform: scale(1.05);
}

.group:hover .group-hover\:scale-110 {
  transform: scale(1.1);
}

/* Focus States */
.focus\:ring-2:focus {
  ring-width: 2px;
}

.focus\:ring-blue-500:focus {
  ring-color: rgb(59 130 246);
}

.focus\:border-transparent:focus {
  border-color: transparent;
}

/* Smooth Transitions */
* {
  transition-property: transform, box-shadow, background-color, border-color, color, fill, stroke;
  transition-duration: 300ms;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Custom Scrollbar for Search */
input[type="text"]::-webkit-scrollbar {
  display: none;
}

/* Loading Animation Enhancement */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Category Filter Buttons */
.category-filter-enter-active,
.category-filter-leave-active {
  transition: all 0.3s ease;
}

.category-filter-enter-from,
.category-filter-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Search Bar Focus Effect */
.search-input:focus-within {
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Backdrop Blur Support */
@supports (backdrop-filter: blur(10px)) {
  .backdrop-blur-sm {
    backdrop-filter: blur(4px);
  }
}

/* Print Styles */
@media print {
  .masonry-grid {
    display: block;
  }
  
  .masonry-grid article {
    break-inside: avoid;
    margin-bottom: 1rem;
  }
}

/* High Contrast Mode */
@media (prefers-contrast: high) {
  .shadow-lg {
    box-shadow: 0 0 0 2px black;
  }
  
  .text-gray-600 {
    color: black;
  }
}

/* Reduced Motion */
@media (prefers-reduced-motion: reduce) {
  * {
    transition-duration: 0ms !important;
    animation-duration: 0ms !important;
  }
}
</style> 