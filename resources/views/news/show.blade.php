@extends('layouts.app')

@section('title', $article->title . ' - News Portal')
@section('description', $article->excerpt)
@section('og_type', 'article')
@section('og_image', $article->featured_image ?? asset('images/news-portal-logo.jpg'))

@section('content')
<x-app-layout>
    <article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Article Header -->
        <div class="mt-8 mb-12">
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($article->tags as $tag)
                <a href="{{ route('tag.show', $tag) }}" class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                    {{ $tag->name }}
                </a>
                @endforeach
            </div>
            
            <h1 class="text-4xl font-extrabold text-gray-900 mb-4">{{ $article->title }}</h1>
            
            <div class="flex items-center text-gray-500 text-sm mb-8">
                <time datetime="{{ $article->published_at->format('Y-m-d') }}">
                    {{ $article->published_at->format('M d, Y') }}
                </time>
                <span class="mx-2">&middot;</span>
                <span>{{ $article->reading_time }} min read</span>
                <span class="mx-2">&middot;</span>
                <span>{{ $article->view_count }} views</span>
            </div>

            @if($article->image)
            <div class="relative aspect-[16/9] mb-8">
                <img src="{{ asset('storage/' . $article->image) }}" 
                     alt="{{ $article->title }}"
                     class="rounded-lg object-cover w-full h-full">
                @if($article->image_caption)
                <p class="mt-2 text-sm text-gray-500 text-center">{{ $article->image_caption }}</p>
                @endif
            </div>
            @endif

            @if($article->excerpt)
            <div class="text-xl text-gray-500 mb-8 font-medium">
                {{ $article->excerpt }}
            </div>
            @endif
        </div>

        <!-- Article Content -->
        <div class="prose prose-lg max-w-none mb-12">
            {!! $article->content !!}
        </div>

        <!-- Tags -->
        <div class="border-t border-gray-200 pt-8 mb-12">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Topics</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($article->tags as $tag)
                <a href="{{ route('tag.show', $tag) }}" class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                    {{ $tag->name }}
                </a>
                @endforeach
            </div>
        </div>

        <!-- Related Articles -->
        @if($relatedArticles->isNotEmpty())
        <div class="border-t border-gray-200 pt-12 mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">Related Articles</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($relatedArticles as $related)
                <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
                    <div class="flex-shrink-0">
                        <img class="h-48 w-full object-cover" src="{{ asset('storage/' . $related->image) }}" alt="{{ $related->title }}">
                    </div>
                    <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                        <div class="flex-1">
                            <a href="{{ route('article.show', $related) }}" class="block">
                                <p class="text-xl font-semibold text-gray-900">{{ $related->title }}</p>
                                <p class="mt-3 text-base text-gray-500">{{ $related->excerpt }}</p>
                            </a>
                        </div>
                        <div class="mt-6 flex items-center">
                            <div class="flex space-x-1 text-sm text-gray-500">
                                <time datetime="{{ $related->published_at->format('Y-m-d') }}">
                                    {{ $related->published_at->format('M d, Y') }}
                                </time>
                                <span aria-hidden="true">&middot;</span>
                                <span>{{ $related->reading_time }} min read</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </article>
</x-app-layout>

<script>
function copyToClipboard() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        // You could add a toast notification here
        alert('Link copied to clipboard!');
    });
}
</script>
@endsection 