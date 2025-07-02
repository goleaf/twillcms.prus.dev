<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <!-- Skip to main content link for accessibility -->
    <a 
      href="#main-content" 
      class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-blue-600 text-white px-4 py-2 rounded-md z-50"
    >
      Skip to main content
    </a>

    <!-- Header -->
    <AppHeader />

    <!-- Main Content -->
    <main id="main-content" class="flex-1">
      <router-view v-slot="{ Component, route }">
        <transition 
          name="page" 
          mode="out-in"
          @enter="onPageEnter"
          @leave="onPageLeave"
        >
          <component 
            :is="Component" 
            :key="route.path"
            class="min-h-screen"
          />
        </transition>
      </router-view>
    </main>

    <!-- Footer -->
    <AppFooter />

    <!-- Loading Overlay -->
    <LoadingOverlay v-if="isInitializing" />

    <!-- Error Notification -->
    <ErrorNotification />

    <!-- Back to Top Button -->
    <BackToTop />
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useSiteStore } from '@/stores/site';
import { useCategoryStore } from '@/stores/category';
import { useBlogStore } from '@/stores/blog';
import AppHeader from '@/components/layout/AppHeader.vue';
import AppFooter from '@/components/layout/AppFooter.vue';
import LoadingOverlay from '@/components/ui/LoadingOverlay.vue';
import ErrorNotification from '@/components/ui/ErrorNotification.vue';
import BackToTop from '@/components/ui/BackToTop.vue';

const siteStore = useSiteStore();
const categoryStore = useCategoryStore();
const blogStore = useBlogStore();

const isInitializing = ref(true);

// Page transition handlers
const onPageEnter = (el: Element) => {
  // Add any enter animations here
  el.classList.add('page-enter-active');
};

const onPageLeave = (el: Element) => {
  // Add any leave animations here
  el.classList.add('page-leave-active');
};

// Initialize application
onMounted(async () => {
  try {
    // Initialize core data
    await Promise.all([
      siteStore.initialize(),
      categoryStore.initializeNavigation(),
      blogStore.initializeSidebar(),
    ]);
  } catch (error) {
    console.error('Failed to initialize application:', error);
  } finally {
    isInitializing.value = false;
  }
});
</script>

<style scoped>
/* Page transition animations */
.page-enter-active,
.page-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.page-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.page-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Accessibility improvements */
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}

.focus\:not-sr-only:focus {
  position: static;
  width: auto;
  height: auto;
  padding: 0.5rem 1rem;
  margin: 0;
  overflow: visible;
  clip: auto;
  white-space: normal;
}

/* Smooth scrolling for the entire page */
html {
  scroll-behavior: smooth;
}

/* Focus styles for better accessibility */
*:focus {
  outline: 2px solid #3B82F6;
  outline-offset: 2px;
}

/* Reduced motion for accessibility */
@media (prefers-reduced-motion: reduce) {
  .page-enter-active,
  .page-leave-active {
    transition: none;
  }
  
  html {
    scroll-behavior: auto;
  }
}
</style> 