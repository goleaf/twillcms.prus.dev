@extends('layouts.app')

@section('title', 'Search Results for "' . $query . '" - News Portal')
@section('description', 'Search results for "' . $query . '". Find relevant news articles, stories, and updates matching your search query.')

@section('content')
<div class="bg-white dark:bg-gray-900">
    <!-- Search Header -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center">
            <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-5xl">
                Search Results
            </h1>
            @if($query)
            <p class="mt-4 text-lg leading-8 text-gray-600 dark:text-gray-300">
                Results for: <span class="font-semibold">"{{ $query }}"</span>
            </p>
            @endif
        </div>

        <!-- Search Form -->
        <div class="mt-8 mx-auto max-w-md">
            <form action="{{ route('search') }}" method="GET" class="relative">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ $query ?? '' }}"
                        placeholder="Search articles..." 
                        class="block w-full rounded-md border-0 py-2 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 dark:bg-gray-800 dark:text-white dark:ring-gray-600 dark:placeholder:text-gray-400"
                    >
                </div>
            </form>
        </div>
    </div>

    <!-- Search Results -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
        @if($articles && $articles->count() > 0)
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Search Results
            </h2>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                {{ pagination_count($articles, 'result') }}
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 xl:grid-cols-3">
            @foreach($articles as $article)
            <article class="group relative flex flex-col overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm hover:shadow-lg transition-shadow duration-200">
                <div class="aspect-[16/9] overflow-hidden">
                    <img 
                        src="{{ $article->image ? asset('storage/' . $article->image) : 'https://picsum.photos/800/600?random=' . $article->id }}" 
                        alt="{{ $article->title }}"
                        class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-200"
                        loading="lazy"
                    >
                </div>
                
                <div class="flex-1 p-6">
                    <div class="flex items-center gap-x-4 text-xs mb-3">
                        <time datetime="{{ $article->published_at->format('Y-m-d') }}" class="text-gray-500 dark:text-gray-400">
                            {{ $article->published_at->format('M j, Y') }}
                        </time>
                        <div class="flex items-center">
                            <svg class="h-4 w-4 mr-1 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-gray-500 dark:text-gray-400">{{ number_format($article->view_count) }}</span>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 mb-3">
                        <a href="{{ route('news.show', $article->slug) }}">
                            <span class="absolute inset-0"></span>
                            {{ $article->title }}
                        </a>
                    </h3>
                    
                    @if($article->excerpt)
                    <p class="text-sm leading-6 text-gray-600 dark:text-gray-300 mb-4">
                        {{ Str::limit($article->excerpt, 120) }}
                    </p>
                    @endif
                    
                    <!-- Tags -->
                    @if($article->tags->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($article->tags->take(3) as $tag)
                        <a href="{{ route('tags.show', $tag->slug) }}" 
                           class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium transition-all duration-200 hover:scale-105"
                           style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}40;">
                            {{ $tag->name }}
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>
            </article>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $articles->appends(['q' => $query])->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No articles found</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                @if($query)
                    Try adjusting your search terms or browse our latest articles.
                @else
                    Enter a search term to find articles.
                @endif
            </p>
            <div class="mt-6">
                <a href="{{ route('home') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Browse latest articles
                </a>
            </div>
        </div>
        @endif
    </div>

    <!-- Popular Tags -->
    @if(isset($popularTags) && $popularTags->count() > 0)
    <div class="bg-gray-50 dark:bg-gray-800 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    Popular Topics
                </h2>
                <p class="mt-2 text-lg leading-8 text-gray-600 dark:text-gray-300">
                    Discover trending topics
                </p>
            </div>
            
            <div class="flex flex-wrap justify-center gap-4">
                @foreach($popularTags as $tag)
                <a href="{{ route('tags.show', $tag->slug) }}" 
                   class="inline-flex items-center rounded-full px-4 py-2 text-sm font-medium transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                   style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}40;">
                    {{ $tag->name }}
                    <span class="ml-2 inline-flex items-center rounded-full bg-white/20 px-2 py-0.5 text-xs font-medium">
                        {{ $tag->usage_count }}
                    </span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection 