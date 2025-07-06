<template>
  <div class="px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">{{ $t('search.results_for') }}</h1>
    <p class="text-gray-600">Search query: {{ $route.query.q }}</p>
  </div>
</template>

<script>
export default {
  name: 'SearchResults'
}
</script> 