# SYSTEM PATTERNS - ENTERPRISE NEWS ARCHITECTURE

## 🏗️ **REPOSITORY PATTERN ARCHITECTURE**

### **Repository Interface Pattern**
```php
// Base Repository Interface
interface RepositoryInterface 
{
    public function find(int $id): ?Model;
    public function findOrFail(int $id): Model;
    public function all(array $columns = ['*']): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): Model;
    public function update(int $id, array $data): Model;
    public function delete(int $id): bool;
    public function with(array $relations): static;
    public function where(string $column, $operator, $value = null): static;
}

// Model-Specific Repository Interfaces
interface PostRepositoryInterface extends RepositoryInterface 
{
    public function published(): static;
    public function byCategory(int $categoryId): static;
    public function withMetrics(): static;
    public function popular(int $limit = 10): Collection;
    public function recent(int $days = 30): static;
    public function search(string $query): static;
}

interface CategoryRepositoryInterface extends RepositoryInterface 
{
    public function withPostCounts(): static;
    public function navigation(): Collection;
    public function hierarchical(): Collection;
}
```

### **Eloquent Repository Implementation Pattern**
```php
// Base Eloquent Repository
abstract class EloquentRepository implements RepositoryInterface 
{
    protected Model $model;
    protected array $with = [];
    protected array $criteria = [];
    
    public function __construct(Model $model) 
    {
        $this->model = $model;
    }
    
    public function find(int $id): ?Model 
    {
        return $this->query()->find($id);
    }
    
    public function with(array $relations): static 
    {
        $this->with = array_merge($this->with, $relations);
        return $this;
    }
    
    protected function query(): Builder 
    {
        $query = $this->model->newQuery();
        
        if (!empty($this->with)) {
            $query->with($this->with);
        }
        
        foreach ($this->criteria as $criterion) {
            $query = $criterion->apply($query);
        }
        
        return $query;
    }
}
```

## 🎯 **ADVANCED MODEL PATTERNS**

### **Performance-Optimized Scopes**
```php
// Advanced Eloquent Scopes with Caching
trait PerformanceScopes 
{
    public function scopePublishedWithCache($query, int $cacheTtl = 3600) 
    {
        return Cache::tags(['posts', 'published'])
            ->remember("posts.published.{$cacheTtl}", $cacheTtl, function() use ($query) {
                return $query->where('published', true)
                           ->whereNotNull('published_at')
                           ->where('published_at', '<=', now());
            });
    }
    
    public function scopeWithMetrics($query) 
    {
        return $query->addSelect([
            'view_count' => PostMetric::selectRaw('SUM(views)')
                ->whereColumn('post_id', 'posts.id'),
            'comment_count' => Comment::selectRaw('COUNT(*)')
                ->whereColumn('post_id', 'posts.id')
                ->where('approved', true),
            'average_rating' => Rating::selectRaw('AVG(rating)')
                ->whereColumn('post_id', 'posts.id')
        ]);
    }
    
    public function scopePopularThisWeek($query, int $limit = 10) 
    {
        return $query->withMetrics()
            ->where('published_at', '>=', now()->subWeek())
            ->orderByDesc('view_count')
            ->limit($limit);
    }
}
```

### **Advanced Attribute Patterns**
```php
// Smart Attributes with Caching
trait SmartAttributes 
{
    public function getReadingTimeAttribute(): int 
    {
        return Cache::rememberForever("post.{$this->id}.reading_time", function() {
            $wordCount = str_word_count(strip_tags($this->content ?? ''));
            return max(1, ceil($wordCount / 200));
        });
    }
    
    public function getExcerptAttribute(): string 
    {
        return Cache::rememberForever("post.{$this->id}.excerpt", function() {
            return Str::limit(strip_tags($this->content), 160);
        });
    }
    
    public function getSeoTitleAttribute(): string 
    {
        return $this->seo_title ?: $this->title;
    }
    
    public function getCanonicalUrlAttribute(): string 
    {
        return route('blog.post', $this->slug);
    }
}
```

### **Model Observer Patterns**
```php
// Performance-Focused Model Observers
class PostObserver 
{
    public function created(Post $post): void 
    {
        // Clear relevant caches
        Cache::tags(['posts', 'sitemap'])->flush();
        
        // Update search index
        dispatch(new UpdateSearchIndex($post));
        
        // Generate social media cards
        dispatch(new GenerateSocialCards($post));
    }
    
    public function updated(Post $post): void 
    {
        // Clear model-specific cache
        Cache::forget("post.{$post->id}.reading_time");
        Cache::forget("post.{$post->id}.excerpt");
        
        // Update search index if content changed
        if ($post->wasChanged(['title', 'content', 'description'])) {
            dispatch(new UpdateSearchIndex($post));
        }
    }
}
```

## 🎨 **VUE.JS 3 COMPOSITION PATTERNS**

