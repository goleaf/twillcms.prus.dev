<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <section class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center">
          <h1 class="text-5xl lg:text-6xl font-bold text-gray-900 mb-8 leading-tight">
            Article Archive
          </h1>
          <p class="text-xl lg:text-2xl text-gray-600 mb-12 max-w-3xl mx-auto leading-relaxed">
            Explore our complete collection of articles, organized chronologically. 
            Discover insights from {{ totalArticles }} published articles across {{ totalYears }} years.
          </p>

          <!-- Archive Stats -->
          <div class="grid gap-8 md:grid-cols-4 max-w-4xl mx-auto">
            <div class="text-center">
              <div class="text-3xl font-bold text-blue-600 mb-2">{{ totalArticles }}</div>
              <div class="text-sm text-gray-600 uppercase tracking-wide">Total Articles</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold text-indigo-600 mb-2">{{ totalYears }}</div>
              <div class="text-sm text-gray-600 uppercase tracking-wide">Years Published</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold text-amber-600 mb-2">{{ totalCategories }}</div>
              <div class="text-sm text-gray-600 uppercase tracking-wide">Categories</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold text-green-600 mb-2">{{ totalViews }}</div>
              <div class="text-sm text-gray-600 uppercase tracking-wide">Total Views</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Archive Filters -->
    <section class="bg-white py-8 sticky top-0 z-30 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-between gap-6">
          <!-- View Toggle -->
          <div class="flex items-center gap-2">
            <span class="text-sm font-medium text-gray-700">View:</span>
            <div class="flex bg-gray-100 rounded-lg p-1">
              <button
                @click="viewMode = 'timeline'"
                :class="[
                  'px-4 py-2 text-sm font-medium rounded-md transition-all',
                  viewMode === 'timeline'
                    ? 'bg-white text-blue-600 shadow-sm'
                    : 'text-gray-600 hover:text-gray-900'
                ]"
              >
                <ClockIcon class="h-4 w-4 mr-2 inline" />
                Timeline
              </button>
              <button
                @click="viewMode = 'grid'"
                :class="[
                  'px-4 py-2 text-sm font-medium rounded-md transition-all',
                  viewMode === 'grid'
                    ? 'bg-white text-blue-600 shadow-sm'
                    : 'text-gray-600 hover:text-gray-900'
                ]"
              >
                <Squares2X2Icon class="h-4 w-4 mr-2 inline" />
                Grid
              </button>
            </div>
          </div>

          <!-- Year Filter -->
          <div class="flex items-center gap-4">
            <label class="text-sm font-medium text-gray-700">Year:</label>
            <select
              v-model="selectedYear"
              @change="filterByYear"
              class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">All Years</option>
              <option v-for="year in availableYears" :key="year" :value="year">
                {{ year }}
              </option>
            </select>

            <label class="text-sm font-medium text-gray-700">Category:</label>
            <select
              v-model="selectedCategory"
              @change="filterByCategory"
              class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">All Categories</option>
              <option v-for="category in availableCategories" :key="category" :value="category">
                {{ category }}
              </option>
            </select>
          </div>

          <!-- Search in Archive -->
          <div class="relative">
            <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
            <input
              v-model="searchQuery"
              @input="handleSearch"
              type="text"
              placeholder="Search in archive..."
              class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent w-64"
            />
          </div>
        </div>
      </div>
    </section>

    <!-- Archive Content -->
    <section class="py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Timeline View -->
        <div v-if="viewMode === 'timeline'" class="relative">
          <!-- Timeline Line -->
          <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gradient-to-b from-blue-500 to-indigo-600"></div>

          <div class="space-y-12">
            <div
              v-for="(yearGroup, year) in groupedArticles"
              :key="year"
              class="relative"
            >
              <!-- Year Marker -->
              <div class="flex items-center mb-8">
                <div class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full text-white font-bold text-lg shadow-lg relative z-10">
                  {{ year }}
                </div>
                <div class="ml-6">
                  <h2 class="text-3xl font-bold text-gray-900">{{ year }}</h2>
                  <p class="text-gray-600">{{ yearGroup.length }} articles published</p>
                </div>
              </div>

              <!-- Month Groups -->
              <div class="ml-20 space-y-8">
                <div
                  v-for="(monthGroup, month) in groupByMonth(yearGroup)"
                  :key="month"
                  class="relative"
                >
                  <!-- Month Header -->
                  <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-white border-4 border-blue-200 rounded-full flex items-center justify-center shadow-md relative z-10">
                      <CalendarIcon class="h-5 w-5 text-blue-600" />
                    </div>
                    <div class="ml-4">
                      <h3 class="text-xl font-semibold text-gray-900">{{ getMonthName(month) }}</h3>
                      <p class="text-sm text-gray-500">{{ monthGroup.length }} articles</p>
                    </div>
                  </div>

                  <!-- Articles in Month -->
                  <div class="ml-16 space-y-6">
                    <article
                      v-for="article in monthGroup"
                      :key="article.id"
                      class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden group border border-gray-100"
                    >
                      <div class="p-6">
                        <div class="flex items-start justify-between">
                          <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                              <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                                {{ article.category }}
                              </span>
                              <span class="text-sm text-gray-500">
                                {{ formatDate(article.published_at) }}
                              </span>
                              <span class="text-sm text-gray-500">
                                {{ article.reading_time }} min read
                              </span>
                            </div>

                            <h4 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                              <router-link :to="`/blog/${article.slug}`">
                                {{ article.title }}
                              </router-link>
                            </h4>

                            <p class="text-gray-600 text-sm leading-relaxed mb-4">
                              {{ article.excerpt }}
                            </p>

                            <div class="flex items-center justify-between">
                              <div class="flex items-center gap-2">
                                <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                                  <UserIcon class="h-3 w-3 text-white" />
                                </div>
                                <span class="text-sm text-gray-600">{{ article.author }}</span>
                              </div>

                              <router-link
                                :to="`/blog/${article.slug}`"
                                class="inline-flex items-center text-blue-600 hover:text-blue-700 text-sm font-medium group"
                              >
                                Read Article
                                <ArrowRightIcon class="ml-1 h-3 w-3 group-hover:translate-x-1 transition-transform" />
                              </router-link>
                            </div>
                          </div>
                        </div>
                      </div>
                    </article>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Grid View -->
        <div v-else class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
          <article
            v-for="article in filteredArticles"
            :key="article.id"
            class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden group"
          >
            <div class="h-48 relative overflow-hidden">
              <img
                v-if="article.featured_image"
                :src="article.featured_image"
                :alt="article.title"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
              <div v-else class="w-full h-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                <DocumentTextIcon class="h-12 w-12 text-white opacity-50" />
              </div>
            </div>

            <div class="p-6">
              <div class="flex items-center gap-3 mb-3">
                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                  {{ article.category }}
                </span>
                <span class="text-sm text-gray-500">
                  {{ formatDate(article.published_at) }}
                </span>
              </div>

              <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                <router-link :to="`/blog/${article.slug}`">
                  {{ article.title }}
                </router-link>
              </h3>

              <p class="text-gray-600 text-sm leading-relaxed mb-4">
                {{ article.excerpt }}
              </p>

              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                    <UserIcon class="h-3 w-3 text-white" />
                  </div>
                  <span class="text-sm text-gray-600">{{ article.author }}</span>
                </div>

                <span class="text-xs text-gray-500">{{ article.reading_time }} min</span>
              </div>
            </div>
          </article>
        </div>

        <!-- Empty State -->
        <div v-if="filteredArticles.length === 0" class="text-center py-20">
          <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <ArchiveBoxIcon class="h-10 w-10 text-gray-400" />
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-4">No articles found</h3>
          <p class="text-gray-600 mb-8 max-w-md mx-auto">
            No articles match your current filters. Try adjusting your search criteria.
          </p>
          <button
            @click="clearFilters"
            class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors"
          >
            Clear All Filters
          </button>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import {
  ClockIcon,
  Squares2X2Icon,
  CalendarIcon,
  MagnifyingGlassIcon,
  UserIcon,
  ArrowRightIcon,
  DocumentTextIcon,
  ArchiveBoxIcon
} from '@heroicons/vue/24/outline';

