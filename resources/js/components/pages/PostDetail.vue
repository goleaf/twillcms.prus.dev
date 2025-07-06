<template>
  <div class="min-h-screen bg-neutral-50 dark:bg-neutral-900">
    <!-- Reading Progress Bar -->
    <div class="fixed top-0 left-0 w-full h-1 bg-neutral-200 dark:bg-neutral-800 z-50">
      <div 
        class="h-full bg-gradient-to-r from-primary-500 to-secondary-500 transition-all duration-300 ease-out"
        :style="{ width: readingProgress + '%' }"
      ></div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="animate-pulse space-y-8">
        <div class="h-8 bg-neutral-200 dark:bg-neutral-700 rounded w-3/4"></div>
        <div class="h-96 bg-neutral-200 dark:bg-neutral-700 rounded-2xl"></div>
        <div class="space-y-4">
          <div class="h-4 bg-neutral-200 dark:bg-neutral-700 rounded w-full"></div>
          <div class="h-4 bg-neutral-200 dark:bg-neutral-700 rounded w-5/6"></div>
          <div class="h-4 bg-neutral-200 dark:bg-neutral-700 rounded w-4/6"></div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center animate-fade-in">
      <div class="inline-flex items-center justify-center w-20 h-20 bg-danger-100 dark:bg-danger-900/20 rounded-full mb-8">
        <svg class="w-10 h-10 text-danger-600 dark:text-danger-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <h1 class="text-3xl font-bold font-serif text-danger-800 dark:text-danger-200 mb-4">{{ $t('blog.post_not_found') }}</h1>
      <p class="text-lg text-danger-600 dark:text-danger-400 mb-8 max-w-md mx-auto">{{ error }}</p>
      <router-link 
        to="/" 
        class="inline-flex items-center bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl"
      >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
        </svg>
        {{ $t('common.home') }}
      </router-link>
    </div>

    <!-- Article Content -->
    <article v-else-if="post" class="animate-fade-in">
      <!-- Breadcrumb -->
      <nav class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-4">
        <ol class="flex items-center space-x-2 text-sm text-neutral-500 dark:text-neutral-400">
          <li>
            <router-link to="/" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200">
              {{ $t('common.home') }}
            </router-link>
          </li>
          <li>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </li>
          <li v-if="post.categories && post.categories[0]">
            <router-link 
              :to="{ name: 'category.detail', params: { slug: post.categories[0].slug } }"
              class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200"
            >
              {{ post.categories[0].title || post.categories[0].name }}
            </router-link>
          </li>
          <li v-if="post.categories && post.categories[0]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </li>
          <li class="text-neutral-900 dark:text-white truncate max-w-xs">{{ post.title }}</li>
        </ol>
      </nav>

      <!-- Article Header -->
      <header class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <!-- Categories -->
        <div v-if="post.categories && post.categories.length > 0" class="flex flex-wrap gap-2 mb-6">
          <router-link
            v-for="category in post.categories"
            :key="category.id"
            :to="{ name: 'category.detail', params: { slug: category.slug } }"
            class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-primary-100 dark:bg-primary-900/20 text-primary-800 dark:text-primary-300 hover:bg-primary-200 dark:hover:bg-primary-900/40 transition-all duration-200 hover:scale-105"
          >
            {{ category.title || category.name }}
          </router-link>
        </div>

        <!-- Title -->
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold font-serif text-neutral-900 dark:text-white mb-6 leading-tight">
          {{ post.title }}
        </h1>

        <!-- Description -->
        <p v-if="post.description" class="text-xl sm:text-2xl text-neutral-600 dark:text-neutral-300 mb-8 leading-relaxed font-light">
          {{ post.description }}
        </p>

        <!-- Meta information -->
        <div class="flex flex-wrap items-center gap-6 text-neutral-500 dark:text-neutral-400 border-b border-neutral-200 dark:border-neutral-800 pb-8">
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <time :datetime="post.published_at" class="font-medium">
              {{ formatDate(post.published_at) }}
            </time>
          </div>
          
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="font-medium">{{ post.reading_time || 5 }} min read</span>
          </div>

          <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            <span class="font-medium">{{ post.view_count || 0 }} views</span>
          </div>
        </div>
      </header>

      <!-- Featured Image -->
      <div v-if="post.featured_image" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mb-12">
        <div class="relative overflow-hidden rounded-3xl shadow-strong">
          <img 
            :src="post.featured_image" 
            :alt="post.title"
            class="w-full h-96 lg:h-[500px] object-cover"
          >
          <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
        </div>
      </div>

      <!-- Article Content -->
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div ref="articleContent" class="prose prose-lg max-w-none">
          <div 
            v-html="formatContent(post.content)" 
            class="article-content text-neutral-800 dark:text-neutral-200 leading-relaxed"
          ></div>
        </div>

        <!-- Social Sharing -->
        <div class="mt-16 pt-12 border-t border-neutral-200 dark:border-neutral-800">
          <h3 class="text-2xl font-bold font-serif text-neutral-900 dark:text-white mb-6">Share this article</h3>
          <div class="flex flex-wrap gap-4">
            <button
              @click="shareOnTwitter"
              class="share-button bg-blue-500 hover:bg-blue-600 text-white"
            >
              <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
              </svg>
              Twitter
            </button>
            
            <button
              @click="shareOnFacebook"
              class="share-button bg-blue-600 hover:bg-blue-700 text-white"
            >
              <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
              Facebook
            </button>
            
            <button
              @click="shareOnLinkedIn"
              class="share-button bg-blue-700 hover:bg-blue-800 text-white"
            >
              <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
              </svg>
              LinkedIn
            </button>
            
            <button
              @click="copyLink"
              class="share-button bg-neutral-600 hover:bg-neutral-700 text-white"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
              </svg>
              Copy Link
            </button>
          </div>
        </div>

        <!-- Related Posts -->
        <section v-if="relatedPosts && relatedPosts.length > 0" class="mt-20 pt-12 border-t border-neutral-200 dark:border-neutral-800">
          <h3 class="text-3xl font-bold font-serif text-neutral-900 dark:text-white mb-8">Related Articles</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <article
              v-for="relatedPost in relatedPosts.slice(0, 4)"
              :key="relatedPost.id"
              class="group"
            >
              <router-link :to="{ name: 'post.detail', params: { slug: relatedPost.slug } }">
                <div class="bg-white dark:bg-neutral-800 rounded-2xl overflow-hidden shadow-soft hover:shadow-strong transition-all duration-300 hover:scale-[1.02]">
                  <div class="aspect-w-16 aspect-h-9">
                    <img 
                      v-if="relatedPost.featured_image" 
                      :src="relatedPost.featured_image" 
                      :alt="relatedPost.title"
                      class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500"
                    >
                    <div v-else class="w-full h-48 bg-gradient-to-br from-neutral-200 to-neutral-300 dark:from-neutral-700 dark:to-neutral-800 flex items-center justify-center">
                      <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                      </svg>
                    </div>
                  </div>
                  
                  <div class="p-6">
                    <div class="flex items-center gap-3 text-sm text-neutral-500 dark:text-neutral-400 mb-3">
                      <time :datetime="relatedPost.published_at">
                        {{ formatDate(relatedPost.published_at) }}
                      </time>
                      <span class="w-1 h-1 bg-neutral-400 rounded-full"></span>
                      <span>{{ relatedPost.reading_time || 3 }} min</span>
                    </div>
                    
                    <h4 class="text-xl font-semibold font-serif text-neutral-900 dark:text-white mb-3 leading-tight group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors duration-300">
                      {{ relatedPost.title }}
                    </h4>
                    
                    <p class="text-neutral-600 dark:text-neutral-300 text-sm leading-relaxed line-clamp-3">
                      {{ relatedPost.description || relatedPost.excerpt }}
                    </p>
                  </div>
                </div>
              </router-link>
            </article>
          </div>
        </section>

        <!-- Article Navigation -->
        <nav class="mt-20 pt-12 border-t border-neutral-200 dark:border-neutral-800">
          <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
            <router-link
              v-if="previousPost"
              :to="{ name: 'post.detail', params: { slug: previousPost.slug } }"
              class="group flex items-center text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition-colors duration-300"
            >
              <div class="flex items-center mr-4">
                <svg class="w-6 h-6 mr-2 group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <div>
                  <div class="text-sm text-neutral-500 dark:text-neutral-400">Previous Article</div>
                  <div class="font-semibold">{{ previousPost.title }}</div>
                </div>
              </div>
            </router-link>
            
            <router-link
              v-if="nextPost"
              :to="{ name: 'post.detail', params: { slug: nextPost.slug } }"
              class="group flex items-center text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition-colors duration-300 md:ml-auto"
            >
              <div class="flex items-center ml-4">
                <div class="text-right">
                  <div class="text-sm text-neutral-500 dark:text-neutral-400">Next Article</div>
                  <div class="font-semibold">{{ nextPost.title }}</div>
                </div>
                <svg class="w-6 h-6 ml-2 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </div>
            </router-link>
          </div>
        </nav>
      </div>
    </article>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import { useRoute } from 'vue-router'

