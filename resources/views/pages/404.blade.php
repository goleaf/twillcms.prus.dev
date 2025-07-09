<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 bg-indigo-700 text-white px-4 py-2 rounded z-50">Skip to main content</a>
<main id="main-content" tabindex="-1" class="min-h-screen flex flex-col items-center justify-center bg-white dark:bg-gray-900 px-4">
    <div class="text-center">
        <div class="flex justify-center mb-8">
            <svg class="h-24 w-24 text-indigo-600 dark:text-indigo-400 animate-bounce" fill="none" viewBox="0 0 64 64" stroke="currentColor" aria-hidden="true">
                <circle cx="32" cy="32" r="30" stroke-width="4" class="opacity-30" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M24 24l16 16m0-16L24 40" />
            </svg>
        </div>
        <h1 class="text-6xl font-extrabold text-gray-900 dark:text-white mb-4">404</h1>
        <h2 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-2">Page Not Found</h2>
        <p class="text-lg text-gray-500 dark:text-gray-400 mb-8">Sorry, the page you are looking for does not exist or has been moved.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Go to Homepage
            </a>
            <form action="{{ route('search') }}" method="GET" class="flex items-center w-full sm:w-auto">
                <input type="text" name="q" placeholder="Search..." class="px-4 py-3 rounded-l-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" aria-label="Search">
                <button type="submit" class="px-4 py-3 bg-indigo-500 hover:bg-indigo-600 text-white rounded-r-lg font-semibold transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Search
                </button>
            </form>
        </div>
        <p class="text-sm text-gray-400 dark:text-gray-500">If you believe this is an error, please contact support.</p>
    </div>
</main> 