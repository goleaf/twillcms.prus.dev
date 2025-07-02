/**
 * Comprehensive Cache Utilities for TwillCMS
 * Provides multiple caching strategies with TTL support and memory management
 */

// Cache strategies enum
export enum CacheStrategy {
  CACHE_FIRST = 'cache-first',
  NETWORK_FIRST = 'network-first',
  STALE_WHILE_REVALIDATE = 'stale-while-revalidate',
  NETWORK_ONLY = 'network-only',
  CACHE_ONLY = 'cache-only'
}

// Cache entry interface
export interface CacheItem<T = any> {
  data: T
  timestamp: number
  ttl: number
  key: string
  size?: number
  hits?: number
}

// Cache configuration options
export interface CacheOptions {
  ttl?: number // Time to live in milliseconds
  maxSize?: number // Maximum cache size in bytes
  maxItems?: number // Maximum number of items
  storageType?: 'memory' | 'localStorage' | 'sessionStorage'
  compress?: boolean
}

// Cache statistics
export interface CacheStats {
  hits: number
  misses: number
  size: number
  itemCount: number
  hitRate: number
}

/**
 * Abstract Storage Interface for different storage types
 */
export abstract class CacheStorage {
  abstract get<T>(key: string): CacheItem<T> | null
  abstract set<T>(key: string, item: CacheItem<T>): boolean
  abstract delete(key: string): boolean
  abstract clear(): void
  abstract keys(): string[]
  abstract size(): number
}

/**
 * Memory-based cache storage
 */
export class MemoryStorage extends CacheStorage {
  private cache = new Map<string, CacheItem>()
  private maxSize: number
  private maxItems: number

  constructor(maxSize = 50 * 1024 * 1024, maxItems = 1000) { // 50MB default
    super()
    this.maxSize = maxSize
    this.maxItems = maxItems
  }

  get<T>(key: string): CacheItem<T> | null {
    const item = this.cache.get(key)
    if (!item) return null
    
    // Check TTL
    if (this.isExpired(item)) {
      this.cache.delete(key)
      return null
    }
    
    // Update hit count
    if (item.hits !== undefined) {
      item.hits++
    }
    
    return item as CacheItem<T>
  }

  set<T>(key: string, item: CacheItem<T>): boolean {
    // Check if we need to evict items
    this.evictIfNeeded()
    
    this.cache.set(key, item)
    return true
  }

  delete(key: string): boolean {
    return this.cache.delete(key)
  }

  clear(): void {
    this.cache.clear()
  }

  keys(): string[] {
    return Array.from(this.cache.keys())
  }

  size(): number {
    let totalSize = 0
    for (const item of this.cache.values()) {
      totalSize += item.size || this.estimateSize(item)
    }
    return totalSize
  }

  private isExpired(item: CacheItem): boolean {
    return Date.now() - item.timestamp > item.ttl
  }

  private evictIfNeeded(): void {
    // Remove expired items first
    for (const [key, item] of this.cache.entries()) {
      if (this.isExpired(item)) {
        this.cache.delete(key)
      }
    }

    // If still over limits, remove oldest items
    while (this.cache.size >= this.maxItems || this.size() >= this.maxSize) {
      const oldestKey = this.cache.keys().next().value
      if (oldestKey) {
        this.cache.delete(oldestKey)
      } else {
        break
      }
    }
  }

  private estimateSize(item: CacheItem): number {
    return JSON.stringify(item).length * 2 // Rough estimate (2 bytes per char)
  }
}

/**
 * Browser storage (localStorage/sessionStorage) based cache
 */
export class BrowserStorage extends CacheStorage {
  private storage: Storage
  private prefix: string

  constructor(storageType: 'localStorage' | 'sessionStorage' = 'localStorage', prefix = 'twill_cache_') {
    super()
    this.storage = storageType === 'localStorage' ? localStorage : sessionStorage
    this.prefix = prefix
  }

  get<T>(key: string): CacheItem<T> | null {
    try {
      const rawData = this.storage.getItem(this.prefix + key)
      if (!rawData) return null
      
      const item: CacheItem<T> = JSON.parse(rawData)
      
      // Check TTL
      if (Date.now() - item.timestamp > item.ttl) {
        this.storage.removeItem(this.prefix + key)
        return null
      }
      
      return item
    } catch (error) {
      console.warn(`Cache: Failed to get item ${key}:`, error)
      return null
    }
  }

  set<T>(key: string, item: CacheItem<T>): boolean {
    try {
      this.storage.setItem(this.prefix + key, JSON.stringify(item))
      return true
    } catch (error) {
      console.warn(`Cache: Failed to set item ${key}:`, error)
      
      // Try to free up space by removing expired items
      this.cleanup()
      
      try {
        this.storage.setItem(this.prefix + key, JSON.stringify(item))
        return true
      } catch (secondError) {
        console.error(`Cache: Still failed to set item ${key} after cleanup:`, secondError)
        return false
      }
    }
  }

  delete(key: string): boolean {
    this.storage.removeItem(this.prefix + key)
    return true
  }

  clear(): void {
    const keysToRemove: string[] = []
    for (let i = 0; i < this.storage.length; i++) {
      const key = this.storage.key(i)
      if (key && key.startsWith(this.prefix)) {
        keysToRemove.push(key)
      }
    }
    keysToRemove.forEach(key => this.storage.removeItem(key))
  }

