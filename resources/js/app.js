import './bootstrap';

// Modern News Portal JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('📰 News Portal Loaded - Modern Laravel Implementation');
    
    // Initialize all functionality
    initializeSearch();
    initializeMobileMenu();
    initializeScrollToTop();
    initializeTagSearch();
    initializeReadingProgress();
    initializeSocialSharing();
    initializeImageLazyLoading();
    initializeInfiniteScroll();
    initializeSmoothScroll();
    initializeLoadingStates();
    initializeModals();
    initializeNavigation();
    initializeSearchOverlay();
    initializeNewsletterForm();
    initializeNotifications();
    showLoadingCompleteNotification();
    initializeSmoothScrolling();
});

// Enhanced search functionality with autocomplete
function initializeSearch() {
    const searchInputs = document.querySelectorAll('input[name="q"]');
    const searchForms = document.querySelectorAll('form[action*="search"]');
    
    searchForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const input = form.querySelector('input[name="q"]');
            const query = input.value.trim();
            if (!query) {
                e.preventDefault();
                showNotification('Please enter a search term', 'warning');
            }
        });
    });

    // Live search suggestions (could be enhanced with API calls)
    searchInputs.forEach(input => {
        input.addEventListener('input', debounce(function() {
            const query = this.value.trim();
            if (query.length > 2) {
                // Placeholder for live search functionality
                console.log('Searching for:', query);
            }
        }, 300));
    });
}

// Enhanced mobile menu with smooth animations
function initializeMobileMenu() {
    const mobileMenuButton = document.querySelector('#mobile-menu-button');
    const mobileMenu = document.querySelector('#mobile-menu');

    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            const isHidden = mobileMenu.classList.contains('hidden');
            
            if (isHidden) {
                mobileMenu.classList.remove('hidden');
                mobileMenu.style.maxHeight = '0';
                requestAnimationFrame(() => {
                    mobileMenu.style.maxHeight = mobileMenu.scrollHeight + 'px';
                });
            } else {
                mobileMenu.style.maxHeight = '0';
                setTimeout(() => {
                    mobileMenu.classList.add('hidden');
                }, 300);
            }
        });
    }
}

// Enhanced scroll to top with progress indicator
function initializeScrollToTop() {
    const scrollButton = document.querySelector('#scroll-to-top');
    
    if (scrollButton) {
        let ticking = false;
        
        window.addEventListener('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(function() {
                    const scrollTop = window.pageYOffset;
                    const docHeight = document.body.scrollHeight - window.innerHeight;
                    const scrollPercent = scrollTop / docHeight;
                    
                    if (scrollTop > 300) {
                        scrollButton.classList.remove('opacity-0', 'invisible');
                        scrollButton.classList.add('opacity-100', 'visible');
                    } else {
                        scrollButton.classList.add('opacity-0', 'invisible');
                        scrollButton.classList.remove('opacity-100', 'visible');
                    }
                    
                    ticking = false;
                });
                ticking = true;
            }
        });
        
        scrollButton.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
}

// Tag search functionality
function initializeTagSearch() {
    const tagSearchInput = document.querySelector('[data-tag-search]');
    const tagElements = document.querySelectorAll('[data-tag]');
            
    if (tagSearchInput && tagElements.length > 0) {
        tagSearchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            
            tagElements.forEach(tag => {
                const tagName = tag.dataset.tag.toLowerCase();
                const tagElement = tag.closest('.tag-item');
                
                if (tagName.includes(query)) {
                    tagElement.style.display = '';
                    tagElement.classList.remove('hidden');
                } else {
                    tagElement.style.display = 'none';
                    tagElement.classList.add('hidden');
                }
            });
        });
    }
}

// Reading progress indicator for articles
function initializeReadingProgress() {
    const progressBar = document.querySelector('#reading-progress');
    const articleContent = document.querySelector('.article-content');
    
    if (progressBar && articleContent) {
        window.addEventListener('scroll', throttle(function() {
            const scrollTop = window.pageYOffset;
            const contentTop = articleContent.offsetTop;
            const contentHeight = articleContent.scrollHeight;
            const windowHeight = window.innerHeight;
            
            const progress = Math.max(0, Math.min(100, 
                ((scrollTop - contentTop + windowHeight) / contentHeight) * 100
            ));
            
            progressBar.style.width = progress + '%';
        }, 10));
    }
}

// Social sharing functionality
function initializeSocialSharing() {
    const shareButtons = document.querySelectorAll('[data-share]');
    
    shareButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const platform = this.dataset.share;
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title);
            const text = encodeURIComponent(document.querySelector('meta[name="description"]')?.content || '');
            
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
                case 'copy':
                    navigator.clipboard.writeText(window.location.href).then(() => {
                        showNotification('Link copied to clipboard!', 'success');
                    });
                    return;
            }
            
            if (shareUrl) {
                window.open(shareUrl, '_blank', 'width=600,height=400');
            }
        });
    });
}

