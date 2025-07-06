<template>
  <div id="app" :class="{ 'dark': isDarkMode }" class="min-h-screen bg-neutral-50 dark:bg-neutral-900 transition-colors duration-300">
    <!-- Navigation Header -->
    <header class="bg-white/90 dark:bg-neutral-900/90 backdrop-blur-md border-b border-neutral-200 dark:border-neutral-800 sticky top-0 z-50 transition-all duration-300">
      <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <div class="flex-shrink-0">
            <router-link to="/" class="flex items-center group">
              <div class="w-8 h-8 bg-gradient-to-r from-primary-500 to-secondary-500 rounded-lg mr-3 flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"/>
                </svg>
              </div>
              <span class="text-xl font-bold bg-gradient-to-r from-neutral-900 to-neutral-600 dark:from-white dark:to-neutral-300 bg-clip-text text-transparent group-hover:from-primary-600 group-hover:to-secondary-600 transition-all duration-300">
                {{ $t('common.home') }}
              </span>
            </router-link>
          </div>

          <!-- Desktop Navigation -->
          <div class="hidden md:block">
            <div class="ml-10 flex items-center space-x-1">
              <router-link 
                to="/" 
                class="nav-link"
                :class="{ 'nav-link-active': $route.name === 'home' }"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                {{ $t('common.home') }}
              </router-link>
              <router-link 
                to="/categories" 
                class="nav-link"
                :class="{ 'nav-link-active': $route.name === 'categories.index' }"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-4a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2m14 0V6a2 2 0 00-2-2H7a2 2 0 00-2 2v1m14 0H5"/>
                </svg>
                {{ $t('categories.categories') }}
              </router-link>
              <router-link 
                to="/archives" 
                class="nav-link"
                :class="{ 'nav-link-active': $route.name === 'archives.index' }"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ $t('archive.title') }}
              </router-link>
            </div>
          </div>

          <!-- Search & Theme Toggle -->
          <div class="flex items-center space-x-2">
            <!-- Search Button -->
            <button 
              @click="toggleSearch"
              class="p-2 rounded-lg text-neutral-600 dark:text-neutral-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-all duration-200"
              aria-label="Search"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </button>

            <!-- Newsletter Button -->
            <BaseTooltip content="Subscribe to our newsletter" placement="bottom">
              <button
                @click="showNewsletterModal = true"
                class="p-2 rounded-lg text-neutral-600 dark:text-neutral-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-all duration-200"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
              </button>
            </BaseTooltip>

            <!-- Keyboard Shortcuts Button -->
            <BaseTooltip content="Keyboard shortcuts" placement="bottom">
              <button
                @click="showShortcutsModal = true"
                class="p-2 rounded-lg text-neutral-600 dark:text-neutral-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-all duration-200"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </button>
            </BaseTooltip>

            <!-- Dark Mode Toggle -->
            <button 
              @click="toggleDarkMode"
              class="p-2 rounded-lg text-neutral-600 dark:text-neutral-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-all duration-200"
              aria-label="Toggle dark mode"
            >
              <svg v-if="isDarkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
              </svg>
            </button>

            <!-- Mobile menu button -->
            <button 
              @click="toggleMobileMenu"
              class="md:hidden p-2 rounded-lg text-neutral-600 dark:text-neutral-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-all duration-200"
              aria-label="Open menu"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Navigation -->
        <Transition
          enter-active-class="transition duration-200 ease-out"
          enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100"
          leave-active-class="transition duration-150 ease-in"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-95"
        >
          <div v-if="mobileMenuOpen" class="md:hidden border-t border-neutral-200 dark:border-neutral-800 mt-4 pt-4 pb-4">
            <div class="flex flex-col space-y-2">
              <router-link 
                to="/" 
                class="mobile-nav-link"
                @click="closeMobileMenu"
              >
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                {{ $t('common.home') }}
              </router-link>
              <router-link 
                to="/categories" 
                class="mobile-nav-link"
                @click="closeMobileMenu"
              >
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-4a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2m14 0V6a2 2 0 00-2-2H7a2 2 0 00-2 2v1m14 0H5"/>
                </svg>
                {{ $t('categories.categories') }}
              </router-link>
              <router-link 
                to="/archives" 
                class="mobile-nav-link"
                @click="closeMobileMenu"
              >
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ $t('archive.title') }}
              </router-link>
            </div>
          </div>
        </Transition>
      </nav>
    </header>

    <!-- Search Overlay -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="searchOpen" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm" @click="closeSearch">
        <div class="absolute top-20 left-1/2 transform -translate-x-1/2 w-full max-w-2xl mx-auto px-4">
          <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-strong p-6" @click.stop>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
              <input 
                ref="searchInput"
                v-model="searchQuery"
                @keyup.enter="performSearch"
                @keyup.escape="closeSearch"
                type="text" 
                class="block w-full pl-12 pr-4 py-4 border-0 rounded-xl bg-neutral-50 dark:bg-neutral-700 text-neutral-900 dark:text-white placeholder-neutral-500 dark:placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary-500 text-lg"
                placeholder="Search articles..."
                autocomplete="off"
              >
            </div>
            <div class="mt-4 text-sm text-neutral-500 dark:text-neutral-400">
              Press Enter to search or Escape to close
            </div>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Main Content -->
    <main class="relative">
      <Transition
        name="page"
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 transform translate-y-4"
        enter-to-class="opacity-100 transform translate-y-0"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 transform translate-y-0"
        leave-to-class="opacity-0 transform translate-y-4"
        mode="out-in"
      >
        <router-view />
      </Transition>
    </main>

    <!-- Back to Top Button -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 scale-0"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-0"
    >
      <button
        v-if="showBackToTop"
        @click="scrollToTop"
        class="fixed bottom-8 right-8 z-40 w-12 h-12 bg-gradient-to-r from-primary-500 to-secondary-500 text-white rounded-full shadow-strong hover:shadow-glow transform hover:scale-110 transition-all duration-300 flex items-center justify-center"
        aria-label="Back to top"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
        </svg>
      </button>
    </Transition>

    <!-- Footer -->
    <footer class="bg-white dark:bg-neutral-900 border-t border-neutral-200 dark:border-neutral-800 mt-20">
      <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <!-- Brand -->
          <div class="col-span-1 md:col-span-2">
            <div class="flex items-center mb-4">
              <div class="w-8 h-8 bg-gradient-to-r from-primary-500 to-secondary-500 rounded-lg mr-3 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"/>
                </svg>
              </div>
              <span class="text-xl font-bold bg-gradient-to-r from-neutral-900 to-neutral-600 dark:from-white dark:to-neutral-300 bg-clip-text text-transparent">
                {{ $t('common.home') }}
              </span>
            </div>
            <p class="text-neutral-600 dark:text-neutral-400 max-w-md">
              Your trusted source for the latest news and insights. Stay informed with our comprehensive coverage of global events.
            </p>
          </div>

          <!-- Quick Links -->
          <div>
            <h3 class="text-sm font-semibold text-neutral-900 dark:text-white uppercase tracking-wider mb-4">Navigation</h3>
            <ul class="space-y-2">
              <li><router-link to="/" class="footer-link">Home</router-link></li>
              <li><router-link to="/categories" class="footer-link">Categories</router-link></li>
              <li><router-link to="/archives" class="footer-link">Archives</router-link></li>
            </ul>
          </div>

          <!-- Social -->
          <div>
            <h3 class="text-sm font-semibold text-neutral-900 dark:text-white uppercase tracking-wider mb-4">Follow Us</h3>
            <div class="flex space-x-3">
              <a href="#" class="social-link">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                </svg>
              </a>
              <a href="#" class="social-link">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                </svg>
              </a>
              <a href="#" class="social-link">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.042-3.441.219-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 2.568-1.649 0-3.785-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 01.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.357-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001 12.017.001z"/>
                </svg>
              </a>
            </div>
          </div>
        </div>

        <div class="mt-8 pt-8 border-t border-neutral-200 dark:border-neutral-800">
          <div class="flex flex-col md:flex-row justify-between items-center">
            <p class="text-neutral-500 dark:text-neutral-400 text-sm">
              &copy; {{ currentYear }} NewsHub. Built with Vue.js and Laravel.
            </p>
            <div class="mt-4 md:mt-0 flex space-x-6">
              <a href="#" class="footer-link text-sm">Privacy Policy</a>
              <a href="#" class="footer-link text-sm">Terms of Service</a>
              <a href="#" class="footer-link text-sm">Contact</a>
            </div>
          </div>
        </div>
      </div>
    </footer>

    <!-- Lazy Load Modals -->
    <Suspense>
      <template #default>
    <NewsletterModal
          v-if="showNewsletterModal"
      :show="showNewsletterModal"
      @close="showNewsletterModal = false"
      @subscribe="handleNewsletterSubscribe"
    />
      </template>
      <template #fallback>
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500"></div>
        </div>
      </template>
    </Suspense>

    <!-- Keyboard Shortcuts Modal -->
    <Suspense>
      <template #default>
    <BaseModal
          v-if="showShortcutsModal"
      :show="showShortcutsModal"
      @close="showShortcutsModal = false"
      title="Keyboard Shortcuts"
      size="md"
    >
      <div class="space-y-4">
        <div class="grid grid-cols-1 gap-3">
          <div class="flex items-center justify-between py-2 px-3 bg-neutral-50 dark:bg-neutral-800 rounded-lg">
            <span class="text-sm font-medium">Toggle Search</span>
            <div class="flex items-center space-x-1">
              <kbd class="kbd">Ctrl</kbd>
              <span class="text-neutral-400">+</span>
              <kbd class="kbd">K</kbd>
            </div>
          </div>
          <div class="flex items-center justify-between py-2 px-3 bg-neutral-50 dark:bg-neutral-800 rounded-lg">
            <span class="text-sm font-medium">Toggle Dark Mode</span>
            <div class="flex items-center space-x-1">
              <kbd class="kbd">Ctrl</kbd>
              <span class="text-neutral-400">+</span>
              <kbd class="kbd">D</kbd>
            </div>
          </div>
          <div class="flex items-center justify-between py-2 px-3 bg-neutral-50 dark:bg-neutral-800 rounded-lg">
            <span class="text-sm font-medium">Show Shortcuts</span>
            <div class="flex items-center space-x-1">
              <kbd class="kbd">?</kbd>
            </div>
          </div>
          <div class="flex items-center justify-between py-2 px-3 bg-neutral-50 dark:bg-neutral-800 rounded-lg">
            <span class="text-sm font-medium">Go to Home</span>
            <div class="flex items-center space-x-1">
              <kbd class="kbd">G</kbd>
              <span class="text-neutral-400">+</span>
              <kbd class="kbd">H</kbd>
            </div>
          </div>
        </div>
      </div>
    </BaseModal>
      </template>
      <template #fallback>
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500"></div>
        </div>
      </template>
    </Suspense>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, nextTick, defineAsyncComponent } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useThrottleFn, useDebounce } from '@vueuse/core'

