@extends('layouts.app')

@section('title', $article->title . ' - News Portal')
@section('description', $article->excerpt)
@section('og_type', 'article')
@section('og_image', $article->featured_image ?? asset('images/news-portal-logo.jpg'))

@section('content')
<div class="bg-white dark:bg-gray-900">
    <!-- Reading Progress Bar -->
    <div id="reading-progress" class="fixed top-16 left-0 h-1 bg-indigo-600 z-50 transition-all duration-300" style="width: 0%"></div>

    <!-- Article Header -->
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center">
            <!-- Tags -->
            @if($article->tags->count() > 0)
            <div class="flex flex-wrap justify-center gap-2 mb-6">
                @foreach($article->tags as $tag)
                <a href="{{ route('tags.show', $tag->slug) }}" 
                   class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium transition-all duration-200 hover:scale-105"
                   style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}40;">
                    {{ $tag->name }}
                </a>
                @endforeach
            </div>
            @endif

            <!-- Title -->
            <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-5xl lg:text-6xl">
                {{ $article->title }}
            </h1>

            <!-- Excerpt -->
            @if($article->excerpt)
            <p class="mt-6 text-xl leading-8 text-gray-600 dark:text-gray-300">
                {{ $article->excerpt }}
            </p>
            @endif

            <!-- Meta Information -->
            <div class="mt-8 flex items-center justify-center space-x-6 text-sm text-gray-500 dark:text-gray-400">
                <div class="flex items-center">
                    <svg class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-5.5-2.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zM10 12a5.99 5.99 0 00-4.793 2.39A6.483 6.483 0 0010 16.5a6.483 6.483 0 004.793-2.11A5.99 5.99 0 0010 12z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ $article->author ?? 'News Team' }}</span>
                </div>
                <div class="flex items-center">
                    <svg class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                    </svg>
                    <time datetime="{{ $article->published_at->format('Y-m-d') }}">
                        {{ $article->published_at->format('F j, Y') }}
                    </time>
                </div>
                <div class="flex items-center">
                    <svg class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ number_format($article->view_count) }} views</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Image -->
    @if($article->image)
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="aspect-[16/9] overflow-hidden rounded-2xl">
            <img 
                src="{{ asset('storage/' . $article->image) }}" 
                alt="{{ $article->title }}"
                class="h-full w-full object-cover"
            >
        </div>
    </div>
    @endif

    <!-- Article Content -->
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-12">
        <div class="prose prose-lg prose-gray dark:prose-invert max-w-none">
            {!! nl2br(e($article->content)) !!}
        </div>

        <!-- Social Sharing -->
        <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Share this article</h3>
                <div class="flex space-x-4">
                    <button 
                        onclick="shareArticle('twitter')"
                        class="inline-flex items-center rounded-full bg-blue-500 p-2 text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        title="Share on Twitter"
                    >
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84" />
                        </svg>
                    </button>
                    <button 
                        onclick="shareArticle('facebook')"
                        class="inline-flex items-center rounded-full bg-blue-600 p-2 text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2"
                        title="Share on Facebook"
                    >
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M20 10C20 4.477 15.523 0 10 0S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <button 
                        onclick="shareArticle('linkedin')"
                        class="inline-flex items-center rounded-full bg-blue-700 p-2 text-white shadow-sm hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2"
                        title="Share on LinkedIn"
                    >
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-.003-3.096 1.548 1.548 0 01.003 3.096zm-1.337 9.763H6.34v-8.59H3.667v8.59zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.403C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.299V2.298C19 1.581 18.402 1 17.668 1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <button 
                        onclick="shareArticle('copy')"
                        class="inline-flex items-center rounded-full bg-gray-500 p-2 text-white shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                        title="Copy link"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Articles -->
    @if($relatedArticles->count() > 0)
    <div class="bg-gray-50 dark:bg-gray-800 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">Related Articles</h2>
                <p class="mt-2 text-lg leading-8 text-gray-600 dark:text-gray-300">More stories you might be interested in</p>
            </div>
            
            <div class="mx-auto mt-16 grid max-w-2xl auto-rows-fr grid-cols-1 gap-8 sm:mt-20 lg:mx-0 lg:max-w-none lg:grid-cols-3">
                @foreach($relatedArticles as $relatedArticle)
                <article class="relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 pt-80 sm:pt-48 lg:pt-80">
                    <img 
                        src="{{ $relatedArticle->image ? asset('storage/' . $relatedArticle->image) : 'https://picsum.photos/800/600?random=' . $relatedArticle->id }}" 
                        alt="{{ $relatedArticle->title }}"
                        class="absolute inset-0 -z-10 h-full w-full object-cover"
                        loading="lazy"
                    >
                    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40"></div>
                    <div class="absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10"></div>

                    <div class="flex flex-wrap items-center gap-y-1 overflow-hidden text-sm leading-6 text-gray-300">
                        <time datetime="{{ $relatedArticle->published_at->format('Y-m-d') }}" class="mr-8">
                            {{ $relatedArticle->published_at->format('M j, Y') }}
                        </time>
                        <div class="-ml-4 flex items-center gap-x-4">
                            <svg viewBox="0 0 2 2" class="-ml-0.5 h-0.5 w-0.5 flex-none fill-white/50">
                                <circle cx="1" cy="1" r="1" />
                            </svg>
                            <div class="flex gap-x-2.5">
                                {{ $relatedArticle->author ?? 'News Team' }}
                            </div>
                        </div>
                    </div>
                    <h3 class="mt-3 text-lg font-semibold leading-6 text-white">
                        <a href="{{ route('news.show', $relatedArticle->slug) }}">
                            <span class="absolute inset-0"></span>
                            {{ Str::limit($relatedArticle->title, 60) }}
                        </a>
                    </h3>
                </article>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
// Share functionality
function shareArticle(platform) {
    const url = window.location.href;
    const title = '{{ addslashes($article->title) }}';
    const text = '{{ addslashes(Str::limit($article->excerpt, 100)) }}';
    
    let shareUrl = '';
    
    switch(platform) {
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`;
            break;
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
            break;
        case 'linkedin':
            shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`;
            break;
        case 'copy':
            navigator.clipboard.writeText(url).then(() => {
                // Show success message
                const button = event.target.closest('button');
                const originalHtml = button.innerHTML;
                button.innerHTML = '<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>';
                setTimeout(() => {
                    button.innerHTML = originalHtml;
                }, 2000);
            });
            return;
    }
    
    if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=400');
    }
}

// Reading progress
function updateReadingProgress() {
    const article = document.querySelector('.prose');
    if (!article) return;
    
    const articleTop = article.offsetTop;
    const articleHeight = article.offsetHeight;
    const windowHeight = window.innerHeight;
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    const progress = Math.min(
        Math.max((scrollTop - articleTop + windowHeight) / articleHeight, 0),
        1
    );
    
    document.getElementById('reading-progress').style.width = `${progress * 100}%`;
}

// Update reading progress on scroll
window.addEventListener('scroll', updateReadingProgress);
window.addEventListener('resize', updateReadingProgress);
document.addEventListener('DOMContentLoaded', updateReadingProgress);
</script>
@endpush
@endsection 