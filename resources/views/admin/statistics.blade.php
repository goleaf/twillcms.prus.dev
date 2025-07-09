@extends('layouts.admin')

@section('title', 'Statistics')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <h1 class="text-3xl font-bold mb-8">Portal Statistics</h1>
    <h1 class="text-2xl font-bold mb-6">Statistics</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Articles by Status</h2>
            <ul>
                <li>Total: {{ $articleStats['total'] ?? 0 }}</li>
                <li>Published: {{ $articleStats['published'] ?? 0 }}</li>
                <li>Draft: {{ $articleStats['draft'] ?? 0 }}</li>
                <li>Last Updated: {{ isset($articleStats['last_updated']) && $articleStats['last_updated'] ? $articleStats['last_updated']->diffForHumans() : 'N/A' }}</li>
                <li>First Published: {{ isset($articleStats['first_published']) && $articleStats['first_published'] ? $articleStats['first_published']->diffForHumans() : 'N/A' }}</li>
            </ul>
        </div>
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Popular Tags</h2>
            <ul>
                @foreach($trendingTags as $tag)
                <li>{{ $tag->name }} ({{ $tag->usage_count }})</li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mt-8">
        <h2 class="text-lg font-semibold mb-4">Recent Activity</h2>
        <ul>
            @foreach($trendingArticles as $article)
            <li>{{ $article->title }} ({{ isset($article->updated_at) && $article->updated_at ? $article->updated_at->diffForHumans() : 'N/A' }})</li>
            @endforeach
        </ul>
    </div>
</div>
@endsection 