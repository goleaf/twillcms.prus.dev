<template>
  <div class="news-grid-container">
    <!-- Loading State -->
    <div v-if="loading" class="space-y-6">
      <div class="animate-pulse grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div v-for="i in 6" :key="i" class="bg-gray-200 h-64 rounded-lg"></div>
      </div>
    </div>

    <!-- News Grid Layout -->
    <div v-else-if="articles.length > 0" class="news-grid">
      <!-- Featured Section -->
      <section v-if="featuredArticles.length > 0" class="featured-section mb-8 lg:mb-12">
        <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-6 font-serif">
          {{ featuredSectionTitle }}
        </h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
          <!-- Main Featured Article -->
          <div class="lg:col-span-8">
            <ArticleCard
              :article="featuredArticles[0]"
              variant="featured"
              :featured="true"
              :show-read-more="true"
              :show-excerpt="true"
              :excerpt-length="200"
            />
          </div>
          
          <!-- Secondary Featured Articles -->
          <div class="lg:col-span-4 space-y-4">
            <ArticleCard
              v-for="article in featuredArticles.slice(1, 3)"
              :key="article.id"
              :article="article"
              variant="compact"
              :show-excerpt="false"
              :show-meta="false"
            />
          </div>
        </div>
      </section>

      <!-- Three-Column Layout -->
      <div class="news-columns grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
        <!-- Primary Column -->
        <main class="lg:col-span-8 space-y-6">
          <section>
            <header class="border-b border-gray-200 pb-4 mb-6">
              <h2 class="text-xl lg:text-2xl font-bold text-gray-900 font-serif">
                {{ latestSectionTitle }}
              </h2>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <ArticleCard
                v-for="article in latestArticles"
                :key="article.id"
                :article="article"
                variant="default"
                :trending="isTrending(article)"
                :show-excerpt="true"
                :show-meta="true"
                :excerpt-length="100"
              />
            </div>
          </section>
        </main>

        <!-- Sidebar -->
        <aside class="lg:col-span-4 space-y-8">
          <!-- Trending Section -->
          <section v-if="trendingArticles.length > 0" class="bg-gray-50 rounded-lg p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6 font-serif flex items-center">
              <FireIcon class="h-5 w-5 text-red-500 mr-2" />
              Trending Now
            </h2>
            <div class="space-y-4">
              <ArticleCard
                v-for="article in trendingArticles"
                :key="article.id"
                :article="article"
                variant="minimal"
                :trending="true"
                :show-excerpt="false"
                :show-meta="false"
              />
            </div>
          </section>
        </aside>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-16">
      <NewspaperIcon class="h-16 w-16 text-gray-300 mx-auto mb-4" />
      <h3 class="text-lg font-semibold text-gray-900 mb-2">No articles found</h3>
      <p class="text-gray-600">{{ emptyStateMessage }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { FireIcon, NewspaperIcon } from '@heroicons/vue/24/outline'
import ArticleCard from './ArticleCard.vue'

interface Article {
  id: number
  title: string
  slug: string
  excerpt?: string
  featured?: boolean
  trending?: boolean
  view_count?: number
  published_at: string
}

interface Props {
  articles: Article[]
  loading?: boolean
  featuredSectionTitle?: string
  latestSectionTitle?: string
  emptyStateMessage?: string
}

const props = withDefaults(defineProps<Props>(), {
  loading: false,
  featuredSectionTitle: 'Featured Stories',
  latestSectionTitle: 'Latest News',
  emptyStateMessage: 'Check back later for new articles.'
})

const featuredArticles = computed(() => {
  return props.articles.filter(article => article.featured).slice(0, 3)
})

const latestArticles = computed(() => {
  return props.articles.filter(article => !article.featured).slice(0, 8)
})

const trendingArticles = computed(() => {
  return props.articles
    .filter(article => article.trending || (article.view_count && article.view_count > 1000))
    .slice(0, 5)
})

const isTrending = (article: Article): boolean => {
  return article.trending || (article.view_count && article.view_count > 1000) || false
}
</script>

<style scoped>
.news-grid-container {
  font-family: 'Inter', sans-serif;
}

.news-grid-container h1,
.news-grid-container h2,
.news-grid-container h3 {
  font-family: 'Playfair Display', serif;
  font-feature-settings: 'kern' 1, 'liga' 1;
}

/* Responsive Typography */
@media (max-width: 640px) {
  .news-grid-container {
    font-size: 14px;
  }
}

/* Print Styles */
@media print {
  .news-grid-container {
    @apply text-black bg-white;
  }
  
  aside {
    @apply hidden;
  }
}
</style>
