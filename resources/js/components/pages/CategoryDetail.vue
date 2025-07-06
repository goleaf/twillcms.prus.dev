<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800">
    <!-- Header Section -->
    <div v-if="loading" class="bg-gradient-to-r from-indigo-600 to-purple-600 relative overflow-hidden">
      <div class="container mx-auto px-4 py-16 relative z-10">
        <div class="text-center text-white">
          <div class="animate-pulse">
            <div class="h-12 bg-white/20 rounded-lg mx-auto max-w-md mb-4"></div>
            <div class="h-6 bg-white/10 rounded mx-auto max-w-lg"></div>
          </div>
        </div>
      </div>
    </div>

    <div v-else-if="category" class="bg-gradient-to-r from-indigo-600 to-purple-600 relative overflow-hidden">
      <!-- Floating Background Elements -->
      <div class="absolute inset-0">
        <div class="absolute top-10 left-10 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
        <div class="absolute bottom-10 right-10 w-24 h-24 bg-white/5 rounded-full blur-xl"></div>
        <div class="absolute top-1/2 left-1/2 w-40 h-40 bg-white/5 rounded-full blur-2xl transform -translate-x-1/2 -translate-y-1/2"></div>
      </div>
      
      <div class="container mx-auto px-4 py-16 relative z-10">
        <div class="text-center text-white">
          <!-- Breadcrumb -->
          <nav class="mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center justify-center space-x-2">
              <li>
                <router-link 
                  to="/" 
                  class="text-white/80 hover:text-white transition-colors duration-200"
                >
                  Home
                </router-link>
              </li>
              <li class="text-white/60">/</li>
              <li>
                <router-link 
                  to="/categories" 
                  class="text-white/80 hover:text-white transition-colors duration-200"
                >
                  Categories
                </router-link>
              </li>
              <li class="text-white/60">/</li>
              <li class="text-white font-medium">{{ category.name || category.title }}</li>
            </ol>
          </nav>
          
          <!-- Category Title -->
          <h1 class="text-4xl md:text-5xl font-bold tracking-tight mb-4">
            {{ category.name || category.title }}
          </h1>
          <p v-if="category.description" class="text-xl text-white/90 max-w-3xl mx-auto leading-relaxed mb-6">
            {{ category.description }}
          </p>
          
          <!-- Statistics -->
          <div class="flex items-center justify-center space-x-8">
            <div class="text-center">
              <div class="text-3xl font-bold text-white">{{ category.posts_count || 0 }}</div>
              <div class="text-white/70 text-sm font-medium">{{ category.posts_count === 1 ? 'Post' : 'Posts' }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="bg-gradient-to-r from-red-600 to-pink-600 relative overflow-hidden">
      <div class="container mx-auto px-4 py-16 relative z-10">
        <div class="text-center text-white">
          <h1 class="text-4xl md:text-5xl font-bold tracking-tight mb-4">
            {{ $t('categories.category_not_found') }}
          </h1>
          <p class="text-xl text-white/90 max-w-3xl mx-auto leading-relaxed">
            The category you're looking for doesn't exist or has been moved.
          </p>
        </div>
      </div>
    </div>

    <!-- Content Section -->
    <div class="container mx-auto px-4 py-12">
      <div v-if="loading" class="max-w-4xl mx-auto">
        <!-- Loading Skeleton -->
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
          <div v-for="i in 6" :key="i" class="animate-pulse">
            <div class="bg-white dark:bg-slate-800 rounded-xl p-6 space-y-4">
              <div class="h-4 bg-slate-200 dark:bg-slate-700 rounded w-3/4"></div>
              <div class="h-3 bg-slate-200 dark:bg-slate-700 rounded w-full"></div>
              <div class="h-3 bg-slate-200 dark:bg-slate-700 rounded w-2/3"></div>
              <div class="h-8 bg-slate-200 dark:bg-slate-700 rounded w-1/3"></div>
            </div>
          </div>
        </div>
      </div>
      
      <div v-else-if="error" class="max-w-4xl mx-auto">
        <!-- Error State -->
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-8 text-center">
          <div class="w-16 h-16 mx-auto mb-4 text-red-500">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-red-800 dark:text-red-200 mb-2">
            Failed to Load Category
          </h3>
          <p class="text-red-600 dark:text-red-300 mb-4">{{ error }}</p>
          <button 
            @click="fetchCategory"
            class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Try Again
          </button>
        </div>
      </div>
      
      <div v-else-if="!category" class="max-w-4xl mx-auto">
        <!-- Category Not Found -->
        <div class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-8 text-center">
          <div class="w-16 h-16 mx-auto mb-4 text-slate-400 dark:text-slate-500">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-4a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2m14 0V6a2 2 0 00-2-2H7a2 2 0 00-2 2v1m14 0H5"/>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-2">
            {{ $t('categories.category_not_found') }}
          </h3>
          <p class="text-slate-600 dark:text-slate-400 mb-4">
            The category you're looking for doesn't exist or has been moved.
          </p>
          <router-link 
            to="/categories"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors duration-200"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-4a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2m14 0V6a2 2 0 00-2-2H7a2 2 0 00-2 2v1m14 0H5"/>
            </svg>
            {{ $t('categories.view_all_categories') }}
          </router-link>
        </div>
      </div>
      
      <div v-else-if="category && posts.length === 0" class="max-w-4xl mx-auto">
        <!-- No Posts -->
        <div class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-8 text-center">
          <div class="w-16 h-16 mx-auto mb-4 text-slate-400 dark:text-slate-500">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-2">
            {{ $t('categories.no_posts_yet') }}
          </h3>
          <p class="text-slate-600 dark:text-slate-400 mb-4">
            This category doesn't have any posts yet. Check back later!
          </p>
          <router-link 
            to="/"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors duration-200"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Back to Home
          </router-link>
        </div>
      </div>
      
      <div v-else class="max-w-6xl mx-auto">
        <!-- Posts Grid -->
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
          <article 
            v-for="post in posts" 
            :key="post.id"
            class="group bg-white dark:bg-slate-800 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02] border border-slate-200 dark:border-slate-700"
          >
            <!-- Featured Image -->
            <div v-if="post.featured_image" class="aspect-video overflow-hidden">
              <img 
                :src="post.featured_image" 
                :alt="post.title"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                loading="lazy"
              />
            </div>
            
            <!-- Content -->
            <div class="p-6">
              <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300 line-clamp-2">
                <router-link :to="{ name: 'post.detail', params: { slug: post.slug } }">
                  {{ post.title }}
                </router-link>
              </h3>
              
              <p v-if="post.excerpt" class="text-slate-600 dark:text-slate-300 mb-4 line-clamp-3">
                {{ post.excerpt }}
              </p>
              
              <!-- Meta -->
              <div class="flex items-center justify-between text-sm">
                <div class="flex items-center text-slate-500 dark:text-slate-400">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  {{ formatDate(post.published_at) }}
                </div>
                
                <router-link 
                  :to="{ name: 'post.detail', params: { slug: post.slug } }"
                  class="inline-flex items-center text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold group-hover:gap-2 transition-all duration-300"
                >
                  Read More
                  <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                  </svg>
                </router-link>
              </div>
            </div>
          </article>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useHead } from '@unhead/vue'

