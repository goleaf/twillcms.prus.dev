<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 py-24">
      <div class="absolute inset-0 bg-black/20"></div>
      <div class="absolute inset-0 opacity-10">
        <div class="absolute top-16 left-16 w-36 h-36 bg-white rounded-full blur-2xl"></div>
        <div class="absolute bottom-16 right-16 w-28 h-28 bg-white rounded-full blur-xl"></div>
      </div>
      
      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur-md rounded-2xl mb-8">
          <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
        </div>
        
        <h1 class="text-5xl sm:text-6xl font-black text-white mb-6">
          {{ $t('archive.title') }}
        </h1>
        
        <p class="text-xl sm:text-2xl text-white/90 max-w-3xl mx-auto mb-12 leading-relaxed">
          Explore our comprehensive collection of stories, organized by time and topic. 
          <span class="font-semibold">Discover insights from the past to understand the future.</span>
        </p>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-2xl mx-auto">
          <div class="text-center">
            <div class="text-3xl font-bold text-white mb-2">{{ totalPosts }}+</div>
            <div class="text-white/70 text-sm font-medium">Total Articles</div>
          </div>
          <div class="text-center">
            <div class="text-3xl font-bold text-white mb-2">{{ archiveYears.length }}</div>
            <div class="text-white/70 text-sm font-medium">Years Covered</div>
          </div>
          <div class="text-center">
            <div class="text-3xl font-bold text-white mb-2">{{ totalCategories }}</div>
            <div class="text-white/70 text-sm font-medium">Categories</div>
          </div>
          <div class="text-center">
            <div class="text-3xl font-bold text-white mb-2">24/7</div>
            <div class="text-white/70 text-sm font-medium">Updated</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <!-- Filters & Search -->
      <div class="mb-12">
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-soft border border-slate-200 dark:border-slate-700">
          <div class="flex flex-col lg:flex-row gap-6 items-center">
            <!-- Search -->
            <div class="flex-1 relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
              </div>
              <input 
                v-model="searchQuery"
                type="text" 
                placeholder="Search archives by title, content, or category..."
                class="block w-full pl-12 pr-4 py-4 border-0 rounded-xl bg-slate-50 dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-lg"
              />
            </div>
            
            <!-- Year Filter -->
            <select 
              v-model="selectedYear" 
              class="px-6 py-4 bg-slate-50 dark:bg-slate-700 border-0 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 font-medium"
            >
              <option value="">All Years</option>
              <option v-for="year in archiveYears" :key="year" :value="year">{{ year }}</option>
            </select>
            
            <!-- View Toggle -->
            <div class="flex items-center bg-slate-100 dark:bg-slate-700 rounded-xl p-1">
              <button 
                @click="viewMode = 'grid'"
                :class="[
                  'flex items-center gap-2 px-4 py-3 rounded-lg font-medium transition-all duration-300',
                  viewMode === 'grid' 
                    ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-md' 
                    : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Grid
              </button>
              <button 
                @click="viewMode = 'timeline'"
                :class="[
                  'flex items-center gap-2 px-4 py-3 rounded-lg font-medium transition-all duration-300',
                  viewMode === 'timeline' 
                    ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-md' 
                    : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Timeline
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div v-for="i in 9" :key="i" class="bg-white dark:bg-slate-800 rounded-2xl p-6 animate-pulse">
          <div class="bg-slate-300 dark:bg-slate-600 h-48 rounded-xl mb-4"></div>
          <div class="space-y-3">
            <div class="h-4 bg-slate-300 dark:bg-slate-600 rounded w-3/4"></div>
            <div class="h-4 bg-slate-300 dark:bg-slate-600 rounded w-full"></div>
            <div class="h-4 bg-slate-300 dark:bg-slate-600 rounded w-1/2"></div>
          </div>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center py-16">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 dark:bg-red-900/20 rounded-2xl mb-8">
          <svg class="w-10 h-10 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Unable to Load Archives</h3>
        <p class="text-slate-600 dark:text-slate-400 mb-8">{{ error }}</p>
        <button @click="loadArchives" class="btn-primary">
          Try Again
        </button>
      </div>

      <!-- Content Display -->
      <div v-else>
        <!-- Grid View -->
        <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <article 
            v-for="post in filteredPosts" 
            :key="post.id"
            class="group bg-white dark:bg-slate-800 rounded-2xl overflow-hidden shadow-soft hover:shadow-strong transition-all duration-300 hover:scale-[1.02] border border-slate-200 dark:border-slate-700"
          >
            <div class="relative">
              <img 
                v-if="post.featured_image" 
                :src="post.featured_image" 
                :alt="post.title"
                class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500"
              />
              <div v-else class="w-full h-48 bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-700 dark:to-slate-800 flex items-center justify-center">
                <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
              </div>
              
              <!-- Date Badge -->
              <div class="absolute top-4 left-4">
                <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-semibold text-slate-900 dark:text-white">
                  {{ formatDate(post.published_at) }}
                </div>
              </div>
              
              <!-- Bookmark -->
              <div class="absolute top-4 right-4">
                <button class="p-2 bg-white/20 backdrop-blur-md rounded-full text-white hover:bg-white/30 transition-colors duration-300">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                  </svg>
                </button>
              </div>
            </div>
            
            <div class="p-6">
              <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400 mb-4">
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                  {{ post.reading_time || 5 }} min read
                </span>
                <span class="w-1 h-1 bg-slate-400 rounded-full"></span>
                <span>{{ formatCategory(post.categories) }}</span>
              </div>
              
              <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4 leading-tight group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300 line-clamp-2">
                <router-link :to="{ name: 'post.detail', params: { slug: post.slug } }">
                  {{ post.title }}
                </router-link>
              </h3>
              
              <p class="text-slate-600 dark:text-slate-300 mb-6 leading-relaxed line-clamp-3">
                {{ post.excerpt || post.description }}
              </p>
              
              <div class="flex items-center justify-between">
                <div class="flex gap-2">
                  <span 
                    v-for="category in (post.categories || []).slice(0, 2)" 
                    :key="category.id"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300"
                  >
                    {{ category.title || category.name }}
                  </span>
                </div>
                <router-link 
                  :to="{ name: 'post.detail', params: { slug: post.slug } }"
                  class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold"
                >
                  Read More →
                </router-link>
              </div>
            </div>
          </article>
        </div>

        <!-- Timeline View -->
        <div v-else-if="viewMode === 'timeline'" class="space-y-12">
          <div v-for="yearGroup in timelineData" :key="yearGroup.year" class="relative">
            <!-- Year Header -->
            <div class="sticky top-24 z-10 mb-8">
              <div class="inline-flex items-center bg-white dark:bg-slate-800 px-8 py-4 rounded-2xl shadow-soft border border-slate-200 dark:border-slate-700">
                <div class="w-6 h-6 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full mr-4"></div>
                <h2 class="text-3xl font-bold text-slate-900 dark:text-white">{{ yearGroup.year }}</h2>
                <span class="ml-4 text-lg text-slate-500 dark:text-slate-400">{{ yearGroup.posts.length }} articles</span>
              </div>
            </div>
            
            <!-- Posts Timeline -->
            <div class="relative pl-8">
              <!-- Timeline Line -->
              <div class="absolute left-4 top-0 bottom-0 w-px bg-gradient-to-b from-indigo-500 to-purple-500"></div>
              
              <div class="space-y-8">
                <div 
                  v-for="post in yearGroup.posts" 
                  :key="post.id"
                  class="relative group"
                >
                  <!-- Timeline Dot -->
                  <div class="absolute -left-[1.125rem] top-6 w-4 h-4 bg-white dark:bg-slate-800 border-4 border-indigo-500 rounded-full shadow-lg group-hover:border-purple-500 transition-colors duration-300"></div>
                  
                  <!-- Post Card -->
                  <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-soft hover:shadow-strong transition-all duration-300 hover:scale-[1.01] border border-slate-200 dark:border-slate-700 ml-8">
                    <div class="flex flex-col lg:flex-row gap-8">
                      <!-- Image -->
                      <div class="lg:w-1/3">
                        <div class="relative">
                          <img 
                            v-if="post.featured_image" 
                            :src="post.featured_image" 
                            :alt="post.title"
                            class="w-full h-48 lg:h-32 object-cover rounded-xl group-hover:scale-105 transition-transform duration-500"
                          />
                          <div v-else class="w-full h-48 lg:h-32 bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-700 dark:to-slate-800 rounded-xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                          </div>
                        </div>
                      </div>
                      
                      <!-- Content -->
                      <div class="lg:w-2/3">
                        <div class="flex items-center gap-4 text-sm text-slate-500 dark:text-slate-400 mb-4">
                          <time :datetime="post.published_at">
                            {{ formatDetailedDate(post.published_at) }}
                          </time>
                          <span class="w-1 h-1 bg-slate-400 rounded-full"></span>
                          <span>{{ post.reading_time || 5 }} min read</span>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4 leading-tight group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300">
                          <router-link :to="{ name: 'post.detail', params: { slug: post.slug } }">
                            {{ post.title }}
                          </router-link>
                        </h3>
                        
                        <p class="text-slate-600 dark:text-slate-300 mb-6 leading-relaxed line-clamp-2">
                          {{ post.excerpt || post.description }}
                        </p>
                        
                        <div class="flex items-center justify-between">
                          <div class="flex gap-2">
                            <span 
                              v-for="category in (post.categories || []).slice(0, 2)" 
                              :key="category.id"
                              class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300"
                            >
                              {{ category.title || category.name }}
                            </span>
                          </div>
                          <router-link 
                            :to="{ name: 'post.detail', params: { slug: post.slug } }"
                            class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold group-hover:gap-3 transition-all duration-300"
                          >
                            Read Full Article
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                          </router-link>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredPosts.length === 0" class="text-center py-16">
          <div class="inline-flex items-center justify-center w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-2xl mb-8">
            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">No Articles Found</h3>
          <p class="text-slate-600 dark:text-slate-400 mb-8">Try adjusting your search terms or filters to find what you're looking for.</p>
          <button @click="clearFilters" class="btn-primary">
            Clear Filters
          </button>
        </div>

        <!-- Load More -->
        <div v-if="filteredPosts.length > 0 && hasMore" class="text-center mt-16">
          <button 
            @click="loadMore"
            :disabled="loadingMore"
            class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 disabled:from-slate-400 disabled:to-slate-500 text-white px-12 py-4 rounded-2xl font-bold text-lg transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl disabled:cursor-not-allowed disabled:transform-none"
          >
            <span v-if="loadingMore" class="flex items-center gap-3">
              <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
              </svg>
              Loading More...
            </span>
            <span v-else>Load More Articles</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'

