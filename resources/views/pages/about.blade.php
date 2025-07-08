<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 bg-indigo-700 text-white px-4 py-2 rounded z-50">{{ __('Skip to main content') }}</a>
<main id="main-content" tabindex="-1" class="container mx-auto py-12 relative overflow-hidden">
    <!-- SVG Gradient Background -->
    <svg class="absolute inset-0 w-full h-full pointer-events-none" aria-hidden="true" focusable="false">
        <defs>
            <linearGradient id="about-hero-gradient" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%" stop-color="#818cf8" stop-opacity="0.13" />
                <stop offset="100%" stop-color="#f472b6" stop-opacity="0.10" />
            </linearGradient>
            <linearGradient id="about-hero-gradient-dark" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%" stop-color="#a5b4fc" stop-opacity="0.18" />
                <stop offset="100%" stop-color="#f9a8d4" stop-opacity="0.13" />
            </linearGradient>
        </defs>
        <rect width="100%" height="100%" fill="url(#about-hero-gradient)" class="block dark:hidden" />
        <rect width="100%" height="100%" fill="url(#about-hero-gradient-dark)" class="hidden dark:block" />
    </svg>
    <section class="relative prose prose-lg max-w-none dark:prose-invert prose-indigo">
                        <h1 class="text-3xl font-bold mb-4 text-gray-900 dark:text-white">About</h1>
                <p class="text-lg text-gray-700 dark:text-gray-300">This is the about page.</p>
    </section>
</main> 