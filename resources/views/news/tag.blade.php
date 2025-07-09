<x-app-layout>
    <div class="relative bg-gray-50 pt-16 pb-20 px-4 sm:px-6 lg:pt-24 lg:pb-28 lg:px-8">
        <div class="relative max-w-7xl mx-auto">
            <div class="text-center">
                <h1 class="text-3xl tracking-tight font-extrabold text-gray-900 sm:text-4xl">{{ $tag->name }}</h1>
                <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4">
                    Articles tagged with "{{ $tag->name }}"
                </p>
            </div>

            <!-- Articles Grid -->
            <div class="mt-12 max-w-lg mx-auto grid gap-5 lg:grid-cols-3 lg:max-w-none">
                @foreach($articles as $article)
                <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
                    <div class="flex-shrink-0">
                        <img class="h-48 w-full object-cover" src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}">
                    </div>
                    <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                        <div class="flex-1">
                            <div class="flex space-x-2">
                                @foreach($article->tags as $articleTag)
                                <a href="{{ route('tags.show', $articleTag->slug) }}" 
                                   class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium {{ $articleTag->id === $tag->id ? 'bg-indigo-600 text-white' : 'bg-indigo-100 text-indigo-800' }}">
                                    {{ $articleTag->name }}
                                </a>
                                @endforeach
                            </div>
                            <a href="{{ route('news.show', $article->slug) }}" class="block mt-2">
                                <p class="text-xl font-semibold text-gray-900">{{ $article->title }}</p>
                                <p class="mt-3 text-base text-gray-500">{{ $article->excerpt }}</p>
                            </a>
                        </div>
                        <div class="mt-6 flex items-center">
                            <div class="flex space-x-1 text-sm text-gray-500">
                                <time datetime="{{ $article->published_at->format('Y-m-d') }}">
                                    {{ $article->published_at->format('M d, Y') }}
                                </time>
                                <span aria-hidden="true">&middot;</span>
                                <span>{{ $article->reading_time }} min read</span>
                                <span aria-hidden="true">&middot;</span>
                                <span>{{ $article->view_count }} views</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $articles->links() }}
            </div>
        </div>
    </div>
</x-app-layout> 