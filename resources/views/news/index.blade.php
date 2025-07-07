@extends('layouts.app')

@section('title', 'News Portal - Latest Breaking News & Updates')
@section('description', 'Stay informed with the latest breaking news, trending stories, and in-depth analysis from around the world. Your trusted source for real-time news updates.')

@section('content')
<!-- Breaking News Banner -->
@if($featuredNews->isNotEmpty())
<div class="bg-red-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
        <div class="flex items-center">
            <span class="bg-white text-red-600 px-3 py-1 rounded text-sm font-bold mr-4">BREAKING</span>
            <div class="ticker overflow-hidden whitespace-nowrap">
                <span class="inline-block animate-scroll">
                    @foreach($featuredNews->take(1) as $featured)
                        {{ $featured->title }}
                    @endforeach
                </span>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Featured News Section -->
@if($featuredNews->isNotEmpty())
<section class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-8">Featured News</h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Main Featured Article -->
            @if($featuredNews->first())
            <article class="lg:col-span-1">
                <div class="relative">
                    @if($featuredNews->first()->featured_image)
                        <img src="{{ $featuredNews->first()->featured_image }}" 
                             alt="{{ $featuredNews->first()->title }}"
                             class="w-full h-64 object-cover rounded-lg">
                    @else
                        <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    @endif
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-6 rounded-b-lg">
                        <div class="flex items-center mb-2">
                            @foreach($featuredNews->first()->tags->take(2) as $tag)
                                <span class="bg-red-600 text-white px-2 py-1 rounded text-xs font-semibold mr-2">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">
                            <a href="{{ route('news.show', $featuredNews->first()->slug) }}" class="hover:underline">
                                {{ $featuredNews->first()->title }}
                            </a>
                        </h3>
                        <p class="text-gray-200 text-sm">
                            {{ \Str::limit($featuredNews->first()->excerpt, 120) }}
                        </p>
                        <div class="flex items-center mt-3 text-gray-300 text-xs">
                            <time datetime="{{ $featuredNews->first()->created_at->toISOString() }}">
                                {{ $featuredNews->first()->created_at->diffForHumans() }}
                            </time>
                            <span class="mx-2">•</span>
                            <span>{{ $featuredNews->first()->view_count }} views</span>
                        </div>
                    </div>
                </div>
            </article>
            @endif

            <!-- Secondary Featured Articles -->
            <div class="space-y-6">
                @foreach($featuredNews->skip(1)->take(2) as $featured)
                <article class="flex space-x-4">
                    <div class="flex-shrink-0">
                        @if($featured->featured_image)
                            <img src="{{ $featured->featured_image }}" 
                                 alt="{{ $featured->title }}"
                                 class="w-24 h-18 object-cover rounded">
                        @else
                            <div class="w-24 h-18 bg-gray-200 rounded flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center mb-1">
                            @foreach($featured->tags->take(1) as $tag)
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">
                            <a href="{{ route('news.show', $featured->slug) }}" class="hover:text-red-600">
                                {{ $featured->title }}
                            </a>
                        </h3>
                        <p class="text-gray-600 text-sm mb-2">
                            {{ \Str::limit($featured->excerpt, 80) }}
                        </p>
                        <div class="flex items-center text-gray-500 text-xs">
                            <time datetime="{{ $featured->created_at->toISOString() }}">
                                {{ $featured->created_at->diffForHumans() }}
                            </time>
                            <span class="mx-2">•</span>
                            <span>{{ $featured->view_count }} views</span>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

<!-- Main Content Area -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Latest News Feed -->
        <div class="lg:col-span-2">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Latest News</h2>
                <a href="{{ route('news.index') }}" class="text-red-600 hover:text-red-700 font-medium">View All →</a>
            </div>

            <div class="space-y-6">
                @forelse($latestNews as $article)
                <article class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            @if($article->featured_image)
                                <img src="{{ $article->featured_image }}" 
                                     alt="{{ $article->title }}"
                                     class="w-32 h-24 object-cover">
                            @else
                                <div class="w-32 h-24 bg-gray-200 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 p-4">
                            <div class="flex items-center mb-2">
                                @foreach($article->tags->take(2) as $tag)
                                    <a href="{{ route('tags.show', $tag->slug) }}" 
                                       class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-2 py-1 rounded text-xs font-medium mr-2">
                                        {{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                <a href="{{ route('news.show', $article->slug) }}" class="hover:text-red-600">
                                    {{ $article->title }}
                                </a>
                            </h3>
                            <p class="text-gray-600 text-sm mb-3">
                                {{ \Str::limit($article->excerpt, 100) }}
                            </p>
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <time datetime="{{ $article->created_at->toISOString() }}">
                                    {{ $article->created_at->diffForHumans() }}
                                </time>
                                <div class="flex items-center space-x-3">
                                    <span>{{ $article->view_count }} views</span>
                                    <span>{{ $article->reading_time }} min read</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
                @empty
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No news available</h3>
                    <p class="mt-1 text-sm text-gray-500">Check back later for the latest updates.</p>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($latestNews->hasPages())
            <div class="mt-8">
                {{ $latestNews->links() }}
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Popular Tags -->
            @if($popularTags->isNotEmpty())
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Popular Topics</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($popularTags as $tag)
                        <a href="{{ route('tags.show', $tag->slug) }}" 
                           class="bg-gray-100 hover:bg-red-100 hover:text-red-700 text-gray-700 px-3 py-1 rounded-full text-sm font-medium transition-colors">
                            {{ $tag->name }}
                            <span class="text-xs text-gray-500 ml-1">({{ $tag->posts_count }})</span>
                        </a>
                    @endforeach
                </div>
                <div class="mt-4">
                    <a href="{{ route('tags.index') }}" class="text-red-600 hover:text-red-700 text-sm font-medium">
                        View All Topics →
                    </a>
                </div>
            </div>
            @endif

            <!-- News Categories -->
            @if($categories->isNotEmpty())
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">News Sections</h3>
                <ul class="space-y-2">
                    @foreach($categories as $category)
                        <li>
                            <a href="{{ route('categories.show', $category->slug) }}" 
                               class="flex items-center justify-between text-gray-700 hover:text-red-600 py-1">
                                <span>{{ $category->title }}</span>
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                    {{ $category->posts_count }}
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-4">
                    <a href="{{ route('categories.index') }}" class="text-red-600 hover:text-red-700 text-sm font-medium">
                        View All Sections →
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection 