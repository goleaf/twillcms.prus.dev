@extends('layouts.app')
@section('title', __('Latest News'))
@section('meta')
    <meta name="description" content="{{ __('Latest news and updates from the portal.') }}">
@endsection

@section('content')
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 bg-indigo-700 text-white px-4 py-2 rounded z-50">{{ __('Skip to main content') }}</a>
    <h1 class="text-3xl tracking-tight font-extrabold text-gray-900 dark:text-white sm:text-4xl" id="main-content" tabindex="-1">{{ __('Latest News') }}</h1>
    <!-- Hero Section with Featured Articles -->
    <div class="relative bg-gray-50 dark:bg-gray-900 pt-16 pb-20 px-4 sm:px-6 lg:pt-24 lg:pb-28 lg:px-8 overflow-hidden">
        <!-- SVG Gradient Background -->
        <svg class="absolute inset-0 w-full h-full pointer-events-none" aria-hidden="true" focusable="false">
            <defs>
                <linearGradient id="hero-gradient" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="#6366f1" stop-opacity="0.15" />
                    <stop offset="100%" stop-color="#06b6d4" stop-opacity="0.10" />
                </linearGradient>
                <linearGradient id="hero-gradient-dark" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="#818cf8" stop-opacity="0.18" />
                    <stop offset="100%" stop-color="#0ea5e9" stop-opacity="0.12" />
                </linearGradient>
            </defs>
            <rect width="100%" height="100%" fill="url(#hero-gradient)" class="block dark:hidden" />
            <rect width="100%" height="100%" fill="url(#hero-gradient-dark)" class="hidden dark:block" />
        </svg>
        <div class="relative max-w-7xl mx-auto">
            <div class="text-center">
                <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 dark:text-gray-400 sm:mt-4">
                    {{ __('Stay informed with our latest news and updates') }}
                </p>
            </div>

            <!-- Featured Articles -->
            @if($featuredArticles->isNotEmpty())
            <div class="mt-12 max-w-lg mx-auto grid gap-5 lg:grid-cols-3 lg:max-w-none">
                @foreach($featuredArticles as $article)
                <div class="flex flex-col rounded-lg shadow-lg overflow-hidden transform transition-transform duration-200 hover:scale-105 focus-within:scale-105 hover:ring-2 focus-within:ring-2 ring-indigo-400 dark:ring-indigo-600">
                    <div class="flex-shrink-0">
                        <img class="h-48 w-full object-cover" src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" />
                    </div>
                    <div class="flex-1 bg-white dark:bg-gray-800 p-6 flex flex-col justify-between">
                        <div class="flex-1">
                            <div class="flex space-x-2">
                                @foreach($article->tags as $tag)
                                <a href="{{ route('tag.show', $tag) }}" class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-900 transition-shadow">
                                    {{ $tag->name }}
                                </a>
                                @endforeach
                            </div>
                            <a href="{{ route('article.show', $article) }}" class="block mt-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-900 transition-shadow">
                                <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ $article->title }}</p>
                                <p class="mt-3 text-base text-gray-500 dark:text-gray-400">{{ $article->excerpt }}</p>
                            </a>
                        </div>
                        <div class="mt-6 flex items-center">
                            <div class="flex space-x-1 text-sm text-gray-500 dark:text-gray-400">
                                <time datetime="{{ $article->published_at->format('Y-m-d') }}">
                                    {{ $article->published_at->format('M d, Y') }}
                                </time>
                                <span aria-hidden="true">&middot;</span>
                                <span>{{ $article->reading_time ?? 5 }} {{ __('min read') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Tag Filter -->
            <div class="mt-8">
                <div class="flex flex-wrap gap-2">
                    @foreach($tags as $tag)
                    <a href="{{ route('home', ['tag' => $tag->slug]) }}" 
                       class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-900 transition-shadow {{ request('tag') === $tag->slug ? 'bg-indigo-600 text-white' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200' }}">
                        {{ $tag->name }}
                        <span class="ml-1 text-xs">({{ $tag->articles_count }})</span>
                    </a>
                    @endforeach
                </div>
            </div>

            <!-- Articles Grid -->
            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($articles as $article)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow flex flex-col h-full transform transition-transform duration-200 hover:scale-105 focus-within:scale-105 hover:ring-2 focus-within:ring-2 ring-indigo-400 dark:ring-indigo-600">
                        <div class="p-6 flex-1 flex flex-col">
                            <h2 class="text-xl font-semibold mb-2 text-gray-900 dark:text-white">{{ $article->title }}</h2>
                            <p class="text-gray-600 dark:text-gray-300 flex-1">{{ $article->excerpt }}</p>
                        </div>
                        <div class="p-6 pt-0">
                            <a href="{{ route('news.show', $article->slug) }}" class="focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-900 transition-shadow">
                                <x-button variant="primary">{{ __('Read More') }}</x-button>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($articles->hasPages())
            <div class="mt-8" aria-live="polite">
                {{ $articles->links() }}
            </div>
            @endif
        </div>
    </div>
@endsection 
</div>
@endsection 