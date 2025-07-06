import { createRouter, createWebHistory } from 'vue-router';
import type { RouteRecordRaw } from 'vue-router';
import { useSiteStore } from '@/stores/site';

// Lazy load components for better performance
const Home = () => import('@/components/pages/Home.vue');
const PostDetail = () => import('@/components/pages/PostDetail.vue');
const CategoryDetail = () => import('@/components/pages/CategoryDetail.vue');
const CategoriesIndex = () => import('@/components/pages/CategoriesIndex.vue');
const SearchResults = () => import('@/components/pages/SearchResults.vue');
const ArchiveIndex = () => import('@/components/pages/ArchiveIndex.vue');
const NotFound = () => import('@/components/pages/NotFound.vue');

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'home',
    component: Home,
    meta: {
      title: 'Home',
      description: 'Welcome to our news portal',
    },
  },
  {
    path: '/post/:slug',
    name: 'post.detail',
    component: PostDetail,
    props: true,
    meta: {
      title: 'Article',
      description: 'Read our latest article',
    },
  },
  {
    path: '/categories',
    name: 'categories.index',
    component: CategoriesIndex,
    meta: {
      title: 'Categories',
      description: 'Browse all news categories',
    },
  },
  {
    path: '/category/:slug',
    name: 'category.detail',
    component: CategoryDetail,
    props: true,
    meta: {
      title: 'Category',
      description: 'Articles in this category',
    },
  },
  {
    path: '/search',
    name: 'search',
    component: SearchResults,
    meta: {
      title: 'Search',
      description: 'Search articles',
    },
  },
  {
    path: '/archives',
    name: 'archives',
    component: ArchiveIndex,
    meta: {
      title: 'Archives',
      description: 'Article archives',
    },
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: NotFound,
    meta: {
      title: 'Page Not Found',
      description: 'The page you are looking for does not exist',
      noIndex: true,
    },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    // Return saved position if available (back/forward navigation)
    if (savedPosition) {
      return savedPosition;
    }
    
    // Scroll to anchor if present
    if (to.hash) {
      return {
        el: to.hash,
        behavior: 'smooth',
      };
    }
    
    // Scroll to top for new pages
    return { top: 0, behavior: 'smooth' };
  },
});

// Global navigation guards
router.beforeEach(async (to, from, next) => {
  // Initialize site store if not already done
  const siteStore = useSiteStore();
  if (!siteStore.config) {
    try {
      await siteStore.initialize();
    } catch (error) {
      console.error('Failed to initialize site configuration:', error);
    }
  }
  
  next();
});

router.afterEach((to, from) => {
  // Update document title
  const siteStore = useSiteStore();
  const baseTitle = siteStore.siteName;
  const pageTitle = to.meta.title as string;
  
  document.title = pageTitle && pageTitle !== 'Home' 
    ? `${pageTitle} | ${baseTitle}`
    : baseTitle;
  
  // Update meta description
  const metaDescription = document.querySelector('meta[name="description"]');
  if (metaDescription && to.meta.description) {
    metaDescription.setAttribute('content', to.meta.description as string);
  }
  
  // Update canonical URL
  const canonical = document.querySelector('link[rel="canonical"]');
  if (canonical) {
    canonical.setAttribute('href', window.location.href);
  }
  
  // Handle noIndex meta tag
  let robotsMeta = document.querySelector('meta[name="robots"]');
  if (to.meta.noIndex) {
    if (!robotsMeta) {
      robotsMeta = document.createElement('meta');
      robotsMeta.setAttribute('name', 'robots');
      document.head.appendChild(robotsMeta);
    }
    robotsMeta.setAttribute('content', 'noindex, nofollow');
  } else if (robotsMeta) {
    robotsMeta.setAttribute('content', 'index, follow');
  }
  
  // Google Analytics page view (if GA is configured)
  if (typeof gtag !== 'undefined') {
    gtag('config', 'GA_MEASUREMENT_ID', {
      page_path: to.fullPath,
    });
  }
});

export default router; 