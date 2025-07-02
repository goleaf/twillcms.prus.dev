<template>
  <footer class="bg-gradient-to-r from-gray-900 to-gray-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Site Info -->
        <div class="lg:col-span-2">
          <h3 class="text-2xl font-bold mb-4 bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
            {{ siteStore.siteName }}
          </h3>
          <p class="text-gray-300 mb-6 leading-relaxed max-w-md">
            {{ siteStore.siteDescription || t('blog.site_description') }}
          </p>
          
          <!-- Social Links (if available) -->
          <div v-if="siteStore.socialLinks && Object.keys(siteStore.socialLinks).length > 0" class="flex space-x-4">
            <a
              v-for="(url, platform) in siteStore.socialLinks"
              :key="platform"
              :href="url"
              target="_blank"
              rel="noopener noreferrer"
              class="p-2 bg-gray-700 hover:bg-blue-600 rounded-lg transition-colors duration-200"
              :aria-label="`Follow us on ${platform}`"
            >
              <component :is="getSocialIcon(platform)" class="h-5 w-5" />
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div>
          <h4 class="text-lg font-semibold mb-4 text-white">{{ t('navigation.menu') }}</h4>
          <ul class="space-y-2">
            <li>
              <router-link
                to="/"
                class="text-gray-300 hover:text-blue-400 transition-colors duration-200 flex items-center"
              >
                <HomeIcon class="h-4 w-4 mr-2" />
                {{ t('navigation.home') }}
              </router-link>
            </li>
            <li>
              <router-link
                to="/blog"
                class="text-gray-300 hover:text-blue-400 transition-colors duration-200 flex items-center"
              >
                <DocumentTextIcon class="h-4 w-4 mr-2" />
                {{ t('navigation.blog') }}
              </router-link>
            </li>
            <li>
              <router-link
                to="/categories"
                class="text-gray-300 hover:text-blue-400 transition-colors duration-200 flex items-center"
              >
                <FolderIcon class="h-4 w-4 mr-2" />
                {{ t('navigation.categories') }}
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Popular Categories -->
        <div>
          <h4 class="text-lg font-semibold mb-4 text-white">{{ t('categories.popular_categories') }}</h4>
          <ul class="space-y-2">
            <li v-for="category in categoryStore.popularCategories.slice(0, 5)" :key="category.id">
              <router-link
                :to="`/category/${category.slug}`"
                class="text-gray-300 hover:text-blue-400 transition-colors duration-200 flex items-center"
              >
                <span 
                  class="w-2 h-2 rounded-full mr-2"
                  :style="{ backgroundColor: category.color }"
                ></span>
                {{ category.name }}
                <span class="ml-auto text-xs text-gray-500">({{ category.posts_count }})</span>
              </router-link>
            </li>
          </ul>
        </div>
      </div>

      <!-- Bottom Section -->
      <div class="border-t border-gray-700 mt-12 pt-8">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <div class="text-center md:text-left mb-4 md:mb-0">
            <p class="text-gray-300">
              &copy; {{ currentYear }} {{ siteStore.siteName }}. {{ t('blog.all_rights_reserved') }}
            </p>
          </div>
          
          <div class="flex items-center space-x-4 text-sm text-gray-400">
            <span>{{ t('blog.made_with_twill') }}</span>
            <div class="flex items-center space-x-2">
              <span>{{ t('navigation.language') }}:</span>
              <button
                v-for="locale in availableLocales"
                :key="locale"
                @click="setLocale(locale)"
                class="px-2 py-1 rounded transition-colors duration-200"
                :class="locale === currentLocale 
                  ? 'bg-blue-600 text-white' 
                  : 'text-gray-400 hover:text-white hover:bg-gray-700'"
              >
                {{ locale.toUpperCase() }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Back to Top Button -->
    <BackToTop />
  </footer>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { useSiteStore } from '@/stores/site';
import { useCategoryStore } from '@/stores/category';
import { useTranslations } from '@/composables/useTranslations';
import BackToTop from '@/components/ui/BackToTop.vue';
import {
  HomeIcon,
  DocumentTextIcon,
  FolderIcon,
} from '@heroicons/vue/24/outline';

const siteStore = useSiteStore();
const categoryStore = useCategoryStore();
const { t, currentLocale, availableLocales, setLocale } = useTranslations();

const currentYear = computed(() => new Date().getFullYear());

// Social media icons mapping
const getSocialIcon = (platform: string) => {
  // This is a placeholder - you can add actual social media icons
  // For now, we'll use a generic icon
  return DocumentTextIcon;
};

onMounted(() => {
  // Load popular categories if not already loaded
  if (categoryStore.popularCategories.length === 0) {
    categoryStore.fetchPopularCategories(5);
  }
});
</script>