// Reactive state
const loading = ref(true)
const loadingMore = ref(false)
const error = ref(null)
const posts = ref([])
const searchQuery = ref('')
const selectedYear = ref('')
const viewMode = ref('grid')
const currentPage = ref(1)
const hasMore = ref(true)

// Computed properties
const archiveYears = computed(() => {
  const years = [...new Set(posts.value.map(post => new Date(post.published_at).getFullYear()))]
  return years.sort((a, b) => b - a)
})

const totalPosts = computed(() => posts.value.length)
const totalCategories = computed(() => {
  const categories = new Set()
  posts.value.forEach(post => {
    if (post.categories) {
      post.categories.forEach(cat => categories.add(cat.id))
    }
  })
  return categories.size
})

const filteredPosts = computed(() => {
  let filtered = posts.value

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(post => 
      post.title.toLowerCase().includes(query) ||
      (post.excerpt || post.description || '').toLowerCase().includes(query) ||
      (post.categories || []).some(cat => 
        (cat.title || cat.name || '').toLowerCase().includes(query)
      )
    )
  }

  if (selectedYear.value) {
    filtered = filtered.filter(post => 
      new Date(post.published_at).getFullYear() === parseInt(selectedYear.value)
    )
  }

  return filtered.sort((a, b) => new Date(b.published_at) - new Date(a.published_at))
})

