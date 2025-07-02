// Loading state management store for TwillCMS
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

// Loading categories for different types of operations
export enum LoadingCategory {
  PAGE = 'page',
  API = 'api',
  IMAGE = 'image',
  COMPONENT = 'component',
  NAVIGATION = 'navigation',
  FORM = 'form',
  UPLOAD = 'upload'
}

// Loading state interface
export interface LoadingState {
  id: string
  category: LoadingCategory
  message?: string
  progress?: number
  startTime: number
  metadata?: Record<string, any>
}

// Performance metrics interface
export interface PerformanceMetrics {
  averageLoadTime: number
  successfulLoads: number
  failedLoads: number
  totalLoads: number
  categoryMetrics: Record<LoadingCategory, {
    count: number
    averageTime: number
    successRate: number
  }>
}

export const useLoadingStore = defineStore('loading', () => {
  // State
  const loadingStates = ref<Map<string, LoadingState>>(new Map())
  const globalLoadingMessage = ref<string>('')
  const performanceMetrics = ref<PerformanceMetrics>({
    averageLoadTime: 0,
    successfulLoads: 0,
    failedLoads: 0,
    totalLoads: 0,
    categoryMetrics: Object.values(LoadingCategory).reduce((acc, category) => {
      acc[category] = {
        count: 0,
        averageTime: 0,
        successRate: 0
      }
      return acc
    }, {} as Record<LoadingCategory, any>)
  })

  // Computed properties
  const isLoading = computed(() => loadingStates.value.size > 0)
  
  const hasCategory = computed(() => (category: LoadingCategory) => {
    return Array.from(loadingStates.value.values()).some(state => state.category === category)
  })
  
  const categoryProgress = computed(() => (category: LoadingCategory) => {
    const states = Array.from(loadingStates.value.values()).filter(state => state.category === category)
    if (states.length === 0) return 0
    
    const totalProgress = states.reduce((sum, state) => sum + (state.progress || 0), 0)
    return totalProgress / states.length
  })
  
  const overallProgress = computed(() => {
    if (loadingStates.value.size === 0) return 0
    
    const states = Array.from(loadingStates.value.values())
    const totalProgress = states.reduce((sum, state) => sum + (state.progress || 0), 0)
    return totalProgress / states.length
  })
  
  const activeLoadingStates = computed(() => Array.from(loadingStates.value.values()))

  // Actions
  const startLoading = (
    id: string,
    category: LoadingCategory,
    message?: string,
    metadata?: Record<string, any>
  ) => {
    const loadingState: LoadingState = {
      id,
      category,
      message,
      progress: 0,
      startTime: Date.now(),
      metadata
    }
    
    loadingStates.value.set(id, loadingState)
    
    if (message && !globalLoadingMessage.value) {
      globalLoadingMessage.value = message
    }
  }

  const updateProgress = (id: string, progress: number, message?: string) => {
    const state = loadingStates.value.get(id)
    if (state) {
      state.progress = Math.max(0, Math.min(100, progress))
      if (message) {
        state.message = message
      }
    }
  }

  const finishLoading = (id: string, success: boolean = true, metadata?: Record<string, any>) => {
    const state = loadingStates.value.get(id)
    if (!state) return
    
    const duration = Date.now() - state.startTime
    
    // Update performance metrics
    performanceMetrics.value.totalLoads++
    if (success) {
      performanceMetrics.value.successfulLoads++
    } else {
      performanceMetrics.value.failedLoads++
    }
    
    // Remove loading state
    loadingStates.value.delete(id)
    
    // Clear global message if no more loading operations
    if (loadingStates.value.size === 0) {
      globalLoadingMessage.value = ''
    }
  }

  const clearAllLoading = () => {
    loadingStates.value.clear()
    globalLoadingMessage.value = ''
  }

  // Utility methods for common loading scenarios
  const withLoading = async <T>(
    operation: () => Promise<T>,
    category: LoadingCategory,
    message?: string,
    id?: string
  ): Promise<T> => {
    const loadingId = id || `${category}_${Date.now()}`
    
    try {
      startLoading(loadingId, category, message)
      const result = await operation()
      finishLoading(loadingId, true)
      return result
    } catch (error) {
      finishLoading(loadingId, false, { error: error instanceof Error ? error.message : 'Unknown error' })
      throw error
    }
  }

  const trackApiCall = (endpoint: string) => {
    const id = `api_${endpoint}_${Date.now()}`
    startLoading(id, LoadingCategory.API, `Fetching ${endpoint}...`)
    return {
      updateProgress: (progress: number) => updateProgress(id, progress),
      finish: (success: boolean = true) => finishLoading(id, success),
      cancel: () => finishLoading(id, false)
    }
  }

  return {
    // State
    loadingStates: computed(() => loadingStates.value),
    globalLoadingMessage: computed(() => globalLoadingMessage.value),
    performanceMetrics: computed(() => performanceMetrics.value),
    
    // Computed
    isLoading,
    hasCategory,
    categoryProgress,
    overallProgress,
    activeLoadingStates,
    
    // Actions
    startLoading,
    updateProgress,
    finishLoading,
    clearAllLoading,
    
    // Utilities
    withLoading,
    trackApiCall
  }
})
