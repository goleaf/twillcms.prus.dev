@extends('layouts.admin')

@section('title', __('Create Tag'))

@section('content')
    <div class="max-w-xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow p-8 mt-8">
        <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">{{ __('Create New Tag') }}</h1>
        <form x-data="{ loading: false }" x-on:submit="loading = true" method="POST" action="{{ route('admin.tags.store') }}">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Name') }}</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-600 focus:ring-opacity-50">
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Description') }}</label>
                <textarea name="description" id="description" rows="3" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-600 focus:ring-opacity-50">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Color') }}</label>
                <input type="color" name="color" id="color" value="{{ old('color', '#3B82F6') }}" class="w-12 h-8 p-0 border-0 bg-transparent">
                @error('color')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4 flex items-center">
                <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-700 text-blue-600 shadow-sm focus:ring focus:ring-blue-200 dark:focus:ring-blue-600 focus:ring-opacity-50">
                <label for="is_featured" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">{{ __('Featured') }}</label>
            </div>
            <div>
                <x-button type="submit" variant="primary" x-bind:disabled="loading">
                    <span x-show="!loading">{{ __('Create Tag') }}</span>
                    <span x-show="loading"><x-loader>{{ __('Saving...') }}</x-loader></span>
                </x-button>
            </div>
        </form>
    </div>
@endsection 