import { setActivePinia, createPinia } from 'pinia';
import { useBlogStore } from '@/stores/blog';
import { apiClient } from '@/api/client';

// Mock the API client
jest.mock('@/api/client');
const mockApiClient = apiClient as jest.Mocked<typeof apiClient>;

describe('Blog Store', () => {
  beforeEach(() => {
    setActivePinia(createPinia());
    jest.clearAllMocks();
  });

  describe('initial state', () => {
    it('has correct initial state', () => {
      const store = useBlogStore();

      expect(store.posts).toEqual([]);
      expect(store.currentPost).toBeNull();
      expect(store.loading).toBe(false);
      expect(store.error).toBeNull();
      expect(store.pagination).toEqual({
        currentPage: 1,
        lastPage: 1,
        perPage: 12,
        total: 0,
      });
    });
  });

  describe('fetchPosts', () => {
    it('fetches posts successfully', async () => {
      const mockResponse = {
        data: [
          { id: 1, title: 'Post 1', slug: 'post-1' },
          { id: 2, title: 'Post 2', slug: 'post-2' },
        ],
        meta: {
          current_page: 1,
          last_page: 2,
          per_page: 12,
          total: 20,
        },
      };

      mockApiClient.get.mockResolvedValueOnce(mockResponse);

      const store = useBlogStore();
      await store.fetchPosts();

      expect(mockApiClient.get).toHaveBeenCalledWith('/posts', {
        page: 1,
        per_page: 12,
      });
      expect(store.posts).toEqual(mockResponse.data);
      expect(store.pagination).toEqual({
        currentPage: 1,
        lastPage: 2,
        perPage: 12,
        total: 20,
      });
      expect(store.loading).toBe(false);
      expect(store.error).toBeNull();
    });

    it('handles fetch posts error', async () => {
      const error = new Error('Network error');
      mockApiClient.get.mockRejectedValueOnce(error);

      const store = useBlogStore();
      await store.fetchPosts();

      expect(store.posts).toEqual([]);
      expect(store.loading).toBe(false);
      expect(store.error).toBe('Failed to fetch posts');
    });

    it('fetches posts with filters', async () => {
      const mockResponse = {
        data: [{ id: 1, title: 'Filtered Post' }],
        meta: { current_page: 1, last_page: 1, per_page: 12, total: 1 },
      };

      mockApiClient.get.mockResolvedValueOnce(mockResponse);

      const store = useBlogStore();
      await store.fetchPosts({ category: 'tech', page: 2 });

      expect(mockApiClient.get).toHaveBeenCalledWith('/posts', {
        page: 2,
        per_page: 12,
        category: 'tech',
      });
    });
  });

  describe('fetchPost', () => {
    it('fetches single post successfully', async () => {
      const mockPost = {
        id: 1,
        title: 'Test Post',
        slug: 'test-post',
        content: 'Test content',
      };

      mockApiClient.get.mockResolvedValueOnce(mockPost);

      const store = useBlogStore();
      await store.fetchPost('test-post');

      expect(mockApiClient.get).toHaveBeenCalledWith('/posts/test-post');
      expect(store.currentPost).toEqual(mockPost);
      expect(store.loading).toBe(false);
      expect(store.error).toBeNull();
    });

    it('handles fetch post error', async () => {
      const error = new Error('Post not found');
      mockApiClient.get.mockRejectedValueOnce(error);

      const store = useBlogStore();
      await store.fetchPost('nonexistent');

      expect(store.currentPost).toBeNull();
      expect(store.loading).toBe(false);
      expect(store.error).toBe('Failed to fetch post');
    });
  });

  describe('searchPosts', () => {
    it('searches posts successfully', async () => {
      const mockResponse = {
        data: [{ id: 1, title: 'Search Result' }],
        meta: { query: 'test', current_page: 1, last_page: 1, per_page: 12, total: 1 },
      };

      mockApiClient.get.mockResolvedValueOnce(mockResponse);

      const store = useBlogStore();
      await store.searchPosts('test');

      expect(mockApiClient.get).toHaveBeenCalledWith('/posts/search', {
        q: 'test',
        page: 1,
        per_page: 12,
      });
      expect(store.posts).toEqual(mockResponse.data);
    });

    it('handles search error', async () => {
      const error = new Error('Search failed');
      mockApiClient.get.mockRejectedValueOnce(error);

      const store = useBlogStore();
      await store.searchPosts('test');

      expect(store.error).toBe('Failed to search posts');
    });
  });

  describe('getters', () => {
    it('hasNextPage returns correct value', () => {
      const store = useBlogStore();
      
      store.pagination.currentPage = 1;
      store.pagination.lastPage = 2;
      expect(store.hasNextPage).toBe(true);

      store.pagination.currentPage = 2;
      store.pagination.lastPage = 2;
      expect(store.hasNextPage).toBe(false);
    });

    it('hasPrevPage returns correct value', () => {
      const store = useBlogStore();
      
      store.pagination.currentPage = 1;
      expect(store.hasPrevPage).toBe(false);

      store.pagination.currentPage = 2;
      expect(store.hasPrevPage).toBe(true);
    });

    it('totalPages returns correct value', () => {
      const store = useBlogStore();
      store.pagination.lastPage = 5;
      expect(store.totalPages).toBe(5);
    });
  });

  describe('loading states', () => {
    it('sets loading state during fetch operations', async () => {
      let resolvePromise: (value: any) => void;
      const promise = new Promise((resolve) => {
        resolvePromise = resolve;
      });

      mockApiClient.get.mockReturnValueOnce(promise);

      const store = useBlogStore();
      const fetchPromise = store.fetchPosts();

      expect(store.loading).toBe(true);

      resolvePromise!({
        data: [],
        meta: { current_page: 1, last_page: 1, per_page: 12, total: 0 },
      });

      await fetchPromise;
      expect(store.loading).toBe(false);
    });
  });
});
