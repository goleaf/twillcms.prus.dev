<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-600 py-24">
      <div class="absolute inset-0 bg-black/20"></div>
      <div class="absolute inset-0 opacity-10">
        <div class="absolute top-20 left-20 w-40 h-40 bg-white rounded-full blur-2xl"></div>
        <div class="absolute bottom-20 right-20 w-32 h-32 bg-white rounded-full blur-2xl"></div>
        <div class="absolute top-1/2 left-1/2 w-24 h-24 bg-white rounded-full blur-xl"></div>
      </div>
      
      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur-md rounded-2xl mb-8">
          <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-4a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2m14 0V6a2 2 0 00-2-2H7a2 2 0 00-2 2v1m14 0H5"/>
          </svg>
        </div>
        
        <h1 class="text-5xl sm:text-6xl font-black text-white mb-6">
          {{ $t('categories.categories') }}
        </h1>
        
        <p class="text-xl sm:text-2xl text-white/90 max-w-3xl mx-auto mb-12 leading-relaxed">
          Discover stories organized by topics that matter to you. From technology to culture, find your interests and dive deep into expertly curated content.
        </p>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-2xl mx-auto">
          <div class="text-center">
            <div class="text-3xl font-bold text-white mb-2">{{ totalCategories }}</div>
            <div class="text-white/70 text-sm font-medium">{{ $t('categories.categories') }}</div>
          </div>
          <div class="text-center">
            <div class="text-3xl font-bold text-white mb-2">{{ totalPosts }}</div>
            <div class="text-white/70 text-sm font-medium">{{ $t('categories.total_posts') }}</div>
          </div>
          <div class="text-center">
            <div class="text-3xl font-bold text-white mb-2">{{ averagePostsPerCategory }}</div>
            <div class="text-white/70 text-sm font-medium">{{ $t('categories.avg_posts') }}</div>
          </div>
          <div class="text-center">
            <div class="text-3xl font-bold text-white mb-2">{{ featuredCategories.length }}</div>
            <div class="text-white/70 text-sm font-medium">{{ $t('categories.featured') }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <!-- Search & Filters -->
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
                placeholder="Search categories by name or description..."
                class="block w-full pl-12 pr-4 py-4 border-0 rounded-xl bg-slate-50 dark:bg-slate-700 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-lg"
              />
            </div>
            
            <!-- Sort Options -->
            <select 
              v-model="sortBy" 
              class="px-6 py-4 bg-slate-50 dark:bg-slate-700 border-0 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium"
            >
              <option value="posts_count">{{ $t('categories.most_popular') }}</option>
              <option value="name">{{ $t('categories.alphabetical') }}</option>
              <option value="recent">{{ $t('categories.most_recent') }}</option>
            </select>
            
            <!-- View Toggle -->
            <div class="flex items-center bg-slate-100 dark:bg-slate-700 rounded-xl p-1">
              <button 
                @click="viewMode = 'grid'"
                :class="[
                  'flex items-center gap-2 px-4 py-3 rounded-lg font-medium transition-all duration-300',
                  viewMode === 'grid' 
                    ? 'bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-md' 
                    : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/>
                </svg>
                Grid
              </button>
              <button 
                @click="viewMode = 'list'"
                :class="[
                  'flex items-center gap-2 px-4 py-3 rounded-lg font-medium transition-all duration-300',
                  viewMode === 'list' 
                    ? 'bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-md' 
                    : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                ]"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                List
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Featured Categories -->
      <div v-if="featuredCategories.length > 0" class="mb-16">
        <div class="flex items-center justify-between mb-8">
          <h2 class="text-3xl font-bold text-slate-900 dark:text-white">{{ $t('categories.popular_categories') }}</h2>
          <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
            <span class="font-medium">{{ $t('categories.editor_choice') }}</span>
          </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
          <div 
            v-for="category in featuredCategories" 
            :key="category.id"
            class="group relative overflow-hidden bg-gradient-to-br from-white to-slate-50 dark:from-slate-800 dark:to-slate-900 rounded-2xl shadow-soft hover:shadow-strong transition-all duration-300 hover:scale-[1.02] border border-slate-200 dark:border-slate-700"
          >
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-5">
              <div class="absolute inset-0" :style="{ backgroundColor: category.color || '#10b981' }"></div>
            </div>
            
            <!-- Content -->
            <div class="relative z-10 p-8">
              <div class="flex items-start justify-between mb-6">
                <div 
                  class="flex items-center justify-center w-16 h-16 rounded-2xl text-white shadow-lg"
                  :style="{ backgroundColor: category.color || '#10b981' }"
                >
                  <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getCategoryIcon(category.slug)"/>
                  </svg>
                </div>
                
                <div class="flex items-center gap-2">
                  <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                  <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400">{{ $t('categories.featured') }}</span>
                </div>
              </div>
              
              <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-3 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors duration-300">
                {{ category.name || category.title }}
              </h3>
              
              <p class="text-slate-600 dark:text-slate-300 mb-6 leading-relaxed">
                {{ category.description || getDefaultDescription(category.slug) }}
              </p>
              
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                  <span class="text-2xl font-bold text-slate-900 dark:text-white">
                    {{ category.posts_count || 0 }}
                  </span>
                  <span class="text-sm text-slate-500 dark:text-slate-400">
                    {{ category.posts_count === 1 ? 'Post' : 'Posts' }}
                  </span>
                </div>
                
                <router-link 
                  :to="{ name: 'category.detail', params: { slug: category.slug } }"
                  class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 hover:scale-105 shadow-md hover:shadow-lg"
                >
                  Explore
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                  </svg>
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- All Categories -->
      <div>
        <div class="flex items-center justify-between mb-8">
          <h2 class="text-3xl font-bold text-slate-900 dark:text-white">{{ $t('categories.all_categories') }}</h2>
          <div class="text-slate-600 dark:text-slate-400">
            {{ filteredCategories.length }} {{ filteredCategories.length === 1 ? 'Category' : 'Categories' }} Found
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div v-for="i in 9" :key="i" class="bg-white dark:bg-slate-800 rounded-2xl p-8 animate-pulse">
            <div class="flex items-center gap-4 mb-6">
              <div class="w-16 h-16 bg-slate-300 dark:bg-slate-600 rounded-2xl"></div>
              <div class="flex-1 space-y-2">
                <div class="h-4 bg-slate-300 dark:bg-slate-600 rounded w-3/4"></div>
                <div class="h-4 bg-slate-300 dark:bg-slate-600 rounded w-1/2"></div>
              </div>
            </div>
            <div class="space-y-3">
              <div class="h-4 bg-slate-300 dark:bg-slate-600 rounded w-full"></div>
              <div class="h-4 bg-slate-300 dark:bg-slate-600 rounded w-2/3"></div>
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
          <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">{{ $t('categories.unable_to_load_categories') }}</h3>
          <p class="text-slate-600 dark:text-slate-400 mb-8">{{ error }}</p>
          <button @click="loadCategories" class="btn-primary">
            {{ $t('categories.try_again') }}
          </button>
        </div>

        <!-- Content Display -->
        <div v-else>
          <!-- Grid View -->
          <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div 
              v-for="category in filteredCategories" 
              :key="category.id"
              class="group bg-white dark:bg-slate-800 rounded-2xl overflow-hidden shadow-soft hover:shadow-strong transition-all duration-300 hover:scale-[1.02] border border-slate-200 dark:border-slate-700"
            >
              <!-- Header -->
              <div class="p-8 pb-6">
                <div class="flex items-start justify-between mb-6">
                  <div 
                    class="flex items-center justify-center w-14 h-14 rounded-xl text-white shadow-md"
                    :style="{ backgroundColor: category.color || '#6366f1' }"
                  >
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getCategoryIcon(category.slug)"/>
                    </svg>
                  </div>
                  
                  <div class="text-right">
                    <div class="text-2xl font-bold text-slate-900 dark:text-white">
                      {{ category.posts_count || 0 }}
                    </div>
                    <div class="text-sm text-slate-500 dark:text-slate-400">
                      {{ category.posts_count === 1 ? 'Post' : 'Posts' }}
                    </div>
                  </div>
                </div>
                
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors duration-300">
                  {{ category.name || category.title }}
                </h3>
                
                <p class="text-slate-600 dark:text-slate-300 leading-relaxed line-clamp-3">
                  {{ category.description || getDefaultDescription(category.slug) }}
                </p>
              </div>
              
              <!-- Footer -->
              <div class="px-8 pb-8">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <div 
                      class="w-3 h-3 rounded-full"
                      :style="{ backgroundColor: category.color || '#6366f1' }"
                    ></div>
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-400">
                      {{ getCategoryType(category.slug) }}
                    </span>
                  </div>
                  
                  <router-link 
                    :to="{ name: 'category.detail', params: { slug: category.slug } }"
                    class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 font-semibold group-hover:gap-3 transition-all duration-300"
                  >
                    View Posts
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                  </router-link>
                </div>
              </div>
            </div>
          </div>

          <!-- List View -->
          <div v-else-if="viewMode === 'list'" class="space-y-6">
            <div 
              v-for="category in filteredCategories" 
              :key="category.id"
              class="group bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-soft hover:shadow-strong transition-all duration-300 hover:scale-[1.01] border border-slate-200 dark:border-slate-700"
            >
              <div class="flex items-center gap-8">
                <!-- Icon -->
                <div 
                  class="flex items-center justify-center w-16 h-16 rounded-2xl text-white shadow-md flex-shrink-0"
                  :style="{ backgroundColor: category.color || '#6366f1' }"
                >
                  <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getCategoryIcon(category.slug)"/>
                  </svg>
                </div>
                
                <!-- Content -->
                <div class="flex-1">
                  <div class="flex items-start justify-between mb-3">
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors duration-300">
                      {{ category.name || category.title }}
                    </h3>
                    
                    <div class="flex items-center gap-6">
                      <div class="text-right">
                        <div class="text-2xl font-bold text-slate-900 dark:text-white">
                          {{ category.posts_count || 0 }}
                        </div>
                        <div class="text-sm text-slate-500 dark:text-slate-400">
                          {{ category.posts_count === 1 ? 'Post' : 'Posts' }}
                        </div>
                      </div>
                      
                      <router-link 
                        :to="{ name: 'category.detail', params: { slug: category.slug } }"
                        class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 hover:scale-105 shadow-md hover:shadow-lg"
                      >
                        Explore
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                      </router-link>
                    </div>
                  </div>
                  
                  <p class="text-slate-600 dark:text-slate-300 leading-relaxed mb-4">
                    {{ category.description || getDefaultDescription(category.slug) }}
                  </p>
                  
                  <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                      <div 
                        class="w-3 h-3 rounded-full"
                        :style="{ backgroundColor: category.color || '#6366f1' }"
                      ></div>
                      <span class="text-sm font-medium text-slate-600 dark:text-slate-400">
                        {{ getCategoryType(category.slug) }}
                      </span>
                    </div>
                    
                    <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      Updated recently
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="filteredCategories.length === 0" class="text-center py-16">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-2xl mb-8">
              <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-4a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2m14 0V6a2 2 0 00-2-2H7a2 2 0 00-2 2v1m14 0H5"/>
              </svg>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">{{ $t('categories.no_categories_found') }}</h3>
            <p class="text-slate-600 dark:text-slate-400 mb-8">{{ $t('categories.try_adjusting_search_terms') }}</p>
            <button @click="clearFilters" class="btn-primary">
              {{ $t('categories.clear_search') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

// Reactive state
const loading = ref(true)
const error = ref(null)
const categories = ref([])
const searchQuery = ref('')
const sortBy = ref('posts_count')
const viewMode = ref('grid')

// Computed properties
const totalCategories = computed(() => categories.value.length)
const totalPosts = computed(() => {
  return categories.value.reduce((sum, cat) => sum + (cat.posts_count || 0), 0)
})
const averagePostsPerCategory = computed(() => {
  if (categories.value.length === 0) return 0
  return Math.round(totalPosts.value / categories.value.length)
})

const featuredCategories = computed(() => {
  return categories.value
    .filter(cat => (cat.posts_count || 0) > 15)
    .sort((a, b) => (b.posts_count || 0) - (a.posts_count || 0))
    .slice(0, 6)
})

const filteredCategories = computed(() => {
  let filtered = categories.value.filter(cat => !featuredCategories.value.includes(cat))

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(cat => 
      (cat.name || cat.title || '').toLowerCase().includes(query) ||
      (cat.description || '').toLowerCase().includes(query)
    )
  }

  // Sort
  if (sortBy.value === 'posts_count') {
    filtered.sort((a, b) => (b.posts_count || 0) - (a.posts_count || 0))
  } else if (sortBy.value === 'name') {
    filtered.sort((a, b) => (a.name || a.title || '').localeCompare(b.name || b.title || ''))
  } else if (sortBy.value === 'recent') {
    filtered.sort((a, b) => new Date(b.updated_at || b.created_at || 0) - new Date(a.updated_at || a.created_at || 0))
  }

  return filtered
})

// Lifecycle
onMounted(async () => {
  await loadCategories()
})

// Methods
const loadCategories = async () => {
  loading.value = true
  error.value = null
  
  try {
    // Fetch real data from API
    const response = await fetch('/api/v1/categories')
    const data = await response.json()
    
    if (!response.ok) {
      throw new Error(data.message || 'Failed to fetch categories')
    }
    
    // Transform the data to match the expected structure
    categories.value = data.data.map(category => ({
      id: category.id,
      name: category.name || category.title,
      slug: category.slug,
      description: category.description || getDefaultDescription(category.slug),
      posts_count: category.posts_count || 0,
      color: category.color || '#3B82F6',
      updated_at: category.updated_at || category.created_at
    }))
    
  } catch (err) {
    console.error('Error loading categories:', err)
    error.value = 'Failed to load categories. Please try again.'
  } finally {
    loading.value = false
  }
}

const getCategoryIcon = (slug) => {
  const icons = {
    'technology': 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    'science': 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    'health-wellness': 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
    'business-finance': 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    'environment': 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
    'culture-society': 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
    'sports': 'M15 12a3 3 0 11-6 0 3 3 0 016 0z',
    'entertainment': 'M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m0 0V1a1 1 0 011-1h2a1 1 0 011 1v3M7 4V1a1 1 0 00-1-1H4a1 1 0 00-1 1v3m4 0H4m16 0h-3M7 4v16a1 1 0 001 1h8a1 1 0 001-1V4M7 4h10',
    'travel': 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012-2v-1a2 2 0 012-2h1.945M8 15h8m-8 0V9m8 6V9m-8 0h8M8 9V5a2 2 0 012-2h4a2 2 0 012 2v4',
    'food-cooking': 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4',
    'education': 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
    'art-design': 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v14a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v14a4 4 0 004 4 4 4 0 004-4V5z'
  }
  return icons[slug] || 'M19 11H5m14-4a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2m14 0V6a2 2 0 00-2-2H7a2 2 0 00-2 2v1m14 0H5'
}

const getDefaultDescription = (slug) => {
  const descriptions = {
    'technology': 'Explore the latest in technology, innovation, and digital transformation.',
    'science': 'Discover scientific breakthroughs and research findings.',
    'health-wellness': 'Your guide to health, wellness, and medical advances.',
    'business-finance': 'Business insights, market analysis, and financial guidance.',
    'environment': 'Environmental news, sustainability, and climate action.',
    'culture-society': 'Cultural trends, social movements, and societal insights.',
    'sports': 'Sports coverage, athlete stories, and championship updates.',
    'entertainment': 'Entertainment news, celebrity updates, and pop culture.',
    'travel': 'Travel guides, destination insights, and cultural experiences.',
    'food-cooking': 'Culinary adventures, recipes, and food culture.',
    'education': 'Educational resources, learning innovations, and academic insights.',
    'art-design': 'Creative inspirations, design trends, and artistic expressions.'
  }
  return descriptions[slug] || 'Discover interesting content in this category.'
}

const getCategoryType = (slug) => {
  const types = {
    'technology': 'Innovation',
    'science': 'Research',
    'health-wellness': 'Lifestyle',
    'business-finance': 'Professional',
    'environment': 'Sustainability',
    'culture-society': 'Social',
    'sports': 'Athletics',
    'entertainment': 'Media',
    'travel': 'Adventure',
    'food-cooking': 'Culinary',
    'education': 'Learning',
    'art-design': 'Creative'
  }
  return types[slug] || 'General'
}

const clearFilters = () => {
  searchQuery.value = ''
}
</script> 

<style scoped>
.btn-primary {
  @apply bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-8 py-4 rounded-2xl font-bold transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style> 