export default {
  name: 'PostDetail',
  setup() {
    const route = useRoute()
    const loading = ref(true)
    const error = ref(null)
    const post = ref(null)
    const relatedPosts = ref([])
    const previousPost = ref(null)
    const nextPost = ref(null)
    const readingProgress = ref(0)
    const articleContent = ref(null)

    onMounted(async () => {
      await loadPost()
      window.addEventListener('scroll', handleScroll)
    })

    onUnmounted(() => {
      window.removeEventListener('scroll', handleScroll)
    })

    const loadPost = async () => {
      loading.value = true
      error.value = null
      
      try {
        const slug = route.params.slug
        console.log('Loading post with slug:', slug) // Debug log
        
        // Fetch post data from API
        const response = await fetch(`/api/v1/posts/${slug}`)
        
        if (!response.ok) {
          if (response.status === 404) {
            throw new Error('Article not found')
          }
          throw new Error(`Failed to load article: ${response.status}`)
        }
        
        const postData = await response.json()
        console.log('Loaded post data:', postData) // Debug log
        
        post.value = postData
        relatedPosts.value = postData.relatedPosts || []
        
        // Update page title
        if (post.value.title) {
          document.title = `${post.value.title} - NewsHub`
        }
        
      } catch (err) {
        console.error('Error loading post:', err)
        error.value = 'Article not found or failed to load. Please try again.'
      } finally {
        loading.value = false
      }
    }

    const handleScroll = () => {
      if (!articleContent.value) return
      
      const windowHeight = window.innerHeight
      const documentHeight = document.documentElement.scrollHeight
      const scrollTop = window.scrollY
      const progress = (scrollTop / (documentHeight - windowHeight)) * 100
      
      readingProgress.value = Math.min(Math.max(progress, 0), 100)
    }

    const formatDate = (dateString) => {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }
    
    const formatContent = (content) => {
      if (!content) return ''
      
      // Check if content is already HTML formatted
      if (content.includes('<p>') || content.includes('<h1>') || content.includes('<h2>') || content.includes('<div>')) {
        // Content is already HTML, return as-is
        return content
      }
      
      // Content is plain text, apply formatting
      return content
        .replace(/\n\n/g, '</p><p>')
        .replace(/^(.+)/, '<p>$1')
        .replace(/(.+)$/, '$1</p>')
        .replace(/<p><h([1-6])>/g, '<h$1>')
        .replace(/<\/h([1-6])><\/p>/g, '</h$1>')
        .replace(/<p><blockquote>/g, '<blockquote>')
        .replace(/<\/blockquote><\/p>/g, '</blockquote>')
    }
    
    const shareOnTwitter = () => {
      const url = window.location.href
      const text = post.value.title
      window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}`, '_blank')
    }
    
    const shareOnFacebook = () => {
      const url = window.location.href
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank')
    }

    const shareOnLinkedIn = () => {
      const url = window.location.href
      const title = post.value.title
      window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`, '_blank')
    }
    
    const copyLink = async () => {
      try {
        await navigator.clipboard.writeText(window.location.href)
        // You could add a toast notification here
        alert('Link copied to clipboard!')
      } catch (error) {
        console.error('Failed to copy link:', error)
      }
    }

    return {
      loading,
      error,
      post,
      relatedPosts,
      previousPost,
      nextPost,
      readingProgress,
      articleContent,
      loadPost,
      formatDate,
      formatContent,
      shareOnTwitter,
      shareOnFacebook,
      shareOnLinkedIn,
      copyLink
    }
  }
}
</script>