// State
const viewMode = ref('timeline');
const selectedYear = ref('');
const selectedCategory = ref('');
const searchQuery = ref('');
const articles = ref([]);

// Mock data - replace with actual API data
const mockArticles = [
  {
    id: 1,
    title: 'Getting Started with Vue.js 3',
    slug: 'getting-started-vuejs-3',
    excerpt: 'Learn the fundamentals of Vue.js 3 and its Composition API with practical examples.',
    category: 'Frontend',
    author: 'John Smith',
    published_at: '2024-01-15T10:00:00Z',
    reading_time: 8,
    featured_image: null
  },
  {
    id: 2,
    title: 'Advanced TypeScript Patterns',
    slug: 'advanced-typescript-patterns',
    excerpt: 'Explore advanced TypeScript patterns and techniques for better type safety.',
    category: 'Development',
    author: 'Sarah Johnson',
    published_at: '2024-01-10T14:30:00Z',
    reading_time: 12,
    featured_image: null
  },
  {
    id: 3,
    title: 'Building RESTful APIs with Laravel',
    slug: 'building-restful-apis-laravel',
    excerpt: 'Complete guide to building robust RESTful APIs using Laravel framework.',
    category: 'Backend',
    author: 'Mike Wilson',
    published_at: '2023-12-20T09:15:00Z',
    reading_time: 15,
    featured_image: null
  },
  {
    id: 4,
    title: 'CSS Grid Layout Mastery',
    slug: 'css-grid-layout-mastery',
    excerpt: 'Master CSS Grid Layout with practical examples and advanced techniques.',
    category: 'Design',
    author: 'Emily Chen',
    published_at: '2023-11-28T16:45:00Z',
    reading_time: 10,
    featured_image: null
  },
  {
    id: 5,
    title: 'Docker for Developers',
    slug: 'docker-for-developers',
    excerpt: 'Learn how to use Docker to streamline your development workflow.',
    category: 'DevOps',
    author: 'Alex Rodriguez',
    published_at: '2023-10-15T11:20:00Z',
    reading_time: 18,
    featured_image: null
  }
];

