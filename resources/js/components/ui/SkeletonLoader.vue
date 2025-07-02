<template>
  <div 
    class="skeleton-loader"
    :class="[
      `skeleton-${variant}`,
      `skeleton-${theme}`,
      { 'skeleton-animated': animated },
      { 'skeleton-rounded': rounded }
    ]"
    :style="skeletonStyles"
    role="status"
    aria-label="Loading content"
  >
    
    <!-- Text Skeleton -->
    <template v-if="variant === 'text'">
      <div 
        v-for="line in lines" 
        :key="line"
        class="skeleton-line"
        :class="{ 'skeleton-line-last': line === lines && lastLineWidth }"
        :style="line === lines && lastLineWidth ? { width: lastLineWidth } : {}"
      ></div>
    </template>
    
    <!-- Avatar Skeleton -->
    <div v-else-if="variant === 'avatar'" class="skeleton-avatar">
      <div class="skeleton-circle"></div>
    </div>
    
    <!-- Card Skeleton -->
    <div v-else-if="variant === 'card'" class="skeleton-card">
      <div class="skeleton-image"></div>
      <div class="skeleton-content">
        <div class="skeleton-title"></div>
        <div class="skeleton-text-lines">
          <div class="skeleton-line"></div>
          <div class="skeleton-line"></div>
          <div class="skeleton-line short"></div>
        </div>
        <div class="skeleton-footer">
          <div class="skeleton-button"></div>
          <div class="skeleton-meta"></div>
        </div>
      </div>
    </div>
    
    <!-- List Item Skeleton -->
    <div v-else-if="variant === 'list-item'" class="skeleton-list-item">
      <div class="skeleton-avatar-small"></div>
      <div class="skeleton-content">
        <div class="skeleton-title-small"></div>
        <div class="skeleton-subtitle"></div>
      </div>
      <div class="skeleton-action"></div>
    </div>
    
    <!-- Article Skeleton -->
    <div v-else-if="variant === 'article'" class="skeleton-article">
      <div class="skeleton-header">
        <div class="skeleton-category"></div>
        <div class="skeleton-title-large"></div>
        <div class="skeleton-meta-row">
          <div class="skeleton-avatar-small"></div>
          <div class="skeleton-author-info">
            <div class="skeleton-author-name"></div>
            <div class="skeleton-date"></div>
          </div>
        </div>
      </div>
      <div class="skeleton-featured-image"></div>
      <div class="skeleton-content">
        <div class="skeleton-paragraph">
          <div class="skeleton-line"></div>
          <div class="skeleton-line"></div>
          <div class="skeleton-line"></div>
          <div class="skeleton-line short"></div>
        </div>
        <div class="skeleton-paragraph">
          <div class="skeleton-line"></div>
          <div class="skeleton-line"></div>
          <div class="skeleton-line medium"></div>
        </div>
      </div>
    </div>
    
    <!-- Table Skeleton -->
    <div v-else-if="variant === 'table'" class="skeleton-table">
      <div class="skeleton-table-header">
        <div v-for="col in columns" :key="col" class="skeleton-th"></div>
      </div>
      <div v-for="row in rows" :key="row" class="skeleton-table-row">
        <div v-for="col in columns" :key="col" class="skeleton-td"></div>
      </div>
    </div>
    
    <!-- Form Skeleton -->
    <div v-else-if="variant === 'form'" class="skeleton-form">
      <div v-for="field in formFields" :key="field" class="skeleton-field">
        <div class="skeleton-label"></div>
        <div class="skeleton-input"></div>
      </div>
      <div class="skeleton-form-actions">
        <div class="skeleton-button primary"></div>
        <div class="skeleton-button secondary"></div>
      </div>
    </div>
    
    <!-- Custom Rectangle -->
    <div v-else-if="variant === 'rectangle'" class="skeleton-rectangle"></div>
    
    <!-- Custom Circle -->
    <div v-else-if="variant === 'circle'" class="skeleton-circle"></div>
    
    <!-- Default Text -->
    <div v-else class="skeleton-line"></div>
    
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

export interface Props {
  variant?: 'text' | 'avatar' | 'card' | 'list-item' | 'article' | 'table' | 'form' | 'rectangle' | 'circle'
  lines?: number
  lastLineWidth?: string
  width?: string
  height?: string
  rounded?: boolean
  animated?: boolean
  theme?: 'light' | 'dark' | 'auto'
  columns?: number
  rows?: number
  formFields?: number
  speed?: 'slow' | 'normal' | 'fast'
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'text',
  lines: 3,
  lastLineWidth: '60%',
  width: 'auto',
  height: 'auto',
  rounded: true,
  animated: true,
  theme: 'auto',
  columns: 4,
  rows: 5,
  formFields: 3,
  speed: 'normal'
})

