@extends('layouts.admin')

@section('title', 'Edit Post')

@section('header')
    <div class="md:flex md:items-center md:justify-between">
        <div class="min-w-0 flex-1">
            <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Edit Post: {{ $post->title }}</h1>
        </div>
        <div class="mt-4 flex md:ml-4 md:mt-0">
            <a href="{{ route('admin.posts.index') }}" 
               class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500 mr-3">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Posts
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <form action="{{ route('admin.posts.update', $post) }}" method="POST" class="space-y-8 divide-y divide-gray-200">
        @csrf
        @method('PUT')
        
        <div class="space-y-8 divide-y divide-gray-200">
            <div>
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Basic Information</h3>
                    <p class="mt-1 text-sm text-gray-500">Update the basic details of your post.</p>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <!-- Title -->
                    <div class="sm:col-span-4">
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <div class="mt-1">
                            <input type="text" name="title" id="title" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('title', $post->title) }}" required>
                        </div>
                        @error('title')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Published Status -->
                    <div class="sm:col-span-2">
                        <label for="published" class="block text-sm font-medium text-gray-700">Status</label>
                        <div class="mt-1">
                            <select name="published" id="published" 
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                <option value="1" {{ old('published', $post->published) ? 'selected' : '' }}>Published</option>
                                <option value="0" {{ !old('published', $post->published) ? 'selected' : '' }}>Draft</option>
                            </select>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="sm:col-span-6">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <div class="mt-1">
                            <textarea name="description" id="description" rows="3" 
                                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                      placeholder="Brief description of the post">{{ old('description', $post->description) }}</textarea>
                        </div>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Content -->
                    <div class="sm:col-span-6">
                        <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                        <div class="mt-1">
                            <textarea name="content" id="content" rows="10" 
                                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                      placeholder="Post content">{{ old('content', $post->content) }}</textarea>
                        </div>
                        @error('content')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="pt-8">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Publishing Options</h3>
                    <p class="mt-1 text-sm text-gray-500">Configure publishing settings and visibility.</p>
                </div>
                
                <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <!-- Published Date -->
                    <div class="sm:col-span-3">
                        <label for="published_at" class="block text-sm font-medium text-gray-700">Published Date</label>
                        <div class="mt-1">
                            <input type="datetime-local" name="published_at" id="published_at" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('published_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}">
                        </div>
                    </div>

                    <!-- Priority -->
                    <div class="sm:col-span-3">
                        <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                        <div class="mt-1">
                            <input type="number" name="priority" id="priority" min="0" max="100"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('priority', $post->priority ?? 0) }}">
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="sm:col-span-6">
                        <label for="categories" class="block text-sm font-medium text-gray-700">Categories</label>
                        <div class="mt-1">
                            <select name="categories[]" id="categories" multiple 
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md h-32">
                                @foreach(\App\Models\Category::published()->get() as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ (old('categories') ? in_array($category->id, old('categories')) : $post->categories->contains($category->id)) ? 'selected' : '' }}>
                                        {{ $category->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Hold Ctrl/Cmd to select multiple categories</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-5">
            <div class="flex justify-end">
                <a href="{{ route('admin.posts.index') }}" 
                   class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
                <button type="submit" 
                        class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Update Post
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
