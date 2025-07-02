<template>
  <div class="premium-loader" :class="[`variant-${variant}`, { 'with-overlay': withOverlay }]">
    <!-- Overlay for full-screen loading -->
    <div v-if="withOverlay" class="loader-overlay" @click="$emit('cancel')">
      <div class="overlay-content" @click.stop>
        <slot name="overlay-content">
          <div class="overlay-loader">
            <component :is="loaderComponent" v-bind="loaderProps" />
            <p v-if="message" class="overlay-message">{{ message }}</p>
            <div v-if="showProgress && progress !== undefined" class="overlay-progress">
              <div class="progress-bar">
                <div class="progress-fill" :style="{ width: `${progress}%` }"></div>
              </div>
              <span class="progress-text">{{ Math.round(progress) }}%</span>
            </div>
          </div>
        </slot>
      </div>
    </div>

    <!-- Inline loader variants -->
    <template v-else>
      <!-- Spinner variant -->
      <div v-if="variant === 'spinner'" class="spinner-loader" :style="{ width: size, height: size }">
        <div class="spinner-ring"></div>
        <div class="spinner-ring"></div>
        <div class="spinner-ring"></div>
        <div class="spinner-center">
          <slot name="center">
            <div class="spinner-dot"></div>
          </slot>
        </div>
      </div>

      <!-- Pulse variant -->
      <div v-else-if="variant === 'pulse'" class="pulse-loader">
        <div class="pulse-dot" v-for="i in 3" :key="i" :style="{ animationDelay: `${i * 0.1}s` }"></div>
      </div>

      <!-- Wave variant -->
      <div v-else-if="variant === 'wave'" class="wave-loader">
        <div class="wave-bar" v-for="i in 5" :key="i" :style="{ animationDelay: `${i * 0.1}s` }"></div>
      </div>

      <!-- Progress variant -->
      <div v-else-if="variant === 'progress'" class="progress-loader">
        <div class="progress-track">
          <div class="progress-thumb" :style="{ width: `${progress || 0}%` }"></div>
        </div>
        <span v-if="showProgress" class="progress-label">{{ Math.round(progress || 0) }}%</span>
      </div>

      <!-- Skeleton variant -->
      <div v-else-if="variant === 'skeleton'" class="skeleton-loader">
        <div class="skeleton-line" v-for="i in skeletonLines" :key="i" 
             :style="{ width: `${Math.random() * 40 + 60}%`, animationDelay: `${i * 0.1}s` }"></div>
      </div>

      <!-- Dots variant -->
      <div v-else-if="variant === 'dots'" class="dots-loader">
        <div class="loader-dot" v-for="i in 4" :key="i" :style="{ animationDelay: `${i * 0.2}s` }"></div>
      </div>

      <!-- Particles variant (premium effect) -->
      <div v-else-if="variant === 'particles'" class="particles-loader" ref="particlesContainer">
        <canvas ref="particlesCanvas" :width="canvasSize.width" :height="canvasSize.height"></canvas>
        <div class="particles-overlay">
          <slot>
            <div class="particles-content">
              <div class="particles-spinner"></div>
              <p v-if="message" class="particles-message">{{ message }}</p>
            </div>
          </slot>
        </div>
      </div>
    </template>

    <!-- Loading message -->
    <p v-if="message && !withOverlay && variant !== 'particles'" class="loader-message" :class="`message-${variant}`">
      {{ message }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'

// Props
interface Props {
  variant?: 'spinner' | 'pulse' | 'wave' | 'progress' | 'skeleton' | 'dots' | 'particles'
  size?: string
  message?: string
  progress?: number
  showProgress?: boolean
  withOverlay?: boolean
  skeletonLines?: number
  color?: string
  speed?: 'slow' | 'normal' | 'fast'
  particleCount?: number
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'spinner',
  size: '40px',
  showProgress: false,
  withOverlay: false,
  skeletonLines: 3,
  color: '#3b82f6',
  speed: 'normal',
  particleCount: 50
})

// Emits
const emit = defineEmits<{
  cancel: []
}>()

// Refs for particles effect
const particlesContainer = ref<HTMLDivElement>()
const particlesCanvas = ref<HTMLCanvasElement>()
const canvasSize = ref({ width: 300, height: 200 })

// Computed properties
const loaderComponent = computed(() => {
  return 'div' // This would be dynamically determined
})

const loaderProps = computed(() => {
  return {
    variant: props.variant,
    size: props.size,
    message: props.message
  }
})

