@extends('layouts.app')

@section('title', $tag->name . ' - News Topics')
@section('description', 'Read the latest news articles about ' . $tag->name . '. Stay updated with stories and developments in this topic.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Tag Header -->
    <div class="text-center mb-12">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
        </div>
        <h1 class="text-4xl font-bold text-gray-900 mb-4">#{{ $tag->name }}</h1>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-6">
            {{ $posts->total() }} articles about {{ $tag->name }}
        </p>
        
        <!-- Breadcrumb -->
        <nav class="flex justify-center">
            <ol class="flex items-center space-x-2 text-sm text-gray-500">
                <li><a href="{{ route('home') }}" class="hover:text-red-600">Home</a></li>
                <li><span class="mx-2">/</span></li>
                <li><a href="{{ route('tags.index') }}" class="hover:text-red-600">Topics</a></li>
                <li><span class="mx-2">/</span></li>
                <li class="text-gray-900 font-medium">{{ $tag->name }}</li>
            </ol>
        </nav>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Articles -->
        <div class="lg:col-span-2">
            @if($posts->isNotEmpty())
                <div class="space-y-6">
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
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold mr-3">
                                        {{ $tag->name }}
                                    </span>
                                    @foreach($article->tags->where('id', '!=', $tag->id)->take(2) as $otherTag)
                                        <a href="{{ route('tags.show', $otherTag->slug) }}" 
                                           class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-2 py-1 rounded text-xs font-medium mr-2">
                                            {{ $otherTag->name }}
                                        </a>
                                    @endforeach
                                </div>
                                
                                <h2 class="text-xl font-semibold text-gray-900 mb-3">
                                    <a href="{{ route('news.show', $article->slug) }}" class="hover:text-red-600">
                                        {{ $article->title }}
                                    </a>
                                </h2>
                                
                                <p class="text-gray-600 mb-4">
                                    {{ \Str::limit($article->excerpt, 150) }}
                                </p>
                                
                                <div class="flex items-center justify-between text-sm text-gray-500">
                                    <div class="flex items-center space-x-4">
                                        <time datetime="{{ $article->created_at->toISOString() }}">
                                            {{ $article->created_at->diffForHumans() }}
                                        </time>
                                        <span>{{ $article->view_count }} views</span>
                                        <span>{{ $article->reading_time }} min read</span>
                                    </div>
                                    <a href="{{ route('news.show', $article->slug) }}" 
                                       class="text-red-600 hover:text-red-700 font-medium">
                                        Read More →
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($posts->hasPages())
                <div class="mt-12">
                    {{ $posts->links() }}
                </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No articles found</h3>
                    <p class="mt-1 text-sm text-gray-500">There are no articles tagged with "{{ $tag->name }}" yet.</p>
                    <div class="mt-6">
                        <a href="{{ route('tags.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                            Browse All Topics
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Related Tags -->
            @if($relatedTags->isNotEmpty())
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Related Topics</h3>
                <div class="space-y-2">
                    @foreach($relatedTags as $relatedTag)
                        <a href="{{ route('tags.show', $relatedTag->slug) }}" 
                           class="flex items-center justify-between text-gray-700 hover:text-red-600 py-2 px-3 rounded hover:bg-gray-50 transition-colors">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                {{ $relatedTag->name }}
                            </span>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                {{ $relatedTag->posts_count }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Popular Topics -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Popular Topics</h3>
                <div class="flex flex-wrap gap-2">
                    @php
                        $popularTags = \App\Models\Tag::withCount(['posts' => function ($query) {
                            $query->where('status', 'published');
                        }])
                        ->orderBy('posts_count', 'desc')
                        ->take(15)
                        ->get();
                    @endphp
                    @foreach($popularTags as $popularTag)
                        <a href="{{ route('tags.show', $popularTag->slug) }}" 
                           class="bg-gray-100 hover:bg-red-100 hover:text-red-700 text-gray-700 px-3 py-1 rounded-full text-sm font-medium transition-colors {{ $popularTag->id === $tag->id ? 'bg-red-100 text-red-700' : '' }}">
                            {{ $popularTag->name }}
                        </a>
                    @endforeach
                </div>
                <div class="mt-4">
                    <a href="{{ route('tags.index') }}" class="text-red-600 hover:text-red-700 text-sm font-medium">
                        View All Topics →
                    </a>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('search', ['q' => $tag->name]) }}" 
                       class="flex items-center text-gray-700 hover:text-red-600 py-2">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Search "{{ $tag->name }}"
                    </a>
                    
                    <button onclick="shareTag()" 
                            class="flex items-center text-gray-700 hover:text-blue-600 py-2 w-full text-left">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                        </svg>
                        Share Topic
                    </button>
                    
                    <a href="{{ route('home') }}" 
                       class="flex items-center text-gray-700 hover:text-green-600 py-2">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function shareTag() {
    if (navigator.share) {
        navigator.share({
            title: 'News about {{ $tag->name }}',
            text: 'Check out the latest news articles about {{ $tag->name }}',
            url: window.location.href,
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(function() {
            alert('Link copied to clipboard!');
        });
    }
}
</script>
@endsection 