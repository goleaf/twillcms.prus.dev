<template>
  <div class="min-h-screen">
    <!-- Enhanced Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 min-h-[85vh] flex items-center">
      <!-- Animated Background -->
      <div class="absolute inset-0">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,rgba(255,255,255,0.05)_1px,transparent_0)] [background-size:50px_50px] animate-pulse-slow"></div>
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl animate-bounce-gentle"></div>
        <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-purple-500/20 rounded-full blur-3xl animate-bounce-gentle" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/2 left-1/2 w-72 h-72 bg-cyan-500/20 rounded-full blur-3xl animate-bounce-gentle" style="animation-delay: 4s;"></div>
      </div>

      <!-- Hero Content -->
      <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <!-- Brand Logo -->
          <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl mb-8 shadow-glow">
            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
          </div>
          
          <!-- Hero Title -->
          <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black text-white mb-6 leading-tight">
            <span class="block">{{ $t('home.hero_title') }}</span>
            <span class="bg-gradient-to-r from-blue-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent animate-shimmer">
              NewsHub
            </span>
          </h1>
          
          <!-- Hero Subtitle -->
          <p class="text-xl sm:text-2xl text-blue-100 max-w-4xl mx-auto mb-12 leading-relaxed font-medium">
            Your premier destination for breaking news, expert analysis, and global insights. 
            <span class="text-white font-semibold">{{ $t('home.hero_subtitle') }}</span>
          </p>
          
          <!-- CTA Buttons -->
          <div class="flex flex-col sm:flex-row gap-6 justify-center mb-16">
            <button 
              @click="scrollToNews"
              class="group bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-10 py-5 rounded-2xl font-bold text-lg shadow-strong hover:shadow-glow transform hover:scale-105 transition-all duration-300 flex items-center justify-center gap-3"
            >
              <svg class="w-6 h-6 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Explore Latest News
            </button>
            <button 
              @click="scrollToCategories"
              class="group bg-white/10 backdrop-blur-md hover:bg-white/20 text-white px-10 py-5 rounded-2xl font-bold text-lg border-2 border-white/30 hover:border-white/50 transition-all duration-300 flex items-center justify-center gap-3"
            >
              <svg class="w-6 h-6 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-4a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2m14 0V6a2 2 0 00-2-2H7a2 2 0 00-2 2v1m14 0H5"/>
              </svg>
              Browse Categories
            </button>
          </div>
          
          <!-- Stats -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-2xl mx-auto">
            <div class="text-center">
              <div class="text-3xl font-bold text-white mb-2">{{ totalPosts }}+</div>
              <div class="text-blue-200 text-sm font-medium">{{ $t('home.articles') }}</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold text-white mb-2">{{ popularCategories.length }}+</div>
              <div class="text-blue-200 text-sm font-medium">{{ $t('home.categories') }}</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold text-white mb-2">50K+</div>
              <div class="text-blue-200 text-sm font-medium">{{ $t('home.readers') }}</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold text-white mb-2">24/7</div>
              <div class="text-blue-200 text-sm font-medium">{{ $t('home.coverage') }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Scroll Indicator -->
      <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <div class="w-6 h-10 border-2 border-white/50 rounded-full flex justify-center">
          <div class="w-1 h-3 bg-white/70 rounded-full mt-2 animate-pulse"></div>
        </div>
      </div>
    </section>

    <!-- Main Content -->
    <div class="bg-slate-50 dark:bg-slate-900 min-h-screen">
      <!-- Loading State -->
      <div v-if="loading" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="lg:col-span-2 space-y-8">
            <div v-for="i in 4" :key="i" class="glass-morphism rounded-3xl p-8 animate-pulse">
              <div class="bg-slate-300 dark:bg-slate-600 h-64 rounded-2xl mb-6"></div>
              <div class="space-y-3">
                <div class="h-6 bg-slate-300 dark:bg-slate-600 rounded w-3/4"></div>
                <div class="h-4 bg-slate-300 dark:bg-slate-600 rounded w-full"></div>
                <div class="h-4 bg-slate-300 dark:bg-slate-600 rounded w-5/6"></div>
              </div>
            </div>
          </div>
          <div class="space-y-6">
            <div v-for="i in 6" :key="i" class="glass-morphism rounded-2xl p-6 animate-pulse">
              <div class="space-y-3">
                <div class="h-4 bg-slate-300 dark:bg-slate-600 rounded w-full"></div>
                <div class="h-4 bg-slate-300 dark:bg-slate-600 rounded w-2/3"></div>
          </div>
        </div>
          </div>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32">
        <div class="text-center">
          <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 dark:bg-red-900/20 rounded-2xl mb-8">
            <svg class="w-10 h-10 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
          <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">{{ $t('home.error_title') }}</h3>
          <p class="text-slate-600 dark:text-slate-400 mb-8 max-w-md mx-auto">{{ error }}</p>
          <button @click="loadData" class="btn-primary">
          {{ $t('home.try_again') }}
        </button>
        </div>
      </div>

      <!-- Content -->
      <div v-else class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div ref="newsSection" class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Main Content -->
          <div class="lg:col-span-2 space-y-16">
            <!-- Featured Article -->
          <article 
            v-if="featuredPost" 
              class="group glass-morphism rounded-3xl overflow-hidden hover:shadow-glow transition-all duration-500 hover:scale-[1.02]"
          >
              <div class="relative">
              <img 
                v-if="featuredPost.featured_image" 
                :src="featuredPost.featured_image" 
                :alt="featuredPost.title"
                  class="w-full h-96 object-cover group-hover:scale-105 transition-transform duration-700"
              >
                <div v-else class="w-full h-96 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                  <svg class="w-20 h-20 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
              </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                <div class="absolute top-6 left-6">
                  <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-gradient-to-r from-orange-500 to-red-500 text-white shadow-lg">
                    ⭐ Featured Story
                </span>
              </div>
                <div class="absolute top-6 right-6">
                  <button class="p-3 bg-white/20 backdrop-blur-md rounded-full text-white hover:bg-white/30 transition-colors duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                    </svg>
                  </button>
            </div>
              </div>
              
              <div class="p-10">
                <div class="flex items-center gap-4 text-slate-500 dark:text-slate-400 mb-6">
                  <time :datetime="featuredPost.published_at" class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ formatDate(featuredPost.published_at) }}
                  </time>
                  <span class="w-2 h-2 bg-slate-400 rounded-full"></span>
                  <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    {{ featuredPost.reading_time || 5 }} min read
                  </span>
                </div>
                
                <h1 class="text-4xl sm:text-5xl font-black text-slate-900 dark:text-white mb-6 leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">
                <router-link :to="{ name: 'post.detail', params: { slug: featuredPost.slug } }">
                  {{ featuredPost.title }}
                </router-link>
              </h1>
              
                <p class="text-xl text-slate-600 dark:text-slate-300 mb-8 leading-relaxed">
                {{ featuredPost.excerpt || featuredPost.description }}
              </p>
              
              <div class="flex items-center justify-between">
                  <div class="flex flex-wrap gap-3">
                  <span 
                      v-for="category in featuredPost.categories?.slice(0, 3)" 
                    :key="category.id"
                      class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-gradient-to-r from-blue-100 to-purple-100 dark:from-blue-900/30 dark:to-purple-900/30 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-700"
                  >
                    {{ category.title || category.name }}
                  </span>
                </div>
                <router-link 
                  :to="{ name: 'post.detail', params: { slug: featuredPost.slug } }"
                    class="group inline-flex items-center gap-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-4 rounded-2xl font-bold transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl"
                >
                  Read Full Story
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                  </svg>
                </router-link>
              </div>
            </div>
          </article>

            <!-- Latest Stories -->
          <section>
              <div class="flex items-center justify-between mb-12">
                <h2 class="text-4xl font-black text-slate-900 dark:text-white">{{ $t('home.latest_posts') }}</h2>
              <router-link 
                to="/archives" 
                  class="group text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-bold flex items-center gap-2 hover:gap-4 transition-all duration-300"
              >
                  View All Stories
                  <svg class="w-5 h-5 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
              </router-link>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <article 
                v-for="post in recentPosts.slice(0, 6)" 
                :key="post.id"
                  class="group glass-morphism rounded-2xl overflow-hidden hover:shadow-strong transition-all duration-300 hover:scale-[1.02]"
              >
                  <div class="relative">
                  <img 
                    v-if="post.featured_image" 
                    :src="post.featured_image" 
                    :alt="post.title"
                      class="w-full h-56 object-cover group-hover:scale-105 transition-transform duration-500"
                  >
                    <div v-else class="w-full h-56 bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-700 dark:to-slate-800 flex items-center justify-center">
                      <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    </div>
                    <div class="absolute top-4 right-4">
                      <button class="p-2 bg-white/20 backdrop-blur-md rounded-full text-white hover:bg-white/30 transition-colors duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                        </svg>
                      </button>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400 mb-4">
                    <time :datetime="post.published_at">
                      {{ formatDate(post.published_at) }}
                    </time>
                      <span class="w-1 h-1 bg-slate-400 rounded-full"></span>
                    <span>{{ post.reading_time || 3 }} min</span>
                  </div>
                  
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4 leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300 line-clamp-2">
                    <router-link :to="{ name: 'post.detail', params: { slug: post.slug } }">
                      {{ post.title }}
                    </router-link>
                  </h3>
                  
                    <p class="text-slate-600 dark:text-slate-300 mb-6 leading-relaxed line-clamp-3">
                    {{ post.excerpt || post.description }}
                  </p>
                  
                  <div class="flex items-center justify-between">
                    <div class="flex gap-2">
                      <span 
                        v-for="category in post.categories?.slice(0, 1)" 
                        :key="category.id"
                          class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300"
                      >
                        {{ category.title || category.name }}
                      </span>
                      </div>
                      <router-link 
                        :to="{ name: 'post.detail', params: { slug: post.slug } }"
                        class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-semibold"
                      >
                        Read More →
                      </router-link>
                  </div>
                </div>
              </article>
            </div>
          </section>

            <!-- Newsletter CTA -->
            <section class="relative overflow-hidden bg-gradient-to-r from-blue-600 via-purple-600 to-cyan-600 rounded-3xl p-12 text-white">
            <div class="absolute inset-0 bg-black/10"></div>
              <div class="absolute inset-0 opacity-10">
                <div class="absolute top-10 left-10 w-32 h-32 bg-white rounded-full blur-xl"></div>
                <div class="absolute bottom-10 right-10 w-40 h-40 bg-white rounded-full blur-xl"></div>
              </div>
              <div class="relative z-10 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-2xl mb-8">
                  <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
                </div>
                <h3 class="text-3xl sm:text-4xl font-bold mb-6">{{ $t('home.newsletter_title') }}</h3>
                <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                  {{ $t('home.newsletter_subtitle') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 max-w-lg mx-auto">
                <input 
                  v-model="newsletterEmail"
                  type="email" 
                    placeholder="Enter your email address"
                    class="flex-1 px-6 py-4 rounded-xl bg-white/10 backdrop-blur-md text-white placeholder-white/70 border-2 border-white/20 focus:outline-none focus:ring-2 focus:ring-white/30 focus:border-white/40 text-lg"
                >
                <button 
                  @click="subscribeNewsletter"
                    class="bg-white text-blue-600 px-8 py-4 rounded-xl font-bold text-lg hover:bg-gray-100 transition-colors duration-300 shadow-lg hover:shadow-xl transform hover:scale-105"
                >
                  {{ $t('home.subscribe') }}
                </button>
              </div>
            </div>
          </section>
        </div>

          <!-- Enhanced Sidebar -->
        <aside ref="categoriesSection" class="space-y-8">
            <!-- Trending Topics (STATIC) -->
            <section class="glass-morphism rounded-2xl p-8">
              <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-8 flex items-center">
                <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-red-500 rounded-lg mr-3 flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M16 6l2.29 2.29c.63.63.63 1.71 0 2.34L16 12.83V17c0 1.1-.9 2-2 2s-2-.9-2-2v-4.17l-2.29-2.12c-.63-.63-.63-1.71 0-2.34L12 6h4z"/>
              </svg>
                </div>
                Trending Topics
              </h3>
              <div class="space-y-3">
                <a v-for="topic in trendingTopics" :key="topic.name" href="#" class="group flex items-center justify-between p-4 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-all duration-300 hover:scale-105">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg mr-4 text-white text-sm font-bold bg-gradient-to-r from-blue-500 to-purple-500">
                      <svg v-if="topic.icon" :class="'w-5 h-5'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path :d="topic.icon" />
                      </svg>
                      <span v-else>{{ topic.name.charAt(0) }}</span>
                    </div>
                    <span class="text-slate-900 dark:text-white font-semibold group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">
                      {{ topic.name }}
                  </span>
                  </div>
                  <span class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ topic.count }}</span>
                </a>
              </div>
            </section>

            <!-- Quick Stats -->
            <section class="bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-900 rounded-2xl p-8">
              <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-8">Quick Stats</h3>
              <div class="grid grid-cols-2 gap-6">
                <div class="text-center">
                  <div class="text-3xl font-black text-blue-600 dark:text-blue-400 mb-2">{{ totalPosts }}</div>
                  <div class="text-sm text-slate-600 dark:text-slate-400 font-medium">{{ $t('home.total_articles') }}</div>
                </div>
                <div class="text-center">
                  <div class="text-3xl font-black text-purple-600 dark:text-purple-400 mb-2">{{ popularCategories.length }}</div>
                  <div class="text-sm text-slate-600 dark:text-slate-400 font-medium">{{ $t('home.categories') }}</div>
            </div>
            </div>
          </section>

          <!-- Archive Links -->
            <section class="glass-morphism rounded-2xl p-8">
              <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-8 flex items-center">
                <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-blue-500 rounded-lg mr-3 flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
                </div>
                Archives
            </h3>
              <div v-if="archives.length > 0" class="space-y-3">
              <router-link
                v-for="archive in archives.slice(0, 8)"
                :key="`${archive.year}-${archive.month}`"
                :to="{ name: 'archives.detail', params: { year: archive.year, month: archive.month } }"
                  class="group flex items-center justify-between p-3 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-all duration-300"
              >
                  <span class="text-slate-900 dark:text-white font-medium group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">
                    {{ formatArchiveDate(archive.year, archive.month) }}
                  </span>
                  <span class="text-sm text-slate-500 dark:text-slate-400">
                    {{ archive.posts_count || 0 }} posts
                  </span>
              </router-link>
            </div>
          </section>
        </aside>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useBlogStore } from '@/stores/blog'

