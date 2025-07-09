import axios from 'axios';

// Set up axios defaults for Laravel Blade forms
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['Content-Type'] = 'application/json';

// Add CSRF token for Laravel forms
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
}

// Simple response interceptor for form submissions
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        console.error('Request failed:', error);
        return Promise.reject(error);
    }
);

// Make axios available globally for form submissions
window.axios = axios;