const route = useRoute()
const router = useRouter()

// State
const category = ref(null)
const posts = ref([])
const loading = ref(true)
const error = ref(null)

// Methods
const fetchCategory = async () => {
  try {
    loading.value = true
    error.value = null
    
    const response = await fetch(`/api/v1/categories/${route.params.slug}`)
    const data = await response.json()
    
    if (!response.ok) {
      throw new Error(data.message || 'Failed to load category')
    }
    
    category.value = data
    posts.value = data.posts || []
    
  } catch (err) {
    error.value = err.message
    category.value = null
    posts.value = []
  } finally {
    loading.value = false
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Meta tags
useHead(computed(() => {
  if (!category.value) {
    return {
      title: 'Category Not Found - NewsHub',
      meta: [
        { name: 'description', content: 'The category you are looking for could not be found.' }
      ]
    }
  }
  
  return {
    title: `${category.value.name || category.value.title} - NewsHub`,
    meta: [
      { name: 'description', content: category.value.description || `Browse posts in ${category.value.name || category.value.title}` },
      { property: 'og:title', content: category.value.name || category.value.title },
      { property: 'og:description', content: category.value.description || `Browse posts in ${category.value.name || category.value.title}` },
      { property: 'og:type', content: 'website' },
      { property: 'og:url', content: window.location.href }
    ]
  }
}))

// Watchers
watch(() => route.params.slug, () => {
  if (route.params.slug) {
    fetchCategory()
    }
}, { immediate: true })

// Lifecycle
onMounted(() => {
  if (route.params.slug) {
    fetchCategory()
}
})
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
</style> 