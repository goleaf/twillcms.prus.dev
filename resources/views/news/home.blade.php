@extends('layouts.app')

@section('content')
<div class="bg-white dark:bg-gray-900">
    <!-- Hero Section -->
    <div class="relative overflow-hidden">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center">
                <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-6xl">
                    Stay <span class="text-indigo-600">Informed</span>
                </h1>
                <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300">
                    Discover the latest news, insights, and stories from around the world. Stay updated with our comprehensive coverage of technology, business, science, and more.
                </p>
                <div class="mt-10 flex items-center justify-center gap-x-6">
                    <a href="#latest-news" class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Explore News
                    </a>
                    <a href="{{ route('tags.index') }}" class="text-sm font-semibold leading-6 text-gray-900 dark:text-white">
                        Browse Tags <span aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Articles -->
    @if($featuredArticles->count() > 0)
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
        <div class="mx-auto max-w-2xl lg:max-w-4xl">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">Featured Stories</h2>
            <p class="mt-2 text-lg leading-8 text-gray-600 dark:text-gray-300">The most important stories of the day</p>
            
            <div class="mt-16 space-y-20 lg:mt-20 lg:space-y-20">
                @foreach($featuredArticles->take(6) as $article)
                <article class="relative isolate flex flex-col gap-8 lg:flex-row">
                    <div class="relative aspect-[16/9] sm:aspect-[2/1] lg:aspect-square lg:w-64 lg:shrink-0">
                        <img 
                            src="{{ $article->image ? asset('storage/' . $article->image) : 'https://picsum.photos/800/600?random=' . $article->id }}" 
                            alt="{{ $article->title }}"
                            class="absolute inset-0 h-full w-full rounded-2xl bg-gray-50 object-cover"
                            loading="lazy"
                        >
                        <div class="absolute inset-0 rounded-2xl ring-1 ring-inset ring-gray-900/10"></div>
                    </div>
                    <div>
                        <div class="flex items-center gap-x-4 text-xs">
                            <time datetime="{{ optional($article->published_at)->format('Y-m-d') }}" class="text-gray-500 dark:text-gray-400">
                                {{ optional($article->published_at)->format('M j, Y') }}
                            </time>
                            @if($article->tags->count() > 0)
                                <a href="{{ route('tags.show', $article->tags->first()->slug) }}" class="relative z-10 rounded-full bg-gray-50 dark:bg-gray-800 px-3 py-1.5 font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    {{ $article->tags->first()->name }}
                                </a>
                            @endif
                        </div>
                        <div class="group relative max-w-xl">
                            <h3 class="mt-3 text-lg font-semibold leading-6 text-gray-900 dark:text-white group-hover:text-gray-600 dark:group-hover:text-gray-300">
                                <a href="{{ route('news.show', $article->slug) }}">
                                    <span class="absolute inset-0"></span>
                                    {{ $article->title }}
                                </a>
                            </h3>
                            <p class="mt-5 text-sm leading-6 text-gray-600 dark:text-gray-300">
                                {{ Str::limit($article->excerpt, 150) }}
                            </p>
                        </div>
                        <div class="mt-6 flex border-t border-gray-900/5 dark:border-gray-700 pt-6">
                            <div class="relative flex items-center gap-x-4">
                                <div class="h-10 w-10 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-5.5-2.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zM10 12a5.99 5.99 0 00-4.793 2.39A6.483 6.483 0 0010 16.5a6.483 6.483 0 004.793-2.11A5.99 5.99 0 0010 12z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="text-sm leading-6">
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        <span class="absolute inset-0"></span>
                                        {{ $article->author ?? 'News Team' }}
                                    </p>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $article->view_count }} views</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Latest News -->
    <div id="latest-news" class="bg-gray-50 dark:bg-gray-800 py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">Latest News</h2>
                <p class="mt-2 text-lg leading-8 text-gray-600 dark:text-gray-300">Stay up to date with the most recent stories</p>
            </div>
            
            <div class="mx-auto mt-16 grid max-w-2xl auto-rows-fr grid-cols-1 gap-8 sm:mt-20 lg:mx-0 lg:max-w-none lg:grid-cols-3">
                @foreach($latestArticles as $article)
                <article class="relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 pt-80 sm:pt-48 lg:pt-80">
                    <img 
                        src="{{ $article->image ? asset('storage/' . $article->image) : 'https://picsum.photos/800/600?random=' . $article->id }}" 
                        alt="{{ $article->title }}"
                        class="absolute inset-0 -z-10 h-full w-full object-cover"
                        loading="lazy"
                    >
                    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40"></div>
                    <div class="absolute inset-0 -z-10 rounded-2xl ring-1 ring-inset ring-gray-900/10"></div>

                    <div class="flex flex-wrap items-center gap-y-1 overflow-hidden text-sm leading-6 text-gray-300">
                        <time datetime="{{ optional($article->published_at)->format('Y-m-d') }}" class="mr-8">
                            {{ optional($article->published_at)->format('M j, Y') }}
                        </time>
                        <div class="-ml-4 flex items-center gap-x-4">
                            <svg viewBox="0 0 2 2" class="-ml-0.5 h-0.5 w-0.5 flex-none fill-white/50">
                                <circle cx="1" cy="1" r="1" />
                            </svg>
                            <div class="flex gap-x-2.5">
                                {{ $article->author ?? 'News Team' }}
                            </div>
                        </div>
                    </div>
                    <h3 class="mt-3 text-lg font-semibold leading-6 text-white">
                        <a href="{{ route('news.show', $article->slug) }}">
                            <span class="absolute inset-0"></span>
                            {{ $article->title }}
                        </a>
                    </h3>
                </article>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Popular Tags -->
    @if($popularTags->count() > 0)
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">Popular Topics</h2>
            <p class="mt-2 text-lg leading-8 text-gray-600 dark:text-gray-300">Explore trending topics and categories</p>
        </div>
        
        <div class="mx-auto mt-16 flex max-w-2xl flex-wrap justify-center gap-4 lg:mx-0 lg:max-w-none">
            @foreach($popularTags as $tag)
            <a href="{{ route('tags.show', $tag->slug) }}" 
               class="inline-flex items-center rounded-full px-4 py-2 text-sm font-medium transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
               style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}40;">
                {{ $tag->name }}
                <span class="ml-2 inline-flex items-center rounded-full bg-white/20 px-2 py-0.5 text-xs font-medium">
                    {{ $tag->usage_count }}
                </span>
            </a>
            @endforeach
        </div>
        
        <div class="mt-10 text-center">
            <a href="{{ route('tags.index') }}" class="text-sm font-semibold leading-6 text-indigo-600 hover:text-indigo-500">
                View all tags <span aria-hidden="true">→</span>
            </a>
        </div>
    </div>
    @endif

    <!-- Newsletter Section -->
    <div class="relative isolate overflow-hidden bg-gray-900 py-16 sm:py-24 lg:py-32">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-2">
                <div class="max-w-xl lg:max-w-lg">
                    <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Subscribe to our newsletter</h2>
                    <p class="mt-4 text-lg leading-8 text-gray-300">
                        Get the latest news and insights delivered directly to your inbox. Stay informed with our weekly newsletter.
                    </p>
                    <div class="mt-6 flex max-w-md gap-x-4">
                        <label for="email-address" class="sr-only">Email address</label>
                        <input 
                            id="email-address" 
                            name="email" 
                            type="email" 
                            autocomplete="email" 
                            required 
                            class="min-w-0 flex-auto rounded-md border-0 bg-white/5 px-3.5 py-2 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm sm:leading-6" 
                            placeholder="Enter your email"
                        >
                        <button 
                            type="submit" 
                            class="flex-none rounded-md bg-indigo-500 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500"
                        >
                            Subscribe
                        </button>
                    </div>
                </div>
                <dl class="grid grid-cols-1 gap-x-8 gap-y-10 sm:grid-cols-2 lg:pt-2">
                    <div class="flex flex-col items-start">
                        <div class="rounded-md bg-white/5 p-2 ring-1 ring-white/10">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" />
                            </svg>
                        </div>
                        <dt class="mt-4 font-semibold text-white">Weekly digest</dt>
                        <dd class="mt-2 leading-7 text-gray-400">Get a curated selection of the week's most important stories.</dd>
                    </div>
                    <div class="flex flex-col items-start">
                        <div class="rounded-md bg-white/5 p-2 ring-1 ring-white/10">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H6.911a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661z" />
                            </svg>
                        </div>
                        <dt class="mt-4 font-semibold text-white">No spam</dt>
                        <dd class="mt-2 leading-7 text-gray-400">We respect your privacy and will never share your email address.</dd>
                    </div>
                </dl>
            </div>
        </div>
        <div class="absolute left-1/2 top-0 -z-10 -translate-x-1/2 blur-3xl xl:-top-6" aria-hidden="true">
            <div class="aspect-[1155/678] w-[72.1875rem] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
        </div>
    </div>
</div>
@endsection 