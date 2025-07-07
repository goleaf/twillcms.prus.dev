@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Edit Post</h1>
    <form method="POST" action="{{ route('admin.posts.update', $post) }}">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $post->title) }}" required>
            @error('title')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug', $post->slug) }}" required>
            @error('slug')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="excerpt" class="form-label">Excerpt</label>
            <textarea class="form-control" id="excerpt" name="excerpt" rows="3">{{ old('excerpt', $post->excerpt) }}</textarea>
            @error('excerpt')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="content" class="form-label">Content</label>
            <textarea class="form-control" id="content" name="content" rows="10" required>{{ old('content', $post->content) }}</textarea>
            @error('content')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="featured_image" class="form-label">Featured Image URL</label>
            <input type="url" class="form-control" id="featured_image" name="featured_image" value="{{ old('featured_image', $post->featured_image) }}">
            @error('featured_image')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>Published</option>
                <option value="archived" {{ old('status', $post->status) == 'archived' ? 'selected' : '' }}>Archived</option>
            </select>
            @error('status')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        @if(isset($categories) && $categories->count() > 0)
        <div class="mb-3">
            <label for="categories" class="form-label">Categories</label>
            <select class="form-control" id="categories" name="categories[]" multiple>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" 
                        {{ in_array($category->id, old('categories', $post->categories->pluck('id')->toArray())) ? 'selected' : '' }}>
                        {{ $category->title ?? $category->name }}
                    </option>
                @endforeach
            </select>
            @error('categories')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        @endif
        
        <div class="mb-3">
            <label for="published_at" class="form-label">Published At</label>
            <input type="datetime-local" class="form-control" id="published_at" name="published_at" 
                   value="{{ old('published_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}">
            @error('published_at')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Update Post</button>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Cancel</a>
            <a href="{{ route('admin.posts.show', $post) }}" class="btn btn-info">View Post</a>
        </div>
    </form>
</div>
@endsection 