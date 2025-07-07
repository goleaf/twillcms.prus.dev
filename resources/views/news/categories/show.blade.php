@extends('layouts.app')

@section('title', $category->title . ' - News Sections')
@section('description', $category->description ?? 'Read the latest news articles in the ' . $category->title . ' section.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Category Header -->
    <div class="text-center mb-12">
        @if($category->image)
            <div class="w-24 h-24 mx-auto mb-4 rounded-full overflow-hidden">
                <img src="{{ asset('storage/' . $category->image) }}" 
                     alt="{{ $category->title }}"
                     class="w-full h-full object-cover">
            </div>
        @else
            <div class="inline-flex items-center justify-center w-24 h-24 bg-blue-100 rounded-full mb-4">
                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
            </div>
        @endif
        
        <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $category->title }}</h1>
        
        @if($category->description)
        <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-6">
            {{ $category->description }}
        </p>
        @endif
        
        <p class="text-lg text-gray-500 mb-6">
            {{ $posts->total() }} article{{ $posts->total() !== 1 ? 's' : '' }} in this section
        </p>
        
        <!-- Breadcrumb -->
        <nav class="flex justify-center">
            <ol class="flex items-center space-x-2 text-sm text-gray-500">
                <li><a href="{{ route('home') }}" class="hover:text-blue-600">Home</a></li>
                <li><span class="mx-2">/</span></li>
                <li><a href="{{ route('categories.index') }}" class="hover:text-blue-600">Sections</a></li>
                <li><span class="mx-2">/</span></li>
                <li class="text-gray-900 font-medium">{{ $category->title }}</li>
            </ol>
        </nav>
    </div>

    @if($posts->isNotEmpty())
        <!-- Articles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            @foreach($posts as $article)
            <article class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                <div class="aspect-w-16 aspect-h-9">
                    @if($article->featured_image)
                        <img src="{{ asset('storage/' . $article->featured_image) }}" 
                             alt="{{ $article->title }}"
                             class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    @endif
                </div>
                
                <div class="p-4">
                    <!-- Tags -->
                    <div class="flex items-center mb-2">
                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-semibold mr-2">
                            {{ $category->title }}
                        </span>
                        @foreach($article->tags->take(2) as $tag)
                            <a href="{{ route('tags.show', $tag->slug) }}" 
                               class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-2 py-1 rounded text-xs font-medium mr-1">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                    
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">
                        <a href="{{ route('news.show', $article->slug) }}" class="hover:text-blue-600">
                            {{ $article->title }}
                        </a>
                    </h2>
                    
                    <p class="text-gray-600 text-sm mb-3">
                        {{ \Str::limit($article->excerpt, 100) }}
                    </p>
                    
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <time datetime="{{ $article->created_at->toISOString() }}">
                            {{ $article->created_at->diffForHumans() }}
                        </time>
                        <div class="flex items-center space-x-2">
                            <span>{{ $article->view_count }} views</span>
                            <span>•</span>
                            <span>{{ $article->reading_time }} min read</span>
                        </div>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($posts->hasPages())
        <div class="flex justify-center">
            {{ $posts->links() }}
        </div>
        @endif

    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No articles found</h3>
            <p class="mt-1 text-sm text-gray-500">There are no articles in the "{{ $category->title }}" section yet.</p>
            <div class="mt-6 space-x-3">
                <a href="{{ route('categories.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Browse Sections
                </a>
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Back to Home
                </a>
            </div>
        </div>
    @endif

    <!-- Related Sections -->
    @php
        $relatedCategories = \App\Models\Category::where('is_active', true)
            ->where('id', '!=', $category->id)
            ->withCount(['posts' => function ($query) {
                $query->where('status', 'published');
            }])
            ->orderBy('posts_count', 'desc')
            ->take(3)
            ->get();
    @endphp

    @if($relatedCategories->isNotEmpty())
    <div class="mt-16 bg-gray-50 rounded-lg p-8">
        <h3 class="text-2xl font-semibold text-gray-900 mb-6 text-center">Explore Other Sections</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($relatedCategories as $relatedCategory)
            <a href="{{ route('categories.show', $relatedCategory->slug) }}" 
               class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 hover:shadow-md hover:border-blue-300 transition-all">
                <h4 class="text-lg font-semibold text-gray-900 mb-2">{{ $relatedCategory->title }}</h4>
                <p class="text-gray-600 text-sm mb-3">
                    {{ \Str::limit($relatedCategory->description, 80) }}
                </p>
                <div class="text-xs text-gray-500">
                    {{ $relatedCategory->posts_count }} articles
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection 