<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 relative overflow-hidden">
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
              <li class="text-white font-medium">{{ page?.title || 'Loading...' }}</li>
            </ol>
          </nav>
          
          <!-- Page Title -->
          <div v-if="loading" class="animate-pulse">
            <div class="h-12 bg-white/20 rounded-lg mx-auto max-w-md mb-4"></div>
            <div class="h-6 bg-white/10 rounded mx-auto max-w-lg"></div>
          </div>
          
          <div v-else-if="page" class="space-y-4">
            <h1 class="text-4xl md:text-5xl font-bold tracking-tight">
              {{ page.title }}
            </h1>
            <p v-if="page.excerpt" class="text-xl text-white/90 max-w-3xl mx-auto leading-relaxed">
              {{ page.excerpt }}
            </p>
          </div>
          
          <div v-else class="space-y-4">
            <h1 class="text-4xl md:text-5xl font-bold tracking-tight">
              {{ $t('errors.page_not_found') }}
            </h1>
            <p class="text-xl text-white/90 max-w-3xl mx-auto leading-relaxed">
              The page you're looking for doesn't exist or has been moved.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Content Section -->
    <div class="container mx-auto px-4 py-12">
      <div v-if="loading" class="max-w-4xl mx-auto">
        <!-- Loading Skeleton -->
        <div class="animate-pulse space-y-6">
          <div class="h-6 bg-slate-200 rounded w-3/4"></div>
          <div class="h-4 bg-slate-200 rounded w-full"></div>
          <div class="h-4 bg-slate-200 rounded w-5/6"></div>
          <div class="h-4 bg-slate-200 rounded w-4/5"></div>
          <div class="h-6 bg-slate-200 rounded w-2/3 mt-8"></div>
          <div class="h-4 bg-slate-200 rounded w-full"></div>
          <div class="h-4 bg-slate-200 rounded w-3/4"></div>
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
            Failed to Load Page
          </h3>
          <p class="text-red-600 dark:text-red-300 mb-4">{{ error }}</p>
          <button 
            @click="fetchPage"
            class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Try Again
          </button>
        </div>
      </div>
      
      <div v-else-if="!page" class="max-w-4xl mx-auto">
        <!-- Page Not Found -->
        <div class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg p-8 text-center">
          <div class="w-16 h-16 mx-auto mb-4 text-slate-400 dark:text-slate-500">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-2">
            {{ $t('errors.page_not_found') }}
          </h3>
          <p class="text-slate-600 dark:text-slate-400 mb-4">
            The page you're looking for doesn't exist or has been moved.
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
      
      <div v-else class="max-w-4xl mx-auto">
        <!-- Page Content -->
        <article class="bg-white dark:bg-slate-800 rounded-xl shadow-lg overflow-hidden">
          <div class="p-8 lg:p-12">
            <!-- Content -->
            <div 
              class="prose prose-lg max-w-none dark:prose-invert prose-headings:text-slate-900 dark:prose-headings:text-slate-100 prose-p:text-slate-700 dark:prose-p:text-slate-300 prose-a:text-indigo-600 dark:prose-a:text-indigo-400 prose-strong:text-slate-900 dark:prose-strong:text-slate-100"
              v-html="page.content"
            ></div>
            
            <!-- Meta Information -->
            <div class="mt-12 pt-8 border-t border-slate-200 dark:border-slate-700">
              <div class="flex items-center justify-between text-sm text-slate-500 dark:text-slate-400">
                <div v-if="page.updated_at">
                  Last updated: {{ formatDate(page.updated_at) }}
                </div>
                <div class="flex items-center space-x-4">
                  <button 
                    @click="scrollToTop"
                    class="inline-flex items-center text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors duration-200"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                    Back to Top
                  </button>
                </div>
              </div>
            </div>
          </div>
        </article>
        
        <!-- Related Links -->
        <div v-if="relatedPages.length > 0" class="mt-12">
          <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-6">
            You might also be interested in:
          </h3>
          <div class="grid gap-4 md:grid-cols-2">
            <router-link
              v-for="relatedPage in relatedPages"
              :key="relatedPage.slug"
              :to="`/pages/${relatedPage.slug}`"
              class="block p-4 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-600 transition-all duration-200 hover:shadow-md group"
            >
              <h4 class="font-medium text-slate-900 dark:text-slate-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-200">
                {{ relatedPage.title }}
              </h4>
              <p v-if="relatedPage.excerpt" class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                {{ relatedPage.excerpt }}
              </p>
            </router-link>
          </div>
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
const page = ref(null)
const loading = ref(true)
const error = ref(null)

