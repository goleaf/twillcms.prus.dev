@extends('layouts.app')

@section('title', 'All Topics - News Portal')
@section('description', 'Browse all news topics and tags. Find articles by your interests and stay updated with the latest stories in your preferred categories.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">All Topics</h1>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto">
            Explore news by topics that interest you. Click on any tag to see related articles and stay informed about the subjects you care about.
        </p>
    </div>

    <!-- Search Tags -->
    <div class="mb-8">
        <div class="max-w-md mx-auto">
            <div class="relative">
                <input type="text" 
                       id="tag-search" 
                       placeholder="Search topics..." 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <svg class="absolute right-3 top-3.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>
    </div>

    @if($tags->isNotEmpty())
    <!-- Tag Cloud -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 mb-8">
        <h2 class="text-2xl font-semibold text-gray-900 mb-6 text-center">Topic Cloud</h2>
        
        <div class="flex flex-wrap gap-3 justify-center" id="tag-cloud">
            @foreach($tags as $tag)
                @php
                    $count = $tag->posts_count;
                    $size = 'text-sm';
                    $weight = 'font-normal';
                    
                    if ($count > 50) {
                        $size = 'text-2xl';
                        $weight = 'font-bold';
                    } elseif ($count > 20) {
                        $size = 'text-xl';
                        $weight = 'font-semibold';
                    } elseif ($count > 10) {
                        $size = 'text-lg';
                        $weight = 'font-medium';
                    } elseif ($count > 5) {
                        $size = 'text-base';
                        $weight = 'font-medium';
                    }
                @endphp
                
                <a href="{{ route('tags.show', $tag->slug) }}" 
                   class="tag-item bg-gray-100 hover:bg-red-100 hover:text-red-700 text-gray-700 px-4 py-2 rounded-full {{ $size }} {{ $weight }} transition-all duration-300 hover:scale-105"
                   data-tag="{{ strtolower($tag->name) }}">
                    {{ $tag->name }}
                    <span class="text-xs text-gray-500 ml-1">({{ $count }})</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Popular Topics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                Most Popular
            </h3>
            <ul class="space-y-2">
                @foreach($tags->sortByDesc('posts_count')->take(5) as $tag)
                    <li>
                        <a href="{{ route('tags.show', $tag->slug) }}" 
                           class="flex items-center justify-between text-gray-700 hover:text-red-600 py-1">
                            <span>{{ $tag->name }}</span>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                {{ $tag->posts_count }}
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Recently Active
            </h3>
            <ul class="space-y-2">
                @foreach($tags->sortBy('name')->take(5) as $tag)
                    <li>
                        <a href="{{ route('tags.show', $tag->slug) }}" 
                           class="flex items-center justify-between text-gray-700 hover:text-blue-600 py-1">
                            <span>{{ $tag->name }}</span>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                {{ $tag->posts_count }}
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h4a1 1 0 011 1v2m3 0V2a1 1 0 011-1h4a1 1 0 011 1v2m-9 4h10a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V10a2 2 0 012-2h2V6a2 2 0 012-2h4a2 2 0 012 2v2h2z"/>
                </svg>
                Browse All ({{ $tags->count() }} Topics)
            </h3>
            <p class="text-gray-600 text-sm mb-4">
                We have {{ $tags->count() }} topics covering a wide range of subjects. Use the search above or browse through the topic cloud.
            </p>
            <div class="text-center">
                <button onclick="showAllTags()" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-medium transition-colors">
                    Show All Topics
                </button>
            </div>
        </div>
    </div>

    <!-- All Topics List (Initially Hidden) -->
    <div id="all-tags" class="hidden bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-6">All Topics (A-Z)</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($tags->sortBy('name') as $tag)
                <a href="{{ route('tags.show', $tag->slug) }}" 
                   class="tag-item flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:border-red-300 hover:bg-red-50 transition-colors"
                   data-tag="{{ strtolower($tag->name) }}">
                    <span class="text-gray-700 hover:text-red-600">{{ $tag->name }}</span>
                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                        {{ $tag->posts_count }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>
    @else
    <!-- Empty State -->
    <div class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No topics available</h3>
        <p class="mt-1 text-sm text-gray-500">Check back later as we add more content and topics.</p>
    </div>
    @endif
</div>

<script>
// Search functionality
document.getElementById('tag-search').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const tagItems = document.querySelectorAll('.tag-item');
    
    tagItems.forEach(item => {
        const tagName = item.getAttribute('data-tag');
        if (tagName.includes(searchTerm)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
});

// Show all tags function
function showAllTags() {
    const allTagsDiv = document.getElementById('all-tags');
    allTagsDiv.classList.toggle('hidden');
}
</script>
@endsection 