@extends('layouts.app')

@section('title', __('News Sections'))
@section('description', 'Browse news by sections and categories. Find articles organized by topics like Technology, Business, Sports, and more.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">{{ __('News Sections') }}</h1>
        <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
            {{ __('Browse news articles organized by sections. Each section contains curated content from specific areas of interest.') }}
        </p>
    </div>
    @if($categories->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($categories as $category)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow hover:shadow-lg transition-shadow flex flex-col h-full border border-gray-100 dark:border-gray-700">
                    <div class="p-6 flex-1 flex flex-col">
                        <h2 class="text-2xl font-semibold mb-2 text-gray-900 dark:text-white">{{ $category->title }}</h2>
                        <p class="text-gray-600 dark:text-gray-300 flex-1 mb-4">{{ $category->description }}</p>
                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mt-auto">
                            <span class="font-semibold text-blue-600 dark:text-blue-400 mr-2">{{ $category->posts_count }}</span>
                            <span>{{ Str::plural('article', $category->posts_count) }}</span>
                        </div>
                    </div>
                    <div class="p-6 pt-0">
                        <a href="{{ route('categories.show', $category->slug) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ __('View Section') }}
                        </a>
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
        <div class="text-center py-16">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">{{ __('No categories available') }}</h3>
            <p class="mt-1 text-base text-gray-500 dark:text-gray-400">{{ __('Check back later as we add more content sections.') }}</p>
            <div class="mt-6">
                <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    {{ __('Back to Home') }}
                </a>
            </div>
        </div>
    @endif
</div>
@endsection 