@extends('layouts.admin')

@section('title', 'View Post')

@section('header')
    <div class="md:flex md:items-center md:justify-between">
        <div class="min-w-0 flex-1">
            <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">{{ $post->title }}</h1>
            <div class="mt-1 flex items-center space-x-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $post->published ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $post->published ? 'Published' : 'Draft' }}
                </span>
                @if($post->priority > 0)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Priority: {{ $post->priority }}
                    </span>
                @endif
                <span class="text-sm text-gray-500">
                    {{ $post->view_count }} views
                </span>
            </div>
        </div>
        <div class="mt-4 flex md:ml-4 md:mt-0">
            <a href="{{ route('admin.posts.index') }}" 
               class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500 mr-3">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Posts
            </a>
            <a href="{{ route('admin.posts.edit', $post) }}" 
               class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>
                Edit
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Post Information</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Detailed view of the post content and metadata.</p>
        </div>
        
        <div class="border-t border-gray-200">
            <dl>
                <!-- Title -->
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Title</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $post->title }}</dd>
                </div>

                <!-- Description -->
                @if($post->description)
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $post->description }}</dd>
                </div>
                @endif

                <!-- Content -->
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Content</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="max-w-none prose prose-sm">
                            {!! nl2br(e($post->content)) !!}
                        </div>
                    </dd>
                </div>

                <!-- Status -->
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $post->published ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $post->published ? 'Published' : 'Draft' }}
                        </span>
                    </dd>
                </div>

                <!-- Published Date -->
                @if($post->published_at)
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Published Date</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $post->published_at->format('F j, Y \a\t g:i A') }}</dd>
                </div>
                @endif

                <!-- Categories -->
                @if($post->categories->count() > 0)
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Categories</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="flex flex-wrap gap-2">
                            @foreach($post->categories as $category)
                                <a href="{{ route('admin.categories.show', $category) }}" 
                                   class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200">
                                    @if($category->color_code)
                                        <div class="w-2 h-2 rounded-full mr-1" style="background-color: {{ $category->color_code }}"></div>
                                    @endif
                                    @if($category->icon)
                                        <span class="mr-1">{{ $category->icon }}</span>
                                    @endif
                                    {{ $category->title }}
                                </a>
                            @endforeach
                        </div>
                    </dd>
                </div>
                @endif

                <!-- Priority -->
                @if($post->priority > 0)
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Priority</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $post->priority }}</dd>
                </div>
                @endif

                <!-- Statistics -->
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Statistics</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-2xl font-semibold text-gray-900">{{ $post->view_count }}</span>
                                <p class="text-xs text-gray-500">Total Views</p>
                            </div>
                            <div>
                                <span class="text-2xl font-semibold text-gray-900">{{ $post->reading_time }}</span>
                                <p class="text-xs text-gray-500">Reading Time (min)</p>
                            </div>
                        </div>
                    </dd>
                </div>

                <!-- Timestamps -->
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Timestamps</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                        <div class="space-y-1">
                            <p><strong>Created:</strong> {{ $post->created_at->format('F j, Y \a\t g:i A') }}</p>
                            <p><strong>Last Updated:</strong> {{ $post->updated_at->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Action buttons -->
    <div class="mt-6 flex justify-end space-x-3">
        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" 
              onsubmit="return confirm('Are you sure you want to delete this post?')" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                Delete Post
            </button>
        </form>
    </div>
</div>
@endsection
