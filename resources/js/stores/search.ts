import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { PostSummary, SearchResult, SearchFilters } from '@/types';
import { apiClient } from '@/api/client';

export const useSearchStore = defineStore('search', () => {
  // State
  const query = ref<string>('');
  const results = ref<PostSummary[]>([]);
  const pagination = ref<any>(null);
  const filters = ref<SearchFilters>({});
  const recentSearches = ref<string[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);
  const hasSearched = ref(false);

  // Getters
  const hasResults = computed(() => results.value.length > 0);
  const totalResults = computed(() => pagination.value?.total || 0);
  const currentPage = computed(() => pagination.value?.current_page || 1);
  const lastPage = computed(() => pagination.value?.last_page || 1);
  const hasMoreResults = computed(() => currentPage.value < lastPage.value);
  
  const searchSummary = computed(() => {
    if (!hasSearched.value) return '';
    
    const total = totalResults.value;
    const queryText = query.value;
    const categoryText = filters.value.category ? ` in ${filters.value.category}` : '';
    
    return total > 0 
      ? `Found ${total} result${total === 1 ? '' : 's'} for "${queryText}"${categoryText}`
      : `No results found for "${queryText}"${categoryText}`;
  });

  // Actions
  const clearError = () => {
    error.value = null;
  };

  const setError = (message: string) => {
    error.value = message;
  };

  const search = async (searchQuery: string, searchFilters?: Omit<SearchFilters, 'query'>) => {
    if (!searchQuery.trim()) {
      reset();
      return;
    }

    loading.value = true;
    clearError();
    query.value = searchQuery.trim();
    filters.value = { query: query.value, ...searchFilters };
    
    try {
      const response = await apiClient.searchPosts(query.value, searchFilters);
      results.value = response.data;
      pagination.value = {
        ...response.meta,
        links: response.links,
      };
      hasSearched.value = true;
      
      // Add to recent searches
      addToRecentSearches(query.value);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Search failed');
      results.value = [];
      pagination.value = null;
    } finally {
      loading.value = false;
    }
  };

  const loadMoreResults = async () => {
    if (!hasMoreResults.value || loading.value || !query.value) return;
    
    loading.value = true;
    
    try {
      const nextPage = currentPage.value + 1;
      const response = await apiClient.searchPosts(query.value, {
        ...filters.value,
        page: nextPage,
      });
      
      results.value.push(...response.data);
      pagination.value = {
        ...response.meta,
        links: response.links,
      };
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load more results');
    } finally {
      loading.value = false;
    }
  };

  const addToRecentSearches = (searchQuery: string) => {
    const trimmed = searchQuery.trim();
    if (!trimmed) return;
    
    // Remove if already exists
    const index = recentSearches.value.indexOf(trimmed);
    if (index > -1) {
      recentSearches.value.splice(index, 1);
    }
    
    // Add to beginning
    recentSearches.value.unshift(trimmed);
    
    // Keep only last 10 searches
    if (recentSearches.value.length > 10) {
      recentSearches.value = recentSearches.value.slice(0, 10);
    }
    
    // Persist to localStorage
    try {
      localStorage.setItem('blog_recent_searches', JSON.stringify(recentSearches.value));
    } catch (err) {
      console.error('Failed to save recent searches:', err);
    }
  };

  const loadRecentSearches = () => {
    try {
      const saved = localStorage.getItem('blog_recent_searches');
      if (saved) {
        recentSearches.value = JSON.parse(saved);
      }
    } catch (err) {
      console.error('Failed to load recent searches:', err);
      recentSearches.value = [];
    }
  };

  const clearRecentSearches = () => {
    recentSearches.value = [];
    try {
      localStorage.removeItem('blog_recent_searches');
    } catch (err) {
      console.error('Failed to clear recent searches:', err);
    }
  };

  const reset = () => {
    query.value = '';
    results.value = [];
    pagination.value = null;
    filters.value = {};
    hasSearched.value = false;
    clearError();
  };

  const setFilters = (newFilters: SearchFilters) => {
    filters.value = { ...filters.value, ...newFilters };
  };

  // Initialize recent searches from localStorage
  loadRecentSearches();

  return {
    // State
    query,
    results,
    pagination,
    filters,
    recentSearches,
    loading,
    error,
    hasSearched,
    
    // Getters
    hasResults,
    totalResults,
    currentPage,
    lastPage,
    hasMoreResults,
    searchSummary,
    
    // Actions
    search,
    loadMoreResults,
    addToRecentSearches,
    loadRecentSearches,
    clearRecentSearches,
    reset,
    setFilters,
    clearError,
  };
}); 