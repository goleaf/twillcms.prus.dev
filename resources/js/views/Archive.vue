<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Archive</h1>
        <p class="text-lg text-gray-600">Posts from {{ year }}{{ month ? ` - ${getMonthName(month)}` : '' }}</p>
      </div>
      
      <div class="text-center py-12">
        <p class="text-gray-500">Archive posts will be loaded here</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  year: number;
  month?: number;
}

const props = defineProps<Props>();

const getMonthName = (month: number) => {
  const date = new Date();
  date.setMonth(month - 1);
  return date.toLocaleDateString('en-US', { month: 'long' });
};
</script> 