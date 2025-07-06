<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Loading State -->
    <div v-if="blogStore.loading.post" class="animate-pulse">
      <div class="h-96 bg-gray-200 mb-8"></div>
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="h-8 bg-gray-200 rounded mb-4"></div>
        <div class="h-4 bg-gray-200 rounded mb-8 w-1/2"></div>
        <div class="space-y-4">
          <div class="h-4 bg-gray-200 rounded"></div>
          <div class="h-4 bg-gray-200 rounded"></div>
          <div class="h-4 bg-gray-200 rounded w-3/4"></div>
        </div>
      </div>
    </div>

    <!-- Post Content -->
    <article v-else-if="blogStore.currentPost" class="relative">
      <!-- Hero Section with Image and Overlay -->
      <div class="relative h-96 lg:h-[500px] overflow-hidden">
        <img
          v-if="blogStore.currentPost.featured_image"
          :src="blogStore.currentPost.featured_image.large || blogStore.currentPost.featured_image.url"
          :alt="blogStore.currentPost.featured_image.alt || blogStore.currentPost.title"
          class="w-full h-full object-cover"
        />
        <div v-else class="w-full h-full bg-gradient-to-br from-blue-600 to-purple-600"></div>
        
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
        
        <!-- Hero Content -->
        <div class="absolute inset-0 flex items-end">
          <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 w-full">
            <div class="text-white">
              <!-- Categories -->
              <div v-if="blogStore.currentPost.categories?.length" class="flex flex-wrap gap-2 mb-4">
                <span
                  v-for="category in blogStore.currentPost.categories.slice(0, 2)"
                  :key="category.id"
                  class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 backdrop-blur-sm"
                >
                  {{ category.name }}
                </span>
              </div>
              
              <!-- Title -->
              <h1 class="text-4xl lg:text-6xl font-bold mb-6 leading-tight">
                {{ blogStore.currentPost.title }}
              </h1>
              
              <!-- Meta Information -->
              <div class="flex flex-wrap items-center gap-6 text-lg">
                <div class="flex items-center">
                  <CalendarIcon class="h-5 w-5 mr-2" />
                  <time :datetime="blogStore.currentPost.published_at">
                    {{ formatDate(blogStore.currentPost.published_at) }}
                  </time>
                </div>
                
                <div v-if="blogStore.currentPost.reading_time" class="flex items-center">
                  <ClockIcon class="h-5 w-5 mr-2" />
                  <span>{{ blogStore.currentPost.reading_time }} min read</span>
                </div>
                
                <div v-if="blogStore.currentPost.view_count" class="flex items-center">
                  <EyeIcon class="h-5 w-5 mr-2" />
                  <span>{{ formatNumber(blogStore.currentPost.view_count) }} views</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content Container -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 relative z-10">
        <div class="lg:grid lg:grid-cols-12 lg:gap-8">
          <!-- Main Content -->
          <main class="lg:col-span-8">
            <!-- Content Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8 lg:p-12 mb-8">
              <!-- Excerpt -->
              <div v-if="blogStore.currentPost.excerpt" class="text-xl text-gray-600 leading-relaxed mb-8 p-6 bg-gray-50 rounded-xl border-l-4 border-blue-500">
                {{ blogStore.currentPost.excerpt }}
              </div>

              <!-- Article Content -->
              <div 
                class="prose prose-lg max-w-none prose-headings:font-bold prose-headings:text-gray-900 prose-p:text-gray-700 prose-p:leading-relaxed prose-a:text-blue-600 prose-a:no-underline hover:prose-a:underline prose-img:rounded-xl prose-img:shadow-lg prose-code:bg-gray-100 prose-code:px-2 prose-code:py-1 prose-code:rounded"
                v-html="blogStore.currentPost.content"
              ></div>
            </div>

            <!-- Social Sharing -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Share this article</h3>
              <div class="flex space-x-4">
                <button
                  @click="shareToTwitter"
                  class="flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
                >
                  <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                  </svg>
                  Twitter
                </button>
                
                <button
                  @click="shareToFacebook"
                  class="flex items-center px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 transition-colors"
                >
                  <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                  </svg>
                  Facebook
                </button>
                
                <button
                  @click="shareToLinkedIn"
                  class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                  <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                  </svg>
                  LinkedIn
                </button>
                
                <button
                  @click="copyLink"
                  class="flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
                >
                  <LinkIcon class="w-5 h-5 mr-2" />
                  Copy Link
                </button>
              </div>
            </div>

            <!-- Author Bio -->
            <div v-if="blogStore.currentPost.author" class="bg-white rounded-2xl shadow-lg p-6 mb-8">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">About the Author</h3>
              <div class="flex items-start space-x-4">
                <img
                  v-if="blogStore.currentPost.author.avatar"
                  :src="blogStore.currentPost.author.avatar"
                  :alt="blogStore.currentPost.author.name"
                  class="w-16 h-16 rounded-full object-cover"
                />
                <div v-else class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                  <UserIcon class="w-8 h-8 text-white" />
                </div>
                
                <div class="flex-1">
                  <h4 class="text-lg font-semibold text-gray-900 mb-2">
                    {{ blogStore.currentPost.author.name }}
                  </h4>
                  <p v-if="blogStore.currentPost.author.bio" class="text-gray-600 leading-relaxed mb-3">
                    {{ blogStore.currentPost.author.bio }}
                  </p>
                  <div v-if="blogStore.currentPost.author.social_links" class="flex space-x-3">
                    <a
                      v-for="(link, platform) in blogStore.currentPost.author.social_links"
                      :key="platform"
                      :href="link"
                      target="_blank"
                      rel="noopener noreferrer"
                      class="text-gray-400 hover:text-gray-600 transition-colors"
                    >
                      <span class="sr-only">{{ platform }}</span>
                      <component :is="getSocialIcon(platform)" class="w-5 h-5" />
                    </a>
                  </div>
                </div>
              </div>
            </div>

            <!-- Related Articles -->
            <div v-if="relatedPosts.length > 0" class="bg-white rounded-2xl shadow-lg p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-6">Related Articles</h3>
              <div class="grid gap-6 md:grid-cols-2">
                <article
                  v-for="post in relatedPosts"
                  :key="post.id"
                  class="group"
                >
                  <router-link :to="`/blog/${post.slug}`" class="block">
                    <div class="aspect-w-16 aspect-h-9 mb-4 overflow-hidden rounded-lg">
                      <img
                        v-if="post.featured_image"
                        :src="post.featured_image.thumb || post.featured_image.url"
                        :alt="post.featured_image.alt || post.title"
                        class="w-full h-32 object-cover group-hover:scale-105 transition-transform duration-300"
                      />
                      <div v-else class="w-full h-32 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                        <DocumentTextIcon class="h-8 w-8 text-gray-400" />
                      </div>
                    </div>
                    <h4 class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2">
                      {{ post.title }}
                    </h4>
                    <p class="text-xs text-gray-500 mt-1">
                      {{ formatDate(post.published_at) }}
                    </p>
                  </router-link>
                </article>
              </div>
            </div>
          </main>

          <!-- Sidebar -->
          <aside class="lg:col-span-4">
            <!-- Table of Contents -->
            <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-8">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Table of Contents</h3>
              <nav v-if="tableOfContents.length > 0" class="space-y-2">
                <a
                  v-for="heading in tableOfContents"
                  :key="heading.id"
                  :href="`#${heading.id}`"
                  :class="[
                    'block text-sm transition-colors hover:text-blue-600',
                    heading.level === 2 ? 'font-medium text-gray-900' : 'text-gray-600 ml-4'
                  ]"
                  @click.prevent="scrollToHeading(heading.id)"
                >
                  {{ heading.text }}
                </a>
              </nav>
              <p v-else class="text-gray-500 text-sm">No headings found</p>
            </div>
          </aside>
        </div>
      </div>
    </article>

    <!-- Not Found -->
    <div v-else class="text-center py-12">
      <DocumentTextIcon class="h-16 w-16 text-gray-300 mx-auto mb-4" />
      <h1 class="text-2xl font-bold text-gray-900 mb-4">Post not found</h1>
      <router-link 
        to="/blog" 
        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-colors"
      >
        <ArrowLeftIcon class="h-5 w-5 mr-2" />
        Back to blog
      </router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, nextTick } from 'vue';