// Related pages (excluding current page)
const relatedPages = computed(() => {
  const staticPages = [
    { slug: 'privacy-policy', title: 'Privacy Policy', excerpt: 'Learn about how we collect, use, and protect your personal information.' },
    { slug: 'terms-of-service', title: 'Terms of Service', excerpt: 'Read our terms and conditions for using NewsHub services.' },
    { slug: 'contact', title: 'Contact Us', excerpt: 'Get in touch with the NewsHub team for inquiries, support, or feedback.' }
  ]
  
  return staticPages.filter(p => p.slug !== route.params.slug)
})

// Methods
const fetchPage = async () => {
  try {
    loading.value = true
    error.value = null
    
    const response = await fetch(`/api/v1/pages/${route.params.slug}`)
    const data = await response.json()
    
    if (!response.ok) {
      throw new Error(data.message || 'Failed to load page')
    }
    
    if (data.success) {
      page.value = data.data
    } else {
      throw new Error(data.message || 'Page not found')
    }
  } catch (err) {
    error.value = err.message
    page.value = null
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

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// Meta tags
useHead(computed(() => {
  if (!page.value) {
    return {
      title: 'Page Not Found - NewsHub',
      meta: [
        { name: 'description', content: 'The page you are looking for could not be found.' }
      ]
    }
  }
  
  return {
    title: page.value.meta?.title || page.value.title,
    meta: [
      { name: 'description', content: page.value.meta?.description || page.value.excerpt },
      { name: 'keywords', content: page.value.meta?.keywords || '' },
      { property: 'og:title', content: page.value.title },
      { property: 'og:description', content: page.value.meta?.description || page.value.excerpt },
      { property: 'og:type', content: 'article' },
      { property: 'og:url', content: window.location.href }
    ]
  }
}))

// Watchers
watch(() => route.params.slug, () => {
  if (route.params.slug) {
    fetchPage()
  }
}, { immediate: true })

// Lifecycle
onMounted(() => {
  if (route.params.slug) {
    fetchPage()
  }
})
</script>

<style scoped>
/* Custom styles for better readability */
.prose {
  @apply text-slate-700 dark:text-slate-300;
}

.prose h2 {
  @apply text-2xl font-bold text-slate-900 dark:text-slate-100 mt-8 mb-4;
}

.prose h3 {
  @apply text-xl font-semibold text-slate-900 dark:text-slate-100 mt-6 mb-3;
}

.prose p {
  @apply mb-4 leading-relaxed;
}

.prose ul {
  @apply mb-4 ml-6;
}

.prose li {
  @apply mb-2;
}

.prose strong {
  @apply font-semibold text-slate-900 dark:text-slate-100;
}

.prose a {
  @apply text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 no-underline hover:underline;
}

.prose .bg-gray-50 {
  @apply bg-slate-50 dark:bg-slate-700;
}

.prose .bg-blue-50 {
  @apply bg-blue-50 dark:bg-blue-900/20;
}

.prose .bg-yellow-50 {
  @apply bg-yellow-50 dark:bg-yellow-900/20;
}

.prose .border-yellow-400 {
  @apply border-yellow-400 dark:border-yellow-600;
}
</style> 