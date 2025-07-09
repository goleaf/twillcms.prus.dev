@extends('layouts.admin')

@section('title', 'View Article')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">{{ $article->title }}</h1>
    <div class="mb-4">
        <span class="font-semibold">Excerpt:</span> {{ $article->excerpt }}
    </div>
    <div class="mb-4">
        <span class="font-semibold">Content:</span>
        <div class="prose dark:prose-invert">{!! nl2br(e($article->content)) !!}</div>
    </div>
    <div class="mb-4">
        <span class="font-semibold">Image:</span>
        @if($article->image)
            <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" class="w-full max-w-xs rounded-lg mt-2">
        @else
            <span class="text-gray-500">No image</span>
        @endif
    </div>
    <div class="mb-4">
        <span class="font-semibold">Featured:</span> {{ $article->is_featured ? 'Yes' : 'No' }}
    </div>
    <div class="mb-4">
        <span class="font-semibold">Status:</span> {{ ucfirst($article->status) }}
    </div>
    <div class="mb-4">
        <span class="font-semibold">Tags:</span>
        @if($article->tags->count())
            @foreach($article->tags as $tag)
                <span class="inline-block bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded px-2 py-1 text-xs font-medium mr-1">{{ $tag->name }}</span>
            @endforeach
        @else
            <span class="text-gray-500">No tags</span>
        @endif
    </div>
    <div class="mb-4">
        <span class="font-semibold">Published At:</span> {{ $article->published_at ? $article->published_at->format('Y-m-d H:i') : 'N/A' }}
    </div>
    <div class="flex justify-end space-x-2">
        <a href="{{ route('admin.articles.edit', $article) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">Edit</a>
        <form method="POST" action="{{ route('admin.articles.destroy', $article) }}" onsubmit="return confirm('Are you sure you want to delete this article?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-medium">Delete</button>
        </form>
    </div>
</div>
@endsection 