import { useRoute } from 'vue-router';
import { useBlogStore } from '@/stores/blog';
import { 
  CalendarIcon, 
  ClockIcon, 
  EyeIcon, 
  UserIcon, 
  LinkIcon,
  DocumentTextIcon,
  ArrowLeftIcon
} from '@heroicons/vue/24/outline';

interface TableOfContentsItem {
  id: string;
  text: string;
  level: number;
}

const route = useRoute();
const blogStore = useBlogStore();
const tableOfContents = ref<TableOfContentsItem[]>([]);
const relatedPosts = ref<any[]>([]);

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const formatNumber = (num: number) => {
  return new Intl.NumberFormat().format(num);
};

const generateTableOfContents = () => {
  nextTick(() => {
    const headings = document.querySelectorAll('.prose h2, .prose h3');
    tableOfContents.value = Array.from(headings).map((heading, index) => {
      const id = `heading-${index}`;
      heading.id = id;
      return {
        id,
        text: heading.textContent || '',
        level: parseInt(heading.tagName.charAt(1))
      };
    });
  });
};

const scrollToHeading = (id: string) => {
  const element = document.getElementById(id);
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' });
  }
};

const shareToTwitter = () => {
  const url = encodeURIComponent(window.location.href);
  const text = encodeURIComponent(blogStore.currentPost?.title || '');
  window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
};

