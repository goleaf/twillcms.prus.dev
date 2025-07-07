@extends('layouts.app')

@section('title', 'News Sections - News Portal')
@section('description', 'Browse news by sections and categories. Find articles organized by topics like Technology, Business, Sports, and more.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">News Sections</h1>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto">
            Browse news articles organized by sections. Each section contains curated content from specific areas of interest.
        </p>
    </div>

    @if($categories->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($categories as $category)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                @if($category->image)
                    <img src="{{ asset('storage/' . $category->image) }}" 
                         alt="{{ $category->title }}"
                         class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                    </div>
                @endif
                
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">
                        <a href="{{ route('categories.show', $category->slug) }}" class="hover:text-blue-600">
                            {{ $category->title }}
                        </a>
                    </h2>
                    
                    @if($category->description)
                    <p class="text-gray-600 mb-4">
                        {{ \Str::limit($category->description, 100) }}
                    </p>
                    @endif
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                            {{ $category->posts_count }} article{{ $category->posts_count !== 1 ? 's' : '' }}
                        </div>
                        
                        <a href="{{ route('categories.show', $category->slug) }}" 
                           class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                            View Articles
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Categories Stats -->
        <div class="mt-12 bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            <div class="text-center">
                <h3 class="text-2xl font-semibold text-gray-900 mb-4">Explore by Category</h3>
                <p class="text-gray-600 mb-6">
                    We have {{ $categories->count() }} active news sections covering 
                    {{ $categories->sum('posts_count') }} total articles across various topics.
                </p>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($categories->take(4) as $category)
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $category->posts_count }}</div>
                        <div class="text-sm text-gray-500">{{ $category->title }}</div>
                    </div>
                    @endforeach
                </div>
                
                <div class="mt-6">
                    <a href="{{ route('home') }}" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No categories available</h3>
            <p class="mt-1 text-sm text-gray-500">Check back later as we add more content sections.</p>
            <div class="mt-6">
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Back to Home
                </a>
            </div>
        </div>
    @endif
</div>
@endsection 