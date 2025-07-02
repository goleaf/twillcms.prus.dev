// API caching layer for TwillCMS - Intelligent caching strategies
import type { AxiosInstance, AxiosRequestConfig, AxiosResponse } from 'axios'
import { createCache, generateApiCacheKey, CacheStrategy } from '../utils/cache'

// Cache configuration for different endpoints
export interface ApiCacheConfig {
  strategy: CacheStrategy
  ttl?: number
  prefetch?: boolean
  revalidateOnFocus?: boolean
  retryOnError?: boolean
  maxRetries?: number
}

// Default cache configurations by endpoint pattern
const DEFAULT_ENDPOINT_CONFIGS: Record<string, ApiCacheConfig> = {
  '/api/v1/posts': {
    strategy: CacheStrategy.STALE_WHILE_REVALIDATE,
    ttl: 5 * 60 * 1000, // 5 minutes
    prefetch: true,
    revalidateOnFocus: true
  },
  '/api/v1/categories': {
    strategy: CacheStrategy.CACHE_FIRST,
    ttl: 15 * 60 * 1000, // 15 minutes
    prefetch: true
  },
  '/api/v1/site/config': {
    strategy: CacheStrategy.CACHE_FIRST,
    ttl: 60 * 60 * 1000, // 1 hour
    prefetch: true
  },
  '/api/v1/search': {
    strategy: CacheStrategy.NETWORK_FIRST,
    ttl: 2 * 60 * 1000, // 2 minutes
    prefetch: false
  }
}

// Create cache instance for API responses
const apiCache = createCache({
  storageType: 'localStorage',
  ttl: 10 * 60 * 1000, // Default 10 minutes
  maxItems: 200,
  maxSize: 20 * 1024 * 1024 // 20MB
})

// In-flight request tracking to prevent duplicate requests
const inFlightRequests = new Map<string, Promise<any>>()

// Request queue for retry logic
const requestQueue: Array<() => Promise<any>> = []

// Enhanced API client with caching
export class CachedApiClient {
  private axios: AxiosInstance
  private isOnline: boolean = navigator.onLine

  constructor(axiosInstance: AxiosInstance) {
    this.axios = axiosInstance
    
    // Set up online/offline detection
    window.addEventListener('online', () => {
      this.isOnline = true
      this.processRequestQueue()
    })
    
    window.addEventListener('offline', () => {
      this.isOnline = false
    })

    // Set up focus revalidation
    window.addEventListener('focus', () => {
      this.revalidateOnFocus()
    })
  }

  // Main request method with caching
  async request<T = any>(config: AxiosRequestConfig): Promise<AxiosResponse<T>> {
    const endpoint = this.normalizeEndpoint(config.url || '')
    const cacheConfig = this.getCacheConfig(endpoint)
    const cacheKey = this.generateCacheKey(config)

    // Handle different cache strategies
    switch (cacheConfig.strategy) {
      case CacheStrategy.CACHE_FIRST:
        return this.cacheFirstStrategy<T>(config, cacheKey, cacheConfig)
      
      case CacheStrategy.NETWORK_FIRST:
        return this.networkFirstStrategy<T>(config, cacheKey, cacheConfig)
      
      case CacheStrategy.STALE_WHILE_REVALIDATE:
        return this.staleWhileRevalidateStrategy<T>(config, cacheKey, cacheConfig)
      
      case CacheStrategy.NETWORK_ONLY:
        return this.networkOnlyStrategy<T>(config)
      
      case CacheStrategy.CACHE_ONLY:
        return this.cacheOnlyStrategy<T>(cacheKey)
      
      default:
        return this.networkFirstStrategy<T>(config, cacheKey, cacheConfig)
    }
  }

  // Cache-first strategy: Check cache first, fallback to network
  private async cacheFirstStrategy<T>(
    config: AxiosRequestConfig,
    cacheKey: string,
    cacheConfig: ApiCacheConfig
  ): Promise<AxiosResponse<T>> {
    const cached = apiCache.get<AxiosResponse<T>>(cacheKey)
    
    if (cached) {
      console.log(`[API Cache] Cache hit: ${cacheKey}`)
      return cached
    }
    
    console.log(`[API Cache] Cache miss, fetching: ${cacheKey}`)
    const response = await this.makeRequest<T>(config, cacheConfig)
    
    if (response && this.shouldCache(response)) {
      apiCache.set(cacheKey, response, cacheConfig.ttl)
    }
    
    return response
  }

  // Network-first strategy: Try network first, fallback to cache
  private async networkFirstStrategy<T>(
    config: AxiosRequestConfig,
    cacheKey: string,
    cacheConfig: ApiCacheConfig
  ): Promise<AxiosResponse<T>> {
    try {
      console.log(`[API Cache] Network first: ${cacheKey}`)
      const response = await this.makeRequest<T>(config, cacheConfig)
      
      if (response && this.shouldCache(response)) {
        apiCache.set(cacheKey, response, cacheConfig.ttl)
      }
      
      return response
    } catch (error) {
      console.log(`[API Cache] Network failed, trying cache: ${cacheKey}`)
      const cached = apiCache.get<AxiosResponse<T>>(cacheKey)
      
      if (cached) {
        console.log(`[API Cache] Serving stale data: ${cacheKey}`)
        return cached
      }
      
      throw error
    }
  }

  // Stale-while-revalidate strategy: Return cache immediately, update in background
  private async staleWhileRevalidateStrategy<T>(
    config: AxiosRequestConfig,
    cacheKey: string,
    cacheConfig: ApiCacheConfig
  ): Promise<AxiosResponse<T>> {
    const cached = apiCache.get<AxiosResponse<T>>(cacheKey)
    
    if (cached) {
      console.log(`[API Cache] Serving stale data: ${cacheKey}`)
      
      // Revalidate in background
      this.revalidateInBackground(config, cacheKey, cacheConfig)
      
      return cached
    }
    
    console.log(`[API Cache] No cache, fetching: ${cacheKey}`)
    const response = await this.makeRequest<T>(config, cacheConfig)
    
    if (response && this.shouldCache(response)) {
      apiCache.set(cacheKey, response, cacheConfig.ttl)
    }
    
    return response
  }

  // Network-only strategy: Always fetch from network
  private async networkOnlyStrategy<T>(config: AxiosRequestConfig): Promise<AxiosResponse<T>> {
    console.log(`[API Cache] Network only: ${config.url}`)
    return this.makeRequest<T>(config, { strategy: CacheStrategy.NETWORK_ONLY })
  }

  // Cache-only strategy: Only return cached data
  private async cacheOnlyStrategy<T>(cacheKey: string): Promise<AxiosResponse<T>> {
    const cached = apiCache.get<AxiosResponse<T>>(cacheKey)
    
    if (cached) {
      console.log(`[API Cache] Cache only hit: ${cacheKey}`)
      return cached
    }
    
    throw new Error(`No cached data available for ${cacheKey}`)
  }

  // Make actual HTTP request with deduplication
  private async makeRequest<T>(
    config: AxiosRequestConfig,
    cacheConfig: ApiCacheConfig
  ): Promise<AxiosResponse<T>> {
    const requestKey = this.generateRequestKey(config)
    
    // Check if request is already in flight
    if (inFlightRequests.has(requestKey)) {
      console.log(`[API Cache] Deduplicating request: ${requestKey}`)
      return inFlightRequests.get(requestKey)!
    }
    
    // Create and track request
    const requestPromise = this.axios.request<T>(config)
    inFlightRequests.set(requestKey, requestPromise)
    
    try {
      const response = await requestPromise
      return response
    } catch (error) {
      // Handle retries if configured
      if (cacheConfig.retryOnError && this.isOnline) {
        return this.retryRequest<T>(config, cacheConfig)
      }
      
      throw error
    } finally {
      inFlightRequests.delete(requestKey)
    }
  }

  // Retry request with exponential backoff
  private async retryRequest<T>(
    config: AxiosRequestConfig,
    cacheConfig: ApiCacheConfig,
    attempt: number = 1
  ): Promise<AxiosResponse<T>> {
    const maxRetries = cacheConfig.maxRetries || 3
    
    if (attempt > maxRetries) {
      throw new Error(`Max retries exceeded for ${config.url}`)
    }
    
    const delay = Math.min(1000 * Math.pow(2, attempt - 1), 10000) // Max 10s delay
    
    console.log(`[API Cache] Retrying request (${attempt}/${maxRetries}) in ${delay}ms: ${config.url}`)
    
    await new Promise(resolve => setTimeout(resolve, delay))
    
    try {
      return await this.axios.request<T>(config)
    } catch (error) {
      return this.retryRequest<T>(config, cacheConfig, attempt + 1)
    }
  }