const skeletonStyles = computed(() => {
  const styles: Record<string, string> = {}
  
  if (props.width !== 'auto') {
    styles.width = props.width
  }
  
  if (props.height !== 'auto') {
    styles.height = props.height
  }
  
  // Animation speed
  switch (props.speed) {
    case 'slow':
      styles['--skeleton-duration'] = '2s'
      break
    case 'fast':
      styles['--skeleton-duration'] = '0.8s'
      break
    default:
      styles['--skeleton-duration'] = '1.5s'
  }
  
  return styles
})
</script>

<style scoped>
.skeleton-loader {
  --skeleton-base: #e5e7eb;
  --skeleton-highlight: #f3f4f6;
  --skeleton-duration: 1.5s;
  
  display: block;
  width: 100%;
}

.skeleton-dark {
  --skeleton-base: #374151;
  --skeleton-highlight: #4b5563;
}

@media (prefers-color-scheme: dark) {
  .skeleton-loader:not(.skeleton-light) {
    --skeleton-base: #374151;
    --skeleton-highlight: #4b5563;
  }
}

/* Base skeleton element */
.skeleton-base {
  background: linear-gradient(
    90deg,
    var(--skeleton-base) 25%,
    var(--skeleton-highlight) 50%,
    var(--skeleton-base) 75%
  );
  background-size: 200% 100%;
  border-radius: 4px;
}

.skeleton-animated .skeleton-base,
.skeleton-animated .skeleton-line,
.skeleton-animated .skeleton-circle,
.skeleton-animated .skeleton-rectangle,
.skeleton-animated .skeleton-avatar,
.skeleton-animated .skeleton-image,
.skeleton-animated .skeleton-title,
.skeleton-animated .skeleton-title-small,
.skeleton-animated .skeleton-title-large,
.skeleton-animated .skeleton-subtitle,
.skeleton-animated .skeleton-button,
.skeleton-animated .skeleton-input,
.skeleton-animated .skeleton-th,
.skeleton-animated .skeleton-td,
.skeleton-animated .skeleton-category,
.skeleton-animated .skeleton-author-name,
.skeleton-animated .skeleton-date,
.skeleton-animated .skeleton-meta,
.skeleton-animated .skeleton-action,
.skeleton-animated .skeleton-label,
.skeleton-animated .skeleton-featured-image {
  animation: skeleton-pulse var(--skeleton-duration) ease-in-out infinite;
}

/* Text Skeleton */
.skeleton-line {
  @apply skeleton-base;
  height: 1rem;
  margin-bottom: 0.5rem;
}

.skeleton-line:last-child {
  margin-bottom: 0;
}

.skeleton-line.short {
  width: 75%;
}

.skeleton-line.medium {
  width: 85%;
}

/* Circle Elements */
.skeleton-circle {
  @apply skeleton-base;
  width: 3rem;
  height: 3rem;
  border-radius: 50%;
}

.skeleton-avatar {
  @apply skeleton-circle;
  width: 4rem;
  height: 4rem;
}

.skeleton-avatar-small {
  @apply skeleton-base;
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 50%;
  flex-shrink: 0;
}

/* Rectangle */
.skeleton-rectangle {
  @apply skeleton-base;
  width: 100%;
  height: 4rem;
}

/* Card Skeleton */
.skeleton-card {
  border: 1px solid var(--skeleton-base);
  border-radius: 0.5rem;
  overflow: hidden;
  background: white;
}

.skeleton-image {
  @apply skeleton-base;
  height: 12rem;
  border-radius: 0;
}

.skeleton-content {
  padding: 1rem;
}

.skeleton-title {
  @apply skeleton-base;
  height: 1.5rem;
  width: 80%;
  margin-bottom: 1rem;
}

.skeleton-text-lines {
  margin-bottom: 1rem;
}

.skeleton-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.skeleton-button {
  @apply skeleton-base;
  height: 2.5rem;
  width: 6rem;
}

.skeleton-button.primary {
  width: 8rem;
}

.skeleton-button.secondary {
  width: 6rem;
}

.skeleton-meta {
  @apply skeleton-base;
  height: 1rem;
  width: 4rem;
}

/* List Item Skeleton */
.skeleton-list-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border-bottom: 1px solid var(--skeleton-base);
}

.skeleton-list-item .skeleton-content {
  flex: 1;
  padding: 0;
}

.skeleton-title-small {
  @apply skeleton-base;
  height: 1.25rem;
  width: 70%;
  margin-bottom: 0.5rem;
}

