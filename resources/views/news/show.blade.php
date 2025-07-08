@extends('layouts.app')
@section('title', $article->title)
@section('meta')
    <meta name="description" content="{{ $article->excerpt ?? __('Read the full article on our news portal.') }}">
@endsection

@section('content')
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 bg-indigo-700 text-white px-4 py-2 rounded z-50">{{ __('Skip to main content') }}</a>
    <main id="main-content" tabindex="-1" aria-label="{{ __('Article Content') }}">
        <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow p-8 mt-8">
            <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-5xl lg:text-6xl mb-4">{{ $article->title }}</h1>
            <div class="mb-4 flex flex-wrap items-center gap-4 text-gray-600 dark:text-gray-300 text-sm">
                <span class="font-semibold">{{ __('Published At') }}:</span> {{ $article->published_at ? $article->published_at->format('Y-m-d H:i') : __('N/A') }}
                @if($article->author)
                    <span class="ml-4"><span class="font-semibold">{{ __('Author') }}:</span> {{ $article->author->name }}</span>
                @endif
                @if($article->tags->isNotEmpty())
                    <span class="ml-4 flex flex-wrap gap-2 items-center">
                        <span class="font-semibold">{{ __('Tags') }}:</span>
                        @foreach($article->tags as $tag)
                            <a href="{{ route('tag.show', $tag) }}" class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-900 transition-shadow">{{ $tag->name }}</a>
                        @endforeach
                    </span>
                @endif
            </div>
            <div class="prose prose-lg max-w-none dark:prose-invert prose-indigo mb-8">
                {!! $article->content !!}
            </div>
            <div>
                <a href="{{ route('news.index') }}" class="focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-900 transition-shadow">
                    <x-button variant="secondary">{{ __('Back to News') }}</x-button>
                </a>
            </div>
        </div>
    </main>
@endsection 