// Lazy loading for images
function initializeImageLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');

    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('opacity-0');
                    img.classList.add('opacity-100');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback for older browsers
        images.forEach(img => {
            img.src = img.dataset.src;
            img.classList.remove('opacity-0');
            img.classList.add('opacity-100');
        });
    }
}

// Infinite scroll for news feeds
function initializeInfiniteScroll() {
    const nextPageUrl = document.querySelector('[data-next-page]')?.dataset.nextPage;
    const loadingIndicator = document.querySelector('#loading-indicator');
    
    if (nextPageUrl && loadingIndicator) {
        let loading = false;
        
        window.addEventListener('scroll', throttle(function() {
            if (loading) return;
            
            const scrollTop = window.pageYOffset;
            const windowHeight = window.innerHeight;
            const documentHeight = document.body.scrollHeight;
            
            if (scrollTop + windowHeight >= documentHeight - 1000) {
                loading = true;
                loadingIndicator.classList.remove('hidden');
                
                fetch(nextPageUrl)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newArticles = doc.querySelectorAll('.news-article');
                        const container = document.querySelector('#news-container');
                        
                        newArticles.forEach(article => {
                            container.appendChild(article);
                        });
                        
                        loading = false;
                        loadingIndicator.classList.add('hidden');
                    })
                    .catch(() => {
                        loading = false;
                        loadingIndicator.classList.add('hidden');
                    });
            }
        }, 100));
    }
}

// Smooth scroll animation for internal links
function initializeSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                const headerOffset = 80; // Account for fixed header
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
}

// Loading state manager with skeleton screens
function initializeLoadingStates() {
    const articlesGrid = document.querySelector('#articles-grid');
    const skeletonTemplate = document.querySelector('#skeleton-loader');
    
    if (articlesGrid && skeletonTemplate) {
        // Show loading state
        window.showLoading = function(count = 6) {
            articlesGrid.innerHTML = '';
            for (let i = 0; i < count; i++) {
                const clone = document.importNode(skeletonTemplate.content, true);
                articlesGrid.appendChild(clone);
            }
        };
        
        // Hide loading state and show content
        window.hideLoading = function(content) {
            articlesGrid.innerHTML = content;
        };
    }
}

// Utility functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
    
    // Set notification style based on type
    const styles = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        warning: 'bg-yellow-500 text-black',
        info: 'bg-blue-500 text-white'
    };
    
    notification.className += ` ${styles[type] || styles.info}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Animate in
    requestAnimationFrame(() => {
        notification.classList.remove('translate-x-full');
    });
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Search overlay functionality
function initializeSearchOverlay() {
    const searchToggle = document.querySelector('#toggle-search');
    const searchOverlay = document.querySelector('[x-data*="show"]');
    
    if (searchToggle) {
        searchToggle.addEventListener('click', function(e) {
            e.preventDefault();
            // Dispatch custom event that Alpine.js can listen to
            window.dispatchEvent(new CustomEvent('toggle-search'));
        });
    }
    
    // Keyboard shortcut (Ctrl+K or Cmd+K)
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            window.dispatchEvent(new CustomEvent('toggle-search'));
        }
    });
}

// Modal management
function initializeModals() {
    // Open modal
    window.openModal = function(modalId) {
        window.dispatchEvent(new CustomEvent('open-modal', {
            detail: modalId
        }));
    };

    // Close modal
    window.closeModal = function(modalId) {
        window.dispatchEvent(new CustomEvent('close-modal', {
            detail: modalId
        }));
    };

    // Close all modals
    window.closeAllModals = function() {
        window.dispatchEvent(new CustomEvent('close-modal'));
    };

    // Handle ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.closeAllModals();
        }
    });
}

// Navigation enhancements
function initializeNavigation() {
    // Enhanced navigation with current page highlighting
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('nav a[href]');
    
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
            link.setAttribute('aria-current', 'page');
        }
    });
}

// Newsletter form handling
function initializeNewsletterForm() {
    const newsletterForms = document.querySelectorAll('[data-newsletter-form]');
    
    newsletterForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            
            if (email) {
                showNotification('Thank you for subscribing!', 'success');
                this.reset();
            } else {
                showNotification('Please enter a valid email address', 'error');
            }
        });
    });
}

// Notifications system
function initializeNotifications() {
    // Create notification container if it doesn't exist
    if (!document.querySelector('#notifications-container')) {
        const container = document.createElement('div');
        container.id = 'notifications-container';
        container.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(container);
    }
}

// Loading complete notification
function showLoadingCompleteNotification() {
    setTimeout(() => {
        console.log('✅ All systems loaded successfully');
    }, 100);
}

// Smooth scrolling enhancements
function initializeSmoothScrolling() {
    // Enhanced smooth scrolling for the entire page
    if ('scrollBehavior' in document.documentElement.style) {
        document.documentElement.style.scrollBehavior = 'smooth';
    }
}
