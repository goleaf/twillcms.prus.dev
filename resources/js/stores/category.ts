import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Category, NavigationItem, SearchFilters } from '@/types';
import { apiClient } from '@/api/client';

export const useCategoryStore = defineStore('category', () => {
  // State
  const categories = ref<Category[]>([]);
  const currentCategory = ref<Category | null>(null);
  const navigationCategories = ref<NavigationItem[]>([]);
  const popularCategories = ref<NavigationItem[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  // Getters
  const getCategoryBySlug = computed(() => {
    return (slug: string) => categories.value.find(cat => cat.slug === slug);
  });

  const getCategoryById = computed(() => {
    return (id: number) => categories.value.find(cat => cat.id === id);
  });

  const publishedCategories = computed(() => {
    return categories.value.filter(cat => cat.published);
  });

  const categoriesWithPosts = computed(() => {
    return categories.value.filter(cat => cat.posts_count && cat.posts_count > 0);
  });

  // Actions
  const clearError = () => {
    error.value = null;
  };

  const setError = (message: string) => {
    error.value = message;
  };

  const fetchCategories = async (includeTranslations: boolean = false) => {
    loading.value = true;
    clearError();
    
    try {
      const data = await apiClient.getCategories(includeTranslations);
      categories.value = data;
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to fetch categories');
    } finally {
      loading.value = false;
    }
  };

  const fetchCategory = async (slug: string, filters?: SearchFilters) => {
    loading.value = true;
    clearError();
    
    try {
      const category = await apiClient.getCategory(slug, filters);
      currentCategory.value = category;
      return category;
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to fetch category');
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const fetchNavigationCategories = async () => {
    try {
      const data = await apiClient.getCategoryNavigation();
      navigationCategories.value = data;
    } catch (err) {
      console.error('Failed to fetch navigation categories:', err);
    }
  };

  const fetchPopularCategories = async (limit: number = 5) => {
    try {
      const data = await apiClient.getPopularCategories(limit);
      popularCategories.value = data;
    } catch (err) {
      console.error('Failed to fetch popular categories:', err);
    }
  };

  const reset = () => {
    categories.value = [];
    currentCategory.value = null;
    clearError();
  };

  const clearCurrentCategory = () => {
    currentCategory.value = null;
    clearError();
  };

  // Initialize navigation data
  const initializeNavigation = async () => {
    await Promise.all([
      fetchNavigationCategories(),
      fetchPopularCategories(),
    ]);
  };

  return {
    // State
    categories,
    currentCategory,
    navigationCategories,
    popularCategories,
    loading,
    error,
    
    // Getters
    getCategoryBySlug,
    getCategoryById,
    publishedCategories,
    categoriesWithPosts,
    
    // Actions
    fetchCategories,
    fetchCategory,
    fetchNavigationCategories,
    fetchPopularCategories,
    reset,
    clearCurrentCategory,
    clearError,
    initializeNavigation,
  };
}); 