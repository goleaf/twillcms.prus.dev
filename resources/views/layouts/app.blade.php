<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'News Portal') }}</title>

    <!-- Use system fonts for performance -->
    <style>
        :root {
            --font-sans: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            --font-serif: Georgia, Cambria, 'Times New Roman', Times, serif;
        }
        body { font-family: var(--font-sans); }
        .font-display { font-family: var(--font-serif); }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900">
    <!-- Search Overlay -->
    <x-search-overlay />

    <!-- Navigation -->
    <nav class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 sticky top-0 z-50" x-data="{ mobileOpen: false, trapFocus(e) { if (!this.mobileOpen) return; const focusable = $refs.mobileMenu.querySelectorAll('a, button, [tabindex]:not([tabindex=\'-1\'])'); if (!focusable.length) return; const first = focusable[0]; const last = focusable[focusable.length - 1]; if (e.key === 'Tab') { if (e.shiftKey) { if (document.activeElement === first) { e.preventDefault(); last.focus(); } } else { if (document.activeElement === last) { e.preventDefault(); first.focus(); } } } } }" x-on:keydown.tab="trapFocus($event)">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="flex items-center text-xl font-bold text-blue-600 dark:text-blue-400 transition-colors duration-200 hover:scale-105 motion-safe:transition-transform motion-reduce:transition-none font-display">
                        {{ config('app.name', 'News Portal') }}
                    </a>
                    <!-- Desktop Nav -->
                    <div class="hidden sm:-my-px sm:ml-10 sm:flex space-x-8">
                        <a href="{{ route('home') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('home') ? 'border-blue-500 text-gray-900 dark:text-white' : 'border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-white' }} focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">Home</a>
                        <a href="{{ route('news.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('news.*') ? 'border-blue-500 text-gray-900 dark:text-white' : 'border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-white' }} focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">News</a>
                        <a href="{{ route('tags.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('tags.*') ? 'border-blue-500 text-gray-900 dark:text-white' : 'border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-white' }} focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">Topics</a>
                        <a href="{{ route('about') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('about') ? 'border-blue-500 text-gray-900 dark:text-white' : 'border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-white' }} focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">About</a>
                        <a href="{{ route('contact') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('contact') ? 'border-blue-500 text-gray-900 dark:text-white' : 'border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-white' }} focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">Contact</a>
                        <a href="{{ route('admin') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('admin.*') ? 'border-red-500 text-gray-900 dark:text-white' : 'border-transparent text-red-500 dark:text-red-400 hover:border-red-300 hover:text-red-700 dark:hover:text-red-300' }} focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200">Admin</a>
                    </div>
                    <!-- Hamburger for Mobile -->
                    <button @click="mobileOpen = true" class="sm:hidden ml-4 p-2 rounded-md text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="Open menu" :aria-expanded="mobileOpen.toString()" aria-controls="mobile-menu">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
                <div class="flex items-center">
                    <!-- Search button -->
                    <button id="toggle-search" class="p-2 rounded-full text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="Open search">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div x-ref="mobileMenu" x-show="mobileOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-8" class="fixed inset-0 z-50 bg-black/40 flex sm:hidden" style="display: none;" role="dialog" aria-modal="true" aria-label="Main menu">
            <div class="relative w-80 max-w-full bg-white dark:bg-gray-900 shadow-xl h-full flex flex-col py-6 px-6 focus:outline-none">
                <button @click="mobileOpen = false" class="absolute top-4 right-4 p-2 rounded-md text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="Close menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <nav class="mt-12 flex flex-col space-y-4" aria-label="Mobile main menu">
                    <a href="{{ route('home') }}" class="px-4 py-2 rounded text-lg font-medium {{ request()->routeIs('home') ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800' }} focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">Home</a>
                    <a href="{{ route('news.index') }}" class="px-4 py-2 rounded text-lg font-medium {{ request()->routeIs('news.*') ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800' }} focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">News</a>
                    <a href="{{ route('tags.index') }}" class="px-4 py-2 rounded text-lg font-medium {{ request()->routeIs('tags.*') ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800' }} focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">Topics</a>
                    <a href="{{ route('about') }}" class="px-4 py-2 rounded text-lg font-medium {{ request()->routeIs('about') ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800' }} focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">About</a>
                    <a href="{{ route('contact') }}" class="px-4 py-2 rounded text-lg font-medium {{ request()->routeIs('contact') ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800' }} focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">Contact</a>
                    <a href="{{ route('admin') }}" class="px-4 py-2 rounded text-lg font-medium {{ request()->routeIs('admin.*') ? 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200' : 'text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20' }} focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200">Admin</a>
                </nav>
            </div>
            <div class="flex-1" @click="mobileOpen = false"></div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="min-h-screen">
        @if(session('success'))
            <x-toast type="success" x-transition:enter="motion-safe:transition-opacity motion-safe:duration-300 motion-safe:ease-out" x-transition:leave="motion-safe:transition-opacity motion-safe:duration-200 motion-safe:ease-in">{{ session('success') }}</x-toast>
        @endif
        @if(session('error'))
            <x-toast type="error" x-transition:enter="motion-safe:transition-opacity motion-safe:duration-300 motion-safe:ease-out" x-transition:leave="motion-safe:transition-opacity motion-safe:duration-200 motion-safe:ease-in">{{ session('error') }}</x-toast>
        @endif
        @if(session('info'))
            <x-toast type="info" x-transition:enter="motion-safe:transition-opacity motion-safe:duration-300 motion-safe:ease-out" x-transition:leave="motion-safe:transition-opacity motion-safe:duration-200 motion-safe:ease-in">{{ session('info') }}</x-toast>
        @endif

        @yield('content')
    </main>

    <!-- Back to Top Button -->
    <button
        id="scroll-to-top"
        class="fixed bottom-8 right-8 z-50 rounded-full bg-indigo-600 p-3 text-white shadow-lg transition-all duration-300 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 opacity-0 invisible motion-safe:transition-transform motion-reduce:transition-none"
        @click="window.scrollTo({top: 0, behavior: 'smooth'})"
    >
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
        </svg>
    </button>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 font-display">{{ config('app.name', 'News Portal') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Your trusted source for the latest news and insights across technology, business, science, and more.</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">Home</a></li>
                        <li><a href="{{ route('news.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">News</a></li>
                        <li><a href="{{ route('tags.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">Topics</a></li>
                        <li><a href="{{ route('about') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">About</a></li>
                        <li><a href="{{ route('contact') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('privacy') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('terms') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                    &copy; {{ date('Y') }} {{ config('app.name', 'News Portal') }}. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html> 