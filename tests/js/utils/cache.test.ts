// Tests for cache utilities
import { describe, it, expect, beforeEach, vi } from 'vitest'
import { CacheManager, createCache, generateApiCacheKey } from '../../../resources/js/utils/cache'

describe('Cache Utilities', () => {
  let cache: CacheManager

  beforeEach(() => {
    cache = createCache({
      storageType: 'memory',
      ttl: 1000, // 1 second for fast tests
      maxItems: 10
    })
  })

  describe('Basic Operations', () => {
    it('should set and get cache items', () => {
      cache.set('test-key', 'test-value')
      expect(cache.get('test-key')).toBe('test-value')
    })

    it('should return null for non-existent keys', () => {
      expect(cache.get('non-existent')).toBeNull()
    })

    it('should check if key exists', () => {
      cache.set('existing-key', 'value')
      expect(cache.has('existing-key')).toBe(true)
      expect(cache.has('non-existent')).toBe(false)
    })

    it('should delete cache items', () => {
      cache.set('delete-me', 'value')
      expect(cache.has('delete-me')).toBe(true)
      
      cache.delete('delete-me')
      expect(cache.has('delete-me')).toBe(false)
    })

    it('should clear all cache items', () => {
      cache.set('key1', 'value1')
      cache.set('key2', 'value2')
      expect(cache.getStats().itemCount).toBe(2)
      
      cache.clear()
      expect(cache.getStats().itemCount).toBe(0)
    })
  })

  describe('TTL Handling', () => {
    it('should expire items after TTL', async () => {
      cache.set('expire-me', 'value', 50) // 50ms TTL
      expect(cache.get('expire-me')).toBe('value')
      
      // Wait for expiration
      await new Promise(resolve => setTimeout(resolve, 100))
      expect(cache.get('expire-me')).toBeNull()
    })

    it('should use default TTL when not specified', () => {
      cache.set('default-ttl', 'value')
      expect(cache.has('default-ttl')).toBe(true)
    })
  })

  describe('Cache Statistics', () => {
    it('should track hits and misses', () => {
      cache.set('stats-key', 'value')
      
      // Hit
      cache.get('stats-key')
      let stats = cache.getStats()
      expect(stats.hits).toBe(1)
      expect(stats.misses).toBe(0)
      
      // Miss
      cache.get('non-existent')
      stats = cache.getStats()
      expect(stats.hits).toBe(1)
      expect(stats.misses).toBe(1)
      
      // Calculate hit rate
      expect(stats.hitRate).toBe(0.5)
    })

    it('should track item count', () => {
      expect(cache.getStats().itemCount).toBe(0)
      
      cache.set('item1', 'value1')
      cache.set('item2', 'value2')
      expect(cache.getStats().itemCount).toBe(2)
      
      cache.delete('item1')
      expect(cache.getStats().itemCount).toBe(1)
    })
  })

  describe('Pattern Invalidation', () => {
    it('should invalidate items matching pattern', () => {
      cache.set('user:1:profile', 'user1')
      cache.set('user:2:profile', 'user2') 
      cache.set('post:1:data', 'post1')
      
      cache.invalidatePattern(/^user:/)
      
      expect(cache.has('user:1:profile')).toBe(false)
      expect(cache.has('user:2:profile')).toBe(false)
      expect(cache.has('post:1:data')).toBe(true)
    })
  })

  describe('Async Operations', () => {
    it('should handle getOrSet with fallback', async () => {
      const fallback = vi.fn().mockResolvedValue('fallback-value')
      
      const result = await cache.getOrSet('async-key', fallback)
      
      expect(result).toBe('fallback-value')
      expect(fallback).toHaveBeenCalledOnce()
      expect(cache.get('async-key')).toBe('fallback-value')
    })

    it('should not call fallback if cached value exists', async () => {
      cache.set('cached-key', 'cached-value')
      const fallback = vi.fn()
      
      const result = await cache.getOrSet('cached-key', fallback)
      
      expect(result).toBe('cached-value')
      expect(fallback).not.toHaveBeenCalled()
    })
  })
})

describe('Cache Key Generation', () => {
  it('should generate consistent API cache keys', () => {
    const key1 = generateApiCacheKey('/api/posts', { page: 1, limit: 10 })
    const key2 = generateApiCacheKey('/api/posts', { limit: 10, page: 1 })
    
    expect(key1).toBe(key2) // Should be same regardless of param order
  })

  it('should generate different keys for different parameters', () => {
    const key1 = generateApiCacheKey('/api/posts', { page: 1 })
    const key2 = generateApiCacheKey('/api/posts', { page: 2 })
    
    expect(key1).not.toBe(key2)
  })

  it('should handle endpoints without parameters', () => {
    const key = generateApiCacheKey('/api/config')
    expect(key).toBe('/api/config')
  })
})
