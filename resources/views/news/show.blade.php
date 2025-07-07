@extends('layouts.app')

@section('title', $article->title . ' - News Portal')
@section('description', $article->excerpt)
@section('og_type', 'article')
@section('og_image', $article->featured_image ?? asset('images/news-portal-logo.jpg'))

@section('content')
<article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Article Header -->
    <header class="mb-8">
        <!-- Tags -->
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach($article->tags as $tag)
                <a href="{{ route('tags.show', $tag->slug) }}" 
                   class="bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1 rounded-full text-sm font-medium transition-colors">
                    {{ $tag->name }}
                </a>
            @endforeach
        </div>

        <!-- Title -->
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight mb-4">
            {{ $article->title }}
        </h1>

        <!-- Excerpt -->
        @if($article->excerpt)
        <p class="text-xl text-gray-600 leading-relaxed mb-6">
            {{ $article->excerpt }}
        </p>
        @endif

        <!-- Article Meta -->
        <div class="flex items-center justify-between py-4 border-t border-b border-gray-200">
            <div class="flex items-center space-x-4 text-sm text-gray-500">
                <time datetime="{{ $article->created_at->toISOString() }}" class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $article->created_at->format('M j, Y \a\t g:i A') }}
                </time>
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    {{ $article->view_count }} views
                </span>
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    {{ $article->reading_time }} min read
                </span>
            </div>

            <!-- Social Sharing -->
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500 mr-2">Share:</span>
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($article->title) }}&url={{ urlencode(url()->current()) }}" 
                   target="_blank" 
                   class="text-blue-400 hover:text-blue-600 p-2 rounded-full hover:bg-blue-50 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                    </svg>
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
                   target="_blank" 
                   class="text-blue-600 hover:text-blue-800 p-2 rounded-full hover:bg-blue-50 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                    </svg>
                </a>
                <button onclick="copyToClipboard()" 
                        class="text-gray-600 hover:text-gray-800 p-2 rounded-full hover:bg-gray-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Featured Image -->
    @if($article->featured_image)
    <div class="mb-8">
        <img src="{{ $article->featured_image }}" 
             alt="{{ $article->title }}"
             class="w-full h-64 md:h-96 object-cover rounded-lg shadow-lg">
        @if($article->featured_image_caption)
        <p class="text-sm text-gray-500 mt-2 text-center italic">
            {{ $article->featured_image_caption }}
        </p>
        @endif
    </div>
    @endif

    <!-- Article Content -->
    <div class="prose prose-lg max-w-none mb-12">
        {!! $article->content !!}
    </div>

    <!-- Article Footer -->
    <footer class="border-t border-gray-200 pt-8">
        <!-- Tags -->
        @if($article->tags->isNotEmpty())
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Related Topics</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($article->tags as $tag)
                    <a href="{{ route('tags.show', $tag->slug) }}" 
                       class="bg-gray-100 hover:bg-red-100 hover:text-red-700 text-gray-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Categories -->
        @if($article->categories->isNotEmpty())
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">News Sections</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($article->categories as $category)
                    <a href="{{ route('categories.show', $category->slug) }}" 
                       class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        {{ $category->title }}
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </footer>
</article>

<!-- Related Articles -->
@if($relatedArticles->isNotEmpty())
<section class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">Related Articles</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($relatedArticles as $related)
            <article class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                <div class="aspect-w-16 aspect-h-9">
                    @if($related->featured_image)
                        <img src="{{ $related->featured_image }}" 
                             alt="{{ $related->title }}"
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
                    <div class="flex items-center mb-2">
                        @foreach($related->tags->take(1) as $tag)
                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-medium">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                    
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        <a href="{{ route('news.show', $related->slug) }}" class="hover:text-red-600">
                            {{ $related->title }}
                        </a>
                    </h3>
                    
                    <p class="text-gray-600 text-sm mb-3">
                        {{ \Str::limit($related->excerpt, 80) }}
                    </p>
                    
                    <div class="flex items-center text-xs text-gray-500">
                        <time datetime="{{ $related->created_at->toISOString() }}">
                            {{ $related->created_at->diffForHumans() }}
                        </time>
                        <span class="mx-2">•</span>
                        <span>{{ $related->reading_time }} min read</span>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

<script>
function copyToClipboard() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        // You could add a toast notification here
        alert('Link copied to clipboard!');
    });
}
</script>
@endsection 