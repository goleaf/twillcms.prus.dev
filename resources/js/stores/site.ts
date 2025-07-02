import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { SiteConfig, ArchiveData, TranslationResponse, Translations } from '@/types';
import { apiClient } from '@/api/client';

export const useSiteStore = defineStore('site', () => {
  // State
  const config = ref<SiteConfig | null>(null);
  const translations = ref<Translations | null>(null);
  const currentLocale = ref<string>('en');
  const archives = ref<ArchiveData[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  // Getters
  const siteName = computed(() => config.value?.site.name || 'TwillCMS Blog');
  const siteDescription = computed(() => config.value?.site.description || '');
  const siteUrl = computed(() => config.value?.site.url || '');
  const availableLocales = computed(() => config.value?.site.available_locales || ['en']);
  const features = computed(() => config.value?.features || {});
  
  const t = computed(() => {
    return (key: string, fallback?: string): string => {
      if (!translations.value) return fallback || key;
      
      const keys = key.split('.');
      let value: any = translations.value;
      
      for (const k of keys) {
        value = value?.[k];
        if (value === undefined) break;
      }
      
      return typeof value === 'string' ? value : (fallback || key);
    };
  });

  const archiveYears = computed(() => {
    return archives.value.map(archive => ({
      year: archive.year,
      total: archive.total,
      url: archive.url,
    }));
  });

  // Actions
  const clearError = () => {
    error.value = null;
  };

  const setError = (message: string) => {
    error.value = message;
  };

  const fetchConfig = async () => {
    loading.value = true;
    clearError();
    
    try {
      const data = await apiClient.getSiteConfig();
      config.value = data;
      currentLocale.value = data.site.locale;
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to fetch site configuration');
    } finally {
      loading.value = false;
    }
  };

  const fetchTranslations = async (locale?: string) => {
    const targetLocale = locale || currentLocale.value;
    
    try {
      const response = await apiClient.getTranslations(targetLocale);
      translations.value = response.translations;
      currentLocale.value = response.locale;
    } catch (err) {
      console.error('Failed to fetch translations:', err);
      // Don't set error state for translations as it's not critical
    }
  };

  const fetchArchives = async () => {
    try {
      const data = await apiClient.getArchives();
      archives.value = data;
    } catch (err) {
      console.error('Failed to fetch archives:', err);
    }
  };

  const setLocale = async (locale: string) => {
    if (availableLocales.value.includes(locale)) {
      currentLocale.value = locale;
      await fetchTranslations(locale);
    }
  };

  const getArchiveForYear = (year: number) => {
    return archives.value.find(archive => archive.year === year);
  };

  const getArchiveForMonth = (year: number, month: number) => {
    const yearArchive = getArchiveForYear(year);
    return yearArchive?.months.find(m => m.month === month);
  };

  // Initialize all site data
  const initialize = async () => {
    await Promise.all([
      fetchConfig(),
      fetchTranslations(),
      fetchArchives(),
    ]);
  };

  return {
    // State
    config,
    translations,
    currentLocale,
    archives,
    loading,
    error,
    
    // Getters
    siteName,
    siteDescription,
    siteUrl,
    availableLocales,
    features,
    t,
    archiveYears,
    
    // Actions
    fetchConfig,
    fetchTranslations,
    fetchArchives,
    setLocale,
    getArchiveForYear,
    getArchiveForMonth,
    initialize,
    clearError,
  };
}); 