// Store
const blogStore = useBlogStore()

// State
    const loading = ref(true)
    const error = ref(null)
    const posts = ref([])
    const popularCategories = ref([])
    const archives = ref([])
const realTotalPosts = ref(0)
    const newsletterEmail = ref('')
    const newsSection = ref(null)
    const categoriesSection = ref(null)

// Computed
    const featuredPost = computed(() => posts.value[0])
    const recentPosts = computed(() => posts.value.slice(1))
const totalPosts = computed(() => realTotalPosts.value || posts.value.length)

// Lifecycle
    onMounted(async () => {
      await loadData()
    })

// Methods
    const loadData = async () => {
      loading.value = true
      error.value = null
      
      try {
    // Fetch real data from multiple API endpoints in parallel
    const [postsResponse, categoriesResponse, siteStatsResponse] = await Promise.all([
      fetch('/api/v1/posts?per_page=6'),
      fetch('/api/v1/categories/popular?limit=8'),
      fetch('/api/v1/site/stats')
    ])

    // Check if all requests were successful
    if (!postsResponse.ok || !categoriesResponse.ok || !siteStatsResponse.ok) {
      throw new Error('Failed to fetch data from API')
    }

    const [postsData, categoriesData, statsData] = await Promise.all([
      postsResponse.json(),
      categoriesResponse.json(),
      siteStatsResponse.json()
    ])

    // Transform posts data
    if (postsData.data) {
      posts.value = postsData.data.map(post => ({
        id: post.id,
        title: post.title,
        slug: post.slug,
        excerpt: post.excerpt || post.description,
        featured_image: post.featured_image || post.image_url,
        published_at: post.published_at,
        reading_time: post.reading_time || 5,
        categories: post.categories || []
      }))
    }

    // Transform categories data
    if (Array.isArray(categoriesData)) {
      popularCategories.value = categoriesData.map(category => ({
        id: category.id,
        name: category.name,
        slug: category.slug,
        posts_count: category.posts_count,
        color: category.color || '#3B82F6'
      }))
    }

    // Update total posts count from stats
    if (statsData.posts?.total) {
      // Override the computed totalPosts with real stats
      realTotalPosts.value = statsData.posts.total
    }

    // Mock archives data (since we don't have specific archive stats)
    archives.value = [
      { year: 2024, month: 1, posts_count: 47 },
      { year: 2023, month: 12, posts_count: 52 },
      { year: 2023, month: 11, posts_count: 39 },
      { year: 2023, month: 10, posts_count: 44 },
      { year: 2023, month: 9, posts_count: 36 },
      { year: 2023, month: 8, posts_count: 41 }
    ]
        
      } catch (err) {
        console.error('Error loading data:', err)
        error.value = 'Failed to load content. Please try again.'
      } finally {
        loading.value = false
      }
    }

    const formatDate = (dateString) => {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }
    
    const formatArchiveDate = (year, month) => {
      const date = new Date(year, month - 1)
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long'
      })
    }

    const scrollToNews = () => {
  newsSection.value?.scrollIntoView({ 
    behavior: 'smooth',
    block: 'start'
  })
    }

    const scrollToCategories = () => {
  categoriesSection.value?.scrollIntoView({ 
    behavior: 'smooth',
    block: 'start'
  })
    }

    const subscribeNewsletter = () => {
      if (newsletterEmail.value) {
    // TODO: Implement newsletter subscription API
    alert('🎉 Thank you for subscribing to NewsHub! Welcome to our community.')
        newsletterEmail.value = ''
  }
}
</script>

<style scoped>
.btn-primary {
  @apply bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-4 rounded-2xl font-bold transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl;
}

.glass-morphism {
  @apply bg-white/80 dark:bg-slate-800/80 backdrop-blur-md border border-white/20 dark:border-slate-700/50;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

.animate-shimmer {
  background: linear-gradient(90deg, transparent 25%, rgba(255,255,255,0.5) 50%, transparent 75%);
  background-size: 200% 100%;
  animation: shimmer 3s infinite;
}
</style> 