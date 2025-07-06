import { createApp } from 'vue';
import { pinia } from '@/stores';
import router from '@/router';
import App from '@/components/App.vue';
import { translationInstance } from '@/composables/useTranslations';

// CSS imports
import '../css/app.css';

// Create Vue application
const app = createApp(App);

// Install plugins
app.use(pinia);
app.use(router);

// Global translation function
app.config.globalProperties.$t = translationInstance.t;

// Initialize translations
translationInstance.initialize().catch(console.error);

// Global error handler
app.config.errorHandler = (err, instance, info) => {
  console.error('Vue Error:', err);
  console.error('Component:', instance);
  console.error('Info:', info);
  
  // You can send errors to a logging service here
  // Example: Sentry, LogRocket, etc.
};

// Global warning handler (development only)
if (import.meta.env.DEV) {
  app.config.warnHandler = (msg, instance, trace) => {
    console.warn('Vue Warning:', msg);
    console.warn('Trace:', trace);
  };
}

// Performance monitoring (development only)
if (import.meta.env.DEV) {
  app.config.performance = true;
}

// Mount the application
app.mount('#app');

// Export for potential use in other modules
export default app;
