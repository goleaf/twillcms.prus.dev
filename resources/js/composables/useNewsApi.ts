import { ref, reactive, computed } from 'vue'
import axios from 'axios'

// Types
interface Article {
  id: number
  title: string
  slug: string
  excerpt?: string
  featured_image?: {
    url: string
    thumb?: string
    alt?: string
  }
  category?: {
    id: number
    name: string
    color: string
    slug: string
  }
  published_at: string
  view_count?: number
  reading_time?: number
  featured?: boolean
  trending?: boolean
  cached?: boolean
}

interface Category {
  id: number
  name: string
  slug: string
  color: string
  posts_count: number
}

interface ApiResponse<T> {
  success: boolean
  data: T
  meta?: {
    cached?: boolean
    performance?: string
    timestamp?: string
  }
}

// Composable
export function useNewsApi() {
  // State
  const loading = ref(false)
  const error = ref<string | null>(null)
  const articles = ref<Article[]>([])
  const categories = ref<Category[]>([])
  
  // Performance tracking
  const performance = reactive({
    loadTime: 0,
    cacheHitRate: 0,
    apiResponseTime: 0
  })

  // API Base URL
  const apiBase = '/api/news'

  // Helper function for API calls
  const apiCall = async <T>(endpoint: string): Promise<ApiResponse<T>> => {
    const startTime = performance.now()
    
    try {
      const response = await axios.get(`${apiBase}${endpoint}`, {
        timeout: 10000
      })
      
      const endTime = performance.now()
      performance.apiResponseTime = Math.round(endTime - startTime)
      
      if (response.data.meta?.cached) {
        performance.cacheHitRate = 90
      }
      
      return response.data
    } catch (err: any) {
      console.error('API Error:', err)
      throw new Error(err.response?.data?.message || 'API request failed')
    }
  }

  // Get homepage content
  const getHomepageContent = async () => {
    const startTime = performance.now()
    loading.value = true
    error.value = null
    
    try {
      const response = await apiCall<{
        featured_posts: Article[]
        latest_posts: Article[]
        popular_posts: Article[]
        trending_posts: Article[]
        categories: Category[]
      }>('/homepage')
      
      if (response.success) {
        articles.value = [
          ...response.data.featured_posts,
          ...response.data.latest_posts
        ]
        categories.value = response.data.categories
        
        performance.loadTime = Math.round(performance.now() - startTime)
        return response.data
      } else {
        throw new Error('Failed to load homepage content')
      }
    } catch (err: any) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  // Get news listing
  const getNewsListing = async (page = 1, perPage = 15) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await apiCall<{
        posts: Article[]
        featured_posts: Article[]
        categories: Category[]
      }>(`?page=${page}&per_page=${perPage}`)
      
      if (response.success) {
        articles.value = response.data.posts
        categories.value = response.data.categories
        return response.data
      }
    } catch (err: any) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }

  // Computed properties
  const featuredArticles = computed(() => 
    articles.value.filter(article => article.featured)
  )

  const latestArticles = computed(() => 
    articles.value.filter(article => !article.featured)
  )

  const trendingArticles = computed(() => 
    articles.value.filter(article => 
      article.trending || (article.view_count && article.view_count > 1000)
    )
  )

  const popularCategories = computed(() => 
    categories.value.filter(cat => cat.posts_count > 0)
      .sort((a, b) => b.posts_count - a.posts_count)
  )

  return {
    loading,
    error,
    articles,
    categories,
    performance,
    featuredArticles,
    latestArticles,
    trendingArticles,
    popularCategories,
    getHomepageContent,
    getNewsListing
  }
}
 