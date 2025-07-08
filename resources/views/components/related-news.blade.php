<section aria-labelledby="related-news-title" class="mt-12">
    <h2 id="related-news-title" class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Related News') }}</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($relatedArticles->take(6) as $related)
            <article class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow flex flex-col h-full">
                @if(!empty($related->image))
                    <img src="{{ asset('storage/' . $related->image) }}" alt="{{ $related->title }}" class="h-40 w-full object-cover rounded-t-lg">
                @endif
                <div class="p-6 flex-1 flex flex-col">
                    <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">{{ $related->title }}</h3>
                    <p class="text-gray-600 dark:text-gray-300 flex-1">{{ $related->excerpt }}</p>
                </div>
                <div class="p-6 pt-0">
                    <a href="{{ route('news.show', $related->slug) }}" class="focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-900 transition-shadow">
                        <x-button variant="primary">{{ __('Read More') }}</x-button>
                    </a>
                </div>
            </article>
        @endforeach
    </div>
</section> 