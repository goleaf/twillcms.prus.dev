@extends('layouts.app')

@section('title', 'News Portal - Latest Breaking News & Updates')
@section('description', 'Stay informed with the latest breaking news, trending stories, and in-depth analysis from around the world. Your trusted source for real-time news updates.')

@section('content')
<!-- Hero Section with Featured Articles -->
<div class="relative bg-gray-50 dark:bg-gray-900 pt-16 pb-20 px-4 sm:px-6 lg:pt-24 lg:pb-28 lg:px-8">
    <div class="relative max-w-7xl mx-auto">
        <div class="text-center">
            <h1 class="text-3xl tracking-tight font-extrabold text-gray-900 dark:text-white sm:text-4xl">Latest News</h1>
            <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 dark:text-gray-400 sm:mt-4">
                Stay informed with our latest news and updates
            </p>
        </div>

        <!-- Featured Articles -->
        @if($featuredArticles->isNotEmpty())
        <div class="mt-12 max-w-lg mx-auto grid gap-5 lg:grid-cols-3 lg:max-w-none">
            @foreach($featuredArticles as $article)
            <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
                <div class="flex-shrink-0">
                    <img class="h-48 w-full object-cover" src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}">
                </div>
                <div class="flex-1 bg-white dark:bg-gray-800 p-6 flex flex-col justify-between">
                    <div class="flex-1">
                        <div class="flex space-x-2">
                            @foreach($article->tags as $tag)
                            <a href="{{ route('tag.show', $tag) }}" class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                {{ $tag->name }}
                            </a>
                            @endforeach
                        </div>
                        <a href="{{ route('article.show', $article) }}" class="block mt-2">
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
                            <span>{{ $article->reading_time ?? 5 }} min read</span>
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
                   class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium {{ request('tag') === $tag->slug ? 'bg-indigo-600 text-white' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200' }}">
                    {{ $tag->name }}
                    <span class="ml-1 text-xs">({{ $tag->articles_count }})</span>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Articles Grid -->
        <div class="mt-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($articles as $article)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <div class="flex-shrink-0">
                    <img class="h-48 w-full object-cover" src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}">
                </div>
                <div class="p-6">
                    <div class="flex space-x-2 mb-2">
                        @foreach($article->tags as $tag)
                        <a href="{{ route('tag.show', $tag) }}" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                            {{ $tag->name }}
                        </a>
                        @endforeach
                    </div>
                    <a href="{{ route('article.show', $article) }}" class="block">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $article->title }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ Str::limit($article->excerpt, 100) }}</p>
                    </a>
                    <div class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <time datetime="{{ $article->published_at->format('Y-m-d') }}">
                            {{ $article->published_at->format('M d, Y') }}
                        </time>
                        <span class="mx-2">&middot;</span>
                        <span>{{ number_format($article->view_count) }} views</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($articles->hasPages())
        <div class="mt-8">
            {{ $articles->links() }}
        </div>
        @endif
    </div>
</div>
@endsection 