<template>
  <article 
    class="group relative bg-white rounded-lg border border-gray-200 hover:border-gray-300 transition-all duration-300 hover:shadow-lg overflow-hidden"
    :class="[
      cardVariant,
      { 'featured-article': featured },
      { 'trending-pulse': trending }
    ]"
  >
    <!-- Featured Image -->
    <div 
      class="relative overflow-hidden"
      :class="imageClass"
    >
      <img
        v-if="article.featured_image"
        :src="getImageUrl(article.featured_image)"
        :alt="article.featured_image.alt || article.title"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
        loading="lazy"
      />
      <div 
        v-else 
        class="w-full h-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center"
      >
        <NewspaperIcon class="h-12 w-12 text-slate-400" />
      </div>

      <!-- Category Badge -->
      <div 
        v-if="article.category" 
        class="absolute top-3 left-3 z-10"
      >
        <span 
          class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold tracking-wide uppercase backdrop-blur-sm border border-white/20 shadow-sm"
          :class="getCategoryStyle(article.category)"
        >
          {{ article.category.name }}
        </span>
      </div>

      <!-- Reading Time Badge -->
      <div 
        v-if="article.reading_time" 
        class="absolute top-3 right-3 z-10"
      >
        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-black/60 text-white backdrop-blur-sm">
          <ClockIcon class="h-3 w-3 mr-1" />
          {{ article.reading_time }} min
        </span>
      </div>

      <!-- Trending Badge -->
      <div 
        v-if="trending" 
        class="absolute bottom-3 left-3 z-10"
      >
        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold bg-red-500 text-white">
          <FireIcon class="h-3 w-3 mr-1" />
          {{ $t('news.trending') }}
        </span>
      </div>
    </div>

    <!-- Content -->
    <div class="p-4 lg:p-6">
      <!-- Meta Information -->
      <div class="flex items-center justify-between mb-3 text-sm text-slate-600">
        <time 
          :datetime="article.published_at"
          class="flex items-center font-medium"
        >
          <CalendarIcon class="h-4 w-4 mr-1.5" />
          {{ formatDate(article.published_at) }}
        </time>
        <span 
          v-if="article.view_count" 
          class="flex items-center"
        >
          <EyeIcon class="h-4 w-4 mr-1" />
          {{ formatNumber(article.view_count) }}
        </span>
      </div>

      <!-- Title -->
      <h3 
        class="font-bold mb-3 group-hover:text-blue-600 transition-colors duration-200 leading-tight"
        :class="titleClass"
      >
        <router-link 
          :to="`/blog/${article.slug}`"
          class="hover:text-blue-600 focus:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-sm"
        >
          {{ article.title }}
        </router-link>
      </h3>

      <!-- Excerpt -->
      <p 
        v-if="article.excerpt && showExcerpt" 
        class="text-slate-600 mb-4 leading-relaxed"
        :class="excerptClass"
      >
        {{ truncateText(article.excerpt, excerptLength) }}
      </p>

      <!-- Author & Tags -->
      <div 
        v-if="showMeta" 
        class="flex items-center justify-between pt-4 border-t border-slate-100"
      >
        <div 
          v-if="article.author" 
          class="flex items-center"
        >
          <img
            v-if="article.author.avatar"
            :src="article.author.avatar"
            :alt="article.author.name"
            class="h-8 w-8 rounded-full mr-3 object-cover"
          />
          <div class="text-sm">
            <p class="font-medium text-slate-900">{{ article.author.name }}</p>
            <p class="text-slate-500">{{ article.author.title }}</p>
          </div>
        </div>

        <div 
          v-if="article.tags && article.tags.length > 0" 
          class="flex flex-wrap gap-1"
        >
          <span
            v-for="tag in article.tags.slice(0, 2)"
            :key="tag.id"
            class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors"
          >
            {{ tag.name }}
          </span>
        </div>
      </div>

      <!-- Read More Button for Featured Articles -->
      <div 
        v-if="featured && showReadMore" 
        class="mt-4"
      >
        <router-link
          :to="`/blog/${article.slug}`"
          class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors group"
        >
          {{ $t('news.read_full_story') }}
          <ArrowRightIcon class="h-4 w-4 ml-1 group-hover:translate-x-1 transition-transform" />
        </router-link>
      </div>
    </div>

    <!-- Performance Indicator (Dev Mode) -->
    <div 
      v-if="isDev && article.cached" 
      class="absolute bottom-2 right-2 z-10"
    >
      <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-mono bg-green-100 text-green-700">
        ⚡ Cached
      </span>
    </div>
  </article>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { 
  NewspaperIcon, 
  ClockIcon, 
  CalendarIcon, 
  EyeIcon, 
  FireIcon,
  ArrowRightIcon 
} from '@heroicons/vue/24/outline'

