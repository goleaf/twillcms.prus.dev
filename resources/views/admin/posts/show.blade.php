@extends('layouts.admin')
@section('title', 'View Article')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">{{ $article->title }}</h1>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-6">
            <div class="mb-4">
                <span class="font-semibold text-gray-700 dark:text-gray-300">Excerpt:</span> 
                <p class="mt-1 text-gray-900 dark:text-white">{{ $article->excerpt }}</p>
            </div>
            
            <div class="mb-4">
                <span class="font-semibold text-gray-700 dark:text-gray-300">Content:</span>
                <div class="mt-1 prose prose-lg max-w-none dark:prose-invert text-gray-900 dark:text-white">
                    {!! $article->content !!}
                </div>
            </div>
            
            @if($article->image)
            <div class="mb-4">
                <span class="font-semibold text-gray-700 dark:text-gray-300">Featured Image:</span>
                <div class="mt-1">
                    <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" class="max-w-xs rounded-lg">
                </div>
            </div>
            @endif
            
            <div class="mb-4">
                <span class="font-semibold text-gray-700 dark:text-gray-300">Status:</span> 
                <span class="ml-2 px-2 py-1 rounded text-sm {{ $article->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst($article->status) }}
                </span>
            </div>
            
            <div class="mb-4">
                <span class="font-semibold text-gray-700 dark:text-gray-300">Tags:</span>
                @if($article->tags && $article->tags->count())
                    <div class="mt-1">
                        @foreach($article->tags as $tag)
                            <span class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded px-2 py-0.5 text-xs font-semibold mr-1 mb-1">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                @else
                    <span class="text-gray-500">No tags</span>
                @endif
            </div>
            
            <div class="mb-4">
                <span class="font-semibold text-gray-700 dark:text-gray-300">Published At:</span> 
                <span class="text-gray-900 dark:text-white">{{ $article->published_at ? $article->published_at->format('Y-m-d H:i') : 'Not published' }}</span>
            </div>
            
            <div class="flex gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.articles.edit', $article) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium transition-colors">
                    Edit Article
                </a>
                <a href="{{ route('admin.articles.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md font-medium transition-colors">
                    Back to Articles
                </a>
            </div>
        </div>
    </div>
@endsection 