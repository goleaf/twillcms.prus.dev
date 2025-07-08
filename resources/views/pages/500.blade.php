<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 bg-indigo-700 text-white px-4 py-2 rounded z-50">{{ __('Skip to main content') }}</a>
<main id="main-content" tabindex="-1" class="min-h-screen flex flex-col items-center justify-center bg-white dark:bg-gray-900 px-4">
    <div class="text-center">
        <div class="flex justify-center mb-8">
            <svg class="h-24 w-24 text-red-600 dark:text-red-400 animate-pulse" fill="none" viewBox="0 0 64 64" stroke="currentColor" aria-hidden="true">
                <circle cx="32" cy="32" r="30" stroke-width="4" class="opacity-30" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M32 20v16m0 8h.01" />
            </svg>
        </div>
        <h1 class="text-6xl font-extrabold text-gray-900 dark:text-white mb-4">500</h1>
        <h2 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-2">{{ __('Server Error') }}</h2>
        <p class="text-lg text-gray-500 dark:text-gray-400 mb-8">{{ __('Sorry, something went wrong on our end. Please try again later or contact support if the problem persists.') }}</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                {{ __('Go to Homepage') }}
            </a>
            <a href="mailto:support@newsportal.com" class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 font-semibold rounded-lg shadow transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                {{ __('Contact Support') }}
            </a>
        </div>
        <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('We have been notified and are working to fix the issue.') }}</p>
    </div>
</main> 