import { createRouter, createWebHistory } from 'vue-router';
import type { RouteRecordRaw } from 'vue-router';
import { useSiteStore } from '@/stores/site';

// Lazy load components for better performance
const Home = () => import('@/views/Home.vue');
const BlogIndex = () => import('@/views/BlogIndex.vue');
const BlogPost = () => import('@/views/BlogPost.vue');
const Category = () => import('@/views/Category.vue');
const CategoryIndex = () => import('@/views/CategoryIndex.vue');
const Search = () => import('@/views/Search.vue');
const Archive = () => import('@/views/Archive.vue');
const NotFound = () => import('@/views/NotFound.vue');

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'home',
    component: Home,
    meta: {
      title: 'Home',
      description: 'Welcome to our blog',
    },
  },
  {
    path: '/blog',
    name: 'blog.index',
    component: BlogIndex,
    meta: {
      title: 'Blog',
      description: 'Latest blog posts and articles',
    },
  },
  {
    path: '/blog/:slug',
    name: 'blog.post',
    component: BlogPost,
    props: true,
    meta: {
      title: 'Blog Post',
      description: 'Read our latest blog post',
    },
  },
  {
    path: '/categories',
    name: 'categories.index',
    component: CategoryIndex,
    meta: {
      title: 'Categories',
      description: 'Browse all blog categories',
    },
  },
  {
    path: '/category/:slug',
    name: 'category.show',
    component: Category,
    props: true,
    meta: {
      title: 'Category',
      description: 'Posts in this category',
    },
  },
  {
    path: '/search',
    name: 'search',
    component: Search,
    meta: {
      title: 'Search',
      description: 'Search blog posts',
    },
  },
  {
    path: '/archive/:year(\\d{4})/:month(\\d{1,2})?',
    name: 'archive',
    component: Archive,
    props: route => ({
      year: parseInt(route.params.year as string),
      month: route.params.month ? parseInt(route.params.month as string) : undefined,
    }),
    meta: {
      title: 'Archive',
      description: 'Blog post archive',
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