@extends('layouts.app')
@section('title', 'Latest News')
@section('meta')
    <meta name="description" content="Latest news and updates from the portal.">
@endsection

@section('content')
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-blue-600 text-white px-4 py-2 rounded z-50">Skip to main content</a>
    <h1 class="text-3xl tracking-tight font-extrabold text-gray-900 dark:text-white sm:text-4xl" id="main-content" tabindex="-1">Latest News</h1>

    <!-- Filter Controls -->
    <div class="mb-8 flex flex-wrap gap-4 items-center">
        <!-- Search Form -->
        <form method="GET" action="{{ route('news.index') }}" class="flex-grow max-w-md">
            <div class="relative">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Search news..." 
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </form>

        <!-- Tag Filter -->
        <form method="GET" action="{{ route('news.index') }}" class="flex gap-2">
            <select name="tag" onchange="this.form.submit()" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                <option value="">All Topics</option>
                @foreach($tags as $tag)
                    <option value="{{ $tag->slug }}" {{ request('tag') === $tag->slug ? 'selected' : '' }}>
                        {{ $tag->name }}
                    </option>
                @endforeach
            </select>
            <input type="hidden" name="search" value="{{ request('search') }}">
        </form>

        <!-- Featured Filter -->
        <a href="{{ route('news.index', array_merge(request()->all(), ['featured' => request('featured') ? null : '1'])) }}" 
           class="px-4 py-2 rounded-lg border {{ request('featured') ? 'bg-blue-600 text-white border-blue-600' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600' }} transition-colors">
            {{ request('featured') ? 'Show All' : 'Featured Only' }}
        </a>
    </div>

    <!-- Hero Section with Featured Article -->
    @if($featured && !request('search') && !request('tag'))
    <div class="mb-12 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl overflow-hidden shadow-xl">
        <div class="md:flex">
            <div class="md:w-1/2">
                @if($featured->featured_image)
                    <img src="{{ $featured->featured_image }}" alt="{{ $featured->title }}" class="w-full h-64 md:h-full object-cover">
                @else
                    <div class="w-full h-64 md:h-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                        <svg class="w-20 h-20 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                    </div>
                @endif
            </div>
            <div class="md:w-1/2 p-8 text-white">
                <span class="inline-block bg-white bg-opacity-20 rounded-full px-3 py-1 text-sm font-semibold mb-4">Featured Story</span>
                <h2 class="text-3xl font-bold mb-4">{{ $featured->title }}</h2>
                <p class="text-lg mb-6 line-clamp-3">{{ $featured->excerpt }}</p>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4 text-sm">
                        <span>{{ $featured->published_at ? $featured->published_at->format('M j, Y') : 'N/A' }}</span>
                        <span>{{ $featured->reading_time ?? 5 }} min read</span>
                    </div>
                    <a href="{{ route('news.show', $featured->slug) }}" class="inline-flex items-center px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-gray-100 transition-colors font-semibold">
                        Read More
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Articles Grid -->
    @if($articles->count() > 0)
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            @foreach($articles as $article)
                <article class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden group">
                    <div class="aspect-w-16 aspect-h-9 bg-gray-200 dark:bg-gray-700">
                        @if($article->featured_image)
                            <img src="{{ $article->featured_image }}" alt="{{ $article->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-48 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
                                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <!-- Tags -->
                        @if($article->tags->count() > 0)
                            <div class="flex flex-wrap gap-2 mb-3">
                                @foreach($article->tags->take(2) as $tag)
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }};">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                            <a href="{{ route('news.show', $article->slug) }}">{{ $article->title }}</a>
                        </h2>
                        
                        <p class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-3">{{ $article->excerpt }}</p>
                        
                        <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
                            <div class="flex items-center space-x-4">
                                <span>{{ $article->published_at ? $article->published_at->format('M j, Y') : 'N/A' }}</span>
                                <span>{{ $article->reading_time ?? 5 }} min read</span>
                            </div>
                            @if($article->is_featured)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    Featured
                                </span>
                            @endif
                        </div>
                        
                        <x-button variant="primary">Read More</x-button>
                    </div>
                </article>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($articles->hasPages())
            <div class="mt-12">
                {{ $articles->links() }}
            </div>
        @endif
    @else
        <!-- No Articles Message -->
        <div class="text-center py-12">
            <div class="max-w-md mx-auto">
                <svg class="mx-auto h-24 w-24 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No articles found</h3>
                <p class="mt-2 text-gray-500 dark:text-gray-400">
                    @if(request('search'))
                        No articles match your search for "{{ request('search') }}".
                    @elseif(request('tag'))
                        No articles found with the selected topic.
                    @else
                        No articles have been published yet.
                    @endif
                </p>
                @if(request('search') || request('tag') || request('featured'))
                    <a href="{{ route('news.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Clear Filters
                    </a>
                @endif
            </div>
        </div>
    @endif
@endsection 