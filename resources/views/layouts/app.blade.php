<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'News Portal - Latest News & Updates')</title>
    <meta name="description" content="@yield('description', 'Stay updated with the latest news, breaking stories, and trending topics from around the world.')">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'News Portal - Latest News & Updates')">
    <meta property="og:description" content="@yield('description', 'Stay updated with the latest news, breaking stories, and trending topics from around the world.')">
    <meta property="og:image" content="@yield('og_image', asset('images/news-portal-logo.jpg'))">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('title', 'News Portal - Latest News & Updates')">
    <meta property="twitter:description" content="@yield('description', 'Stay updated with the latest news, breaking stories, and trending topics from around the world.')">
    <meta property="twitter:image" content="@yield('og_image', asset('images/news-portal-logo.jpg'))">

    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 antialiased">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo and Main Navigation -->
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('home') }}" class="text-2xl font-bold text-red-600">
                            📰 News Portal
                        </a>
                    </div>
                    
                    <!-- Desktop Navigation -->
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('home') }}" 
                           class="border-red-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('home') ? 'border-red-500' : 'border-transparent hover:border-gray-300' }}">
                            Home
                        </a>
                        <a href="{{ route('news.index') }}" 
                           class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('news.*') ? 'border-red-500 text-gray-900' : '' }}">
                            Latest News
                        </a>
                        <a href="{{ route('tags.index') }}" 
                           class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('tags.*') ? 'border-red-500 text-gray-900' : '' }}">
                            Topics
                        </a>
                        <a href="{{ route('categories.index') }}" 
                           class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('categories.*') ? 'border-red-500 text-gray-900' : '' }}">
                            Sections
                        </a>
                    </div>
                </div>

                <!-- Search and Mobile Menu Button -->
                <div class="flex items-center">
                    <!-- Search Form -->
                    <div class="hidden md:block">
                        <form action="{{ route('search') }}" method="GET" class="flex">
                            <input type="search" 
                                   name="q" 
                                   value="{{ request('q') }}"
                                   placeholder="Search news..." 
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-l-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            <button type="submit" 
                                    class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="ml-3 sm:hidden">
                        <button type="button" id="mobile-menu-button" class="bg-white inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-red-500">
                            <span class="sr-only">Open main menu</span>
                            <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="sm:hidden hidden" id="mobile-menu">
            <div class="pt-2 pb-3 space-y-1">
                <a href="{{ route('home') }}" class="bg-red-50 border-red-500 text-red-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Home</a>
                <a href="{{ route('news.index') }}" class="border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Latest News</a>
                <a href="{{ route('tags.index') }}" class="border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Topics</a>
                <a href="{{ route('categories.index') }}" class="border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Sections</a>
            </div>
            <div class="pt-4 pb-3 border-t border-gray-200">
                <div class="px-4">
                    <form action="{{ route('search') }}" method="GET" class="flex">
                        <input type="search" 
                               name="q" 
                               value="{{ request('q') }}"
                               placeholder="Search news..." 
                               class="block w-full px-3 py-2 border border-gray-300 rounded-l-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        <button type="submit" 
                                class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 hover:text-gray-700 hover:bg-gray-100">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-lg font-semibold mb-4">📰 News Portal</h3>
                    <p class="text-gray-300 mb-4">
                        Your trusted source for the latest news, breaking stories, and in-depth analysis from around the world.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-300 hover:text-white">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-gray-300 hover:text-white">Home</a></li>
                        <li><a href="{{ route('news.index') }}" class="text-gray-300 hover:text-white">Latest News</a></li>
                        <li><a href="{{ route('tags.index') }}" class="text-gray-300 hover:text-white">Topics</a></li>
                        <li><a href="{{ route('categories.index') }}" class="text-gray-300 hover:text-white">Sections</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Information</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-300 hover:text-white">About Us</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white">Contact</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="mt-8 pt-8 border-t border-gray-700 text-center text-gray-300">
                <p>&copy; {{ date('Y') }} News Portal. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button id="scroll-to-top" class="fixed bottom-4 right-4 bg-red-600 text-white p-3 rounded-full shadow-lg hover:bg-red-700 transition-all duration-300 opacity-0 invisible">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
        </svg>
    </button>
</body>
</html> 