// Particles animation
class Particle {
  x: number
  y: number
  vx: number
  vy: number
  size: number
  opacity: number
  color: string

  constructor(canvasWidth: number, canvasHeight: number, color: string) {
    this.x = Math.random() * canvasWidth
    this.y = Math.random() * canvasHeight
    this.vx = (Math.random() - 0.5) * 2
    this.vy = (Math.random() - 0.5) * 2
    this.size = Math.random() * 3 + 1
    this.opacity = Math.random() * 0.5 + 0.2
    this.color = color
  }

  update(canvasWidth: number, canvasHeight: number) {
    this.x += this.vx
    this.y += this.vy

    if (this.x < 0 || this.x > canvasWidth) this.vx *= -1
    if (this.y < 0 || this.y > canvasHeight) this.vy *= -1

    this.x = Math.max(0, Math.min(canvasWidth, this.x))
    this.y = Math.max(0, Math.min(canvasHeight, this.y))
  }

  draw(ctx: CanvasRenderingContext2D) {
    ctx.save()
    ctx.globalAlpha = this.opacity
    ctx.fillStyle = this.color
    ctx.beginPath()
    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2)
    ctx.fill()
    ctx.restore()
  }
}

const particles = ref<Particle[]>([])
let animationId: number | null = null

const initParticles = () => {
  if (!particlesCanvas.value) return

  const canvas = particlesCanvas.value
  const ctx = canvas.getContext('2d')
  if (!ctx) return

  particles.value = []
  for (let i = 0; i < props.particleCount; i++) {
    particles.value.push(new Particle(canvas.width, canvas.height, props.color))
  }

  const animate = () => {
    ctx.clearRect(0, 0, canvas.width, canvas.height)

    particles.value.forEach(particle => {
      particle.update(canvas.width, canvas.height)
      particle.draw(ctx)
    })

    animationId = requestAnimationFrame(animate)
  }

  animate()
}

const resizeCanvas = () => {
  if (!particlesContainer.value) return
  
  const rect = particlesContainer.value.getBoundingClientRect()
  canvasSize.value = {
    width: rect.width || 300,
    height: rect.height || 200
  }
}

// Lifecycle
onMounted(() => {
  if (props.variant === 'particles') {
    resizeCanvas()
    initParticles()
    window.addEventListener('resize', resizeCanvas)
  }
})

onUnmounted(() => {
  if (animationId) {
    cancelAnimationFrame(animationId)
  }
  window.removeEventListener('resize', resizeCanvas)
})

// Watch for particles variant changes
watch(() => props.variant, (newVariant) => {
  if (newVariant === 'particles') {
    setTimeout(() => {
      resizeCanvas()
      initParticles()
    }, 0)
  } else if (animationId) {
    cancelAnimationFrame(animationId)
    animationId = null
  }
})
</script>

<style scoped>
.premium-loader {
  display: inline-flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
}

/* Overlay styles */
.loader-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.8);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  cursor: pointer;
}

.overlay-content {
  background: white;
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  cursor: default;
  max-width: 400px;
  text-align: center;
}

.overlay-loader {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
}

.overlay-message {
  font-size: 18px;
  font-weight: 500;
  color: #374151;
  margin: 0;
}

.overlay-progress {
  width: 100%;
  max-width: 300px;
}

.progress-bar {
  width: 100%;
  height: 6px;
  background: #e5e7eb;
  border-radius: 3px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #3b82f6, #1d4ed8);
  border-radius: 3px;
  transition: width 0.3s ease;
}

.progress-text {
  font-size: 14px;
  font-weight: 500;
  color: #6b7280;
  margin-top: 8px;
  display: block;
}

/* Spinner variant */
.spinner-loader {
  position: relative;
  display: inline-block;
}

.spinner-ring {
  position: absolute;
  border: 3px solid transparent;
  border-top: 3px solid #3b82f6;
  border-radius: 50%;
  width: 100%;
  height: 100%;
  animation: spin 1.2s linear infinite;
}

.spinner-ring:nth-child(2) {
  border-top-color: #8b5cf6;
  animation-delay: -0.4s;
  transform: scale(0.8);
}

.spinner-ring:nth-child(3) {
  border-top-color: #06b6d4;
  animation-delay: -0.8s;
  transform: scale(0.6);
}

