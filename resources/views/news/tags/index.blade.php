@extends('layouts.app')

@section('title', 'All Topics - News Portal')
@section('description', 'Browse all news topics and tags. Find articles by your interests and stay updated with the latest stories in your preferred categories.')

@section('content')
<div class="bg-white dark:bg-gray-900">
    <!-- Page Header -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center">
            <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-5xl">
                Explore Topics
            </h1>
            <p class="mt-4 text-lg leading-8 text-gray-600 dark:text-gray-300">
                Discover articles by topic and find content that interests you most
            </p>
        </div>

        <!-- Search Form -->
        <div class="mt-8 mx-auto max-w-md">
            <form action="{{ route('tags.index') }}" method="GET" class="relative">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search ?? '' }}"
                        placeholder="Search tags..." 
                        class="block w-full rounded-md border-0 py-2 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 dark:bg-gray-800 dark:text-white dark:ring-gray-600 dark:placeholder:text-gray-400"
                    >
                </div>
                @if($search)
                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Search results for: <span class="font-medium">{{ $search }}</span>
                    <a href="{{ route('tags.index') }}" class="ml-2 text-indigo-600 hover:text-indigo-500">Clear</a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Featured Tags -->
    @if($featuredTags->count() > 0 && !$search)
    <div class="bg-gray-50 dark:bg-gray-800 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    Featured Topics
                </h2>
                <p class="mt-2 text-lg leading-8 text-gray-600 dark:text-gray-300">
                    Trending and popular topics right now
                </p>
            </div>
            
            <div class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($featuredTags as $tag)
                <div class="relative group">
                    <div class="aspect-[16/9] overflow-hidden rounded-2xl bg-gray-900">
                        <div class="absolute inset-0 bg-gradient-to-br opacity-80"
                             style="background: linear-gradient(135deg, {{ $tag->color }}40, {{ $tag->color }}80);">
                        </div>
                        <div class="relative h-full flex items-center justify-center p-8">
                            <div class="text-center">
                                <h3 class="text-2xl font-bold text-white mb-2">{{ $tag->name }}</h3>
                                @if($tag->description)
                                <p class="text-gray-200 text-sm mb-4">{{ Str::limit($tag->description, 100) }}</p>
                                @endif
                                <div class="flex items-center justify-center space-x-4 text-sm text-gray-200">
                                    <span>{{ $tag->usage_count }} articles</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('tags.show', $tag->slug) }}" class="absolute inset-0">
                            <span class="sr-only">View {{ $tag->name }} articles</span>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- All Tags -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $search ? 'Search Results' : 'All Topics' }}
            </h2>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                {{ $tags->total() }} {{ Str::plural('topic', $tags->total()) }} found
            </div>
        </div>

        @if($tags->count() > 0)
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($tags as $tag)
            <div class="group relative rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 hover:shadow-lg transition-shadow duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full mr-3" style="background-color: {{ $tag->color }}"></div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                            {{ $tag->name }}
                        </h3>
                    </div>
                    @if($tag->is_featured)
                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                        Featured
                    </span>
                    @endif
                </div>
                
                @if($tag->description)
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    {{ Str::limit($tag->description, 100) }}
                </p>
                @endif
                
                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                    <span>{{ $tag->usage_count }} {{ Str::plural('article', $tag->usage_count) }}</span>
                    <span class="text-indigo-600 dark:text-indigo-400 group-hover:text-indigo-500">
                        View articles →
                    </span>
                </div>
                
                <a href="{{ route('tags.show', $tag->slug) }}" class="absolute inset-0">
                    <span class="sr-only">View {{ $tag->name }} articles</span>
                </a>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $tags->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No tags found</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $search ? 'Try adjusting your search terms.' : 'No tags have been created yet.' }}
            </p>
            @if($search)
            <div class="mt-6">
                <a href="{{ route('tags.index') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    View all tags
                </a>
            </div>
            @endif
        </div>
        @endif
    </div>

    <!-- Popular Tags Sidebar -->
    @if($popularTags->count() > 0 && !$search)
    <div class="bg-gray-50 dark:bg-gray-800 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    Popular Topics
                </h2>
                <p class="mt-2 text-lg leading-8 text-gray-600 dark:text-gray-300">
                    Most discussed topics this month
                </p>
            </div>
            
            <div class="flex flex-wrap justify-center gap-4">
                @foreach($popularTags as $tag)
                <a href="{{ route('tags.show', $tag->slug) }}" 
                   class="inline-flex items-center rounded-full px-4 py-2 text-sm font-medium transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                   style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}40;">
                    {{ $tag->name }}
                    <span class="ml-2 inline-flex items-center rounded-full bg-white/20 px-2 py-0.5 text-xs font-medium">
                        {{ $tag->usage_count }}
                    </span>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection 