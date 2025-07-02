<template>
  <header class="news-header bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Top Bar -->
      <div class="border-b border-gray-100 py-2">
        <div class="flex items-center justify-between text-sm text-gray-600">
          <div class="flex items-center space-x-4">
            <time :datetime="currentDate" class="font-medium">
              {{ formatCurrentDate() }}
            </time>
            <span class="h-3 w-px bg-gray-300"></span>
            <span>{{ currentWeather }}</span>
          </div>
          <div class="flex items-center space-x-4">
            <span class="font-medium">Breaking:</span>
            <span class="text-red-600">{{ breakingNews }}</span>
          </div>
        </div>
      </div>

      <!-- Main Header -->
      <div class="py-4 lg:py-6">
        <div class="flex items-center justify-between">
          <!-- Logo & Title -->
          <div class="flex items-center">
            <router-link to="/" class="flex items-center space-x-3">
              <img
                v-if="siteLogo"
                :src="siteLogo"
                :alt="siteName"
                class="h-8 lg:h-12 w-auto"
              />
              <div class="text-center">
                <h1 class="text-2xl lg:text-4xl font-bold text-gray-900 font-serif tracking-tight">
                  {{ siteName }}
                </h1>
                <p class="text-xs lg:text-sm text-gray-600 font-medium tracking-wide uppercase">
                  {{ siteTagline }}
                </p>
              </div>
            </router-link>
          </div>

          <!-- Actions -->
          <div class="flex items-center space-x-4">
            <!-- Search -->
            <button
              @click="toggleSearch"
              class="p-2 text-gray-600 hover:text-gray-900 transition-colors"
              aria-label="Search"
            >
              <MagnifyingGlassIcon class="h-5 w-5" />
            </button>

            <!-- Dark Mode Toggle -->
            <button
              @click="toggleDarkMode"
              class="p-2 text-gray-600 hover:text-gray-900 transition-colors"
              :aria-label="isDarkMode ? 'Switch to light mode' : 'Switch to dark mode'"
            >
              <SunIcon v-if="isDarkMode" class="h-5 w-5" />
              <MoonIcon v-else class="h-5 w-5" />
            </button>

            <!-- Newsletter -->
            <button
              @click="$emit('newsletter-signup')"
              class="hidden lg:inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors"
            >
              Subscribe
            </button>
          </div>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="border-t border-gray-100" aria-label="Main navigation">
        <div class="flex items-center justify-between py-4">
          <!-- Main Navigation -->
          <div class="flex items-center space-x-8">
            <router-link
              v-for="category in mainCategories"
              :key="category.id"
              :to="`/category/${category.slug}`"
              class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors relative group"
              :class="{ 'text-blue-600': isActiveCategory(category.slug) }"
            >
              {{ category.name }}
              <span 
                class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 transition-all group-hover:w-full"
                :class="{ 'w-full': isActiveCategory(category.slug) }"
              ></span>
            </router-link>
          </div>

          <!-- Secondary Navigation -->
          <div class="flex items-center space-x-6 text-sm text-gray-600">
            <router-link to="/archive" class="hover:text-gray-900 transition-colors">
              Archive
            </router-link>
            <router-link to="/about" class="hover:text-gray-900 transition-colors">
              About
            </router-link>
            <router-link to="/contact" class="hover:text-gray-900 transition-colors">
              Contact
            </router-link>
          </div>
        </div>
      </nav>
    </div>

    <!-- Search Overlay -->
    <div
      v-if="showSearch"
      class="absolute top-full left-0 right-0 bg-white border-b border-gray-200 shadow-lg"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="relative">
          <input
            ref="searchInput"
            v-model="searchQuery"
            type="text"
            placeholder="Search articles..."
            class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            @keyup.enter="performSearch"
            @keyup.escape="closeSearch"
          />
          <MagnifyingGlassIcon class="absolute left-3 top-3.5 h-5 w-5 text-gray-400" />
          <button
            @click="closeSearch"
            class="absolute right-3 top-3 p-1 text-gray-400 hover:text-gray-600"
          >
            <XMarkIcon class="h-5 w-5" />
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, computed, nextTick, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { 
  MagnifyingGlassIcon, 
  SunIcon, 
  MoonIcon, 
  XMarkIcon 
} from '@heroicons/vue/24/outline'

// Props
interface Category {
  id: number
  name: string
  slug: string
}

interface Props {
  siteName?: string
  siteTagline?: string
  siteLogo?: string
  mainCategories?: Category[]
  breakingNews?: string
  currentWeather?: string
}

const props = withDefaults(defineProps<Props>(), {
  siteName: 'Enterprise News',
  siteTagline: 'Your trusted source for news',
  siteLogo: '',
  mainCategories: () => [],
  breakingNews: 'Stay informed with our latest updates',
  currentWeather: '72°F, Partly Cloudy'
})

// Emits
defineEmits<{
  'newsletter-signup': []
  'search': [query: string]
}>()

// State
const route = useRoute()
const showSearch = ref(false)
const searchQuery = ref('')
const searchInput = ref<HTMLInputElement>()
const isDarkMode = ref(false)

// Computed
const currentDate = computed(() => new Date().toISOString().split('T')[0])

// Methods
const formatCurrentDate = () => {
  return new Date().toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const isActiveCategory = (slug: string) => {
  return route.path.includes(`/category/${slug}`)
}

const toggleSearch = async () => {
  showSearch.value = !showSearch.value
  if (showSearch.value) {
    await nextTick()
    searchInput.value?.focus()
  }
}

const closeSearch = () => {
  showSearch.value = false
  searchQuery.value = ''
}

const performSearch = () => {
  if (searchQuery.value.trim()) {
    // Emit search event or navigate to search results
    console.log('Searching for:', searchQuery.value)
    closeSearch()
  }
}

const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value
  document.documentElement.classList.toggle('dark', isDarkMode.value)
  localStorage.setItem('darkMode', isDarkMode.value.toString())
}

// Lifecycle
onMounted(() => {
  // Check for saved dark mode preference
  const savedDarkMode = localStorage.getItem('darkMode')
  if (savedDarkMode === 'true') {
    isDarkMode.value = true
    document.documentElement.classList.add('dark')
  }
})
</script>

<style scoped>
.news-header {
  font-family: 'Inter', sans-serif;
}

.news-header h1 {
  font-family: 'Playfair Display', serif;
  font-feature-settings: 'kern' 1, 'liga' 1;
}

/* Typography hierarchy */
.news-header h1 {
  line-height: 1.1;
}

/* Sticky header on scroll */
.news-header.scrolled {
  @apply shadow-md;
}

/* Mobile responsive adjustments */
@media (max-width: 640px) {
  .news-header nav {
    @apply overflow-x-auto;
  }
  
  .news-header nav > div {
    @apply space-x-4;
  }
}

/* High contrast mode */
@media (prefers-contrast: high) {
  .news-header {
    @apply border-2 border-black;
  }
}

/* Print styles */
@media print {
  .news-header {
    @apply static shadow-none border-b-2 border-black;
  }
  
  .news-header nav,
  .news-header button {
    @apply hidden;
  }
}
</style> 