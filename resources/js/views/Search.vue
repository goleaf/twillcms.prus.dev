<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <section class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center">
          <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
            Search Results
          </h1>
          <p class="text-xl text-gray-600 mb-8">
            {{ searchResults.length }} results found for "{{ query }}"
          </p>

          <!-- Enhanced Search Bar -->
          <div class="max-w-2xl mx-auto relative">
            <div class="relative">
              <MagnifyingGlassIcon class="absolute left-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
              <input
                v-model="searchQuery"
                @input="performSearch"
                type="text"
                placeholder="Search articles, topics, or authors..."
                class="w-full pl-12 pr-4 py-4 text-lg border border-gray-300 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
              />
              <button
                v-if="searchQuery"
                @click="clearSearch"
                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
              >
                <XMarkIcon class="h-5 w-5" />
              </button>
            </div>
          </div>

          <!-- Search Filters -->
          <div class="flex flex-wrap justify-center gap-4 mt-8">
            <button
              v-for="filter in searchFilters"
              :key="filter.key"
              @click="toggleFilter(filter.key)"
              :class="[
                'px-6 py-2 rounded-full text-sm font-medium transition-all duration-200',
                activeFilters.includes(filter.key)
                  ? 'bg-blue-600 text-white shadow-lg'
                  : 'bg-white text-gray-700 border border-gray-300 hover:border-blue-400 hover:text-blue-600'
              ]"
            >
              {{ filter.label }}
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Search Results -->
    <section class="py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Loading State -->
        <div v-if="isLoading" class="text-center py-20">
          <div class="inline-flex items-center">
            <svg class="animate-spin h-8 w-8 text-blue-600 mr-3" viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" opacity="0.25"/>
              <path fill="currentColor" opacity="0.75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
            </svg>
            <span class="text-lg text-gray-600">Searching...</span>
          </div>
        </div>

        <!-- No Results -->
        <div v-else-if="searchResults.length === 0 && hasSearched" class="text-center py-20">
          <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <MagnifyingGlassIcon class="h-10 w-10 text-gray-400" />
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-4">No results found</h3>
          <p class="text-gray-600 mb-8 max-w-md mx-auto">
            We couldn't find any articles matching your search. Try different keywords or browse our categories.
          </p>
          <router-link
            to="/blog"
            class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors"
          >
            Browse All Articles
          </router-link>
        </div>

        <!-- Results Grid -->
        <div v-else-if="searchResults.length > 0" class="space-y-8">
          <!-- Results Summary -->
          <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="text-gray-600">
              Showing {{ startIndex + 1 }}-{{ Math.min(startIndex + perPage, searchResults.length) }} of {{ searchResults.length }} results
            </div>
            <div class="flex items-center gap-4">
              <label class="text-sm text-gray-600">Sort by:</label>
              <select
                v-model="sortBy"
                @change="applySorting"
                class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="relevance">Relevance</option>
                <option value="date">Date (Newest)</option>
                <option value="title">Title (A-Z)</option>
                <option value="views">Most Popular</option>
              </select>
            </div>
          </div>

          <!-- Search Results Cards -->
          <div class="grid gap-8">
            <article
              v-for="post in paginatedResults"
              :key="post.id"
              class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden group"
            >
              <div class="lg:flex">
                <!-- Featured Image -->
                <div class="lg:w-1/3">
                  <div class="h-64 lg:h-full relative overflow-hidden">
                    <img
                      v-if="post.featured_image"
                      :src="post.featured_image"
                      :alt="post.title"
                      class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    />
                    <div v-else class="w-full h-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                      <DocumentTextIcon class="h-16 w-16 text-white opacity-50" />
                    </div>
                  </div>
                </div>

                <!-- Content -->
                <div class="lg:w-2/3 p-8">
                  <div class="flex items-center gap-4 mb-4">
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                      {{ post.category?.name || 'Uncategorized' }}
                    </span>
                    <span class="text-sm text-gray-500">
                      {{ formatDate(post.published_at) }}
                    </span>
                    <span class="text-sm text-gray-500">
                      {{ post.reading_time }} min read
                    </span>
                  </div>

                  <h2 class="text-2xl font-bold text-gray-900 mb-4 group-hover:text-blue-600 transition-colors">
                    <router-link :to="`/blog/${post.slug}`">
                      {{ post.title }}
                    </router-link>
                  </h2>

                  <p class="text-gray-600 mb-6 leading-relaxed">
                    {{ post.excerpt || post.content?.substring(0, 200) + '...' }}
                  </p>

                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                        <UserIcon class="h-5 w-5 text-white" />
                      </div>
                      <div>
                        <div class="font-medium text-gray-900">{{ post.author_name || 'Admin' }}</div>
                        <div class="text-sm text-gray-500">Author</div>
                      </div>
                    </div>

                    <router-link
                      :to="`/blog/${post.slug}`"
                      class="inline-flex items-center px-6 py-2 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors group"
                    >
                      Read More
                      <ArrowRightIcon class="ml-2 h-4 w-4 group-hover:translate-x-1 transition-transform" />
                    </router-link>
                  </div>
                </div>
              </div>
            </article>
          </div>

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="flex justify-center mt-12">
            <nav class="flex items-center gap-2">
              <button
                @click="goToPage(currentPage - 1)"
                :disabled="currentPage <= 1"
                :class="[
                  'px-4 py-2 rounded-lg border transition-colors',
                  currentPage <= 1
                    ? 'border-gray-200 text-gray-400 cursor-not-allowed'
                    : 'border-gray-300 text-gray-700 hover:bg-gray-50'
                ]"
              >
                Previous
              </button>

              <button
                v-for="page in visiblePages"
                :key="page"
                @click="goToPage(page)"
                :class="[
                  'px-4 py-2 rounded-lg border transition-colors',
                  page === currentPage
                    ? 'bg-blue-600 border-blue-600 text-white'
                    : 'border-gray-300 text-gray-700 hover:bg-gray-50'
                ]"
              >
                {{ page }}
              </button>

              <button
                @click="goToPage(currentPage + 1)"
                :disabled="currentPage >= totalPages"
                :class="[
                  'px-4 py-2 rounded-lg border transition-colors',
                  currentPage >= totalPages
                    ? 'border-gray-200 text-gray-400 cursor-not-allowed'
                    : 'border-gray-300 text-gray-700 hover:bg-gray-50'
                ]"
              >
                Next
              </button>
            </nav>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import {
  MagnifyingGlassIcon,
  XMarkIcon,
  DocumentTextIcon,
  UserIcon,
  ArrowRightIcon
} from '@heroicons/vue/24/outline';

