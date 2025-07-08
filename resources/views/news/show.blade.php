@extends('layouts.app')
@section('title', $article->title)
@section('meta')
    <meta name="description" content="{{ $article->excerpt ?? 'Read the full article on our news portal.' }}">
@endsection

@section('content')
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-blue-600 text-white px-4 py-2 rounded z-50">Skip to main content</a>
    <main id="main-content" tabindex="-1" aria-label="Article Content">
        <article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Article metadata -->
            <div class="mb-6 text-sm text-gray-500 dark:text-gray-400">
                <span class="font-semibold">Published At:</span> {{ $article->published_at ? $article->published_at->format('Y-m-d H:i') : 'N/A' }}
                @if($article->author)
                    <span class="ml-4"><span class="font-semibold">Author:</span> {{ $article->author->name }}</span>
                @endif
                @if($article->tags->count() > 0)
                    <div class="mt-2">
                        <span class="font-semibold">Tags:</span>
                        @foreach($article->tags as $tag)
                            <a href="{{ route('tags.show', $tag->slug) }}" 
                               class="inline-block ml-2 px-2 py-1 text-xs rounded-full hover:opacity-80 transition-opacity"
                               style="background-color: {{ $tag->color }}20; color: {{ $tag->color }};">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Article title -->
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-6">{{ $article->title }}</h1>
            
            <!-- Article content -->
            <div class="prose prose-lg max-w-none dark:prose-invert">
                {!! $article->content !!}
            </div>

            <!-- Navigation -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <x-button variant="secondary">Back to News</x-button>
            </div>
        </article>

        <!-- Related Articles -->
        @if(isset($relatedArticles) && $relatedArticles->count() > 0)
            <x-related-news :articles="$relatedArticles" />
        @endif
    </main>
@endsection 