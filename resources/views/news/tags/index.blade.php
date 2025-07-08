@extends('layouts.app')

@section('title', 'Topics')
@section('description', 'Browse all news topics and tags. Find articles by your interests and stay updated with the latest stories in your preferred categories.')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-900">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-20 relative overflow-hidden">
        <!-- SVG Gradient Background -->
        <svg class="absolute inset-0 w-full h-full pointer-events-none" aria-hidden="true" focusable="false">
            <defs>
                <linearGradient id="topics-hero-gradient" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="#6366f1" stop-opacity="0.18" />
                    <stop offset="100%" stop-color="#7c3aed" stop-opacity="0.12" />
                </linearGradient>
                <linearGradient id="topics-hero-gradient-dark" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="#818cf8" stop-opacity="0.22" />
                    <stop offset="100%" stop-color="#a855f7" stop-opacity="0.15" />
                </linearGradient>
            </defs>
            <rect width="100%" height="100%" fill="url(#topics-hero-gradient)" class="block dark:hidden" />
            <rect width="100%" height="100%" fill="url(#topics-hero-gradient-dark)" class="hidden dark:block" />
        </svg>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">News Topics</h1>
            <p class="text-xl md:text-2xl opacity-90 max-w-3xl mx-auto">
                Explore our comprehensive collection of news topics. From technology and business to sports and entertainment, find the stories that matter to you.
            </p>
        </div>
    </div>

    <!-- Tags Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        @if($tags->count() > 0)
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach($tags as $tag)
                    <div class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden border border-gray-200 dark:border-gray-700">
                        <!-- Color accent bar -->
                        <div class="h-2 w-full" style="background-color: {{ $tag->color }};"></div>
                        
                        <div class="p-6">
                            <!-- Tag icon based on category -->
                            <div class="flex items-center justify-center w-12 h-12 rounded-full mb-4 mx-auto" style="background-color: {{ $tag->color }}20;">
                                @if(str_contains(strtolower($tag->name), 'tech'))
                                    <svg class="w-6 h-6" style="color: {{ $tag->color }};" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd" />
                                    </svg>
                                @elseif(str_contains(strtolower($tag->name), 'business') || str_contains(strtolower($tag->name), 'finance'))
                                    <svg class="w-6 h-6" style="color: {{ $tag->color }};" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" />
                                    </svg>
                                @elseif(str_contains(strtolower($tag->name), 'sport'))
                                    <svg class="w-6 h-6" style="color: {{ $tag->color }};" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd" />
                                    </svg>
                                @elseif(str_contains(strtolower($tag->name), 'health'))
                                    <svg class="w-6 h-6" style="color: {{ $tag->color }};" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-6 h-6" style="color: {{ $tag->color }};" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </div>
                            
                            <div class="text-center">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    {{ $tag->name }}
                                </h3>
                                
                                @if($tag->description)
                                    <p class="text-gray-600 dark:text-gray-300 mb-4 text-sm leading-relaxed">
                                        {{ Str::limit($tag->description, 80) }}
                                    </p>
                                @endif
                                
                                <div class="flex items-center justify-center space-x-4 text-sm text-gray-500 dark:text-gray-400 mb-6">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ $tag->articles_count ?? 0 }} articles</span>
                                    </div>
                                </div>
                                
                                <a href="{{ route('tags.show', $tag->slug) }}" class="group/button">
                                    <x-button variant="primary">View Tag</x-button>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Hover effect overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- No Tags Message -->
            <div class="text-center py-16">
                <div class="max-w-md mx-auto">
                    <svg class="mx-auto h-24 w-24 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No topics available</h3>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">Check back later as we add more topic categories.</p>
                    <a href="{{ route('home') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Back to Home
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection 