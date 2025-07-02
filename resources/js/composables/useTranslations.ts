import { ref, computed } from 'vue';
import { apiClient } from '@/api/client';

interface Translations {
  [key: string]: any;
}

const translations = ref<Translations>({});
const currentLocale = ref<string>('en');
const availableLocales = ref<string[]>(['en', 'lt']);
const loading = ref<boolean>(false);

export function useTranslations() {
  const t = (key: string, params: Record<string, any> = {}): string => {
    const keys = key.split('.');
    let value = translations.value;
    
    for (const k of keys) {
      if (value && typeof value === 'object' && k in value) {
        value = value[k];
      } else {
        return key; // Return key if translation not found
      }
    }
    
    if (typeof value !== 'string') {
      return key;
    }
    
    // Replace parameters
    let result = value;
    for (const [param, paramValue] of Object.entries(params)) {
      result = result.replace(new RegExp(`:${param}`, 'g'), String(paramValue));
    }
    
    return result;
  };

  const setLocale = async (locale: string): Promise<void> => {
    if (!availableLocales.value.includes(locale)) {
      console.warn(`Locale ${locale} is not available`);
      return;
    }
    
    currentLocale.value = locale;
    await loadTranslations(locale);
  };

  const loadTranslations = async (locale: string = currentLocale.value): Promise<void> => {
    if (translations.value[locale]) {
      return; // Already loaded
    }
    
    loading.value = true;
    try {
      const data = await apiClient.get(`/site/translations/${locale}`);
      translations.value[locale] = data.translations;
    } catch (error) {
      console.error(`Failed to load translations for locale ${locale}:`, error);
      // Fallback to English if available
      if (locale !== 'en' && !translations.value.en) {
        await loadTranslations('en');
      }
    } finally {
      loading.value = false;
    }
  };

  const getCurrentTranslations = computed(() => {
    return translations.value[currentLocale.value] || {};
  });

  // Initialize with default locale
  const initialize = async (): Promise<void> => {
    // Check if locale is stored in localStorage
    const storedLocale = localStorage.getItem('locale');
    if (storedLocale && availableLocales.value.includes(storedLocale)) {
      currentLocale.value = storedLocale;
    }
    
    await loadTranslations(currentLocale.value);
  };

  // Watch for locale changes and save to localStorage
  const updateLocale = (locale: string): void => {
    localStorage.setItem('locale', locale);
    setLocale(locale);
  };

  return {
    t,
    currentLocale: computed(() => currentLocale.value),
    availableLocales: computed(() => availableLocales.value),
    loading: computed(() => loading.value),
    translations: getCurrentTranslations,
    setLocale: updateLocale,
    initialize,
  };
}

// Create a singleton instance for global use
export const translationInstance = useTranslations();