const shareToFacebook = () => {
  const url = encodeURIComponent(window.location.href);
  window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
};

const shareToLinkedIn = () => {
  const url = encodeURIComponent(window.location.href);
  const title = encodeURIComponent(blogStore.currentPost?.title || '');
  window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}&title=${title}`, '_blank');
};

const copyLink = async () => {
  try {
    await navigator.clipboard.writeText(window.location.href);
    // You could add a toast notification here
    alert('Link copied to clipboard!');
  } catch (err) {
    console.error('Failed to copy link:', err);
  }
};

const getSocialIcon = (platform: string) => {
  // Return appropriate social media icons based on platform
  // You'll need to implement this based on your icon library
  return 'div'; // placeholder
};

const loadRelatedPosts = async () => {
  // Fetch related posts based on categories or tags
  // This is a placeholder - you'll need to implement the API call
  try {
    if (blogStore.currentPost?.categories?.length) {
      // Load posts from same categories
      const response = await fetch(`/api/v1/posts?related_to=${blogStore.currentPost.id}&limit=4`);
      if (response.ok) {
        const data = await response.json();
        relatedPosts.value = data.data || [];
      }
    }
  } catch (error) {
    console.error('Failed to load related posts:', error);
  }
};

const loadPost = async (slug: string) => {
  try {
    await blogStore.fetchPost(slug);
    if (blogStore.currentPost) {
      generateTableOfContents();
      loadRelatedPosts();
    }
  } catch (error) {
    console.error('Failed to load post:', error);
  }
};

onMounted(() => {
  const slug = route.params.slug as string;
  if (slug) {
    loadPost(slug);
  }
});

watch(() => route.params.slug, (newSlug) => {
  if (newSlug) {
    loadPost(newSlug as string);
  }
});

watch(() => blogStore.currentPost, () => {
  if (blogStore.currentPost) {
    generateTableOfContents();
    loadRelatedPosts();
  }
});
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.aspect-w-16 {
  position: relative;
  padding-bottom: 56.25%;
}

.aspect-w-16 img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}
</style> 