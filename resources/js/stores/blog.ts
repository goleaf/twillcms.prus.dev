import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { 
  Post, 
  PostSummary, 
  PaginatedResponse, 
  SearchFilters,
  LoadingState,
  ErrorState 
} from '@/types';
import { apiClient } from '@/api/client';

export const useBlogStore = defineStore('blog', () => {
  // State
  const posts = ref<PostSummary[]>([]);
  const currentPost = ref<Post | null>(null);
  const popularPosts = ref<PostSummary[]>([]);
  const recentPosts = ref<PostSummary[]>([]);
  const pagination = ref<any>(null);
  
  const loading = ref<LoadingState>({
    posts: false,
    post: false,
    categories: false,
    search: false,
    config: false,
  });
  
  const errors = ref<ErrorState>({});

  // Getters
  const isLoading = computed(() => Object.values(loading.value).some(Boolean));
  const hasErrors = computed(() => Object.values(errors.value).some(Boolean));
  const totalPosts = computed(() => pagination.value?.total || 0);
  const currentPage = computed(() => pagination.value?.current_page || 1);
  const lastPage = computed(() => pagination.value?.last_page || 1);

  // Actions
  const clearErrors = () => {
    errors.value = {};
  };

  const setError = (key: keyof ErrorState, message: string) => {
    errors.value[key] = message;
  };

  const fetchPosts = async (filters?: SearchFilters) => {
    loading.value.posts = true;
    clearErrors();
    
    try {
      const response = await apiClient.getPosts(filters);
      posts.value = response.data;
      pagination.value = {
        ...response.meta,
        links: response.links,
      };
    } catch (error) {
      setError('posts', error instanceof Error ? error.message : 'Failed to fetch posts');
    } finally {
      loading.value.posts = false;
    }
  };

  const fetchPost = async (slug: string, includeTranslations: boolean = false) => {
    loading.value.post = true;
    clearErrors();
    
    try {
      const post = await apiClient.getPost(slug, includeTranslations);
      currentPost.value = post;
      return post;
    } catch (error) {
      setError('post', error instanceof Error ? error.message : 'Failed to fetch post');
      throw error;
    } finally {
      loading.value.post = false;
    }
  };

  const fetchPopularPosts = async (limit: number = 5) => {
    try {
      const posts = await apiClient.getPopularPosts(limit);
      popularPosts.value = posts;
    } catch (error) {
      console.error('Failed to fetch popular posts:', error);
    }
  };

  const fetchRecentPosts = async (limit: number = 5) => {
    try {
      const posts = await apiClient.getRecentPosts(limit);
      recentPosts.value = posts;
    } catch (error) {
      console.error('Failed to fetch recent posts:', error);
    }
  };

  const fetchPostsByArchive = async (year: number, month?: number, filters?: SearchFilters) => {
    loading.value.posts = true;
    clearErrors();
    
    try {
      const response = await apiClient.getPostsByArchive(year, month, filters);
      posts.value = response.data;
      pagination.value = {
        ...response.meta,
        links: response.links,
      };
    } catch (error) {
      setError('posts', error instanceof Error ? error.message : 'Failed to fetch archive posts');
    } finally {
      loading.value.posts = false;
    }
  };

  const loadMore = async (filters?: SearchFilters) => {
    if (!pagination.value?.links?.next || loading.value.posts) return;
    
    loading.value.posts = true;
    
    try {
      const nextPage = currentPage.value + 1;
      const response = await apiClient.getPosts({ ...filters, page: nextPage });
      posts.value.push(...response.data);
      pagination.value = {
        ...response.meta,
        links: response.links,
      };
    } catch (error) {
      setError('posts', error instanceof Error ? error.message : 'Failed to load more posts');
    } finally {
      loading.value.posts = false;
    }
  };

  const reset = () => {
    posts.value = [];
    currentPost.value = null;
    pagination.value = null;
    clearErrors();
  };

  const clearCurrentPost = () => {
    currentPost.value = null;
    delete errors.value.post;
  };

  // Initialize popular and recent posts
  const initializeSidebar = async () => {
    await Promise.all([
      fetchPopularPosts(),
      fetchRecentPosts(),
    ]);
  };

  return {
    // State
    posts,
    currentPost,
    popularPosts,
    recentPosts,
    pagination,
    loading,
    errors,
    
    // Getters
    isLoading,
    hasErrors,
    totalPosts,
    currentPage,
    lastPage,
    
    // Actions
    fetchPosts,
    fetchPost,
    fetchPopularPosts,
    fetchRecentPosts,
    fetchPostsByArchive,
    loadMore,
    reset,
    clearCurrentPost,
    clearErrors,
    initializeSidebar,
  };
}); 