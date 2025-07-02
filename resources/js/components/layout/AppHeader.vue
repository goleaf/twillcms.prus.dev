<template>
  <header class="bg-white/95 backdrop-blur-sm shadow-sm border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo / Site Name -->
        <div class="flex items-center">
          <router-link 
            to="/" 
            class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent hover:from-blue-700 hover:to-purple-700 transition-all duration-200"
          >
            {{ siteStore.siteName }}
          </router-link>
        </div>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center space-x-8">
          <router-link
            to="/"
            class="text-gray-700 hover:text-blue-600 transition-colors font-medium"
            :class="{ 'text-blue-600': $route.name === 'home' }"
          >
            {{ t('navigation.home') }}
          </router-link>
          
          <router-link
            to="/blog"
            class="text-gray-700 hover:text-blue-600 transition-colors font-medium"
            :class="{ 'text-blue-600': $route.name?.toString().startsWith('blog') }"
          >
            {{ t('navigation.blog') }}
          </router-link>
          
          <!-- Categories Dropdown -->
          <div class="relative" ref="categoriesDropdown">
            <button
              @click="toggleCategoriesDropdown"
              class="flex items-center text-gray-700 hover:text-blue-600 transition-colors font-medium"
              :class="{ 'text-blue-600': showCategoriesDropdown || $route.name?.toString().startsWith('categories') }"
            >
              {{ t('navigation.categories') }}
              <ChevronDownIcon class="ml-1 h-4 w-4 transition-transform" :class="{ 'rotate-180': showCategoriesDropdown }" />
            </button>
            
            <transition
              enter-active-class="transition ease-out duration-200"
              enter-from-class="opacity-0 translate-y-1"
              enter-to-class="opacity-100 translate-y-0"
              leave-active-class="transition ease-in duration-150"
              leave-from-class="opacity-100 translate-y-0"
              leave-to-class="opacity-0 translate-y-1"
            >
              <div
                v-if="showCategoriesDropdown"
                class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none border border-gray-100"
              >
                <div class="py-2">
                  <router-link
                    to="/categories"
                    class="block px-4 py-2 text-sm font-medium text-gray-900 hover:bg-blue-50 hover:text-blue-600 border-b border-gray-100"
                    @click="closeCategoriesDropdown"
                  >
                    {{ t('categories.view_all_categories') }}
                  </router-link>
                  <router-link
                    v-for="category in categoryStore.navigationCategories"
                    :key="category.id"
                    :to="`/category/${category.slug}`"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors"
                    @click="closeCategoriesDropdown"
                  >
                    <span class="flex items-center justify-between">
                      <span class="flex items-center">
                        <span 
                          class="w-3 h-3 rounded-full mr-3 shadow-sm"
                          :style="{ backgroundColor: category.color }"
                        ></span>
                        {{ category.name }}
                      </span>
                      <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                        {{ category.posts_count }}
                      </span>
                    </span>
                  </router-link>
                </div>
              </div>
            </transition>
          </div>
        </nav>

        <!-- Search and Mobile Menu -->
        <div class="flex items-center space-x-2">
          <!-- Search Button -->
          <button
            @click="toggleSearch"
            class="p-2 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
            :aria-label="t('navigation.search')"
          >
            <MagnifyingGlassIcon class="h-5 w-5" />
          </button>

          <!-- Language Switcher -->
          <div class="relative">
            <button
              @click="toggleLanguageDropdown"
              class="flex items-center p-2 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
              :aria-label="t('navigation.language')"
            >
              <LanguageIcon class="h-5 w-5" />
              <span class="ml-1 text-sm font-medium uppercase">{{ currentLocale }}</span>
            </button>
            
            <transition
              enter-active-class="transition ease-out duration-200"
              enter-from-class="opacity-0 translate-y-1"
              enter-to-class="opacity-100 translate-y-0"
              leave-active-class="transition ease-in duration-150"
              leave-from-class="opacity-100 translate-y-0"
              leave-to-class="opacity-0 translate-y-1"
            >
              <div
                v-if="showLanguageDropdown"
                class="absolute right-0 mt-2 w-32 bg-white rounded-xl shadow-lg ring-1 ring-black ring-opacity-5 border border-gray-100"
              >
                <div class="py-2">
                  <button
                    v-for="locale in availableLocales"
                    :key="locale"
                    @click="switchLanguage(locale)"
                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                    :class="{ 'bg-blue-50 text-blue-600 font-medium': locale === currentLocale }"
                  >
                    <span class="flex items-center justify-between">
                      <span>{{ getLanguageName(locale) }}</span>
                      <span class="text-xs uppercase font-mono">{{ locale }}</span>
                    </span>
                  </button>
                </div>
              </div>
            </transition>
          </div>

          <!-- Mobile Menu Button -->
          <button
            @click="toggleMobileMenu"
            class="md:hidden p-2 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
            :aria-label="t('navigation.menu')"
          >
            <Bars3Icon v-if="!showMobileMenu" class="h-6 w-6" />
            <XMarkIcon v-else class="h-6 w-6" />
          </button>
        </div>
      </div>

      <!-- Mobile Navigation -->
      <transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 -translate-y-1"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-1"
      >
        <div v-if="showMobileMenu" class="md:hidden border-t border-gray-200 bg-white/95 backdrop-blur-sm">
          <div class="px-4 pt-4 pb-6 space-y-2">
            <router-link
              to="/"
              class="flex items-center px-3 py-3 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
              @click="closeMobileMenu"
            >
              <HomeIcon class="h-5 w-5 mr-3" />
              {{ t('navigation.home') }}
            </router-link>
            
            <router-link
              to="/blog"
              class="flex items-center px-3 py-3 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
              @click="closeMobileMenu"
            >
              <DocumentTextIcon class="h-5 w-5 mr-3" />
              {{ t('navigation.blog') }}
            </router-link>
            
            <router-link
              to="/categories"
              class="flex items-center px-3 py-3 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
              @click="closeMobileMenu"
            >
              <FolderIcon class="h-5 w-5 mr-3" />
              {{ t('navigation.categories') }}
            </router-link>

            <!-- Mobile Categories -->
            <div class="ml-4 space-y-1 border-l-2 border-gray-100 pl-4">
              <router-link
                v-for="category in categoryStore.navigationCategories.slice(0, 5)"
                :key="category.id"
                :to="`/category/${category.slug}`"
                class="flex items-center px-3 py-2 text-sm text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200"
                @click="closeMobileMenu"
              >
                <span 
                  class="w-3 h-3 rounded-full mr-3 shadow-sm"
                  :style="{ backgroundColor: category.color }"
                ></span>
                {{ category.name }}
                <span class="ml-auto text-xs text-gray-400">{{ category.posts_count }}</span>
              </router-link>
            </div>

            <!-- Mobile Language Switcher -->
            <div class="pt-4 border-t border-gray-200">
              <div class="flex items-center justify-between px-3 py-2">
                <span class="text-sm font-medium text-gray-700">{{ t('navigation.language') }}</span>
                <div class="flex space-x-2">
                  <button
                    v-for="locale in availableLocales"
                    :key="locale"
                    @click="switchLanguage(locale)"
                    class="px-3 py-1 text-sm rounded-lg transition-all duration-200"
                    :class="locale === currentLocale 
                      ? 'bg-blue-100 text-blue-600 font-medium' 
                      : 'text-gray-600 hover:bg-gray-100'"
                  >
                    {{ locale.toUpperCase() }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>

    <!-- Search Overlay -->
    <SearchOverlay v-if="showSearch" @close="closeSearch" />
  </header>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useSiteStore } from '@/stores/site';
