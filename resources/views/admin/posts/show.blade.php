@extends('layouts.admin')
@section('title', __('View Post'))

@section('content')
    <h1 class="text-2xl font-bold mb-6">{{ $post->title }}</h1>
    <div class="mb-4">
        <span class="font-semibold">{{ __('Excerpt') }}:</span> {{ $post->excerpt }}
    </div>
    <div class="mb-4">
        <span class="font-semibold">{{ __('Content') }}:</span>
        <div>{{ $post->content }}</div>
    </div>
    <div class="mb-4">
        <span class="font-semibold">{{ __('Featured Image URL') }}:</span> {{ $post->featured_image ?? __('N/A') }}
    </div>
    <div class="mb-4">
        <span class="font-semibold">{{ __('Status') }}:</span> {{ ucfirst($post->status) }}
    </div>
    <div class="mb-4">
        <span class="font-semibold">{{ __('Categories') }}:</span>
        @if($post->categories && $post->categories->count())
            {{ $post->categories->pluck('name')->join(', ') }}
        @else
            {{ __('N/A') }}
        @endif
    </div>
    <div class="mb-4">
        <span class="font-semibold">{{ __('Published At') }}:</span> {{ $post->published_at ? $post->published_at->format('Y-m-d H:i') : __('N/A') }}
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-primary">{{ __('Edit Post') }}</a>
        <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">{{ __('Back to Posts') }}</a>
    </div>
@endsection 