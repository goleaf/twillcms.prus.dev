<template>
  <BaseModal
    :show="show"
    @close="$emit('close')"
    size="md"
    :show-header="false"
    :show-footer="false"
    :close-on-backdrop="true"
  >
    <div class="relative overflow-hidden">
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-500 to-secondary-500"></div>
        <svg class="absolute inset-0 w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
          <defs>
            <pattern id="newsletter-pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
              <circle cx="10" cy="10" r="2" fill="currentColor" opacity="0.1"/>
            </pattern>
          </defs>
          <rect width="100" height="100" fill="url(#newsletter-pattern)"/>
        </svg>
      </div>

      <!-- Close Button -->
      <button
        @click="$emit('close')"
        class="absolute top-4 right-4 z-10 p-2 rounded-full text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-all duration-200"
        aria-label="Close newsletter signup"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <!-- Content -->
      <div class="relative z-10 text-center">
        <!-- Icon -->
        <div class="flex justify-center mb-6">
          <div class="w-16 h-16 bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full flex items-center justify-center animate-pulse-soft">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
          </div>
        </div>

        <!-- Title -->
        <h2 class="text-3xl font-bold font-serif text-neutral-900 dark:text-white mb-4">
          {{ $t('newsletter.title') }}
        </h2>

        <!-- Description -->
        <p class="text-lg text-neutral-600 dark:text-neutral-300 mb-8 max-w-md mx-auto leading-relaxed">
          Get the latest news, insights, and exclusive content delivered directly to your inbox. Join our community of informed readers.
        </p>

        <!-- Success State -->
        <div v-if="submitted" class="animate-fade-in">
          <div class="w-16 h-16 bg-success-100 dark:bg-success-900/20 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-success-600 dark:text-success-400 mb-4">
            Welcome Aboard!
          </h3>
          <p class="text-neutral-600 dark:text-neutral-300 mb-6">
            Thank you for subscribing! Check your email for a confirmation message.
          </p>
          <button
            @click="$emit('close')"
            class="btn-primary"
          >
            Continue Reading
          </button>
        </div>

        <!-- Form -->
        <form v-else @submit.prevent="subscribe" class="space-y-6">
          <!-- Email Input -->
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
              </svg>
            </div>
            <input
              v-model="email"
              type="email"
              placeholder="Enter your email address"
              class="w-full pl-12 pr-4 py-4 border-2 rounded-xl transition-all duration-200 focus:outline-none text-lg"
              :class="[
                errors.email
                  ? 'border-danger-300 focus:border-danger-500 focus:ring-danger-500/20'
                  : 'border-neutral-200 dark:border-neutral-600 focus:border-primary-500 focus:ring-primary-500/20',
                'bg-white dark:bg-neutral-700 text-neutral-900 dark:text-white placeholder-neutral-500 dark:placeholder-neutral-400'
              ]"
              required
              autocomplete="email"
            >
            <div v-if="errors.email" class="absolute inset-y-0 right-0 pr-4 flex items-center">
              <svg class="h-5 w-5 text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>

          <!-- Error Message -->
          <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
          >
            <div v-if="errors.email" class="text-danger-600 dark:text-danger-400 text-sm text-left bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800 rounded-lg p-3">
              {{ errors.email }}
            </div>
          </transition>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="loading || !email"
            class="w-full btn-primary-large group"
          >
            <span v-if="!loading" class="flex items-center justify-center">
              {{ $t('newsletter.subscribe_now') }}
              <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
              </svg>
            </span>
            <span v-else class="flex items-center justify-center">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Subscribing...
            </span>
          </button>

          <!-- Privacy Notice -->
          <p class="text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed">
            By subscribing, you agree to our privacy policy. We respect your privacy and will never share your email address. You can unsubscribe at any time.
          </p>
        </form>

        <!-- Social Proof -->
        <div v-if="!submitted" class="mt-8 pt-6 border-t border-neutral-200 dark:border-neutral-700">
          <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-4">
            Join 10,000+ readers who trust us for their daily news
          </p>
          <div class="flex justify-center items-center space-x-2">
            <div class="flex -space-x-2">
              <div class="w-8 h-8 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full border-2 border-white dark:border-neutral-800"></div>
              <div class="w-8 h-8 bg-gradient-to-r from-green-400 to-green-600 rounded-full border-2 border-white dark:border-neutral-800"></div>
              <div class="w-8 h-8 bg-gradient-to-r from-purple-400 to-purple-600 rounded-full border-2 border-white dark:border-neutral-800"></div>
              <div class="w-8 h-8 bg-gradient-to-r from-pink-400 to-pink-600 rounded-full border-2 border-white dark:border-neutral-800"></div>
              <div class="w-8 h-8 bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-full border-2 border-white dark:border-neutral-800 flex items-center justify-center">
                <span class="text-xs font-bold text-white">+</span>
              </div>
            </div>
            <div class="flex text-yellow-400">
              <svg v-for="i in 5" :key="i" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>
  </BaseModal>
</template>

<script>
import { ref, reactive } from 'vue'
import BaseModal from './BaseModal.vue'

export default {
  name: 'NewsletterModal',
  components: {
    BaseModal
  },
  props: {
    show: {
      type: Boolean,
      default: false
    }
  },
  emits: ['close', 'subscribe'],
  setup(props, { emit }) {
    const email = ref('')
    const loading = ref(false)
    const submitted = ref(false)
    const errors = reactive({
      email: null
    })

    const validateEmail = (email) => {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      return emailRegex.test(email)
    }

    const subscribe = async () => {
      // Reset errors
      errors.email = null

      // Validate email
      if (!email.value) {
        errors.email = 'Email address is required'
        return
      }

      if (!validateEmail(email.value)) {
        errors.email = 'Please enter a valid email address'
        return
      }

      loading.value = true

      try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 2000))

        // Emit subscribe event
        emit('subscribe', {
          email: email.value
        })

        submitted.value = true
      } catch (error) {
        errors.email = 'Something went wrong. Please try again.'
      } finally {
        loading.value = false
      }
    }

    return {
      email,
      loading,
      submitted,
      errors,
      subscribe
    }
  }
}
</script>

<style scoped>
.btn-primary {
  @apply bg-primary-600 hover:bg-primary-700 disabled:bg-primary-300 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 hover:scale-105 disabled:hover:scale-100 shadow-lg hover:shadow-xl disabled:shadow-none;
}

.btn-primary-large {
  @apply bg-gradient-to-r from-primary-600 to-secondary-600 hover:from-primary-700 hover:to-secondary-700 disabled:from-primary-300 disabled:to-secondary-300 text-white px-8 py-4 rounded-xl font-semibold text-lg transition-all duration-300 hover:scale-105 disabled:hover:scale-100 shadow-lg hover:shadow-xl disabled:shadow-none;
}

.animate-pulse-soft {
  animation: pulse-soft 3s ease-in-out infinite;
}

@keyframes pulse-soft {
  0%, 100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.8;
    transform: scale(1.05);
  }
}
</style> 