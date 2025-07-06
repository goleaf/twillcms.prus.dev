<template>
  <teleport to="body">
    <transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click="handleBackdropClick"
        @keydown.esc="close"
        tabindex="-1"
        ref="modalContainer"
      >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
        
        <!-- Modal Container -->
        <div class="flex min-h-full items-center justify-center p-4">
          <transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 scale-95 translate-y-4"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 translate-y-4"
          >
            <div
              v-if="show"
              class="relative w-full transform overflow-hidden rounded-3xl bg-white dark:bg-neutral-800 shadow-strong transition-all"
              :class="sizeClasses"
              @click.stop
              ref="modalContent"
            >
              <!-- Header -->
              <div v-if="showHeader" class="border-b border-neutral-200 dark:border-neutral-700 px-6 py-4">
                <div class="flex items-center justify-between">
                  <h3 class="text-xl font-bold font-serif text-neutral-900 dark:text-white">
                    <slot name="header">{{ title }}</slot>
                  </h3>
                  <button
                    @click="close"
                    class="rounded-lg p-2 text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-all duration-200"
                    aria-label="Close modal"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Content -->
              <div class="px-6 py-6">
                <slot />
              </div>

              <!-- Footer -->
              <div v-if="showFooter" class="border-t border-neutral-200 dark:border-neutral-700 px-6 py-4">
                <slot name="footer">
                  <div class="flex justify-end space-x-3">
                    <button
                      @click="close"
                      class="btn-secondary"
                    >
                      Cancel
                    </button>
                    <button
                      @click="confirm"
                      class="btn-primary"
                      :disabled="confirmDisabled"
                    >
                      {{ confirmText }}
                    </button>
                  </div>
                </slot>
              </div>
            </div>
          </transition>
        </div>
      </div>
    </transition>
  </teleport>
</template>

<script>
import { ref, onMounted, onUnmounted, nextTick, watch } from 'vue'

export default {
  name: 'BaseModal',
  props: {
    show: {
      type: Boolean,
      default: false
    },
    title: {
      type: String,
      default: ''
    },
    size: {
      type: String,
      default: 'md',
      validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl', '2xl', 'full'].includes(value)
    },
    showHeader: {
      type: Boolean,
      default: true
    },
    showFooter: {
      type: Boolean,
      default: false
    },
    confirmText: {
      type: String,
      default: 'Confirm'
    },
    confirmDisabled: {
      type: Boolean,
      default: false
    },
    closeOnBackdrop: {
      type: Boolean,
      default: true
    },
    closeOnEscape: {
      type: Boolean,
      default: true
    }
  },
  emits: ['close', 'confirm'],
  setup(props, { emit }) {
    const modalContainer = ref(null)
    const modalContent = ref(null)
    const previousActiveElement = ref(null)

    const sizeClasses = {
      xs: 'max-w-sm',
      sm: 'max-w-md',
      md: 'max-w-lg',
      lg: 'max-w-2xl',
      xl: 'max-w-4xl',
      '2xl': 'max-w-6xl',
      full: 'max-w-full mx-4'
    }

    const handleBackdropClick = (event) => {
      if (props.closeOnBackdrop && event.target === modalContainer.value) {
        close()
      }
    }

    const close = () => {
      if (props.closeOnEscape) {
        emit('close')
      }
    }

    const confirm = () => {
      emit('confirm')
    }

    const trapFocus = () => {
      if (!modalContent.value) return

      const focusableElements = modalContent.value.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
      )
      
      const firstElement = focusableElements[0]
      const lastElement = focusableElements[focusableElements.length - 1]

      const handleTabKey = (e) => {
        if (e.key === 'Tab') {
          if (e.shiftKey) {
            if (document.activeElement === firstElement) {
              lastElement.focus()
              e.preventDefault()
            }
          } else {
            if (document.activeElement === lastElement) {
              firstElement.focus()
              e.preventDefault()
            }
          }
        }
      }

      document.addEventListener('keydown', handleTabKey)
      return () => document.removeEventListener('keydown', handleTabKey)
    }

    watch(() => props.show, async (newValue) => {
      if (newValue) {
        previousActiveElement.value = document.activeElement
        document.body.style.overflow = 'hidden'
        
        await nextTick()
        const cleanup = trapFocus()
        
        // Focus first focusable element
        const focusableElements = modalContent.value?.querySelectorAll(
          'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        )
        if (focusableElements?.length > 0) {
          focusableElements[0].focus()
        }

        return cleanup
      } else {
        document.body.style.overflow = ''
        if (previousActiveElement.value) {
          previousActiveElement.value.focus()
        }
      }
    })

    onUnmounted(() => {
      document.body.style.overflow = ''
    })

    return {
      modalContainer,
      modalContent,
      sizeClasses: sizeClasses[props.size],
      handleBackdropClick,
      close,
      confirm
    }
  }
}
</script>

<style scoped>
.btn-primary {
  @apply bg-primary-600 hover:bg-primary-700 disabled:bg-primary-300 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 hover:scale-105 disabled:hover:scale-100 shadow-lg hover:shadow-xl disabled:shadow-none;
}

.btn-secondary {
  @apply bg-white dark:bg-neutral-700 border border-neutral-300 dark:border-neutral-600 text-neutral-700 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-600 px-4 py-2 rounded-lg font-medium transition-all duration-200;
}
</style> 