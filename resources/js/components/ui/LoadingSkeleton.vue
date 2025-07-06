<template>
  <div class="animate-pulse">
    <!-- Card Layout -->
    <div v-if="variant === 'card'" class="bg-white dark:bg-neutral-800 rounded-2xl shadow-lg overflow-hidden">
      <!-- Image Placeholder -->
      <div class="h-48 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 loading-shimmer"></div>
      
      <!-- Content -->
      <div class="p-6">
        <!-- Category Badge -->
        <div class="w-20 h-5 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded-full mb-3 loading-shimmer"></div>
        
        <!-- Title -->
        <div class="space-y-2 mb-4">
          <div class="h-6 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded loading-shimmer"></div>
          <div class="h-6 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded w-3/4 loading-shimmer"></div>
        </div>
        
        <!-- Description -->
        <div class="space-y-2 mb-4">
          <div class="h-4 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded loading-shimmer"></div>
          <div class="h-4 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded loading-shimmer"></div>
          <div class="h-4 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded w-2/3 loading-shimmer"></div>
        </div>
        
        <!-- Meta -->
        <div class="flex items-center space-x-4">
          <div class="w-8 h-8 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded-full loading-shimmer"></div>
          <div class="flex-1 space-y-1">
            <div class="h-3 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded w-1/3 loading-shimmer"></div>
            <div class="h-3 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded w-1/4 loading-shimmer"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Hero Layout -->
    <div v-else-if="variant === 'hero'" class="bg-white dark:bg-neutral-800 rounded-3xl shadow-strong overflow-hidden">
      <!-- Large Image -->
      <div class="h-96 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 loading-shimmer"></div>
      
      <!-- Content Overlay -->
      <div class="p-8 -mt-20 relative z-10">
        <div class="bg-white dark:bg-neutral-800 rounded-2xl p-8 shadow-strong">
          <!-- Category -->
          <div class="w-24 h-6 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded-full mb-4 loading-shimmer"></div>
          
          <!-- Title -->
          <div class="space-y-3 mb-6">
            <div class="h-8 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded loading-shimmer"></div>
            <div class="h-8 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded w-4/5 loading-shimmer"></div>
          </div>
          
          <!-- Description -->
          <div class="space-y-2 mb-6">
            <div class="h-4 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded loading-shimmer"></div>
            <div class="h-4 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded loading-shimmer"></div>
            <div class="h-4 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded w-3/4 loading-shimmer"></div>
          </div>
          
          <!-- Meta -->
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="w-10 h-10 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded-full loading-shimmer"></div>
              <div class="space-y-1">
                <div class="h-4 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded w-20 loading-shimmer"></div>
                <div class="h-3 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded w-16 loading-shimmer"></div>
              </div>
            </div>
            <div class="w-24 h-10 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded-xl loading-shimmer"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- List Layout -->
    <div v-else-if="variant === 'list'" class="bg-white dark:bg-neutral-800 rounded-xl shadow-lg p-6">
      <div class="flex items-start space-x-4">
        <!-- Image -->
        <div class="w-24 h-24 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded-lg loading-shimmer flex-shrink-0"></div>
        
        <!-- Content -->
        <div class="flex-1 space-y-3">
          <!-- Category -->
          <div class="w-16 h-4 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded-full loading-shimmer"></div>
          
          <!-- Title -->
          <div class="space-y-2">
            <div class="h-5 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded loading-shimmer"></div>
            <div class="h-5 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded w-3/4 loading-shimmer"></div>
          </div>
          
          <!-- Meta -->
          <div class="flex items-center space-x-4">
            <div class="h-3 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded w-20 loading-shimmer"></div>
            <div class="h-3 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded w-16 loading-shimmer"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Text Layout -->
    <div v-else-if="variant === 'text'" class="space-y-2">
      <div v-for="i in lines" :key="i" class="h-4 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded loading-shimmer"
        :class="i === lines ? 'w-3/4' : 'w-full'"
      ></div>
    </div>

    <!-- Grid Layout -->
    <div v-else-if="variant === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="i in count" :key="i" class="bg-white dark:bg-neutral-800 rounded-xl shadow-lg overflow-hidden">
        <!-- Image -->
        <div class="h-32 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 loading-shimmer"></div>
        
        <!-- Content -->
        <div class="p-4">
          <!-- Category -->
          <div class="w-16 h-4 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded-full mb-2 loading-shimmer"></div>
          
          <!-- Title -->
          <div class="space-y-2 mb-3">
            <div class="h-5 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded loading-shimmer"></div>
            <div class="h-5 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded w-2/3 loading-shimmer"></div>
          </div>
          
          <!-- Meta -->
          <div class="h-3 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded w-1/2 loading-shimmer"></div>
        </div>
      </div>
    </div>

    <!-- Avatar Layout -->
    <div v-else-if="variant === 'avatar'" class="flex items-center space-x-3">
      <div class="w-12 h-12 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded-full loading-shimmer"></div>
      <div class="flex-1 space-y-1">
        <div class="h-4 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded w-1/3 loading-shimmer"></div>
        <div class="h-3 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded w-1/4 loading-shimmer"></div>
      </div>
    </div>

    <!-- Button Layout -->
    <div v-else-if="variant === 'button'" class="h-12 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded-xl loading-shimmer"
      :class="width"
    ></div>

    <!-- Default Layout -->
    <div v-else class="h-6 bg-gradient-to-r from-neutral-200 via-neutral-300 to-neutral-200 dark:from-neutral-700 dark:via-neutral-600 dark:to-neutral-700 rounded loading-shimmer"
      :class="width"
    ></div>
  </div>
</template>

<script>
export default {
  name: 'LoadingSkeleton',
  props: {
    variant: {
      type: String,
      default: 'default',
      validator: value => ['card', 'hero', 'list', 'text', 'grid', 'avatar', 'button', 'default'].includes(value)
    },
    width: {
      type: String,
      default: 'w-full'
    },
    lines: {
      type: Number,
      default: 3
    },
    count: {
      type: Number,
      default: 6
    }
  }
}
</script>

<style scoped>
.loading-shimmer {
  background-size: 200% 100%;
  animation: shimmer 2s infinite;
}

@keyframes shimmer {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}

/* Stagger animation delays for multiple elements */
.animate-pulse:nth-child(1) { animation-delay: 0s; }
.animate-pulse:nth-child(2) { animation-delay: 0.1s; }
.animate-pulse:nth-child(3) { animation-delay: 0.2s; }
.animate-pulse:nth-child(4) { animation-delay: 0.3s; }
.animate-pulse:nth-child(5) { animation-delay: 0.4s; }
.animate-pulse:nth-child(6) { animation-delay: 0.5s; }
</style> 