import { ref, computed } from 'vue';

// Static English translations
const staticTranslations = {
  navigation: {
    home: 'Home',
    blog: 'Blog',
    categories: 'Categories',
    search: 'Search',
    menu: 'Menu',
    about: 'About',
    contact: 'Contact'
  },
  categories: {
    view_all_categories: 'All Categories'
  },
  common: {
    read_more: 'Read More',
    loading: 'Loading...',
    error: 'Error',
    no_results: 'No results found'
  },
  blog: {
    recent_posts: 'Recent Posts',
    popular_posts: 'Popular Posts',
    posted_on: 'Posted on',
    by_author: 'By',
    read_time: 'min read',
    share: 'Share'
  },
  search: {
    placeholder: 'Search articles...',
    results: 'Search Results',
    no_results: 'No articles found matching your search.'
  }
};

export function useTranslations() {
  // Simple translation function that uses static English translations
  const t = (key: string, params: Record<string, any> = {}): string => {
    const keys = key.split('.');
    let value: any = staticTranslations;
    
    for (const k of keys) {
      if (value && typeof value === 'object' && k in value) {
        value = value[k];
      } else {
        // Return a user-friendly fallback instead of the key
        const fallback = key.split('.').pop() || key;
        return fallback.charAt(0).toUpperCase() + fallback.slice(1).replace(/_/g, ' ');
      }
    }
    
    if (typeof value !== 'string') {
      const fallback = key.split('.').pop() || key;
      return fallback.charAt(0).toUpperCase() + fallback.slice(1).replace(/_/g, ' ');
    }
    
    // Replace parameters
    let result = value;
    for (const [param, paramValue] of Object.entries(params)) {
      result = result.replace(new RegExp(`:${param}`, 'g'), String(paramValue));
    }
    
    return result;
  };

  // No-op locale functions for API compatibility
  const setLocale = async (_locale: string): Promise<void> => {
    // Only English is supported, so this is a no-op
  };

  const initialize = async (): Promise<void> => {
    // No initialization needed for static translations
  };

  return {
    t,
    currentLocale: computed(() => 'en'),
    availableLocales: computed(() => ['en']),
    loading: computed(() => false),
    translations: computed(() => staticTranslations),
    setLocale,
    initialize,
  };
}

// Create a singleton instance for global use
export const translationInstance = useTranslations();
