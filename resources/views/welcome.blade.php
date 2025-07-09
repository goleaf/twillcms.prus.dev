@extends('layouts.app')

@section('title', 'Home - News Portal')
@section('meta')
<meta name="description" content="Stay informed with the latest news, breaking stories, and expert analysis. Your trusted source for current events, business, technology, sports, and more.">
@endsection

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-900">
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-blue-600 via-purple-600 to-indigo-700 text-white overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-black/20"></div>
        <svg class="absolute inset-0 w-full h-full" preserveAspectRatio="xMidYMid slice" viewBox="0 0 1024 1024">
            <defs>
                <pattern id="hero-pattern" x="0" y="0" width="32" height="32" patternUnits="userSpaceOnUse">
                    <circle cx="16" cy="16" r="1" fill="currentColor" opacity="0.1" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#hero-pattern)" />
        </svg>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                    Stay Ahead of the <span class="text-yellow-400">Curve</span>
                </h1>
                <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto opacity-90">
                    Breaking news, expert analysis, and in-depth coverage of the stories that matter most.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('news.index') }}" class="inline-flex items-center px-8 py-4 bg-white text-blue-600 rounded-lg font-semibold hover:bg-gray-100 transition-colors transform hover:scale-105">
                        Latest News
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="{{ route('tags.index') }}" class="inline-flex items-center px-8 py-4 border-2 border-white text-white rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-all transform hover:scale-105">
                        Browse Topics
                    </a>
                </div>
            </div>
            
            <!-- Stats -->
            <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="bg-white/10 backdrop-blur-lg rounded-lg p-6">
                    <div class="text-3xl font-bold">{{ $stats['total_articles'] ?? 0 }}</div>
                    <div class="text-sm opacity-80">Articles</div>
                </div>
                <div class="bg-white/10 backdrop-blur-lg rounded-lg p-6">
                    <div class="text-3xl font-bold">{{ $stats['total_tags'] ?? 0 }}</div>
                    <div class="text-sm opacity-80">Topics</div>
                </div>
                <div class="bg-white/10 backdrop-blur-lg rounded-lg p-6">
                    <div class="text-3xl font-bold">{{ $stats['featured_articles'] ?? 0 }}</div>
                    <div class="text-sm opacity-80">Featured</div>
                </div>
                <div class="bg-white/10 backdrop-blur-lg rounded-lg p-6">
                    <div class="text-3xl font-bold">24/7</div>
                    <div class="text-sm opacity-80">Coverage</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Article -->
    @if(isset($featuredArticle) && $featuredArticle)
    <section class="py-16 bg-gray-50 dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Featured Story</h2>
                <p class="text-xl text-gray-600 dark:text-gray-300">Don't miss our top story of the day</p>
            </div>
            
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl overflow-hidden">
                <div class="lg:flex">
                    <div class="lg:w-1/2">
                        @if($featuredArticle->featured_image)
                            <img src="{{ $featuredArticle->featured_image }}" alt="{{ $featuredArticle->title }}" class="w-full h-64 lg:h-full object-cover">
                        @else
                            <div class="w-full h-64 lg:h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                <svg class="w-24 h-24 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="lg:w-1/2 p-8 lg:p-12 flex flex-col justify-center">
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach($featuredArticle->tags->take(3) as $tag)
                                <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }};">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ $featuredArticle->title }}</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-6 text-lg leading-relaxed">{{ $featuredArticle->excerpt }}</p>
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <span>{{ $featuredArticle->published_at ? $featuredArticle->published_at->format('M j, Y') : 'N/A' }}</span>
                                <span class="mx-2">•</span>
                                <span>{{ $featuredArticle->reading_time ?? 5 }} min read</span>
                            </div>
                            <a href="{{ route('news.show', $featuredArticle->slug) }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                                Read Story
                                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Latest News -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-12">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Latest News</h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300">Stay updated with our most recent stories</p>
                </div>
                <a href="{{ route('news.index') }}" class="hidden md:inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    View All News
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            @if(isset($latestArticles) && $latestArticles->count() > 0)
                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($latestArticles->take(6) as $article)
                        <article class="bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden group transform hover:-translate-y-1">
                            <div class="aspect-w-16 aspect-h-9 bg-gray-200 dark:bg-gray-700">
                                @if($article->featured_image)
                                    <img src="{{ $article->featured_image }}" alt="{{ $article->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-48 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
                                        <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-6">
                                @if($article->tags->count() > 0)
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        @foreach($article->tags->take(2) as $tag)
                                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }};">
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    <a href="{{ route('news.show', $article->slug) }}">{{ $article->title }}</a>
                                </h3>
                                <p class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-3">{{ $article->excerpt }}</p>
                                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                    <span>{{ $article->published_at ? $article->published_at->format('M j, Y') : 'N/A' }}</span>
                                    <span>{{ $article->reading_time ?? 5 }} min read</span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                
                <div class="text-center mt-12 md:hidden">
                    <a href="{{ route('news.index') }}" class="inline-flex items-center px-8 py-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                        View All News
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-24 w-24 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No articles available</h3>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">Check back soon for the latest news and stories.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Topics Section -->
    <section class="py-16 bg-gray-50 dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Explore Topics</h2>
                <p class="text-xl text-gray-600 dark:text-gray-300">Discover news by your interests</p>
            </div>

            @if(isset($popularTags) && $popularTags->count() > 0)
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($popularTags->take(8) as $tag)
                        <a href="{{ route('tags.show', $tag->slug) }}" class="group bg-white dark:bg-gray-900 rounded-xl p-6 text-center hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 border border-gray-200 dark:border-gray-700">
                            <div class="w-12 h-12 mx-auto mb-4 rounded-full flex items-center justify-center" style="background-color: {{ $tag->color }}20;">
                                <svg class="w-6 h-6" style="color: {{ $tag->color }};" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                {{ $tag->name }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $tag->articles_count ?? 0 }} articles
                            </p>
                        </a>
                    @endforeach
                </div>
                
                <div class="text-center mt-12">
                    <a href="{{ route('tags.index') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        View All Topics
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="py-16 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold mb-4">Never Miss a Story</h2>
            <p class="text-xl mb-8 opacity-90">Get the latest breaking news, expert analysis, and exclusive content delivered straight to your inbox.</p>
            
            <form class="max-w-md mx-auto flex gap-4">
                <input type="email" placeholder="Your email address" class="flex-1 px-4 py-3 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-white/20">
                <button type="submit" class="px-6 py-3 bg-white text-blue-600 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    Subscribe
                </button>
            </form>
            
            <p class="text-sm mt-4 opacity-70">
                By subscribing, you agree to our privacy policy. We respect your privacy and will never share your email address.
            </p>
        </div>
    </section>
</div>
@endsection 