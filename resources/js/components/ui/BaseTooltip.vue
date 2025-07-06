<template>
  <div class="relative inline-block" ref="triggerRef">
    <!-- Trigger Element -->
    <div
      @mouseenter="showTooltip"
      @mouseleave="hideTooltip"
      @focus="showTooltip"
      @blur="hideTooltip"
      @click="handleClick"
      :aria-describedby="tooltipId"
    >
      <slot />
    </div>

    <!-- Tooltip -->
    <teleport to="body">
      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
      >
        <div
          v-if="visible"
          ref="tooltipRef"
          :id="tooltipId"
          role="tooltip"
          class="absolute z-50 px-3 py-2 text-sm font-medium text-white bg-neutral-900 dark:bg-neutral-700 rounded-lg shadow-strong pointer-events-none select-none"
          :class="[
            positionClasses,
            sizeClasses,
            themeClasses
          ]"
          :style="tooltipStyle"
        >
          <!-- Content -->
          <div>
            <slot name="content">{{ content }}</slot>
          </div>

          <!-- Arrow -->
          <div
            class="absolute w-2 h-2 bg-neutral-900 dark:bg-neutral-700 transform rotate-45"
            :class="arrowClasses"
          ></div>
        </div>
      </transition>
    </teleport>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'

export default {
  name: 'BaseTooltip',
  props: {
    content: {
      type: String,
      default: ''
    },
    placement: {
      type: String,
      default: 'top',
      validator: (value) => ['top', 'bottom', 'left', 'right', 'top-start', 'top-end', 'bottom-start', 'bottom-end', 'left-start', 'left-end', 'right-start', 'right-end'].includes(value)
    },
    trigger: {
      type: String,
      default: 'hover',
      validator: (value) => ['hover', 'click', 'focus', 'manual'].includes(value)
    },
    delay: {
      type: [Number, Object],
      default: () => ({ show: 100, hide: 100 })
    },
    disabled: {
      type: Boolean,
      default: false
    },
    theme: {
      type: String,
      default: 'dark',
      validator: (value) => ['dark', 'light', 'primary', 'success', 'warning', 'danger'].includes(value)
    },
    maxWidth: {
      type: String,
      default: '200px'
    }
  },
  emits: ['show', 'hide'],
  setup(props, { emit }) {
    const triggerRef = ref(null)
    const tooltipRef = ref(null)
    const visible = ref(false)
    const position = ref({ x: 0, y: 0 })
    const showTimeout = ref(null)
    const hideTimeout = ref(null)
    const tooltipId = `tooltip-${Math.random().toString(36).substr(2, 9)}`

    const delayConfig = computed(() => {
      if (typeof props.delay === 'number') {
        return { show: props.delay, hide: props.delay }
      }
      return { show: 100, hide: 100, ...props.delay }
    })

    const positionClasses = computed(() => {
      const classes = {
        'top': 'mb-2',
        'bottom': 'mt-2',
        'left': 'mr-2',
        'right': 'ml-2',
        'top-start': 'mb-2',
        'top-end': 'mb-2',
        'bottom-start': 'mt-2',
        'bottom-end': 'mt-2',
        'left-start': 'mr-2',
        'left-end': 'mr-2',
        'right-start': 'ml-2',
        'right-end': 'ml-2'
      }
      return classes[props.placement] || classes.top
    })

    const arrowClasses = computed(() => {
      const classes = {
        'top': '-bottom-1 left-1/2 transform -translate-x-1/2',
        'bottom': '-top-1 left-1/2 transform -translate-x-1/2',
        'left': '-right-1 top-1/2 transform -translate-y-1/2',
        'right': '-left-1 top-1/2 transform -translate-y-1/2',
        'top-start': '-bottom-1 left-4',
        'top-end': '-bottom-1 right-4',
        'bottom-start': '-top-1 left-4',
        'bottom-end': '-top-1 right-4',
        'left-start': '-right-1 top-4',
        'left-end': '-right-1 bottom-4',
        'right-start': '-left-1 top-4',
        'right-end': '-left-1 bottom-4'
      }
      return classes[props.placement] || classes.top
    })

    const sizeClasses = computed(() => {
      return {
        'max-w-xs': props.maxWidth === '200px',
        'max-w-sm': props.maxWidth === '300px',
        'max-w-md': props.maxWidth === '400px',
        'max-w-lg': props.maxWidth === '500px'
      }
    })

    const themeClasses = computed(() => {
      const themes = {
        dark: 'bg-neutral-900 dark:bg-neutral-700 text-white',
        light: 'bg-white dark:bg-neutral-100 text-neutral-900 dark:text-neutral-800 border border-neutral-200 dark:border-neutral-300',
        primary: 'bg-primary-600 text-white',
        success: 'bg-success-600 text-white',
        warning: 'bg-warning-600 text-white',
        danger: 'bg-danger-600 text-white'
      }
      return themes[props.theme] || themes.dark
    })

    const tooltipStyle = computed(() => {
      return {
        left: `${position.value.x}px`,
        top: `${position.value.y}px`,
        maxWidth: props.maxWidth
      }
    })

    const calculatePosition = async () => {
      if (!triggerRef.value || !tooltipRef.value) return

      await nextTick()

      const triggerRect = triggerRef.value.getBoundingClientRect()
      const tooltipRect = tooltipRef.value.getBoundingClientRect()
      const viewportWidth = window.innerWidth
      const viewportHeight = window.innerHeight
      const scrollX = window.scrollX
      const scrollY = window.scrollY

      let x = 0
      let y = 0

      // Calculate position based on placement
      switch (props.placement) {
        case 'top':
          x = triggerRect.left + (triggerRect.width / 2) - (tooltipRect.width / 2)
          y = triggerRect.top - tooltipRect.height - 8
          break
        case 'bottom':
          x = triggerRect.left + (triggerRect.width / 2) - (tooltipRect.width / 2)
          y = triggerRect.bottom + 8
          break
        case 'left':
          x = triggerRect.left - tooltipRect.width - 8
          y = triggerRect.top + (triggerRect.height / 2) - (tooltipRect.height / 2)
          break
        case 'right':
          x = triggerRect.right + 8
          y = triggerRect.top + (triggerRect.height / 2) - (tooltipRect.height / 2)
          break
        case 'top-start':
          x = triggerRect.left
          y = triggerRect.top - tooltipRect.height - 8
          break
        case 'top-end':
          x = triggerRect.right - tooltipRect.width
          y = triggerRect.top - tooltipRect.height - 8
          break
        case 'bottom-start':
          x = triggerRect.left
          y = triggerRect.bottom + 8
          break
        case 'bottom-end':
          x = triggerRect.right - tooltipRect.width
          y = triggerRect.bottom + 8
          break
        case 'left-start':
          x = triggerRect.left - tooltipRect.width - 8
          y = triggerRect.top
          break
        case 'left-end':
          x = triggerRect.left - tooltipRect.width - 8
          y = triggerRect.bottom - tooltipRect.height
          break
        case 'right-start':
          x = triggerRect.right + 8
          y = triggerRect.top
          break
        case 'right-end':
          x = triggerRect.right + 8
          y = triggerRect.bottom - tooltipRect.height
          break
      }

      // Adjust for viewport boundaries
      if (x < 8) x = 8
      if (x + tooltipRect.width > viewportWidth - 8) {
        x = viewportWidth - tooltipRect.width - 8
      }
      if (y < 8) y = 8
      if (y + tooltipRect.height > viewportHeight - 8) {
        y = viewportHeight - tooltipRect.height - 8
      }

      position.value = {
        x: x + scrollX,
        y: y + scrollY
      }
    }

    const showTooltip = () => {
      if (props.disabled || (props.trigger !== 'hover' && props.trigger !== 'focus')) return

      clearTimeout(hideTimeout.value)
      showTimeout.value = setTimeout(async () => {
        visible.value = true
        await nextTick()
        await calculatePosition()
        emit('show')
      }, delayConfig.value.show)
    }

    const hideTooltip = () => {
      if (props.disabled || (props.trigger !== 'hover' && props.trigger !== 'focus')) return

      clearTimeout(showTimeout.value)
      hideTimeout.value = setTimeout(() => {
        visible.value = false
        emit('hide')
      }, delayConfig.value.hide)
    }

    const handleClick = () => {
      if (props.disabled || props.trigger !== 'click') return

      if (visible.value) {
        visible.value = false
        emit('hide')
      } else {
        visible.value = true
        nextTick(() => {
          calculatePosition()
          emit('show')
        })
      }
    }

    const show = async () => {
      visible.value = true
      await nextTick()
      await calculatePosition()
      emit('show')
    }

    const hide = () => {
      visible.value = false
      emit('hide')
    }

    const handleResize = () => {
      if (visible.value) {
        calculatePosition()
      }
    }

    const handleScroll = () => {
      if (visible.value) {
        calculatePosition()
      }
    }

    onMounted(() => {
      window.addEventListener('resize', handleResize)
      window.addEventListener('scroll', handleScroll, true)
    })

    onUnmounted(() => {
      clearTimeout(showTimeout.value)
      clearTimeout(hideTimeout.value)
      window.removeEventListener('resize', handleResize)
      window.removeEventListener('scroll', handleScroll, true)
    })

    return {
      triggerRef,
      tooltipRef,
      visible,
      tooltipId,
      positionClasses,
      arrowClasses,
      sizeClasses,
      themeClasses,
      tooltipStyle,
      showTooltip,
      hideTooltip,
      handleClick,
      show,
      hide
    }
  }
}
</script>

<style scoped>
/* Ensure tooltip appears above other elements */
.tooltip-enter-active,
.tooltip-leave-active {
  transition: all 0.2s ease;
}

.tooltip-enter-from,
.tooltip-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style> 