  keys(): string[] {
    const keys: string[] = []
    for (let i = 0; i < this.storage.length; i++) {
      const key = this.storage.key(i)
      if (key && key.startsWith(this.prefix)) {
        keys.push(key.substring(this.prefix.length))
      }
    }
    return keys
  }

  size(): number {
    let totalSize = 0
    this.keys().forEach(key => {
      const rawData = this.storage.getItem(this.prefix + key)
      if (rawData) {
        totalSize += rawData.length * 2 // Rough estimate
      }
    })
    return totalSize
  }

  private cleanup(): void {
    const keys = this.keys()
    const expiredKeys: string[] = []
    
    keys.forEach(key => {
      const item = this.get(key)
      if (!item) {
        expiredKeys.push(key)
      }
    })
    
    expiredKeys.forEach(key => this.delete(key))
  }
}

/**
 * Main cache manager
 */
export class CacheManager {
  private storage: CacheStorage
  private stats: CacheStats = {
    hits: 0,
    misses: 0,
    size: 0,
    itemCount: 0,
    hitRate: 0
  }
  private defaultTTL: number

  constructor(options: CacheOptions = {}) {
    this.defaultTTL = options.ttl || 5 * 60 * 1000 // 5 minutes default
    
    // Initialize storage based on type
    switch (options.storageType) {
      case 'localStorage':
        this.storage = new BrowserStorage('localStorage')
        break
      case 'sessionStorage':
        this.storage = new BrowserStorage('sessionStorage')
        break
      case 'memory':
      default:
        this.storage = new MemoryStorage(options.maxSize, options.maxItems)
        break
    }
  }

  get<T>(key: string): T | null {
    const item = this.storage.get<T>(key)
    
    if (item) {
      this.stats.hits++
      this.updateStats()
      return item.data
    } else {
      this.stats.misses++
      this.updateStats()
      return null
    }
  }

  set<T>(key: string, data: T, ttl?: number): boolean {
    const cacheItem: CacheItem<T> = {
      data,
      timestamp: Date.now(),
      ttl: ttl || this.defaultTTL,
      key,
      size: this.estimateSize(data),
      hits: 0
    }
    
    const success = this.storage.set(key, cacheItem)
    if (success) {
      this.updateStats()
    }
    return success
  }

  delete(key: string): boolean {
    const success = this.storage.delete(key)
    if (success) {
      this.updateStats()
    }
    return success
  }

  clear(): void {
    this.storage.clear()
    this.stats = {
      hits: 0,
      misses: 0,
      size: 0,
      itemCount: 0,
      hitRate: 0
    }
  }

  has(key: string): boolean {
    return this.get(key) !== null
  }

  getStats(): CacheStats {
    this.updateStats()
    return { ...this.stats }
  }

  async getOrSet<T>(
    key: string, 
    fallback: () => Promise<T> | T, 
    ttl?: number
  ): Promise<T> {
    const cached = this.get<T>(key)
    
    if (cached !== null) {
      return cached
    }
    
    const value = await fallback()
    this.set(key, value, ttl)
    return value
  }

  invalidatePattern(pattern: string | RegExp): number {
    const keys = this.storage.keys()
    const regex = typeof pattern === 'string' ? new RegExp(pattern) : pattern
    
    let removedCount = 0
    keys.forEach(key => {
      if (regex.test(key)) {
        this.storage.delete(key)
        removedCount++
      }
    })
    
    this.updateStats()
    return removedCount
  }

  async refresh<T>(key: string, fetcher: () => Promise<T> | T, ttl?: number): Promise<T> {
    const value = await fetcher()
    this.set(key, value, ttl)
    return value
  }

  private estimateSize(data: any): number {
    try {
      return JSON.stringify(data).length * 2 // Rough estimate (2 bytes per char)
    } catch {
      return 1024 // Fallback estimate
    }
  }

  private updateStats(): void {
    this.stats.itemCount = this.storage.keys().length
    this.stats.size = this.storage.size()
    this.stats.hitRate = this.stats.hits / Math.max(1, this.stats.hits + this.stats.misses)
  }
}

// Factory function for creating cache instances
export const createCache = (options: CacheOptions = {}) => new CacheManager(options)

// Pre-configured cache instances
export const memoryCache = createCache({ storageType: 'memory', ttl: 5 * 60 * 1000 })
export const persistentCache = createCache({ storageType: 'localStorage', ttl: 24 * 60 * 60 * 1000 })
export const sessionCache = createCache({ storageType: 'sessionStorage', ttl: 30 * 60 * 1000 })

// Cache key generators
export const generateCacheKey = (prefix: string, params: Record<string, any>): string => {
  const sortedParams = Object.keys(params)
    .sort()
    .map(key => `${key}=${encodeURIComponent(String(params[key]))}`)
    .join('&')
  
  return `${prefix}:${sortedParams}`
}

export const generateApiCacheKey = (endpoint: string, params: Record<string, any> = {}): string => {
  return generateCacheKey(`api:${endpoint}`, params)
}

export const createCacheKey = (...parts: (string | number)[]): string => {
  return parts.join(':')
}

// Default export
export default CacheManager