  // Background revalidation
  private revalidateInBackground(
    config: AxiosRequestConfig,
    cacheKey: string,
    cacheConfig: ApiCacheConfig
  ): void {
    this.makeRequest(config, cacheConfig)
      .then(response => {
        if (response && this.shouldCache(response)) {
          apiCache.set(cacheKey, response, cacheConfig.ttl)
          console.log(`[API Cache] Background revalidation complete: ${cacheKey}`)
        }
      })
      .catch(error => {
        console.warn(`[API Cache] Background revalidation failed: ${cacheKey}`, error)
      })
  }

  // Revalidate cached data on window focus
  private revalidateOnFocus(): void {
    const stats = apiCache.getStats()
    console.log(`[API Cache] Revalidating ${stats.itemCount} cached items on focus`)
    
    // This would need to track which items need revalidation
    // For now, we'll just log the action
  }

  // Process queued requests when coming back online
  private processRequestQueue(): void {
    if (!this.isOnline || requestQueue.length === 0) return
    
    console.log(`[API Cache] Processing ${requestQueue.length} queued requests`)
    
    const requests = requestQueue.splice(0)
    requests.forEach(request => {
      request().catch(error => {
        console.warn('[API Cache] Queued request failed:', error)
      })
    })
  }

  // Generate cache key for request
  private generateCacheKey(config: AxiosRequestConfig): string {
    return generateApiCacheKey(
      config.url || '',
      {
        method: config.method || 'GET',
        params: config.params || {},
        data: config.method === 'GET' ? undefined : config.data
      }
    )
  }

  // Generate unique request key for deduplication
  private generateRequestKey(config: AxiosRequestConfig): string {
    return `${config.method || 'GET'}:${config.url}:${JSON.stringify(config.params || {})}`
  }

  // Get cache configuration for endpoint
  private getCacheConfig(endpoint: string): ApiCacheConfig {
    // Find matching configuration
    for (const [pattern, config] of Object.entries(DEFAULT_ENDPOINT_CONFIGS)) {
      if (endpoint.startsWith(pattern)) {
        return config
      }
    }
    
    // Default configuration
    return {
      strategy: CacheStrategy.NETWORK_FIRST,
      ttl: 5 * 60 * 1000,
      prefetch: false
    }
  }

  // Normalize endpoint for configuration matching
  private normalizeEndpoint(url: string): string {
    return url.split('?')[0] // Remove query parameters
  }

  // Check if response should be cached
  private shouldCache(response: AxiosResponse): boolean {
    return response.status >= 200 && response.status < 300
  }

  // Public methods for cache management
  public invalidateCache(pattern?: string | RegExp): void {
    if (pattern) {
      if (typeof pattern === 'string') {
        apiCache.invalidatePattern(new RegExp(pattern))
      } else {
        apiCache.invalidatePattern(pattern)
      }
    } else {
      apiCache.clear()
    }
    
    console.log(`[API Cache] Invalidated cache${pattern ? ` for pattern: ${pattern}` : ''}`)
  }

  public getCacheStats() {
    return apiCache.getStats()
  }

  public prefetchEndpoint(config: AxiosRequestConfig): Promise<void> {
    return this.request(config).then(() => {
      console.log(`[API Cache] Prefetched: ${config.url}`)
    }).catch(error => {
      console.warn(`[API Cache] Prefetch failed: ${config.url}`, error)
    })
  }
}

// Utility functions
export const createCachedApiClient = (axiosInstance: AxiosInstance) => {
  return new CachedApiClient(axiosInstance)
}

// Decorator for caching specific API calls
export const withApiCache = (config: ApiCacheConfig) => {
  return (target: any, propertyKey: string, descriptor: PropertyDescriptor) => {
    const originalMethod = descriptor.value
    
    descriptor.value = async function (...args: any[]) {
      // This would need to be integrated with the actual API client
      // For now, just apply the configuration
      console.log(`[API Cache] Applying cache config to ${propertyKey}:`, config)
      return originalMethod.apply(this, args)
    }
    
    return descriptor
  }
}

// Default cache configurations
export const CACHE_CONFIGS = DEFAULT_ENDPOINT_CONFIGS
