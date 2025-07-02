@extends('layouts.admin')

@section('title', 'View Category')

@section('header')
    <div class="md:flex md:items-center md:justify-between">
        <div class="min-w-0 flex-1">
            <div class="flex items-center">
                @if($category->color_code)
                    <div class="w-6 h-6 rounded-full mr-3" style="background-color: {{ $category->color_code }}"></div>
                @endif
                @if($category->icon)
                    <span class="mr-2 text-2xl">{{ $category->icon }}</span>
                @endif
                <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">{{ $category->title }}</h1>
            </div>
            <div class="mt-1 flex items-center space-x-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $category->published ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $category->published ? 'Published' : 'Draft' }}
                </span>
                <span class="text-sm text-gray-500">
                    {{ $category->posts_count }} posts • {{ $category->view_count }} views
                </span>
                @if($category->parent)
                    <span class="text-sm text-gray-500">
                        Child of <a href="{{ route('admin.categories.show', $category->parent) }}" class="text-indigo-600 hover:text-indigo-500">{{ $category->parent->title }}</a>
                    </span>
                @endif
            </div>
        </div>
        <div class="mt-4 flex md:ml-4 md:mt-0">
            <a href="{{ route('admin.categories.index') }}" 
               class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500 mr-3">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Categories
            </a>
            <a href="{{ route('admin.categories.edit', $category) }}" 
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
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Category Details -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Category Information</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Detailed view of the category.</p>
                </div>
                
                <div class="border-t border-gray-200">
                    <dl>
                        <!-- Title -->
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Title</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $category->title }}</dd>
                        </div>

                        <!-- Description -->
                        @if($category->description)
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $category->description }}</dd>
                        </div>
                        @endif

                        <!-- Visual Design -->
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Visual Design</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <div class="flex items-center space-x-4">
                                    @if($category->color_code)
                                        <div class="flex items-center">
                                            <div class="w-4 h-4 rounded-full mr-2" style="background-color: {{ $category->color_code }}"></div>
                                            <span class="text-sm font-mono">{{ $category->color_code }}</span>
                                        </div>
                                    @endif
                                    @if($category->icon)
                                        <div class="flex items-center">
                                            <span class="mr-2 text-lg">{{ $category->icon }}</span>
                                            <span class="text-sm">Icon</span>
                                        </div>
                                    @endif
                                </div>
                            </dd>
                        </div>

                        <!-- Hierarchy -->
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Hierarchy</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                @if($category->parent)
                                    <p><strong>Parent:</strong> 
                                        <a href="{{ route('admin.categories.show', $category->parent) }}" 
                                           class="text-indigo-600 hover:text-indigo-500">{{ $category->parent->title }}</a>
                                    </p>
                                @else
                                    <p class="text-gray-500">Root category (no parent)</p>
                                @endif
                                
                                @if($category->children->count() > 0)
                                    <p class="mt-1"><strong>Children:</strong> {{ $category->children->count() }} subcategories</p>
                                @endif
                                
                                <p class="mt-1"><strong>Sort Order:</strong> {{ $category->sort_order }}</p>
                            </dd>
                        </div>

                        <!-- Statistics -->
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Statistics</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <span class="text-2xl font-semibold text-gray-900">{{ $category->posts_count }}</span>
                                        <p class="text-xs text-gray-500">Total Posts</p>
                                    </div>
                                    <div>
                                        <span class="text-2xl font-semibold text-gray-900">{{ $category->view_count }}</span>
                                        <p class="text-xs text-gray-500">Total Views</p>
                                    </div>
                                </div>
                            </dd>
                        </div>

                        <!-- Timestamps -->
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Timestamps</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <div class="space-y-1">
                                    <p><strong>Created:</strong> {{ $category->created_at->format('F j, Y \a\t g:i A') }}</p>
                                    <p><strong>Last Updated:</strong> {{ $category->updated_at->format('F j, Y \a\t g:i A') }}</p>
                                </div>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Recent Posts in Category -->
            @if($category->posts->count() > 0)
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Posts</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Latest posts in this category.</p>
                    </div>
                    
                    <ul class="divide-y divide-gray-200">
                        @foreach($category->posts()->latest()->limit(5)->get() as $post)
                            <li class="px-4 py-4 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            <a href="{{ route('admin.posts.show', $post) }}" class="hover:text-indigo-600">
                                                {{ $post->title }}
                                            </a>
                                        </p>
                                        <div class="mt-1 flex items-center text-xs text-gray-500 space-x-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $post->published ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $post->published ? 'Published' : 'Draft' }}
                                            </span>
                                            <span>{{ $post->view_count }} views</span>
                                            <span>{{ $post->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.posts.edit', $post) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 text-sm font-medium ml-4">
                                        Edit
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    
                    @if($category->posts->count() > 5)
                        <div class="px-4 py-3 border-t border-gray-200 text-center">
                            <a href="{{ route('admin.posts.index', ['category' => $category->id]) }}" 
                               class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                View all {{ $category->posts->count() }} posts →
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Children Categories -->
            @if($category->children->count() > 0)
                <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Subcategories</h3>
                    </div>
                    
                    <ul class="divide-y divide-gray-200">
                        @foreach($category->children as $child)
                            <li class="px-4 py-3 hover:bg-gray-50">
                                <div class="flex items-center">
                                    @if($child->color_code)
                                        <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $child->color_code }}"></div>
                                    @endif
                                    @if($child->icon)
                                        <span class="mr-2">{{ $child->icon }}</span>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">
                                            <a href="{{ route('admin.categories.show', $child) }}" class="hover:text-indigo-600">
                                                {{ $child->title }}
                                            </a>
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $child->posts_count }} posts</p>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Actions</h3>
                </div>
                
                <div class="p-4 space-y-3">
                    <a href="{{ route('admin.categories.create', ['parent_id' => $category->id]) }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                        </svg>
                        Add Subcategory
                    </a>
                    
                    <a href="{{ route('admin.posts.create', ['category' => $category->id]) }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                        </svg>
                        Add Post to Category
                    </a>
                    
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this category? This will also affect all subcategories and posts.')" class="w-full">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Delete Category
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