const route = useRoute();
const router = useRouter();

// State
const searchQuery = ref('');
const searchResults = ref([]);
const isLoading = ref(false);
const hasSearched = ref(false);
const activeFilters = ref([]);
const sortBy = ref('relevance');
const currentPage = ref(1);
const perPage = 10;

// Search filters
const searchFilters = [
  { key: 'recent', label: 'Recent Posts' },
  { key: 'popular', label: 'Popular' },
  { key: 'featured', label: 'Featured' },
  { key: 'technology', label: 'Technology' },
  { key: 'design', label: 'Design' },
  { key: 'business', label: 'Business' }
];

// Computed properties
const query = computed(() => searchQuery.value);

const paginatedResults = computed(() => {
  const start = (currentPage.value - 1) * perPage;
  const end = start + perPage;
  return searchResults.value.slice(start, end);
});

const totalPages = computed(() => Math.ceil(searchResults.value.length / perPage));

const startIndex = computed(() => (currentPage.value - 1) * perPage);

const visiblePages = computed(() => {
  const delta = 2;
  const range = [];
  const rangeWithDots = [];

  for (let i = Math.max(2, currentPage.value - delta); 
       i <= Math.min(totalPages.value - 1, currentPage.value + delta); 
       i++) {
    range.push(i);
  }

  if (currentPage.value - delta > 2) {
    rangeWithDots.push(1, '...');
  } else {
    rangeWithDots.push(1);
  }

  rangeWithDots.push(...range);

  if (currentPage.value + delta < totalPages.value - 1) {
    rangeWithDots.push('...', totalPages.value);
  } else {
    rangeWithDots.push(totalPages.value);
  }

  return rangeWithDots.filter((v, i, a) => a.indexOf(v) === i);
});

// Methods
const performSearch = async () => {
  if (searchQuery.value.trim().length < 2) return;

  isLoading.value = true;
  hasSearched.value = true;

  try {
    // Simulate API call - replace with actual API
    await new Promise(resolve => setTimeout(resolve, 800));
    
    // Mock search results
    searchResults.value = generateMockResults(searchQuery.value);
  } catch (error) {
    console.error('Search error:', error);
    searchResults.value = [];
  } finally {
    isLoading.value = false;
  }
};

const generateMockResults = (query) => {
  // Mock data - replace with actual API results
  return [
    {
      id: 1,
      title: `Understanding ${query} in Modern Development`,
      slug: 'understanding-modern-development',
      excerpt: `Comprehensive guide to ${query} and its applications in today's technology landscape.`,
      content: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit...',
      featured_image: null,
      published_at: '2024-01-15T10:00:00Z',
      reading_time: 8,
      author_name: 'John Smith',
      category: { name: 'Technology' }
    },
    {
      id: 2,
      title: `Best Practices for ${query} Implementation`,
      slug: 'best-practices-implementation',
      excerpt: `Learn the most effective strategies for implementing ${query} in your projects.`,
      content: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit...',
      featured_image: null,
      published_at: '2024-01-10T14:30:00Z',
      reading_time: 12,
      author_name: 'Sarah Johnson',
      category: { name: 'Development' }
    }
  ];
};

const toggleFilter = (filterKey) => {
  const index = activeFilters.value.indexOf(filterKey);
  if (index > -1) {
    activeFilters.value.splice(index, 1);
  } else {
    activeFilters.value.push(filterKey);
  }
  applyFilters();
};

const applyFilters = () => {
  // Apply active filters to search results
  console.log('Applying filters:', activeFilters.value);
};

const applySorting = () => {
  // Apply sorting to search results
  console.log('Applying sort:', sortBy.value);
};

const clearSearch = () => {
  searchQuery.value = '';
  searchResults.value = [];
  hasSearched.value = false;
  activeFilters.value = [];
  currentPage.value = 1;
};

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

// Watch for route changes
watch(() => route.query.q, (newQuery) => {
  if (newQuery) {
    searchQuery.value = newQuery;
    performSearch();
  }
}, { immediate: true });

onMounted(() => {
  // Get search query from URL if present
  if (route.query.q) {
    searchQuery.value = route.query.q;
    performSearch();
  }
});
</script>

<style scoped>
/* Loading spinner */
@keyframes spin {
  to { transform: rotate(360deg); }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Smooth transitions */
* {
  transition-property: all;
  transition-duration: 200ms;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Search input focus effects */
input:focus {
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style> 