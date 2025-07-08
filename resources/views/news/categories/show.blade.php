@extends('layouts.app')

@section('title', $category->title . ' - News Sections')
@section('description', $category->description ?? 'Read the latest news articles in the ' . $category->title . ' section.')

@section('content')
    <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow p-8 mt-8 mb-8">
        <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white mb-4">{{ $category->title }}</h1>
        <p class="text-gray-600 dark:text-gray-300">{{ $category->description }}</p>
    </div>
    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">{{ __('Articles in this Section') }}</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($posts as $article)
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
@endsection 