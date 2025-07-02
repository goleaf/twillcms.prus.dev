<template>
  <div class="fixed inset-0 bg-white z-50 flex items-center justify-center">
    <div class="text-center">
      <!-- Loading Spinner -->
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
      
      <!-- Loading Text -->
      <p class="text-gray-600 text-lg font-medium">Loading...</p>
      <p class="text-gray-400 text-sm mt-1">Please wait while we prepare your content</p>
    </div>
  </div>
</template>

<script setup lang="ts">
// This component doesn't need any logic, it's purely presentational
</script>

<style scoped>
/* Additional loading animation styles if needed */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style> 