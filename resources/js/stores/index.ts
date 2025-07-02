import { createPinia } from 'pinia';
import { persistedStatePlugin } from './plugins/persistence';

export const pinia = createPinia();

// Add persistence plugin
pinia.use(persistedStatePlugin);

// Export all stores
export { useBlogStore } from './blog';
export { useCategoryStore } from './category';
export { useSiteStore } from './site';
export { useSearchStore } from './search';
export { useLoadingStore } from './loading'; 