### **Advanced Store Architecture (Pinia)**
```typescript
// Reactive Store with Composables
export const useBlogStore = defineStore('blog', () => {
  // State
  const posts = ref<Post[]>([]);
  const featuredPosts = ref<Post[]>([]);
  const categories = ref<Category[]>([]);
  const isLoading = ref(false);
  const searchQuery = ref('');
  
  // Computed
  const publishedPosts = computed(() => 
    posts.value.filter(post => post.published && new Date(post.published_at) <= new Date())
  );
  
  const categorizedPosts = computed(() => 
    categories.value.reduce((acc, category) => ({
      ...acc,
      [category.slug]: posts.value.filter(post => 
        post.categories.some(cat => cat.id === category.id)
      )
    }), {} as Record<string, Post[]>)
  );
  
  // Actions
  const fetchPosts = async (params: FetchPostsParams = {}) => {
    isLoading.value = true;
    try {
      const response = await blogApi.getPosts(params);
      posts.value = params.append ? [...posts.value, ...response.data] : response.data;
      return response;
    } finally {
      isLoading.value = false;
    }
  };
  
  const searchPosts = useDebounceFn(async (query: string) => {
    searchQuery.value = query;
    if (query.length >= 3) {
      await fetchPosts({ search: query });
    }
  }, 300);
  
  return {
    // State
    posts: readonly(posts),
    featuredPosts: readonly(featuredPosts), 
    categories: readonly(categories),
    isLoading: readonly(isLoading),
    
    // Computed
    publishedPosts,
    categorizedPosts,
    
    // Actions
    fetchPosts,
    searchPosts,
  };
});
```

### **Composable Architecture**
```typescript
// Performance-Optimized Composables
export function useInfiniteScroll<T>(
  fetchFunction: (page: number) => Promise<PaginatedResponse<T>>,
  options: InfiniteScrollOptions = {}
) {
  const items = ref<T[]>([]);
  const isLoading = ref(false);
  const isComplete = ref(false);
  const currentPage = ref(1);
  const error = ref<string | null>(null);
  
  const loadMore = async () => {
    if (isLoading.value || isComplete.value) return;
    
    isLoading.value = true;
    error.value = null;
    
    try {
      const response = await fetchFunction(currentPage.value);
      
      if (response.data.length === 0) {
        isComplete.value = true;
      } else {
        items.value.push(...response.data);
        currentPage.value++;
      }
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'An error occurred';
    } finally {
      isLoading.value = false;
    }
  };
  
  // Intersection Observer for automatic loading
  const { stop } = useIntersectionObserver(
    options.trigger,
    ([{ isIntersecting }]) => {
      if (isIntersecting && !isLoading.value && !isComplete.value) {
        loadMore();
      }
    },
    { threshold: 0.1 }
  );
  
  return {
    items: readonly(items),
    isLoading: readonly(isLoading),
    isComplete: readonly(isComplete),
    error: readonly(error),
    loadMore,
    reset: () => {
      items.value = [];
      currentPage.value = 1;
      isComplete.value = false;
      error.value = null;
    },
    stop
  };
}
```

## 🎨 **TAILWINDCSS DESIGN SYSTEM PATTERNS**

### **Custom Design Tokens**
```javascript
// tailwind.config.js - News-Focused Design System
module.exports = {
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#eff6ff',
          500: '#3b82f6',
          900: '#1e3a8a',
        },
        news: {
          accent: '#ef4444',
          secondary: '#6b7280',
          success: '#10b981',
        }
      },
      typography: {
        'news': {
          css: {
            maxWidth: 'none',
            color: '#374151',
            lineHeight: '1.75',
            '[class~="lead"]': {
              fontSize: '1.25rem',
              lineHeight: '1.6',
            },
            h1: {
              fontSize: '2.5rem',
              fontWeight: '800',
              lineHeight: '1.2',
            },
            h2: {
              fontSize: '2rem',
              fontWeight: '700',
              lineHeight: '1.3',
            },
            'blockquote': {
              borderLeftColor: '#ef4444',
              borderLeftWidth: '4px',
              paddingLeft: '1rem',
              fontStyle: 'italic',
            }
          }
        }
      },
      animation: {
        'fade-in': 'fadeIn 0.5s ease-in-out',
        'slide-up': 'slideUp 0.3s ease-out',
        'pulse-subtle': 'pulseSubtle 2s infinite',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0', transform: 'translateY(10px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        slideUp: {
          '0%': { transform: 'translateY(100%)' },
          '100%': { transform: 'translateY(0)' },
        },
        pulseSubtle: {
          '0%, 100%': { opacity: '1' },
          '50%': { opacity: '0.8' },
        }
      }
    }
  }
}
```

