@extends('layouts.admin')

@section('title', 'Create Category')

@section('header')
    <div class="md:flex md:items-center md:justify-between">
        <div class="min-w-0 flex-1">
            <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Create New Category</h1>
        </div>
        <div class="mt-4 flex md:ml-4 md:mt-0">
            <a href="{{ route('admin.categories.index') }}" 
               class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Back to Categories
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-8 divide-y divide-gray-200">
        @csrf
        
        <div class="space-y-8 divide-y divide-gray-200">
            <div>
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Basic Information</h3>
                    <p class="mt-1 text-sm text-gray-500">Enter the basic details for your new category.</p>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <!-- Title -->
                    <div class="sm:col-span-4">
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <div class="mt-1">
                            <input type="text" name="title" id="title" 
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('title') }}" required>
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
                                <option value="1" {{ old('published', 1) ? 'selected' : '' }}>Published</option>
                                <option value="0" {{ !old('published', 1) ? 'selected' : '' }}>Draft</option>
                            </select>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="sm:col-span-6">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <div class="mt-1">
                            <textarea name="description" id="description" rows="3" 
                                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                      placeholder="Brief description of the category">{{ old('description') }}</textarea>
                        </div>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Parent Category -->
                    <div class="sm:col-span-3">
                        <label for="parent_id" class="block text-sm font-medium text-gray-700">Parent Category</label>
                        <div class="mt-1">
                            <select name="parent_id" id="parent_id" 
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                <option value="">None (Root Category)</option>
                                @foreach(\App\Models\Category::published()->get() as $category)
                                    <option value="{{ $category->id }}" {{ old('parent_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Select a parent category to create a subcategory</p>
                    </div>

                    <!-- Sort Order -->
                    <div class="sm:col-span-3">
                        <label for="sort_order" class="block text-sm font-medium text-gray-700">Sort Order</label>
                        <div class="mt-1">
                            <input type="number" name="sort_order" id="sort_order" min="0"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('sort_order', 0) }}">
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Lower numbers appear first</p>
                    </div>
                </div>
            </div>

            <div class="pt-8">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Visual Design</h3>
                    <p class="mt-1 text-sm text-gray-500">Customize the appearance of your category.</p>
                </div>
                
                <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <!-- Color Code -->
                    <div class="sm:col-span-3">
                        <label for="color_code" class="block text-sm font-medium text-gray-700">Color</label>
                        <div class="mt-1 flex">
                            <input type="color" name="color_code" id="color_code" 
                                   class="h-10 w-20 border border-gray-300 rounded-md"
                                   value="{{ old('color_code', '#3B82F6') }}">
                            <input type="text" name="color_code_text" id="color_code_text" 
                                   class="ml-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('color_code', '#3B82F6') }}" placeholder="#3B82F6">
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Choose a color to represent this category</p>
                    </div>

                    <!-- Icon -->
                    <div class="sm:col-span-3">
                        <label for="icon" class="block text-sm font-medium text-gray-700">Icon (Emoji)</label>
                        <div class="mt-1">
                            <input type="text" name="icon" id="icon" maxlength="2"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                   value="{{ old('icon') }}" placeholder="📁">
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Add an emoji to visually represent this category</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-5">
            <div class="flex justify-end">
                <a href="{{ route('admin.categories.index') }}" 
                   class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
                <button type="submit" 
                        class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Create Category
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    // Sync color picker with text input
    document.getElementById('color_code').addEventListener('input', function() {
        document.getElementById('color_code_text').value = this.value;
    });
    
    document.getElementById('color_code_text').addEventListener('input', function() {
        document.getElementById('color_code').value = this.value;
    });
</script>
@endsection
