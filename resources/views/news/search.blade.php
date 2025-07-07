@extends('layouts.app')

@section('title', 'Search Results for "' . $query . '" - News Portal')
@section('description', 'Search results for "' . $query . '". Find relevant news articles, stories, and updates matching your search query.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Search Header -->
    <div class="mb-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Search Results</h1>
            
            <!-- Search Form -->
            <form action="{{ route('search') }}" method="GET" class="max-w-2xl mx-auto">
                <div class="flex">
                    <input type="search" 
                           name="q" 
                           value="{{ $query }}"
                           placeholder="Search news articles..." 
                           class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 text-lg"
                           autofocus>
                    <button type="submit" 
                            class="px-6 py-3 bg-red-600 text-white rounded-r-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Results Info -->
        <div class="flex items-center justify-between border-b border-gray-200 pb-4">
            <div>
                <p class="text-lg text-gray-900">
                    @if($posts->total() > 0)
                        Found <span class="font-semibold">{{ number_format($posts->total()) }}</span> result{{ $posts->total() !== 1 ? 's' : '' }} for 
                        <span class="text-red-600 font-semibold">"{{ $query }}"</span>
                    @else
                        No results found for <span class="text-red-600 font-semibold">"{{ $query }}"</span>
                    @endif
                </p>
                @if($posts->hasPages())
                <p class="text-sm text-gray-500 mt-1">
                    Showing {{ $posts->firstItem() }}-{{ $posts->lastItem() }} of {{ $posts->total() }} results
                </p>
                @endif
            </div>
            
            @if($posts->total() > 0)
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <span>Sort by:</span>
                <select onchange="sortResults(this.value)" class="border border-gray-300 rounded px-2 py-1 text-sm">
                    <option value="relevance">Relevance</option>
                    <option value="date">Date</option>
                    <option value="views">Most Viewed</option>
                </select>
            </div>
            @endif
        </div>
    </div>

    @if($posts->isNotEmpty())
        <!-- Search Results -->
        <div class="space-y-6 mb-12">
            @foreach($posts as $article)
            <article class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                <div class="md:flex">
                    <div class="md:flex-shrink-0">
                        @if($article->featured_image)
                            <img src="{{ $article->featured_image }}" 
                                 alt="{{ $article->title }}"
                                 class="h-48 w-full object-cover md:h-full md:w-48">
                        @else
                            <div class="h-48 w-full md:h-full md:w-48 bg-gray-200 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-6 flex-1">
                        <div class="flex items-center mb-3">
                            @foreach($article->tags->take(3) as $tag)
                                <a href="{{ route('tags.show', $tag->slug) }}" 
                                   class="bg-gray-100 text-gray-700 hover:bg-red-100 hover:text-red-700 px-2 py-1 rounded text-xs font-medium mr-2 transition-colors">
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                            @foreach($article->categories->take(1) as $category)
                                <a href="{{ route('categories.show', $category->slug) }}" 
                                   class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-2 py-1 rounded text-xs font-medium mr-2 transition-colors">
                                    {{ $category->title }}
                                </a>
                            @endforeach
                        </div>
                        
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">
                            <a href="{{ route('news.show', $article->slug) }}" class="hover:text-red-600">
                                {!! preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark class="bg-yellow-200 px-1 rounded">$1</mark>', $article->title) !!}
                            </a>
                        </h2>
                        
                        <p class="text-gray-600 mb-4">
                            {!! preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark class="bg-yellow-200 px-1 rounded">$1</mark>', \Str::limit($article->excerpt, 150)) !!}
                        </p>
                        
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <div class="flex items-center space-x-4">
                                <time datetime="{{ $article->created_at->toISOString() }}">
                                    {{ $article->created_at->format('M j, Y') }}
                                </time>
                                <span>{{ $article->view_count }} views</span>
                                <span>{{ $article->reading_time }} min read</span>
                            </div>
                            <a href="{{ route('news.show', $article->slug) }}" 
                               class="text-red-600 hover:text-red-700 font-medium">
                                Read Article →
                            </a>
                        </div>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($posts->hasPages())
        <div class="flex justify-center">
            {{ $posts->appends(['q' => $query])->links() }}
        </div>
        @endif

    @else
        <!-- No Results -->
        <div class="text-center py-12">
            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-xl font-medium text-gray-900 mb-2">No articles found</h3>
            <p class="text-gray-500 mb-6 max-w-md mx-auto">
                We couldn't find any articles matching your search. Try different keywords or browse our topics.
            </p>
            
            <!-- Search Suggestions -->
            <div class="bg-gray-50 rounded-lg p-6 max-w-2xl mx-auto mb-8">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Search Suggestions</h4>
                <ul class="text-sm text-gray-600 space-y-2">
                    <li>• Try using different or more general keywords</li>
                    <li>• Check your spelling</li>
                    <li>• Use fewer keywords</li>
                    <li>• Try searching for related topics</li>
                </ul>
            </div>

            <!-- Popular Topics -->
            @php
                $popularTags = \App\Models\Tag::withCount(['posts' => function ($query) {
                    $query->where('status', 'published');
                }])
                ->orderBy('posts_count', 'desc')
                ->take(12)
                ->get();
            @endphp

            @if($popularTags->isNotEmpty())
            <div class="max-w-2xl mx-auto">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Or browse popular topics:</h4>
                <div class="flex flex-wrap gap-2 justify-center">
                    @foreach($popularTags as $tag)
                        <a href="{{ route('tags.show', $tag->slug) }}" 
                           class="bg-white border border-gray-300 hover:border-red-300 hover:bg-red-50 text-gray-700 hover:text-red-700 px-4 py-2 rounded-full text-sm font-medium transition-colors">
                            {{ $tag->name }}
                            <span class="text-xs text-gray-500 ml-1">({{ $tag->posts_count }})</span>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    @endif
</div>

<script>
function sortResults(sortBy) {
    const url = new URL(window.location);
    url.searchParams.set('sort', sortBy);
    window.location.href = url.toString();
}
</script>
@endsection 