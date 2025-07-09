@props(['route' => 'search'])

<a href="#search-overlay" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 bg-indigo-700 text-white px-4 py-2 rounded z-50">Skip to search</a>
<div
    x-data="{
        show: false,
        query: '',
        results: [],
        loading: false,
        selectedIndex: -1,
        lastActive: null,
        trapFocus(e) {
            if (!this.show) return;
            const focusable = $el.querySelectorAll('a, button, textarea, input, select, [tabindex]:not([tabindex="-1"])');
            if (!focusable.length) return;
            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            if (e.key === 'Tab') {
                if (e.shiftKey) {
                    if (document.activeElement === first) {
                        e.preventDefault();
                        last.focus();
                    }
                } else {
                    if (document.activeElement === last) {
                        e.preventDefault();
                        first.focus();
                    }
                }
            }
        },
        focusFirst() {
            this.$nextTick(() => {
                const focusable = $el.querySelectorAll('a, button, textarea, input, select, [tabindex]:not([tabindex="-1"])');
                if (focusable.length) focusable[0].focus();
            });
        },
        init() {
            this.$watch('show', value => {
                if (value) {
                    this.lastActive = document.activeElement;
                    this.$nextTick(() => this.$refs.searchInput.focus());
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                    if (this.lastActive) this.lastActive.focus();
                }
            });
        },
        
        search: debounce(function() {
            if (this.query.length < 2) {
                this.results = [];
                return;
            }
            
            this.loading = true;
            fetch(`/search?q=${encodeURIComponent(this.query)}&ajax=1`)
                .then(response => response.json())
                .then(data => {
                    this.results = data.articles || data;
                    this.loading = false;
                })
                .catch(() => {
                    this.results = [];
                    this.loading = false;
                });
        }, 300),
        
        onKeyDown(e) {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.selectedIndex = Math.min(this.selectedIndex + 1, this.results.length - 1);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.selectedIndex = Math.max(this.selectedIndex - 1, -1);
            } else if (e.key === 'Enter' && this.selectedIndex > -1) {
                e.preventDefault();
                window.location.href = this.results[this.selectedIndex].url;
            }
        }
    }"
    x-on:toggle-search.window="show = !show"
    x-on:keydown.escape="show = false"
    x-on:keydown.tab="trapFocus($event)"
    x-show="show"
    class="fixed inset-0 z-50"
    style="display: none;"
    id="search-overlay"
    role="dialog"
    aria-modal="true"
    aria-labelledby="search-overlay-title"
>
    <!-- Backdrop -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"
        @click="show = false"
    ></div>

    <!-- Search Modal -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative mx-auto mt-[10vh] max-w-xl transform px-4"
    >
        <div class="overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black/5 dark:bg-gray-900 dark:ring-white/10">
            <!-- Visually hidden heading for ARIA -->
            <h2 id="search-overlay-title" class="sr-only">Search</h2>
            <!-- Close Button -->
            <button type="button" @click="show = false" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 bg-white dark:bg-gray-900 rounded-full p-2 z-10" aria-label="Close search">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <!-- Search Input -->
            <div class="relative">
                <label for="search-input" class="sr-only">Search articles</label>
                <svg class="pointer-events-none absolute left-4 top-3.5 h-5 w-5 text-gray-400 dark:text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
                <input
                    id="search-input"
                    x-ref="searchInput"
                    x-model="query"
                    x-on:input="search()"
                    x-on:keydown="onKeyDown"
                    type="text"
                    class="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-900 placeholder:text-gray-400 focus:ring-0 dark:text-white sm:text-sm"
                    placeholder="Search articles..."
                    autocomplete="off"
                >
            </div>

            <!-- Loading State -->
            <div
                x-show="loading"
                class="border-t border-gray-100 px-4 py-6 text-center text-sm dark:border-gray-800"
            >
                <svg class="mx-auto h-5 w-5 animate-spin text-gray-400 dark:text-gray-500" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <!-- No Results -->
            <div
                x-show="query.length > 1 && !loading && results.length === 0"
                class="border-t border-gray-100 px-4 py-6 text-center text-sm dark:border-gray-800"
            >
                <p class="text-gray-500 dark:text-gray-400">No results found for "<span x-text="query"></span>"</p>
            </div>

            <!-- Results -->
            <ul
                x-show="results.length > 0"
                class="max-h-[32rem] divide-y divide-gray-100 overflow-y-auto dark:divide-gray-800"
                role="listbox"
                aria-live="polite"
            >
                <template x-for="(result, index) in results" :key="result.id">
                    <li>
                        <a
                            :href="result.url"
                            class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800"
                            :class="{ 'bg-gray-50 dark:bg-gray-800': index === selectedIndex }"
                            @mouseenter="selectedIndex = index"
                            :aria-selected="index === selectedIndex ? 'true' : 'false'"
                            tabindex="-1"
                        >
                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="result.title"></p>
                            <p class="mt-1 truncate text-sm text-gray-500" x-text="result.excerpt"></p>
                        </a>
                    </li>
                </template>
            </ul>

            <!-- Footer -->
            <div class="flex items-center justify-between border-t border-gray-100 px-4 py-2 dark:border-gray-800">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Press <kbd class="font-sans">↑</kbd> <kbd class="font-sans">↓</kbd> to navigate,
                    <kbd class="font-sans">Enter</kbd> to select,
                    <kbd class="font-sans">Esc</kbd> to close
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func.apply(this, args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>
@endpush 