import './bootstrap';
import './app.ts';
import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import axios from 'axios'
import { createI18n } from 'vue-i18n'
import en from '../lang/en.json'

// Import components
import App from './components/App.vue'
import Home from './components/pages/Home.vue'
import PostDetail from './components/pages/PostDetail.vue'
import CategoryDetail from './components/pages/CategoryDetail.vue'
import CategoriesIndex from './components/pages/CategoriesIndex.vue'
import SearchResults from './components/pages/SearchResults.vue'
import ArchiveIndex from './components/pages/ArchiveIndex.vue'
import NotFound from './components/pages/NotFound.vue'

// Configure axios
axios.defaults.baseURL = '/api/v1'
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.headers.common['Accept'] = 'application/json'

// Create router
const routes = [
  {
    path: '/',
    name: 'home',
    component: Home,
    meta: { title: 'Home' }
  },
  {
    path: '/posts/:slug',
    name: 'post.detail',
    component: PostDetail,
    meta: { title: 'Post' }
  },
  {
    path: '/categories',
    name: 'categories.index',
    component: CategoriesIndex,
    meta: { title: 'Categories' }
  },
  {
    path: '/categories/:slug',
    name: 'category.detail',
    component: CategoryDetail,
    meta: { title: 'Category' }
  },
  {
    path: '/search',
    name: 'search',
    component: SearchResults,
    meta: { title: 'Search Results' }
  },
  {
    path: '/archives',
    name: 'archives.index',
    component: ArchiveIndex,
    meta: { title: 'Archives' }
  },
  {
    path: '/archives/:year/:month?',
    name: 'archives.detail',
    component: ArchiveIndex,
    meta: { title: 'Archive' }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: NotFound,
    meta: { title: 'Page Not Found' }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

// Global navigation guard for title updates
router.beforeEach((to, from, next) => {
  document.title = to.meta.title ? `${to.meta.title} - News Portal` : 'News Portal'
  next()
})

// Set up i18n
const i18n = createI18n({
  legacy: false,
  locale: 'en',
  fallbackLocale: 'en',
  messages: { en },
  globalInjection: true
})

// Create Vue app
const app = createApp(App)

// Install router
app.use(router)
// Install i18n
app.use(i18n)

// Global properties
app.config.globalProperties.$http = axios
app.config.globalProperties.$router = router

// Mount app
app.mount('#app')

// Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Search Overlay
    const searchToggle = document.getElementById('search-toggle');
    const searchOverlay = document.getElementById('search-overlay');
    const searchClose = document.getElementById('search-close');
    const searchInput = document.getElementById('search-input');

    if (searchToggle && searchOverlay) {
        searchToggle.addEventListener('click', function() {
            searchOverlay.classList.remove('hidden');
            if (searchInput) {
                setTimeout(() => searchInput.focus(), 100);
            }
        });
    }

    if (searchClose && searchOverlay) {
        searchClose.addEventListener('click', function() {
            searchOverlay.classList.add('hidden');
        });
    }

    if (searchOverlay) {
        searchOverlay.addEventListener('click', function(e) {
            if (e.target === searchOverlay) {
                searchOverlay.classList.add('hidden');
            }
        });
    }

    // Language Switcher
    const languageToggle = document.getElementById('language-toggle');
    const languageMenu = document.getElementById('language-menu');

    if (languageToggle && languageMenu) {
        languageToggle.addEventListener('click', function(e) {
            e.preventDefault();
            languageMenu.classList.toggle('hidden');
        });

        // Close language menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!languageToggle.contains(e.target) && !languageMenu.contains(e.target)) {
                languageMenu.classList.add('hidden');
            }
        });

        // Handle language selection
        const languageLinks = languageMenu.querySelectorAll('[data-lang]');
        languageLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const locale = this.getAttribute('data-lang');
                
                fetch(`/api/language/${locale}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));

                languageMenu.classList.add('hidden');
            });
        });
    }

    // Newsletter Form Handling
    const newsletterForms = document.querySelectorAll('form');
    newsletterForms.forEach(form => {
        const emailInput = form.querySelector('input[type="email"]');
        if (emailInput && emailInput.placeholder.includes('email')) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const email = emailInput.value;
                
                if (email) {
                    // Simple validation
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (emailRegex.test(email)) {
                        // Show success message
                        showNotification('Thank you for subscribing! We\'ll be in touch soon.', 'success');
                        emailInput.value = '';
                    } else {
                        showNotification('Please enter a valid email address.', 'error');
                    }
                }
            });
        }
    });

    // Smooth scrolling for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Image lazy loading
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        observer.unobserve(img);
                    }
                }
            });
        });

        const lazyImages = document.querySelectorAll('img[data-src]');
        lazyImages.forEach(img => imageObserver.observe(img));
    }

    // Reading progress indicator (for blog posts)
    const progressBar = document.getElementById('reading-progress');
    if (progressBar) {
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset;
            const docHeight = document.body.scrollHeight - window.innerHeight;
            const scrollPercent = (scrollTop / docHeight) * 100;
            progressBar.style.width = scrollPercent + '%';
        });
    }

    // Social sharing
    const shareButtons = document.querySelectorAll('[data-share]');
    shareButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const platform = this.getAttribute('data-share');
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title);
            
            let shareUrl = '';
            switch (platform) {
                case 'twitter':
                    shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
                    break;
                case 'facebook':
                    shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
                    break;
                case 'linkedin':
                    shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
                    break;
            }
            
            if (shareUrl) {
                window.open(shareUrl, '_blank', 'width=600,height=400');
            }
        });
    });

    // Animation on scroll
    if ('IntersectionObserver' in window) {
        const animationObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, { threshold: 0.1 });

        const animateElements = document.querySelectorAll('.animate-on-scroll');
        animateElements.forEach(el => animationObserver.observe(el));
    }
});

// Notification system
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Animate out and remove
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 4000);
}

// Search functionality
function performSearch(query) {
    if (query.length > 2) {
        // Simulate search - in real app, this would make an API call
        console.log('Searching for:', query);
        // You can implement actual search logic here
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K to open search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const searchOverlay = document.getElementById('search-overlay');
        const searchInput = document.getElementById('search-input');
        
        if (searchOverlay && searchInput) {
            searchOverlay.classList.remove('hidden');
            setTimeout(() => searchInput.focus(), 100);
        }
    }
    
    // Escape to close overlays
    if (e.key === 'Escape') {
        const searchOverlay = document.getElementById('search-overlay');
        const mobileMenu = document.getElementById('mobile-menu');
        const languageMenu = document.getElementById('language-menu');
        
        if (searchOverlay && !searchOverlay.classList.contains('hidden')) {
            searchOverlay.classList.add('hidden');
        }
        if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
            mobileMenu.classList.add('hidden');
        }
        if (languageMenu && !languageMenu.classList.contains('hidden')) {
            languageMenu.classList.add('hidden');
        }
    }
});

// Back to top button
function addBackToTopButton() {
    const backToTop = document.createElement('button');
    backToTop.innerHTML = '↑';
    backToTop.className = 'fixed bottom-8 right-8 w-12 h-12 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 transition-all duration-300 opacity-0 pointer-events-none z-40';
    backToTop.setAttribute('aria-label', 'Back to top');
    
    document.body.appendChild(backToTop);
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 500) {
            backToTop.classList.remove('opacity-0', 'pointer-events-none');
        } else {
            backToTop.classList.add('opacity-0', 'pointer-events-none');
        }
    });
    
    backToTop.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// Initialize back to top button
document.addEventListener('DOMContentLoaded', addBackToTopButton);
