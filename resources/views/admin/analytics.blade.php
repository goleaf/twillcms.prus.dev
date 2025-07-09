@extends('layouts.admin')

@section('title', 'Analytics')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Analytics Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Views Analytics</h2>
            <ul>
                @foreach($popularArticles as $article)
                <li>{{ $article->title }} ({{ $article->view_count }} views)</li>
                @endforeach
            </ul>
        </div>
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Content Performance</h2>
            <ul>
                @foreach($recentArticles as $article)
                <li>{{ $article->title }} ({{ isset($article->updated_at) && $article->updated_at ? $article->updated_at->diffForHumans() : 'N/A' }})</li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mt-8">
        <h2 class="text-lg font-semibold mb-4">Popular Articles</h2>
        <ul>
            @foreach($featuredArticles as $article)
            <li>{{ $article->title }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endsection 