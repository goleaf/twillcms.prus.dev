<template>
  <div class="min-h-screen bg-gradient-to-br from-neutral-50 via-white to-primary-50 dark:from-neutral-900 dark:via-neutral-800 dark:to-primary-900/20 flex items-center justify-center relative overflow-hidden">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
      <!-- Floating Shapes -->
      <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-primary-200/20 dark:bg-primary-700/20 rounded-full blur-3xl animate-float"></div>
      <div class="absolute top-3/4 right-1/4 w-96 h-96 bg-secondary-200/20 dark:bg-secondary-700/20 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
      <div class="absolute top-1/2 left-1/2 w-48 h-48 bg-accent-200/20 dark:bg-accent-700/20 rounded-full blur-3xl animate-float" style="animation-delay: 4s;"></div>
      
      <!-- Grid Pattern -->
      <div class="absolute inset-0 opacity-5">
        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
          <defs>
            <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
              <path d="M 10 0 L 0 0 0 10" fill="none" stroke="currentColor" stroke-width="0.5"/>
            </pattern>
          </defs>
          <rect width="100" height="100" fill="url(#grid)" />
        </svg>
      </div>
    </div>

    <!-- Main Content -->
    <div class="relative z-10 text-center px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
      <!-- 404 Animation -->
      <div class="mb-12 relative">
        <!-- Large 404 Text -->
        <div class="relative">
          <h1 class="text-9xl sm:text-[12rem] lg:text-[16rem] font-black text-transparent bg-clip-text bg-gradient-to-br from-primary-600 via-secondary-600 to-accent-600 dark:from-primary-400 dark:via-secondary-400 dark:to-accent-400 leading-none select-none animate-fade-in-up">
            404
          </h1>
          
          <!-- Glitch Effect Overlay -->
          <div class="absolute inset-0 text-9xl sm:text-[12rem] lg:text-[16rem] font-black text-transparent bg-clip-text bg-gradient-to-br from-danger-500 to-warning-500 leading-none opacity-0 animate-glitch pointer-events-none">
            404
          </div>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute -top-8 -left-8 w-16 h-16 border-4 border-primary-300 dark:border-primary-600 rounded-full animate-spin-slow opacity-60"></div>
        <div class="absolute -bottom-8 -right-8 w-12 h-12 bg-gradient-to-r from-secondary-500 to-accent-500 rounded-lg rotate-45 animate-bounce opacity-60" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 left-8 w-8 h-8 border-2 border-accent-400 dark:border-accent-500 rotate-45 animate-pulse" style="animation-delay: 2s;"></div>
      </div>

      <!-- Content -->
      <div class="space-y-8 animate-fade-in-up" style="animation-delay: 0.3s;">
        <!-- Headline -->
        <div>
          <h2 class="text-4xl sm:text-5xl lg:text-6xl font-bold font-serif text-neutral-900 dark:text-white mb-6 leading-tight">
            {{ $t('errors.page_not_found') }}
          </h2>
          <p class="text-xl sm:text-2xl text-neutral-600 dark:text-neutral-300 max-w-2xl mx-auto leading-relaxed">
            The page you're looking for seems to have wandered off into the digital wilderness. Don't worry, we'll help you find your way back home.
          </p>
        </div>

        <!-- Search Box -->
        <div class="max-w-lg mx-auto" style="animation-delay: 0.6s;">
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg class="h-6 w-6 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input
              v-model="searchQuery"
              @keyup.enter="performSearch"
              type="text"
              placeholder="Search for articles..."
              class="w-full pl-12 pr-4 py-4 text-lg border-2 border-neutral-200 dark:border-neutral-700 rounded-2xl bg-white/80 dark:bg-neutral-800/80 backdrop-blur-sm text-neutral-900 dark:text-white placeholder-neutral-500 dark:placeholder-neutral-400 focus:outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20 transition-all duration-300"
            >
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center animate-fade-in-up" style="animation-delay: 0.9s;">
          <router-link 
            to="/" 
            class="btn-primary group"
          >
            <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
            </svg>
            Back to Home
          </router-link>
          
          <router-link 
            to="/categories" 
            class="btn-secondary group"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-4a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2m14 0V6a2 2 0 00-2-2H7a2 2 0 00-2 2v1m14 0H5"/>
            </svg>
            Browse Categories
          </router-link>

          <button 
            @click="showSuggestions = !showSuggestions"
            class="btn-tertiary group"
          >
            <svg class="w-5 h-5 mr-2 group-hover:rotate-180 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
            Get Suggestions
          </button>
        </div>

        <!-- Suggestions Panel -->
        <transition
          enter-active-class="transition duration-300 ease-out"
          enter-from-class="opacity-0 scale-95 translate-y-4"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-active-class="transition duration-200 ease-in"
          leave-from-class="opacity-100 scale-100 translate-y-0"
          leave-to-class="opacity-0 scale-95 translate-y-4"
        >
          <div v-if="showSuggestions" class="mt-8 bg-white/90 dark:bg-neutral-800/90 backdrop-blur-lg rounded-3xl p-8 border border-neutral-200 dark:border-neutral-700 shadow-strong">
            <h3 class="text-2xl font-bold font-serif text-neutral-900 dark:text-white mb-6">Popular Articles</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <router-link
                v-for="suggestion in suggestions"
                :key="suggestion.id"
                :to="{ name: 'post.detail', params: { slug: suggestion.slug } }"
                class="group flex items-start space-x-4 p-4 rounded-xl hover:bg-neutral-50 dark:hover:bg-neutral-700/50 transition-all duration-200"
              >
                <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-primary-500 to-secondary-500 rounded-xl flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                  <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <h4 class="text-lg font-semibold text-neutral-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors duration-200 line-clamp-2">
                    {{ suggestion.title }}
                  </h4>
                  <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">
                    {{ suggestion.reading_time }} min read
                  </p>
                </div>
              </router-link>
            </div>
          </div>
        </transition>

        <!-- Fun Stats -->
        <div class="mt-12 grid grid-cols-2 md:grid-cols-4 gap-6 animate-fade-in-up" style="animation-delay: 1.2s;">
          <div class="text-center">
            <div class="text-3xl font-bold text-primary-600 dark:text-primary-400 mb-2">404</div>
            <div class="text-sm text-neutral-600 dark:text-neutral-400">Error Code</div>
          </div>
          <div class="text-center">
            <div class="text-3xl font-bold text-secondary-600 dark:text-secondary-400 mb-2">∞</div>
            <div class="text-sm text-neutral-600 dark:text-neutral-400">Articles Available</div>
          </div>
          <div class="text-center">
            <div class="text-3xl font-bold text-accent-600 dark:text-accent-400 mb-2">24/7</div>
            <div class="text-sm text-neutral-600 dark:text-neutral-400">Always Online</div>
          </div>
          <div class="text-center">
            <div class="text-3xl font-bold text-success-600 dark:text-success-400 mb-2">100%</div>
            <div class="text-sm text-neutral-600 dark:text-neutral-400">Will Find Content</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