const timelineData = computed(() => {
  const grouped = {}
  filteredPosts.value.forEach(post => {
    const year = new Date(post.published_at).getFullYear()
    if (!grouped[year]) {
      grouped[year] = []
    }
    grouped[year].push(post)
  })

  return Object.keys(grouped)
    .sort((a, b) => b - a)
    .map(year => ({
      year: parseInt(year),
      posts: grouped[year]
    }))
})

// Lifecycle
onMounted(async () => {
  await loadArchives()
})

// Watchers
watch([searchQuery, selectedYear], () => {
  currentPage.value = 1
})

// Methods
const loadArchives = async () => {
  loading.value = true
  error.value = null
  
  try {
    // Fetch real data from API
    const response = await fetch('/api/v1/posts?per_page=50')
    const data = await response.json()
    
    if (!response.ok) {
      throw new Error(data.message || 'Failed to fetch posts')
    }
    
    // Transform posts data
    if (data.data) {
      posts.value = data.data.map(post => ({
        id: post.id,
        title: post.title,
        slug: post.slug,
        excerpt: post.excerpt || post.description,
        featured_image: post.featured_image || post.image_url,
        published_at: post.published_at,
        reading_time: post.reading_time || 5,
        categories: post.categories || []
      }))
    }
    
  } catch (err) {
    console.error('Error loading archives:', err)
    error.value = 'Failed to load archives. Please try again.'
  } finally {
    loading.value = false
  }
}

const loadMore = async () => {
  loadingMore.value = true
  
  // Simulate loading more posts
  await new Promise(resolve => setTimeout(resolve, 1000))
  
  // In a real app, you would load more posts from API
  hasMore.value = false // For demo purposes
  loadingMore.value = false
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const formatDetailedDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatCategory = (categories) => {
  if (!categories || categories.length === 0) return 'General'
  return categories[0].title || categories[0].name || 'General'
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedYear.value = ''
}
</script> 

<style scoped>
.btn-primary {
  @apply bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-8 py-4 rounded-2xl font-bold transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl;
}

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
</style> 