.spinner-center {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.spinner-dot {
  width: 6px;
  height: 6px;
  background: #f59e0b;
  border-radius: 50%;
  animation: pulse 1.5s ease-in-out infinite;
}

/* Pulse variant */
.pulse-loader {
  display: flex;
  gap: 4px;
  align-items: center;
}

.pulse-dot {
  width: 8px;
  height: 8px;
  background: #3b82f6;
  border-radius: 50%;
  animation: pulse-scale 1.4s ease-in-out infinite;
}

/* Wave variant */
.wave-loader {
  display: flex;
  gap: 2px;
  align-items: end;
  height: 20px;
}

.wave-bar {
  width: 3px;
  height: 6px;
  background: #3b82f6;
  border-radius: 2px;
  animation: wave 1.2s ease-in-out infinite;
}

/* Progress variant */
.progress-loader {
  width: 200px;
  text-align: center;
}

.progress-track {
  width: 100%;
  height: 4px;
  background: #e5e7eb;
  border-radius: 2px;
  overflow: hidden;
}

.progress-thumb {
  height: 100%;
  background: linear-gradient(90deg, #3b82f6, #1d4ed8);
  border-radius: 2px;
  transition: width 0.3s ease;
  position: relative;
}

.progress-thumb::after {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 20px;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6));
  animation: shimmer 1.5s infinite;
}

.progress-label {
  font-size: 12px;
  font-weight: 500;
  color: #6b7280;
  margin-top: 4px;
  display: block;
}

/* Skeleton variant */
.skeleton-loader {
  width: 100%;
  max-width: 200px;
}

.skeleton-line {
  height: 12px;
  background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
  background-size: 200% 100%;
  border-radius: 6px;
  margin-bottom: 8px;
  animation: skeleton-shimmer 1.5s infinite;
}

.skeleton-line:last-child {
  margin-bottom: 0;
}

/* Dots variant */
.dots-loader {
  display: flex;
  gap: 6px;
}

.loader-dot {
  width: 10px;
  height: 10px;
  background: #3b82f6;
  border-radius: 50%;
  animation: dots-bounce 1.4s ease-in-out infinite;
}

/* Particles variant */
.particles-loader {
  position: relative;
  min-height: 200px;
  min-width: 300px;
  border-radius: 12px;
  overflow: hidden;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.particles-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2;
}

.particles-content {
  text-align: center;
  color: white;
}

.particles-spinner {
  width: 40px;
  height: 40px;
  border: 3px solid rgba(255, 255, 255, 0.3);
  border-top: 3px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 16px;
}

.particles-message {
  font-size: 16px;
  font-weight: 500;
  margin: 0;
  text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
}

/* Messages */
.loader-message {
  font-size: 14px;
  color: #6b7280;
  margin: 0;
  text-align: center;
}

.message-skeleton {
  font-size: 12px;
}

/* Animations */
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

@keyframes pulse-scale {
  0%, 100% { transform: scale(0.8); opacity: 0.5; }
  50% { transform: scale(1.2); opacity: 1; }
}

@keyframes wave {
  0%, 100% { height: 6px; }
  50% { height: 20px; }
}

@keyframes shimmer {
  0% { transform: translateX(-20px); }
  100% { transform: translateX(200px); }
}

@keyframes skeleton-shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

@keyframes dots-bounce {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}

/* Speed variations */
.premium-loader.speed-slow .spinner-ring { animation-duration: 2s; }
.premium-loader.speed-fast .spinner-ring { animation-duration: 0.8s; }

.premium-loader.speed-slow .pulse-dot { animation-duration: 2s; }
.premium-loader.speed-fast .pulse-dot { animation-duration: 1s; }

.premium-loader.speed-slow .wave-bar { animation-duration: 1.8s; }
.premium-loader.speed-fast .wave-bar { animation-duration: 0.8s; }

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .overlay-content {
    background: #1f2937;
    color: white;
  }
  
  .overlay-message {
    color: #e5e7eb;
  }
  
  .loader-message {
    color: #9ca3af;
  }
  
  .progress-bar {
    background: #374151;
  }
  
  .progress-track {
    background: #374151;
  }
  
  .skeleton-line {
    background: linear-gradient(90deg, #374151 25%, #4b5563 50%, #374151 75%);
    background-size: 200% 100%;
  }
}

/* Responsive */
@media (max-width: 640px) {
  .particles-loader {
    min-width: 250px;
    min-height: 150px;
  }
  
  .progress-loader {
    width: 150px;
  }
  
  .overlay-content {
    margin: 16px;
    padding: 24px;
  }
}
</style>