### **Component Architecture Patterns**
```vue
<!-- News Article Card Component -->
<template>
  <article 
    class="group relative bg-white rounded-lg shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden"
    :class="[
      layoutClass,
      priorityClass,
      { 'border-l-4 border-news-accent': isFeatured }
    ]"
  >
    <!-- Image Container -->
    <div 
      v-if="post.image_url" 
      class="relative overflow-hidden"
      :class="imageContainerClass"
    >
      <img 
        :src="post.image_url" 
        :alt="post.title"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
        loading="lazy"
      />
      <div v-if="isFeatured" class="absolute top-4 left-4">
        <span class="bg-news-accent text-white px-3 py-1 rounded-full text-sm font-medium">
          Featured
        </span>
      </div>
    </div>
    
    <!-- Content Container -->
    <div class="p-6">
      <!-- Category Badge -->
      <div v-if="post.categories?.length" class="mb-3">
        <span 
          v-for="category in post.categories.slice(0, 2)" 
          :key="category.id"
          class="inline-block bg-primary-50 text-primary-700 text-xs px-2 py-1 rounded-full mr-2"
        >
          {{ category.title }}
        </span>
      </div>
      
      <!-- Title -->
      <h3 class="font-bold mb-3 group-hover:text-primary-600 transition-colors">
        <router-link 
          :to="{ name: 'blog.post', params: { slug: post.slug } }"
          class="stretched-link"
        >
          {{ post.title }}
        </router-link>
      </h3>
      
      <!-- Excerpt -->
      <p v-if="showExcerpt" class="text-gray-600 mb-4 line-clamp-3">
        {{ post.excerpt }}
      </p>
      
      <!-- Meta Information -->
      <div class="flex items-center justify-between text-sm text-gray-500">
        <time :datetime="post.published_at">
          {{ formatDate(post.published_at) }}
        </time>
        <span v-if="post.reading_time">
          {{ post.reading_time }} min read
        </span>
      </div>
    </div>
  </article>
</template>

<script setup lang="ts">
interface Props {
  post: Post;
  layout?: 'card' | 'horizontal' | 'minimal' | 'hero';
  priority?: 'high' | 'medium' | 'low';
  showExcerpt?: boolean;
  isFeatured?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  layout: 'card',
  priority: 'medium',
  showExcerpt: true,
  isFeatured: false,
});

const layoutClass = computed(() => {
  switch (props.layout) {
    case 'horizontal': return 'flex flex-row';
    case 'minimal': return 'border-b border-gray-200 pb-4';
    case 'hero': return 'lg:col-span-2 lg:row-span-2';
    default: return 'flex flex-col';
  }
});

const priorityClass = computed(() => {
  switch (props.priority) {
    case 'high': return 'ring-2 ring-primary-500';
    case 'low': return 'opacity-75';
    default: return '';
  }
});

const imageContainerClass = computed(() => {
  switch (props.layout) {
    case 'horizontal': return 'w-1/3 h-32';
    case 'hero': return 'h-64 lg:h-80';
    default: return 'h-48';
  }
});
</script>
```

## 🔧 **SERVICE LAYER PATTERNS**

### **Business Logic Abstraction**
```php
// Content Service Layer
class ContentService 
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private CategoryRepositoryInterface $categoryRepository,
        private SearchService $searchService,
        private CacheService $cacheService
    ) {}
    
    public function getFeaturedContent(): array 
    {
        return $this->cacheService->remember('featured.content', 3600, function() {
            return [
                'hero_post' => $this->postRepository->featured()->first(),
                'trending_posts' => $this->postRepository->trending(5)->get(),
                'featured_categories' => $this->categoryRepository->featured()->get(),
            ];
        });
    }
    
    public function getPersonalizedFeed(User $user): LengthAwarePaginator 
    {
        $preferences = $user->contentPreferences();
        
        return $this->postRepository
            ->published()
            ->byCategories($preferences->preferred_categories)
            ->excludeReadPosts($user)
            ->withMetrics()
            ->orderByRelevance($preferences)
            ->paginate(15);
    }
}
```

## 📊 **PERFORMANCE PATTERNS**

### **Caching Strategy**
```php
// Multi-Layer Caching Pattern
class CacheService 
{
    private const CACHE_TAGS = [
        'posts' => ['posts', 'content'],
        'categories' => ['categories', 'navigation'],
        'pages' => ['pages', 'static'],
        'sitemap' => ['sitemap', 'seo'],
    ];
    
    public function invalidateContentCache(): void 
    {
        Cache::tags(['posts', 'categories', 'sitemap'])->flush();
    }
    
    public function warmupCache(): void 
    {
        // Warm up critical paths
        app(ContentService::class)->getFeaturedContent();
        app(CategoryRepositoryInterface::class)->navigation();
        app(PostRepositoryInterface::class)->popular(10);
    }
}
```

This comprehensive system architecture provides enterprise-grade patterns for repository implementation, advanced model optimization, modern Vue.js composition, and performance-focused caching strategies. 