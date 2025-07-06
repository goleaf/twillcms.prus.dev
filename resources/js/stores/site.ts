import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { SiteConfig, ArchiveData } from '@/types';
import { apiClient } from '@/api/client';

export const useSiteStore = defineStore('site', () => {
  // State
  const config = ref<SiteConfig | null>(null);
  const archives = ref<ArchiveData[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  // Getters
  const siteName = computed(() => config.value?.site.name || 'TwillCMS Blog');
  const siteDescription = computed(() => config.value?.site.description || '');
  const siteUrl = computed(() => config.value?.site.url || '');
  const features = computed(() => config.value?.features || {});

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
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to fetch site configuration');
    } finally {
      loading.value = false;
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
      fetchArchives(),
    ]);
  };

  return {
    // State
    config,
    archives,
    loading,
    error,
    
    // Getters
    siteName,
    siteDescription,
    siteUrl,
    features,
    archiveYears,
    
    // Actions
    fetchConfig,
    fetchArchives,
    getArchiveForYear,
    getArchiveForMonth,
    initialize,
    clearError,
  };
}); 