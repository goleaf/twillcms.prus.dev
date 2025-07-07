@extends('layouts.app')

@section('title', $tag->name . ' - News Topics')
@section('description', 'Read the latest news articles about ' . $tag->name . '. Stay updated with stories and developments in this topic.')

@section('content')
<div class="bg-white dark:bg-gray-900">
    <!-- Tag Header -->
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br opacity-20"
             style="background: linear-gradient(135deg, {{ $tag->color }}40, {{ $tag->color }}80);">
        </div>
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-6 h-6 rounded-full mr-4" style="background-color: {{ $tag->color }}"></div>
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-5xl">
                        {{ $tag->name }}
                    </h1>
                    @if($tag->is_featured)
                    <span class="ml-4 inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                        Featured
                    </span>
                    @endif
                </div>
                
                @if($tag->description)
                <p class="mt-4 text-lg leading-8 text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                    {{ $tag->description }}
                </p>
                @endif
                
                <div class="mt-6 flex items-center justify-center space-x-6 text-sm text-gray-500 dark:text-gray-400">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ $tag->usage_count }} {{ Str::plural('article', $tag->usage_count) }}</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                        </svg>
                        <span>Last updated {{ $tag->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Articles -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Latest Articles
            </h2>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                {{ $articles->total() }} {{ Str::plural('article', $articles->total()) }} found
            </div>
        </div>

        @if($articles->count() > 0)
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 xl:grid-cols-3">
            @foreach($articles as $article)
            <article class="group relative flex flex-col overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm hover:shadow-lg transition-shadow duration-200">
                <div class="aspect-[16/9] overflow-hidden">
                    <img 
                        src="{{ $article->image ? asset('storage/' . $article->image) : 'https://picsum.photos/800/600?random=' . $article->id }}" 
                        alt="{{ $article->title }}"
                        class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-200"
                        loading="lazy"
                    >
                </div>
                
                <div class="flex-1 p-6">
                    <div class="flex items-center gap-x-4 text-xs mb-3">
                        <time datetime="{{ $article->published_at->format('Y-m-d') }}" class="text-gray-500 dark:text-gray-400">
                            {{ $article->published_at->format('M j, Y') }}
                        </time>
                        <div class="flex items-center">
                            <svg class="h-4 w-4 mr-1 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-gray-500 dark:text-gray-400">{{ number_format($article->view_count) }}</span>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 mb-3">
                        <a href="{{ route('news.show', $article->slug) }}">
                            <span class="absolute inset-0"></span>
                            {{ $article->title }}
                        </a>
                    </h3>
                    
                    @if($article->excerpt)
                    <p class="text-sm leading-6 text-gray-600 dark:text-gray-300 mb-4">
                        {{ Str::limit($article->excerpt, 120) }}
                    </p>
                    @endif
                    
                    <!-- Other Tags -->
                    @if($article->tags->count() > 1)
                    <div class="flex flex-wrap gap-2">
                        @foreach($article->tags->where('id', '!=', $tag->id)->take(3) as $otherTag)
                        <a href="{{ route('tags.show', $otherTag->slug) }}" 
                           class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium transition-all duration-200 hover:scale-105"
                           style="background-color: {{ $otherTag->color }}20; color: {{ $otherTag->color }}; border: 1px solid {{ $otherTag->color }}40;">
                            {{ $otherTag->name }}
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>
            </article>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $articles->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No articles found</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                There are no articles tagged with "{{ $tag->name }}" yet.
            </p>
            <div class="mt-6">
                <a href="{{ route('home') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Browse all articles
                </a>
            </div>
        </div>
        @endif
    </div>

    <!-- Related Tags -->
    @if($relatedTags->count() > 0)
    <div class="bg-gray-50 dark:bg-gray-800 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    Related Topics
                </h2>
                <p class="mt-2 text-lg leading-8 text-gray-600 dark:text-gray-300">
                    Explore similar topics that might interest you
                </p>
            </div>
            
            <div class="flex flex-wrap justify-center gap-4">
                @foreach($relatedTags as $relatedTag)
                <a href="{{ route('tags.show', $relatedTag->slug) }}" 
                   class="inline-flex items-center rounded-full px-4 py-2 text-sm font-medium transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                   style="background-color: {{ $relatedTag->color }}20; color: {{ $relatedTag->color }}; border: 1px solid {{ $relatedTag->color }}40;">
                    {{ $relatedTag->name }}
                    <span class="ml-2 inline-flex items-center rounded-full bg-white/20 px-2 py-0.5 text-xs font-medium">
                        {{ $relatedTag->usage_count }}
                    </span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Popular Tags -->
    @if($popularTags->count() > 0)
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Popular Topics
            </h2>
            <p class="mt-2 text-lg leading-8 text-gray-600 dark:text-gray-300">
                Discover what's trending now
            </p>
        </div>
        
        <div class="flex flex-wrap justify-center gap-3">
            @foreach($popularTags as $popularTag)
            <a href="{{ route('tags.show', $popularTag->slug) }}" 
               class="inline-flex items-center rounded-full px-3 py-1.5 text-sm font-medium transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $popularTag->id === $tag->id ? 'opacity-50 cursor-not-allowed' : '' }}"
               style="background-color: {{ $popularTag->color }}20; color: {{ $popularTag->color }}; border: 1px solid {{ $popularTag->color }}40;"
               {{ $popularTag->id === $tag->id ? 'onclick="return false;"' : '' }}>
                {{ $popularTag->name }}
                <span class="ml-2 inline-flex items-center rounded-full bg-white/20 px-1.5 py-0.5 text-xs font-medium">
                    {{ $popularTag->usage_count }}
                </span>
            </a>
            @endforeach
        </div>
        
        <div class="mt-8 text-center">
            <a href="{{ route('tags.index') }}" class="text-sm font-semibold leading-6 text-indigo-600 hover:text-indigo-500">
                View all topics <span aria-hidden="true">→</span>
            </a>
        </div>
    </div>
    @endif
</div>
@endsection 