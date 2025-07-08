@extends('layouts.app')

@section('title', $tag->name . ' - Topics')
@section('description', 'Read the latest news articles about ' . $tag->name . '. Stay updated with stories and developments in this topic.')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-900">
    <!-- Tag Header -->
    <div class="py-16 px-4 sm:px-6 lg:px-8" style="background: linear-gradient(135deg, {{ $tag->color }}15 0%, {{ $tag->color }}05 100%);">
        <div class="max-w-4xl mx-auto text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-6" style="background-color: {{ $tag->color }}20;">
                <svg class="w-8 h-8" style="color: {{ $tag->color }};" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">{{ $tag->name }}</h1>
            @if($tag->description)
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto mb-6">{{ $tag->description }}</p>
            @endif
            <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }};">
                {{ $articles->total() }} articles in this topic
            </div>
        </div>
    </div>

    <!-- Articles Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Articles with this Tag</h2>
        
        @if($articles->count() > 0)
            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach($articles as $article)
                    <article class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                        @if($article->featured_image)
                            <img src="{{ $article->featured_image }}" alt="{{ $article->title }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                            </div>
                        @endif
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3 line-clamp-2">
                                <a href="{{ route('news.show', $article->slug) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                    {{ $article->title }}
                                </a>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-3">{{ $article->excerpt }}</p>
                            <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
                                <span>{{ $article->published_at ? $article->published_at->format('M j, Y') : 'N/A' }}</span>
                                <span>{{ $article->reading_time ?? 5 }} min read</span>
                            </div>
                            <a href="{{ route('news.show', $article->slug) }}">
                                <x-button variant="primary">Read More</x-button>
                            </a>
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
                    <p class="mt-2 text-gray-500 dark:text-gray-400">There are no articles tagged with "{{ $tag->name }}" yet.</p>
                    <a href="{{ route('news.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Browse All News
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Related Tags -->
@if($relatedTags->count() > 0)
<div class="bg-gray-50 dark:bg-gray-800 py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                Related Topics
            </h2>
            <p class="mt-2 text-lg leading-8 text-gray-600 dark:text-gray-300">
                Explore similar topics that might interest you
            </p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 justify-center">
            @foreach($relatedTags as $tag)
            <a href="{{ route('tags.show', $tag->slug) }}" class="group flex flex-col items-center rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <div class="w-4 h-4 rounded-full mb-2 border-2 border-white dark:border-gray-900" style="background-color: {{ $tag->color }}"></div>
                <span class="text-base font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400">{{ $tag->name }}</span>
                <span class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $tag->usage_count }} {{ Str::plural('article', $tag->usage_count) }}</span>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Popular Tags -->
@if($popularTags->count() > 0)
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-12">
        <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
            Popular Topics
        </h2>
        <p class="mt-2 text-lg leading-8 text-gray-600 dark:text-gray-300">
            Discover what's trending now
        </p>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 justify-center">
        @foreach($popularTags as $popularTag)
        <a href="{{ $popularTag->id === $tag->id ? '#' : route('tags.show', $popularTag->slug) }}" class="group flex flex-col items-center rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $popularTag->id === $tag->id ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
            <div class="w-4 h-4 rounded-full mb-2 border-2 border-white dark:border-gray-900" style="background-color: {{ $popularTag->color }}"></div>
            <span class="text-base font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400">{{ $popularTag->name }}</span>
            <span class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $popularTag->usage_count }} {{ Str::plural('article', $popularTag->usage_count) }}</span>
        </a>
        @endforeach
    </div>
    <div class="mt-8 text-center">
        <a href="{{ route('tags.index') }}" class="text-sm font-semibold leading-6 text-indigo-600 hover:text-indigo-500">
            View all topics <span aria-hidden="true">→</span>
        </a>
    </div>
</div>
@endif
@endsection 