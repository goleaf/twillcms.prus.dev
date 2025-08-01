@extends('layouts.admin')

@section('title', 'Create Article')

@section('content')
    <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow p-8 mt-8">
        <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Create New Article</h1>
        <form x-data="{ loading: false }" x-on:submit="loading = true" method="POST" action="{{ route('admin.articles.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required autofocus class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-600 focus:ring-opacity-50">
                @error('title')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="excerpt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Excerpt</label>
                <textarea name="excerpt" id="excerpt" rows="2" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-600 focus:ring-opacity-50">{{ old('excerpt') }}</textarea>
                @error('excerpt')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Content</label>
                <textarea name="content" id="content" rows="8" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-600 focus:ring-opacity-50">{{ old('content') }}</textarea>
                @error('content')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Image</label>
                <input type="file" name="image" id="image" class="block w-full text-sm text-gray-900 dark:text-gray-200 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                @error('image')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tags</label>
                <select name="tags[]" id="tags" multiple class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-600 focus:ring-opacity-50">
                    @foreach($tags as $tag)
                        <option value="{{ $tag->id }}" {{ collect(old('tags'))->contains($tag->id) ? 'selected' : '' }}>{{ $tag->name }}</option>
                    @endforeach
                </select>
                @error('tags')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white" required>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                </select>
            </div>
            <div class="mb-4 flex items-center">
                <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-700 text-blue-600 shadow-sm focus:ring focus:ring-blue-200 dark:focus:ring-blue-600 focus:ring-opacity-50">
                <label for="is_featured" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Featured</label>
            </div>
            <div>
                <x-button type="submit" variant="primary" x-bind:disabled="loading">
                    <span x-show="!loading">Create Article</span>
                    <span x-show="loading"><x-loader>Saving...</x-loader></span>
                </x-button>
            </div>
        </form>
    </div>
@endsection 