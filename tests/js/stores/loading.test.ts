// Tests for loading store
import { describe, it, expect, beforeEach, vi } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { useLoadingStore, LoadingCategory } from '../../../resources/js/stores/loading'

describe('Loading Store', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  describe('Basic Loading Operations', () => {
    it('should start and finish loading', () => {
      const store = useLoadingStore()
      
      expect(store.isLoading).toBe(false)
      
      store.startLoading('test-id', LoadingCategory.API, 'Loading data...')
      expect(store.isLoading).toBe(true)
      expect(store.globalLoadingMessage).toBe('Loading data...')
      
      store.finishLoading('test-id', true)
      expect(store.isLoading).toBe(false)
      expect(store.globalLoadingMessage).toBe('')
    })

    it('should track progress updates', () => {
      const store = useLoadingStore()
      
      store.startLoading('progress-test', LoadingCategory.API)
      store.updateProgress('progress-test', 50, 'Half way...')
      
      const states = store.activeLoadingStates
      expect(states).toHaveLength(1)
      expect(states[0].progress).toBe(50)
      expect(states[0].message).toBe('Half way...')
    })

    it('should calculate overall progress', () => {
      const store = useLoadingStore()
      
      store.startLoading('task1', LoadingCategory.API)
      store.startLoading('task2', LoadingCategory.IMAGE)
      
      store.updateProgress('task1', 60)
      store.updateProgress('task2', 40)
      
      expect(store.overallProgress).toBe(50) // (60 + 40) / 2
    })
  })

  describe('Category Management', () => {
    it('should check if category has active operations', () => {
      const store = useLoadingStore()
      
      expect(store.hasCategory(LoadingCategory.API)).toBe(false)
      
      store.startLoading('api-test', LoadingCategory.API)
      expect(store.hasCategory(LoadingCategory.API)).toBe(true)
      expect(store.hasCategory(LoadingCategory.IMAGE)).toBe(false)
    })

    it('should calculate category progress', () => {
      const store = useLoadingStore()
      
      store.startLoading('api1', LoadingCategory.API)
      store.startLoading('api2', LoadingCategory.API)
      
      store.updateProgress('api1', 80)
      store.updateProgress('api2', 20)
      
      expect(store.categoryProgress(LoadingCategory.API)).toBe(50) // (80 + 20) / 2
    })
  })

  describe('Performance Tracking', () => {
    it('should track successful operations', () => {
      const store = useLoadingStore()
      
      store.startLoading('success-test', LoadingCategory.API)
      store.finishLoading('success-test', true)
      
      const metrics = store.performanceMetrics
      expect(metrics.successfulLoads).toBe(1)
      expect(metrics.failedLoads).toBe(0)
      expect(metrics.totalLoads).toBe(1)
    })

    it('should track failed operations', () => {
      const store = useLoadingStore()
      
      store.startLoading('fail-test', LoadingCategory.API)
      store.finishLoading('fail-test', false)
      
      const metrics = store.performanceMetrics
      expect(metrics.successfulLoads).toBe(0)
      expect(metrics.failedLoads).toBe(1)
      expect(metrics.totalLoads).toBe(1)
    })
  })

  describe('Utility Methods', () => {
    it('should wrap operations with loading state', async () => {
      const store = useLoadingStore()
      
      const operation = vi.fn().mockResolvedValue('result')
      
      const result = await store.withLoading(
        operation, 
        LoadingCategory.API, 
        'Processing...'
      )
      
      expect(result).toBe('result')
      expect(operation).toHaveBeenCalledOnce()
      expect(store.isLoading).toBe(false) // Should be finished
    })

    it('should handle operation failures', async () => {
      const store = useLoadingStore()
      
      const operation = vi.fn().mockRejectedValue(new Error('Test error'))
      
      await expect(store.withLoading(operation, LoadingCategory.API)).rejects.toThrow('Test error')
      
      const metrics = store.performanceMetrics
      expect(metrics.failedLoads).toBe(1)
    })

    it('should track API calls', () => {
      const store = useLoadingStore()
      
      const tracker = store.trackApiCall('/api/posts')
      
      expect(store.isLoading).toBe(true)
      expect(store.hasCategory(LoadingCategory.API)).toBe(true)
      
      tracker.updateProgress(75)
      tracker.finish(true)
      
      expect(store.isLoading).toBe(false)
    })
  })

  describe('Batch Operations', () => {
    it('should clear all loading states', () => {
      const store = useLoadingStore()
      
      store.startLoading('task1', LoadingCategory.API)
      store.startLoading('task2', LoadingCategory.IMAGE)
      
      expect(store.activeLoadingStates).toHaveLength(2)
      
      store.clearAllLoading()
      
      expect(store.activeLoadingStates).toHaveLength(0)
      expect(store.isLoading).toBe(false)
    })
  })

  describe('Error Handling', () => {
    it('should handle non-existent loading IDs gracefully', () => {
      const store = useLoadingStore()
      
      // Should not throw
      store.updateProgress('non-existent', 50)
      store.finishLoading('non-existent')
      
      expect(store.isLoading).toBe(false)
    })

    it('should clamp progress values', () => {
      const store = useLoadingStore()
      
      store.startLoading('clamp-test', LoadingCategory.API)
      
      store.updateProgress('clamp-test', -10)
      expect(store.activeLoadingStates[0].progress).toBe(0)
      
      store.updateProgress('clamp-test', 150)
      expect(store.activeLoadingStates[0].progress).toBe(100)
    })
  })
})
