<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Primary Meta Tags -->
    <title>{{ config('app.name', 'TwillCMS Blog') }}</title>
    <meta name="title" content="{{ config('app.name', 'TwillCMS Blog') }}">
    <meta name="description" content="A modern blog built with TwillCMS and Vue.js">
    <meta name="keywords" content="blog, articles, tutorials, insights">
    <meta name="author" content="{{ config('app.name') }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ config('app.url') }}">
    <meta property="og:title" content="{{ config('app.name', 'TwillCMS Blog') }}">
    <meta property="og:description" content="A modern blog built with TwillCMS and Vue.js">
    <meta property="og:image" content="{{ config('app.url') }}/images/og-image.jpg">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ config('app.url') }}">
    <meta property="twitter:title" content="{{ config('app.name', 'TwillCMS Blog') }}">
    <meta property="twitter:description" content="A modern blog built with TwillCMS and Vue.js">
    <meta property="twitter:image" content="{{ config('app.url') }}/images/og-image.jpg">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ config('app.url') }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    
    <!-- Theme Color -->
    <meta name="theme-color" content="#3B82F6">
    <meta name="msapplication-TileColor" content="#3B82F6">
    
    <!-- Robots -->
    <meta name="robots" content="index, follow">
    
    <!-- Language -->
    <meta name="language" content="en">
    
    <!-- Preconnect to external domains for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- DNS Prefetch for API calls -->
    <link rel="dns-prefetch" href="{{ config('app.url') }}">
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "{{ config('app.name', 'TwillCMS Blog') }}",
        "description": "A modern blog built with TwillCMS and Vue.js",
        "url": "{{ config('app.url') }}",
        "potentialAction": {
            "@type": "SearchAction",
            "target": {
                "@type": "EntryPoint",
                "urlTemplate": "{{ config('app.url') }}/search?q={search_term_string}"
            },
            "query-input": "required name=search_term_string"
        }
    }
    </script>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    
    <!-- Critical CSS for above-the-fold content -->
    <style>
        /* Critical CSS to prevent FOUC */
        #app {
            min-height: 100vh;
            background-color: #f9fafb;
        }
        
        /* Loading state styles */
        .loading-spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 3px solid #f3f3f3;
            border-radius: 50%;
            border-top: 3px solid #3B82F6;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Hide content until Vue is ready */
        [v-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="antialiased">
    <!-- Vue.js Application Mount Point -->
    <div id="app" v-cloak>
        <!-- Fallback content for when JavaScript is disabled -->
        <noscript>
            <div class="min-h-screen flex items-center justify-center bg-gray-50">
                <div class="text-center p-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ config('app.name', 'TwillCMS Blog') }}</h1>
                    <p class="text-lg text-gray-600 mb-6">This website requires JavaScript to function properly.</p>
                    <p class="text-gray-500">Please enable JavaScript in your browser and refresh the page.</p>
                </div>
            </div>
        </noscript>
        
        <!-- Initial loading state -->
        <div class="min-h-screen flex items-center justify-center bg-gray-50" style="display: block;">
            <div class="text-center">
                <div class="loading-spinner mb-4"></div>
                <p class="text-gray-600">Loading...</p>
            </div>
        </div>
    </div>
    
    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('SW registered: ', registration);
                    })
                    .catch(function(registrationError) {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
    </script>
    
    <!-- Google Analytics (if configured) -->
    @if(config('services.google_analytics.id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('services.google_analytics.id') }}');
    </script>
    @endif
</body>
</html> 