import { useCategoryStore } from '@/stores/category';
import { useTranslations } from '@/composables/useTranslations';
import SearchOverlay from '@/components/ui/SearchOverlay.vue';
import {
  MagnifyingGlassIcon,
  Bars3Icon,
  XMarkIcon,
  ChevronDownIcon,
  LanguageIcon,
  HomeIcon,
  DocumentTextIcon,
  FolderIcon,
} from '@heroicons/vue/24/outline';

const siteStore = useSiteStore();
const categoryStore = useCategoryStore();
const { t, currentLocale, availableLocales, setLocale } = useTranslations();

const showMobileMenu = ref(false);
const showCategoriesDropdown = ref(false);
const showLanguageDropdown = ref(false);
const showSearch = ref(false);
const categoriesDropdown = ref<HTMLElement>();

// Language names mapping
const getLanguageName = (locale: string): string => {
  const names: Record<string, string> = {
    'en': 'English',
    'lt': 'Lietuvių',
  };
  return names[locale] || locale.toUpperCase();
};

// Mobile menu handlers
const toggleMobileMenu = () => {
  showMobileMenu.value = !showMobileMenu.value;
  if (showMobileMenu.value) {
    showCategoriesDropdown.value = false;
    showLanguageDropdown.value = false;
  }
};

const closeMobileMenu = () => {
  showMobileMenu.value = false;
};

// Categories dropdown handlers
const toggleCategoriesDropdown = () => {
  showCategoriesDropdown.value = !showCategoriesDropdown.value;
  showLanguageDropdown.value = false;
  showMobileMenu.value = false;
};

const closeCategoriesDropdown = () => {
  showCategoriesDropdown.value = false;
};

// Language dropdown handlers
const toggleLanguageDropdown = () => {
  showLanguageDropdown.value = !showLanguageDropdown.value;
  showCategoriesDropdown.value = false;
  showMobileMenu.value = false;
};

const switchLanguage = async (locale: string) => {
  await setLocale(locale);
  showLanguageDropdown.value = false;
  showMobileMenu.value = false;
};

// Search handlers
const toggleSearch = () => {
  showSearch.value = !showSearch.value;
  showMobileMenu.value = false;
  showCategoriesDropdown.value = false;
  showLanguageDropdown.value = false;
};

const closeSearch = () => {
  showSearch.value = false;
};

// Click outside handlers
const handleClickOutside = (event: Event) => {
  if (categoriesDropdown.value && !categoriesDropdown.value.contains(event.target as Node)) {
    closeCategoriesDropdown();
  }
};

// Close dropdowns on route change
const closeAllDropdowns = () => {
  showMobileMenu.value = false;
  showCategoriesDropdown.value = false;
  showLanguageDropdown.value = false;
  showSearch.value = false;
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
  
  // Load navigation categories if not already loaded
  if (categoryStore.navigationCategories.length === 0) {
    categoryStore.fetchNavigationCategories();
  }
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

// Close dropdowns on route change
import { useRouter } from 'vue-router';
const router = useRouter();
router.afterEach(() => {
  closeAllDropdowns();
});
</script>
