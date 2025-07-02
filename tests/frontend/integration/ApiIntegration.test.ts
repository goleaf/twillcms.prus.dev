import { describe, it, expect, beforeEach, vi, afterEach } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import { useBlogStore } from '@/stores/blog'
import { useCategoryStore } from '@/stores/categories'
import { useSiteStore } from '@/stores/site'

// Mock fetch
const mockFetch = vi.fn()
global.fetch = mockFetch

describe('API Integration Tests', () => {
  let pinia: any

  beforeEach(() => {
    pinia = createPinia()
    setActivePinia(pinia)
    mockFetch.mockClear()
  })

  afterEach(() => {
    vi.resetAllMocks()
  })

  describe('Blog Store API Integration', () => {
    it('fetches posts from API correctly', async () => {
      const mockPosts = {
        data: [
          { id: 1, title: 'Test Post 1', slug: 'test-post-1' },
          { id: 2, title: 'Test Post 2', slug: 'test-post-2' }
        ],
        meta: { total: 2, current_page: 1 }
      }

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockPosts
      })

      const blogStore = useBlogStore()
      await blogStore.fetchPosts()

      expect(mockFetch).toHaveBeenCalledWith('/api/v1/posts')
      expect(blogStore.posts).toEqual(mockPosts.data)
      expect(blogStore.meta).toEqual(mockPosts.meta)
    })

    it('handles API errors when fetching posts', async () => {
      mockFetch.mockRejectedValueOnce(new Error('Network error'))

      const blogStore = useBlogStore()
      
      try {
        await blogStore.fetchPosts()
      } catch (error) {
        expect(error.message).toBe('Network error')
      }
      
      expect(blogStore.loading).toBe(false)
      expect(blogStore.error).toBeTruthy()
    })

    it('fetches single post by slug', async () => {
      const mockPost = {
        id: 1,
        title: 'Test Post',
        slug: 'test-post',
        content: 'Test content'
      }

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockPost
      })

      const blogStore = useBlogStore()
      await blogStore.fetchPost('test-post')

      expect(mockFetch).toHaveBeenCalledWith('/api/v1/posts/test-post')
      expect(blogStore.currentPost).toEqual(mockPost)
    })

    it('fetches popular posts', async () => {
      const mockPopularPosts = [
        { id: 1, title: 'Popular Post 1', views: 100 },
        { id: 2, title: 'Popular Post 2', views: 85 }
      ]

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockPopularPosts
      })

      const blogStore = useBlogStore()
      await blogStore.fetchPopularPosts()

      expect(mockFetch).toHaveBeenCalledWith('/api/v1/posts/popular')
      expect(blogStore.popularPosts).toEqual(mockPopularPosts)
    })

    it('searches posts with query parameters', async () => {
      const mockSearchResults = {
        data: [
          { id: 1, title: 'Matching Post', slug: 'matching-post' }
        ],
        meta: { total: 1 }
      }

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockSearchResults
      })

      const blogStore = useBlogStore()
      await blogStore.searchPosts('test query')

      expect(mockFetch).toHaveBeenCalledWith('/api/v1/posts/search?q=test%20query')
      expect(blogStore.searchResults).toEqual(mockSearchResults.data)
    })
  })

  describe('Category Store API Integration', () => {
    it('fetches categories from API', async () => {
      const mockCategories = [
        { id: 1, name: 'Technology', slug: 'technology', posts_count: 5 },
        { id: 2, name: 'Design', slug: 'design', posts_count: 3 }
      ]

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockCategories
      })

      const categoryStore = useCategoryStore()
      await categoryStore.fetchCategories()

      expect(mockFetch).toHaveBeenCalledWith('/api/v1/categories')
      expect(categoryStore.categories).toEqual(mockCategories)
    })

    it('fetches navigation categories', async () => {
      const mockNavCategories = [
        { id: 1, name: 'Tech', slug: 'tech', featured: true },
        { id: 2, name: 'News', slug: 'news', featured: true }
      ]

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockNavCategories
      })

      const categoryStore = useCategoryStore()
      await categoryStore.fetchNavigationCategories()

      expect(mockFetch).toHaveBeenCalledWith('/api/v1/categories/navigation')
      expect(categoryStore.navigationCategories).toEqual(mockNavCategories)
    })

    it('fetches posts by category', async () => {
      const mockCategoryPosts = {
        data: [
          { id: 1, title: 'Tech Post 1', category_slug: 'technology' }
        ],
        meta: { total: 1 }
      }

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockCategoryPosts
      })

      const categoryStore = useCategoryStore()
      await categoryStore.fetchCategoryPosts('technology')

      expect(mockFetch).toHaveBeenCalledWith('/api/v1/categories/technology/posts')
      expect(categoryStore.categoryPosts).toEqual(mockCategoryPosts.data)
    })
  })

  describe('Site Store API Integration', () => {
    it('fetches site configuration', async () => {
      const mockSiteConfig = {
        name: 'Test Blog',
        description: 'A test blog',
        locale: 'en',
        timezone: 'UTC'
      }

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockSiteConfig
      })

      const siteStore = useSiteStore()
      await siteStore.fetchSiteConfig()

      expect(mockFetch).toHaveBeenCalledWith('/api/v1/site/config')
      expect(siteStore.siteName).toBe(mockSiteConfig.name)
      expect(siteStore.siteDescription).toBe(mockSiteConfig.description)
    })

    it('fetches site statistics', async () => {
      const mockStats = {
        total_posts: 50,
        total_categories: 8,
        total_views: 15000
      }

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockStats
      })

      const siteStore = useSiteStore()
      await siteStore.fetchSiteStats()

      expect(mockFetch).toHaveBeenCalledWith('/api/v1/site/stats')
      expect(siteStore.stats).toEqual(mockStats)
    })

    it('switches language via API', async () => {
      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => ({ success: true, locale: 'lt' })
      })

      const siteStore = useSiteStore()
      await siteStore.switchLanguage('lt')

      expect(mockFetch).toHaveBeenCalledWith('/api/language/lt', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
      })
      expect(siteStore.currentLocale).toBe('lt')
    })
  })

  describe('Error Handling', () => {
    it('handles 404 errors gracefully', async () => {
      mockFetch.mockResolvedValueOnce({
        ok: false,
        status: 404,
        statusText: 'Not Found'
      })

      const blogStore = useBlogStore()
      
      try {
        await blogStore.fetchPost('nonexistent-post')
      } catch (error) {
        expect(error.message).toContain('404')
      }
    })

    it('handles network errors', async () => {
      mockFetch.mockRejectedValueOnce(new Error('Failed to fetch'))

      const siteStore = useSiteStore()
      
      try {
        await siteStore.fetchSiteConfig()
      } catch (error) {
        expect(error.message).toBe('Failed to fetch')
      }
    })

    it('handles invalid JSON responses', async () => {
      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => { throw new Error('Invalid JSON') }
      })

      const categoryStore = useCategoryStore()
      
      try {
        await categoryStore.fetchCategories()
      } catch (error) {
        expect(error.message).toBe('Invalid JSON')
      }
    })
  })

  describe('Loading States', () => {
    it('manages loading state during API calls', async () => {
      mockFetch.mockImplementationOnce(() => 
        new Promise(resolve => setTimeout(() => resolve({
          ok: true,
          json: async () => []
        }), 100))
      )

      const blogStore = useBlogStore()
      
      expect(blogStore.loading).toBe(false)
      
      const fetchPromise = blogStore.fetchPosts()
      expect(blogStore.loading).toBe(true)
      
      await fetchPromise
      expect(blogStore.loading).toBe(false)
    })
  })

  describe('Cache Management', () => {
    it('caches API responses to avoid duplicate requests', async () => {
      const mockResponse = { data: [{ id: 1, title: 'Cached Post' }] }
      
      mockFetch.mockResolvedValue({
        ok: true,
        json: async () => mockResponse
      })

      const blogStore = useBlogStore()
      
      // First call
      await blogStore.fetchPosts()
      // Second call should use cache
      await blogStore.fetchPosts()

      // Should only call API once due to caching
      expect(mockFetch).toHaveBeenCalledTimes(1)
    })
  })
}) 