// Lazy load components for better performance
const NewsletterModal = defineAsyncComponent(() => import('./ui/NewsletterModal.vue'))
const BaseModal = defineAsyncComponent(() => import('./ui/BaseModal.vue'))
const BaseTooltip = defineAsyncComponent(() => import('./ui/BaseTooltip.vue'))

// Composables
const route = useRoute()
const router = useRouter()

// Reactive state
    const searchQuery = ref('')
    const mobileMenuOpen = ref(false)
    const searchOpen = ref(false)
    const showBackToTop = ref(false)
    const isDarkMode = ref(false)
    const showNewsletterModal = ref(false)
    const showShortcutsModal = ref(false)
const searchInput = ref(null)

// Computed properties
const currentYear = computed(() => new Date().getFullYear())

// Debounced search query for better performance
const debouncedSearchQuery = useDebounce(searchQuery, 300)

// Throttled scroll handler for better performance
const throttledScrollHandler = useThrottleFn(() => {
  showBackToTop.value = window.scrollY > 300
}, 100)

// Initialize dark mode and event listeners
    onMounted(() => {
  initializeDarkMode()
  setupEventListeners()
})

// Cleanup event listeners
onUnmounted(() => {
  cleanup()
})

// Methods
const initializeDarkMode = () => {
      const savedDarkMode = localStorage.getItem('darkMode')
      if (savedDarkMode !== null) {
        isDarkMode.value = JSON.parse(savedDarkMode)
      } else {
        isDarkMode.value = window.matchMedia('(prefers-color-scheme: dark)').matches
  }
      }