// Computed properties
const totalArticles = computed(() => articles.value.length);
const totalYears = computed(() => new Set(articles.value.map(article => new Date(article.published_at).getFullYear())).size);
const totalCategories = computed(() => new Set(articles.value.map(article => article.category)).size);
const totalViews = computed(() => '125K'); // Mock value

const availableYears = computed(() => {
  const years = articles.value.map(article => new Date(article.published_at).getFullYear());
  return [...new Set(years)].sort((a, b) => b - a);
});

const availableCategories = computed(() => {
  const categories = articles.value.map(article => article.category);
  return [...new Set(categories)].sort();
});

const filteredArticles = computed(() => {
  let filtered = articles.value;

  if (selectedYear.value) {
    filtered = filtered.filter(article => 
      new Date(article.published_at).getFullYear() === parseInt(selectedYear.value)
    );
  }

  if (selectedCategory.value) {
    filtered = filtered.filter(article => article.category === selectedCategory.value);
  }

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(article =>
      article.title.toLowerCase().includes(query) ||
      article.excerpt.toLowerCase().includes(query) ||
      article.category.toLowerCase().includes(query) ||
      article.author.toLowerCase().includes(query)
    );
  }

  return filtered.sort((a, b) => new Date(b.published_at).getTime() - new Date(a.published_at).getTime());
});

const groupedArticles = computed(() => {
  const grouped = {};
  
  filteredArticles.value.forEach(article => {
    const year = new Date(article.published_at).getFullYear();
    if (!grouped[year]) {
      grouped[year] = [];
    }
    grouped[year].push(article);
  });

  // Sort years in descending order
  const sortedGrouped = {};
  Object.keys(grouped)
    .sort((a, b) => parseInt(b) - parseInt(a))
    .forEach(year => {
      sortedGrouped[year] = grouped[year];
    });

  return sortedGrouped;
});

// Methods
const groupByMonth = (yearArticles) => {
  const grouped = {};
  
  yearArticles.forEach(article => {
    const month = new Date(article.published_at).getMonth();
    if (!grouped[month]) {
      grouped[month] = [];
    }
    grouped[month].push(article);
  });

  // Sort months in descending order
  const sortedGrouped = {};
  Object.keys(grouped)
    .sort((a, b) => parseInt(b) - parseInt(a))
    .forEach(month => {
      sortedGrouped[month] = grouped[month].sort((a, b) => 
        new Date(b.published_at).getTime() - new Date(a.published_at).getTime()
      );
    });

  return sortedGrouped;
};

const getMonthName = (monthIndex) => {
  const months = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ];
  return months[monthIndex];
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const filterByYear = () => {
  // Filtering is handled by computed property
};

const filterByCategory = () => {
  // Filtering is handled by computed property
};

const handleSearch = () => {
  // Searching is handled by computed property
};

const clearFilters = () => {
  selectedYear.value = '';
  selectedCategory.value = '';
  searchQuery.value = '';
};

// Lifecycle
onMounted(() => {
  articles.value = mockArticles;
});
</script>

<style scoped>
/* Timeline animations */
.relative::before {
  content: '';
  position: absolute;
  left: -8px;
  top: 0;
  bottom: 0;
  width: 1px;
  background: linear-gradient(to bottom, #3b82f6, #6366f1);
}

/* Smooth transitions */
* {
  transition-property: all;
  transition-duration: 200ms;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Sticky header */
.sticky {
  backdrop-filter: blur(10px);
  background-color: rgba(255, 255, 255, 0.95);
}

/* Timeline hover effects */
.group:hover {
  transform: translateY(-2px);
}
</style> 