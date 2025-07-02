// Pinia persistence plugin for TwillCMS
import type { PiniaPluginContext } from 'pinia'
import { createCache } from '../../utils/cache'

// Create a dedicated cache instance for Pinia persistence
const persistenceCache = createCache({
  storageType: 'localStorage',
  ttl: 30 * 60 * 1000, // 30 minutes
  maxItems: 50,
  maxSize: 5 * 1024 * 1024 // 5MB
})

// Persistence configuration interface
export interface PersistenceConfig {
  storage?: 'memory' | 'localStorage' | 'sessionStorage'
  ttl?: number
  include?: string[]
  exclude?: string[]
  key?: string | ((storeId: string) => string)
}

// Default configuration
const DEFAULT_CONFIG: PersistenceConfig = {
  storage: 'localStorage',
  ttl: 30 * 60 * 1000, // 30 minutes
  key: (storeId: string) => `pinia_${storeId}`
}

// Filter state properties based on include/exclude options
const filterState = (state: any, include?: string[], exclude?: string[]): any => {
  if (!include && !exclude) return state

  const filtered: any = {}
  
  if (include) {
    include.forEach(key => {
      if (key in state) {
        filtered[key] = state[key]
      }
    })
  } else {
    Object.keys(state).forEach(key => {
      if (!exclude || !exclude.includes(key)) {
        filtered[key] = state[key]
      }
    })
  }
  
  return filtered
}

// Main persistence plugin factory
export const createPersistedStatePlugin = (globalConfig: PersistenceConfig = {}) => {
  return ({ store, options }: PiniaPluginContext) => {
    // Merge global config with store-specific config
    const persistConfig: PersistenceConfig = {
      ...DEFAULT_CONFIG,
      ...globalConfig,
      ...(options.persist as PersistenceConfig || {})
    }

    // Skip if persistence is disabled for this store
    if (options.persist === false) return

    // Generate cache key
    const storeKey = typeof persistConfig.key === 'function' 
      ? persistConfig.key(store.$id)
      : persistConfig.key || `pinia_${store.$id}`

    // Hydrate state from cache on store creation
    const hydrateState = () => {
      try {
        const cachedData = persistenceCache.get<any>(storeKey)
        
        if (cachedData) {
          const mergedState = { ...store.$state, ...cachedData }
          store.$patch(mergedState)
          console.log(`[Persistence] Hydrated store "${store.$id}" from cache`)
        }
      } catch (error) {
        console.warn(`[Persistence] Failed to hydrate store "${store.$id}":`, error)
      }
    }

    // Persist state to cache
    const persistState = () => {
      try {
        let stateToSave = filterState(
          store.$state, 
          persistConfig.include, 
          persistConfig.exclude
        )
        
        persistenceCache.set(
          storeKey, 
          stateToSave, 
          persistConfig.ttl || DEFAULT_CONFIG.ttl
        )
        
        console.log(`[Persistence] Persisted store "${store.$id}" to cache`)
      } catch (error) {
        console.warn(`[Persistence] Failed to persist store "${store.$id}":`, error)
      }
    }

    // Hydrate on store creation
    hydrateState()

    // Subscribe to state changes for auto-persistence
    store.$subscribe(() => {
      persistState()
    }, { 
      detached: true
    })

    // Add manual persistence methods to store
    store.$persist = persistState
    store.$hydrate = hydrateState
    store.$clearPersisted = () => {
      persistenceCache.delete(storeKey)
      console.log(`[Persistence] Cleared persisted data for store "${store.$id}"`)
    }
  }
}

// Pre-configured persistence plugin
export const persistedStatePlugin = createPersistedStatePlugin()

// Legacy export for compatibility
export const defaultPersistence = persistedStatePlugin

// Type augmentation for Pinia stores
declare module 'pinia' {
  export interface DefineStoreOptionsBase<S, Store> {
    persist?: PersistenceConfig | boolean
  }
  
  export interface PiniaCustomProperties {
    $persist: () => void
    $hydrate: () => void
    $clearPersisted: () => void
  }
}