const setupEventListeners = () => {
  window.addEventListener('scroll', throttledScrollHandler, { passive: true })
      document.addEventListener('keydown', handleKeyboardShortcuts)
}

const cleanup = () => {
  window.removeEventListener('scroll', throttledScrollHandler)
      document.removeEventListener('keydown', handleKeyboardShortcuts)
}

    const toggleDarkMode = () => {
      isDarkMode.value = !isDarkMode.value
      localStorage.setItem('darkMode', JSON.stringify(isDarkMode.value))
  
  // Add smooth transition
  document.documentElement.style.transition = 'color-scheme 0.3s ease'
  setTimeout(() => {
    document.documentElement.style.transition = ''
  }, 300)
    }

    const toggleMobileMenu = () => {
      mobileMenuOpen.value = !mobileMenuOpen.value
    }

const closeMobileMenu = () => {
  mobileMenuOpen.value = false
}

    const toggleSearch = () => {
      searchOpen.value = !searchOpen.value
      if (searchOpen.value) {
    nextTick(() => {
      searchInput.value?.focus()
    })
      }
    }

const closeSearch = () => {
        searchOpen.value = false
  searchQuery.value = ''
    }

const performSearch = async () => {
  if (debouncedSearchQuery.value.trim()) {
    try {
      await router.push({
        name: 'search',
        query: { q: debouncedSearchQuery.value.trim() }
      })
      closeSearch()
      closeMobileMenu()
    } catch (error) {
      console.error('Search navigation failed:', error)
    }
  }
    }

    const scrollToTop = () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      })
    }