.skeleton-subtitle {
  @apply skeleton-base;
  height: 1rem;
  width: 50%;
}

.skeleton-action {
  @apply skeleton-base;
  height: 2rem;
  width: 3rem;
  flex-shrink: 0;
}

/* Article Skeleton */
.skeleton-article {
  max-width: 100%;
}

.skeleton-header {
  margin-bottom: 2rem;
}

.skeleton-category {
  @apply skeleton-base;
  height: 1rem;
  width: 6rem;
  margin-bottom: 1rem;
}

.skeleton-title-large {
  @apply skeleton-base;
  height: 2rem;
  width: 90%;
  margin-bottom: 1.5rem;
}

.skeleton-meta-row {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.skeleton-author-info {
  flex: 1;
}

.skeleton-author-name {
  @apply skeleton-base;
  height: 1rem;
  width: 8rem;
  margin-bottom: 0.25rem;
}

.skeleton-date {
  @apply skeleton-base;
  height: 0.875rem;
  width: 6rem;
}

.skeleton-featured-image {
  @apply skeleton-base;
  height: 20rem;
  width: 100%;
  margin-bottom: 2rem;
  border-radius: 0.5rem;
}

.skeleton-paragraph {
  margin-bottom: 1.5rem;
}

/* Table Skeleton */
.skeleton-table {
  width: 100%;
  border-collapse: collapse;
}

.skeleton-table-header {
  display: flex;
  gap: 1rem;
  padding: 1rem;
  border-bottom: 2px solid var(--skeleton-base);
  background: var(--skeleton-highlight);
}

.skeleton-th {
  @apply skeleton-base;
  height: 1.25rem;
  flex: 1;
}

.skeleton-table-row {
  display: flex;
  gap: 1rem;
  padding: 1rem;
  border-bottom: 1px solid var(--skeleton-base);
}

.skeleton-td {
  @apply skeleton-base;
  height: 1rem;
  flex: 1;
}

/* Form Skeleton */
.skeleton-form {
  max-width: 24rem;
}

.skeleton-field {
  margin-bottom: 1.5rem;
}

.skeleton-label {
  @apply skeleton-base;
  height: 1rem;
  width: 6rem;
  margin-bottom: 0.5rem;
}

.skeleton-input {
  @apply skeleton-base;
  height: 2.5rem;
  width: 100%;
}

.skeleton-form-actions {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
}

/* Rounded variant */
.skeleton-rounded .skeleton-line,
.skeleton-rounded .skeleton-rectangle,
.skeleton-rounded .skeleton-button,
.skeleton-rounded .skeleton-input {
  border-radius: 0.5rem;
}

.skeleton-rounded .skeleton-title,
.skeleton-rounded .skeleton-title-small,
.skeleton-rounded .skeleton-title-large {
  border-radius: 0.5rem;
}

/* Animation */
@keyframes skeleton-pulse {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .skeleton-card .skeleton-content {
    padding: 0.75rem;
  }
  
  .skeleton-list-item {
    padding: 0.75rem;
  }
  
  .skeleton-featured-image {
    height: 12rem;
  }
  
  .skeleton-meta-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .skeleton-form-actions {
    flex-direction: column;
  }
  
  .skeleton-table-header,
  .skeleton-table-row {
    padding: 0.5rem;
    gap: 0.5rem;
  }
}

/* High contrast mode */
@media (prefers-contrast: high) {
  .skeleton-loader {
    --skeleton-base: #000000;
    --skeleton-highlight: #333333;
  }
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
  .skeleton-animated .skeleton-base,
  .skeleton-animated .skeleton-line,
  .skeleton-animated .skeleton-circle,
  .skeleton-animated .skeleton-rectangle,
  .skeleton-animated .skeleton-avatar,
  .skeleton-animated .skeleton-image,
  .skeleton-animated .skeleton-title,
  .skeleton-animated .skeleton-title-small,
  .skeleton-animated .skeleton-title-large,
  .skeleton-animated .skeleton-subtitle,
  .skeleton-animated .skeleton-button,
  .skeleton-animated .skeleton-input,
  .skeleton-animated .skeleton-th,
  .skeleton-animated .skeleton-td,
  .skeleton-animated .skeleton-category,
  .skeleton-animated .skeleton-author-name,
  .skeleton-animated .skeleton-date,
  .skeleton-animated .skeleton-meta,
  .skeleton-animated .skeleton-action,
  .skeleton-animated .skeleton-label,
  .skeleton-animated .skeleton-featured-image {
    animation: none;
    background: var(--skeleton-base);
  }
}
</style>