<style scoped>
.share-button {
  @apply flex items-center px-4 py-2 rounded-xl font-semibold transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Enhanced article content styling */
.article-content {
  @apply text-lg leading-8;
}

.article-content :deep(h1) {
  @apply text-4xl font-bold font-serif text-neutral-900 dark:text-white mt-12 mb-6 leading-tight;
}

.article-content :deep(h2) {
  @apply text-3xl font-bold font-serif text-neutral-900 dark:text-white mt-10 mb-6 leading-tight;
}

.article-content :deep(h3) {
  @apply text-2xl font-semibold font-serif text-neutral-900 dark:text-white mt-8 mb-4 leading-tight;
}

.article-content :deep(h4) {
  @apply text-xl font-semibold text-neutral-900 dark:text-white mt-6 mb-4;
}

.article-content :deep(p) {
  @apply mb-6 text-neutral-700 dark:text-neutral-300 leading-relaxed;
}

.article-content :deep(blockquote) {
  @apply border-l-4 border-primary-500 pl-6 py-4 my-8 bg-primary-50 dark:bg-primary-900/20 italic text-lg text-neutral-600 dark:text-neutral-400 rounded-r-lg;
}

.article-content :deep(blockquote p) {
  @apply mb-0 text-neutral-700 dark:text-neutral-300 font-medium;
}

.article-content :deep(ul), 
.article-content :deep(ol) {
  @apply mb-6 pl-8;
}

.article-content :deep(li) {
  @apply mb-2 text-neutral-700 dark:text-neutral-300;
}

.article-content :deep(ul li) {
  @apply list-disc;
}

.article-content :deep(ol li) {
  @apply list-decimal;
}

.article-content :deep(a) {
  @apply text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 underline transition-colors duration-200;
}

.article-content :deep(code) {
  @apply bg-neutral-100 dark:bg-neutral-800 px-2 py-1 rounded text-sm font-mono text-neutral-800 dark:text-neutral-200;
}

.article-content :deep(pre) {
  @apply bg-neutral-100 dark:bg-neutral-800 p-4 rounded-xl overflow-x-auto my-6;
}

.article-content :deep(img) {
  @apply rounded-2xl shadow-lg my-8 mx-auto;
}

.article-content :deep(strong) {
  @apply font-semibold text-neutral-900 dark:text-white;
}

.article-content :deep(em) {
  @apply italic;
}
</style> 