const handleNewsletterSubscribe = async (data) => {
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
      console.log('Newsletter subscription:', data)
        showNewsletterModal.value = false
  } catch (error) {
    console.error('Newsletter subscription failed:', error)
  }
    }

    const handleKeyboardShortcuts = (e) => {
  // Prevent shortcuts when typing in inputs
  if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
    return
  }

  switch (true) {
    case (e.ctrlKey || e.metaKey) && e.key === 'k':
      e.preventDefault()
      toggleSearch()
      break
    case (e.ctrlKey || e.metaKey) && e.key === 'd':
        e.preventDefault()
        toggleDarkMode()
      break
    case e.key === '?' && !searchOpen.value:
        e.preventDefault()
        showShortcutsModal.value = true
      break
    case e.key === 'Escape':
      if (searchOpen.value) {
        closeSearch()
      } else if (mobileMenuOpen.value) {
        closeMobileMenu()
      } else if (showNewsletterModal.value) {
        showNewsletterModal.value = false
      } else if (showShortcutsModal.value) {
        showShortcutsModal.value = false
      }
      break
  }
}
</script>

<style scoped>
.nav-link {
  @apply flex items-center px-3 py-2 rounded-lg text-sm font-medium text-neutral-700 dark:text-neutral-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-all duration-200;
}

.nav-link-active {
  @apply text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20;
}

.mobile-nav-link {
  @apply flex items-center px-4 py-3 text-base font-medium text-neutral-700 dark:text-neutral-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg transition-all duration-200;
}

.footer-link {
  @apply text-neutral-600 dark:text-neutral-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200;
}

.social-link {
  @apply w-10 h-10 bg-neutral-100 dark:bg-neutral-800 text-neutral-600 dark:text-neutral-400 hover:bg-primary-100 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 rounded-lg flex items-center justify-center transition-all duration-200 hover:scale-110;
}

.kbd {
  @apply inline-flex items-center px-2 py-1 text-xs font-mono font-medium bg-neutral-100 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 rounded text-neutral-700 dark:text-neutral-300;
}

/* Page transitions */
.page-enter-active,
.page-leave-active {
  transition: all 0.3s ease;
}

.page-enter-from {
  opacity: 0;
  transform: translateY(20px);
}

.page-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}

/* Performance optimizations */
.nav-link,
.mobile-nav-link,
.footer-link,
.social-link {
  will-change: transform;
}

/* Optimized transitions */
@media (prefers-reduced-motion: reduce) {
  .nav-link,
  .mobile-nav-link,
  .footer-link,
  .social-link {
    transition: none;
  }
}
</style> 