// Types
interface Article {
  id: number
  title: string
  slug: string
  excerpt?: string
  featured_image?: {
    url: string
    thumb?: string
    alt?: string
  }
  category?: {
    id: number
    name: string
    color: string
    slug: string
  }
  author?: {
    id: number
    name: string
    title?: string
    avatar?: string
  }
  tags?: Array<{
    id: number
    name: string
    slug: string
  }>
  published_at: string
  view_count?: number
  reading_time?: number
  cached?: boolean
}

// Props
interface Props {
  article: Article
  variant?: 'default' | 'featured' | 'compact' | 'minimal'
  featured?: boolean
  trending?: boolean
  showExcerpt?: boolean
  showMeta?: boolean
  showReadMore?: boolean
  excerptLength?: number
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'default',
  featured: false,
  trending: false,
  showExcerpt: true,
  showMeta: true,
  showReadMore: false,
  excerptLength: 120
})

// Computed Classes
const cardVariant = computed(() => {
  switch (props.variant) {
    case 'featured':
      return 'lg:col-span-2 lg:row-span-2'
    case 'compact':
      return 'flex lg:flex-col'
    case 'minimal':
      return 'border-none shadow-none hover:shadow-sm'
    default:
      return ''
  }
})

const imageClass = computed(() => {
  switch (props.variant) {
    case 'featured':
      return 'aspect-w-16 aspect-h-9 lg:h-80'
    case 'compact':
      return 'w-24 h-24 lg:w-full lg:h-40 flex-shrink-0'
    case 'minimal':
      return 'aspect-w-16 aspect-h-9 h-32'
    default:
      return 'aspect-w-16 aspect-h-9 h-48'
  }
})

const titleClass = computed(() => {
  switch (props.variant) {
    case 'featured':
      return 'text-2xl lg:text-3xl'
    case 'compact':
      return 'text-sm lg:text-base'
    case 'minimal':
      return 'text-base'
    default:
      return 'text-lg lg:text-xl'
  }
})

const excerptClass = computed(() => {
  switch (props.variant) {
    case 'featured':
      return 'text-base lg:text-lg'
    case 'compact':
      return 'text-sm'
    case 'minimal':
      return 'text-sm'
    default:
      return 'text-sm'
  }
})

// Utility Functions
const isDev = computed(() => import.meta.env.DEV)

const getImageUrl = (image: Article['featured_image']) => {
  if (!image) return ''
  return image.thumb || image.url
}

const getCategoryStyle = (category: Article['category']) => {
  if (!category) return 'bg-white/90 text-slate-700'
  
  // Extract color values for dynamic styling
  const color = category.color || '#6366f1'
  return {
    backgroundColor: `${color}20`,
    color: color,
    borderColor: `${color}40`
  }
}

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffInHours = (now.getTime() - date.getTime()) / (1000 * 60 * 60)
  
  if (diffInHours < 24) {
    return `${Math.floor(diffInHours)}h ago`
  } else if (diffInHours < 168) { // 7 days
    return `${Math.floor(diffInHours / 24)}d ago`
  } else {
    return date.toLocaleDateString('en-US', { 
      month: 'short', 
      day: 'numeric',
      year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
    })
  }
}

const formatNumber = (num: number) => {
  if (num >= 1000000) {
    return (num / 1000000).toFixed(1) + 'M'
  } else if (num >= 1000) {
    return (num / 1000).toFixed(1) + 'K'
  }
  return num.toString()
}

const truncateText = (text: string, length: number) => {
  if (!text || text.length <= length) return text
  return text.substring(0, length).trim() + '...'
}
</script>

<style scoped>
.featured-article {
  @apply ring-2 ring-blue-100 ring-offset-2;
}

.trending-pulse {
  animation: pulse-border 2s infinite;
}

@keyframes pulse-border {
  0%, 100% {
    @apply ring-2 ring-red-100;
  }
  50% {
    @apply ring-4 ring-red-200;
  }
}

/* News Typography */
.article-card h3 {
  font-family: 'Playfair Display', serif;
  font-feature-settings: 'kern' 1, 'liga' 1;
}

/* WCAG 2.1 AA Compliance */
.article-card:focus-within {
  @apply ring-2 ring-blue-500 ring-offset-2;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .article-card {
    @apply border-2 border-black;
  }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .article-card *,
  .article-card *::before,
  .article-card *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
</style> 