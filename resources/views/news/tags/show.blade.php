@extends('layouts.app')

@section('title', $tag->name . ' - News Topics')
@section('description', 'Read the latest news articles about ' . $tag->name . '. Stay updated with stories and developments in this topic.')

@section('content')
    <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow p-8 mt-8 mb-8">
        <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white mb-4">{{ $tag->name }}</h1>
        <p class="text-gray-600 dark:text-gray-300">{{ $tag->description }}</p>
    </div>
    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">{{ __('Articles with this Tag') }}</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($articles as $article)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow flex flex-col h-full">
                <div class="p-6 flex-1 flex flex-col">
                    <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">{{ $article->title }}</h3>
                    <p class="text-gray-600 dark:text-gray-300 flex-1">{{ $article->excerpt }}</p>
                </div>
                <div class="p-6 pt-0">
                    <a href="{{ route('news.show', $article->slug) }}">
                        <x-button variant="primary">{{ __('Read More') }}</x-button>
                    </a>
                </div>
            </div>
        @endforeach
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