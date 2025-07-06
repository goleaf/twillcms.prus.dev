@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Posts</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['total_posts'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Published</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['published_posts'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c1.1045695 0 2 .8954305 2 2v1M7 7c0 1.1045695-.8954305 2-2 2H4c-1.1045695 0-2-.8954305-2-2s.8954305-2 2-2h1M7 7v3c0 1.1045695-.8954305 2-2 2H4c-1.1045695 0-2-.8954305-2-2V7c0-1.1045695.8954305-2 2-2h1c1.1045695 0 2 .8954305 2 2z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Categories</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $stats['total_categories'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Views</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['total_views'] ?? 0) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white shadow rounded-lg mb-8">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="{{ route('admin.posts.create') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <svg class="h-8 w-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <div>
                    <h4 class="text-sm font-medium text-blue-900">New Post</h4>
                    <p class="text-sm text-blue-700">Create a new article</p>
                </div>
            </a>

            <a href="{{ route('admin.categories.create') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                <svg class="h-8 w-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c1.1045695 0 2 .8954305 2 2v1M7 7c0 1.1045695-.8954305 2-2 2H4c-1.1045695 0-2-.8954305-2-2s.8954305-2 2-2h1M7 7v3c0 1.1045695-.8954305 2-2 2H4c-1.1045695 0-2-.8954305-2-2V7c0-1.1045695.8954305-2 2-2h1c1.1045695 0 2 .8954305 2 2z"></path>
                </svg>
                <div>
                    <h4 class="text-sm font-medium text-green-900">New Category</h4>
                    <p class="text-sm text-green-700">Add a new category</p>
                </div>
            </a>

            <a href="{{ route('admin.posts.index') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                <svg class="h-8 w-8 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <div>
                    <h4 class="text-sm font-medium text-purple-900">Manage Posts</h4>
                    <p class="text-sm text-purple-700">Edit existing posts</p>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Recent Posts & Popular Posts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Posts -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Posts</h3>
            <div class="space-y-3">
                @forelse($recent_posts ?? [] as $post)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900">{{ $post->title }}</h4>
                            <p class="text-sm text-gray-500">{{ $post->created_at->format('M j, Y') }}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($post->published)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Published</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Draft</span>
                            @endif
                            <a href="{{ route('admin.posts.edit', $post) }}" class="text-blue-600 hover:text-blue-900">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No posts yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Popular Posts -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Popular Posts</h3>
            <div class="space-y-3">
                @forelse($popular_posts ?? [] as $post)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900">{{ $post->title }}</h4>
                            <p class="text-sm text-gray-500">{{ number_format($post->view_count ?? 0) }} views</p>
                        </div>
                        <a href="{{ route('admin.posts.edit', $post) }}" class="text-blue-600 hover:text-blue-900">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No posts yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection 