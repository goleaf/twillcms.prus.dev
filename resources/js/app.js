import './bootstrap';

// Basic Laravel application JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('Laravel Blog Application Loaded');
    
    // Initialize any basic interactions
    initializeSearch();
    initializeMobileMenu();
    initializeScrollToTop();
});

// Search functionality
function initializeSearch() {
    const searchInput = document.querySelector('#search-input');
    const searchForm = document.querySelector('#search-form');
    
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const query = searchInput.value.trim();
            if (!query) {
                e.preventDefault();
                alert('Please enter a search term');
            }
        });
    }
}

// Mobile menu toggle
function initializeMobileMenu() {
    const mobileMenuButton = document.querySelector('#mobile-menu-button');
    const mobileMenu = document.querySelector('#mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
}

// Scroll to top functionality
function initializeScrollToTop() {
    const scrollButton = document.querySelector('#scroll-to-top');
    
    if (scrollButton) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                scrollButton.classList.remove('hidden');
            } else {
                scrollButton.classList.add('hidden');
            }
        });
        
        scrollButton.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
}