export default {
  name: 'NotFound',
  setup() {
    const router = useRouter()
    const searchQuery = ref('')
    const showSuggestions = ref(false)
    const suggestions = ref([
      {
        id: 1,
        title: "The Future of Artificial Intelligence in Modern Society",
        slug: "future-ai-modern-society",
        reading_time: 8
      },
      {
        id: 2,
        title: "Climate Change: What We Can Do Today",
        slug: "climate-change-action-today",
        reading_time: 6
      },
      {
        id: 3,
        title: "Tech Innovation Trends for 2024",
        slug: "tech-innovation-trends-2024",
        reading_time: 5
      },
      {
        id: 4,
        title: "Global Economy: Recovery and Challenges",
        slug: "global-economy-recovery-challenges",
        reading_time: 7
      }
    ])

    const performSearch = () => {
      if (searchQuery.value.trim()) {
        router.push({
          name: 'search',
          query: { q: searchQuery.value.trim() }
        })
      }
    }

    onMounted(() => {
      // Update page title
      document.title = '404 - Page Not Found | NewsHub'
    })

    return {
      searchQuery,
      showSuggestions,
      suggestions,
      performSearch
    }
  }
}
</script>

<style scoped>
.btn-primary {
  @apply bg-gradient-to-r from-primary-600 to-secondary-600 hover:from-primary-700 hover:to-secondary-700 text-white px-8 py-4 rounded-xl font-semibold text-lg transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl;
}

.btn-secondary {
  @apply bg-white dark:bg-neutral-800 border-2 border-neutral-200 dark:border-neutral-700 text-neutral-700 dark:text-neutral-200 hover:border-primary-300 dark:hover:border-primary-600 hover:bg-neutral-50 dark:hover:bg-neutral-700 px-8 py-4 rounded-xl font-semibold text-lg transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl;
}

.btn-tertiary {
  @apply bg-transparent text-neutral-600 dark:text-neutral-400 hover:text-primary-600 dark:hover:text-primary-400 px-8 py-4 rounded-xl font-semibold text-lg transition-all duration-300 hover:scale-105;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Custom animations */
@keyframes glitch {
  0%, 100% { opacity: 0; transform: translate(0); }
  20% { opacity: 0.1; transform: translate(-2px, 2px); }
  40% { opacity: 0.1; transform: translate(-2px, -2px); }
  60% { opacity: 0.1; transform: translate(2px, 2px); }
  80% { opacity: 0.1; transform: translate(2px, -2px); }
}

@keyframes spin-slow {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.animate-glitch {
  animation: glitch 3s ease-in-out infinite;
}

.animate-spin-slow {
  animation: spin-slow 8s linear infinite;
}

/* Responsive typography adjustments */
@media (max-width: 640px) {
  .text-9xl {
    font-size: 6rem;
  }
}

@media (max-width: 480px) {
  .text-9xl {
    font-size: 4.5rem;
  }
}
</style> 