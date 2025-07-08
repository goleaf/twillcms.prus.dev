<section aria-labelledby="related-news-title" class="mt-12">
    <h2 id="related-news-title" class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Related News</h2>
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($relatedArticles->take(6) as $related)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">{{ $related->title }}</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">{{ $related->excerpt }}</p>
                    <a href="{{ route('news.show', $related->slug) }}">
                        <x-button variant="primary">Read More</x-button>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</section> 