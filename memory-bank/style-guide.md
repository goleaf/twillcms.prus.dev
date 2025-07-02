# ENTERPRISE NEWS DESIGN SYSTEM

**Project**: TwillCMS Enterprise News Portal  
**Framework**: Vue.js 3 + TailwindCSS  
**Inspiration**: NYT, BBC News, The Atlantic, Medium  
**Target**: Best-in-class news reading experience  

## 🎨 **COLOR PALETTE - NEWS EXCELLENCE**

### **Primary Colors**
```css
/* News Brand Colors */
--news-primary-50: #eff6ff    /* Light blue tint */
--news-primary-100: #dbeafe   /* Subtle blue */
--news-primary-500: #3b82f6   /* Main brand blue */
--news-primary-600: #2563eb   /* Hover blue */
--news-primary-900: #1e3a8a   /* Deep blue for headers */

/* News Accent Colors */
--news-red: #dc2626          /* Breaking news, urgent */
--news-orange: #ea580c       /* Trending, popular */
--news-green: #16a34a        /* Positive, success */
--news-purple: #9333ea       /* Premium, special */
```

### **Content Colors**
```css
/* Typography Colors */
--text-primary: #0f172a      /* Main content - very dark */
--text-secondary: #475569    /* Subheadings, metadata */
--text-muted: #64748b        /* Captions, timestamps */
--text-light: #94a3b8       /* Placeholders, disabled */

/* Background Colors */
--bg-primary: #ffffff        /* Main background */
--bg-secondary: #f8fafc     /* Section backgrounds */
--bg-tertiary: #f1f5f9      /* Cards, elevated content */
--bg-dark: #0f172a          /* Dark mode primary */
```

### **Semantic Colors**
```css
/* Status & Feedback */
--success: #059669           /* Positive actions */
--warning: #d97706           /* Alerts, cautions */
--error: #dc2626            /* Errors, destructive */
--info: #0284c7             /* Information, neutral */

/* Content Categories */
--politics: #7c3aed         /* Politics category */
--business: #059669         /* Business/finance */
--tech: #0284c7            /* Technology */
--sports: #dc2626          /* Sports */
--culture: #9333ea         /* Arts & culture */
```

## 📝 **TYPOGRAPHY SYSTEM - READING OPTIMIZED**

### **Font Stack**
```css
/* Primary Font - News Reading */
font-family: 'Inter', 'Helvetica Neue', 'Arial', sans-serif;

/* Secondary Font - Headlines */
font-family: 'Playfair Display', 'Georgia', serif;

/* Monospace - Code, Data */
font-family: 'JetBrains Mono', 'Consolas', monospace;
```

### **Font Scale - News Hierarchy**
```css
/* Headlines */
.text-hero: 3.5rem      /* 56px - Hero articles */
.text-headline: 2.5rem  /* 40px - Main headlines */
.text-subhead: 2rem     /* 32px - Section headers */
.text-title: 1.5rem     /* 24px - Article titles */

/* Body Text */
.text-lead: 1.25rem     /* 20px - Lead paragraphs */
.text-body: 1rem        /* 16px - Main content */
.text-small: 0.875rem   /* 14px - Captions, meta */
.text-xs: 0.75rem       /* 12px - Timestamps */

/* Line Heights - Reading Optimized */
.leading-tight: 1.25    /* Headlines */
.leading-normal: 1.5    /* Body text */
.leading-relaxed: 1.75  /* Long-form content */
```

### **Font Weights**
```css
.font-light: 300        /* Light text, captions */
.font-normal: 400       /* Body text */
.font-medium: 500       /* Subheadings */
.font-semibold: 600     /* Article titles */
.font-bold: 700         /* Main headlines */
.font-black: 900        /* Hero headlines */
```

## 📐 **SPACING SYSTEM - CONTENT RHYTHM**

### **Base Spacing Scale**
```css
/* TailwindCSS Scale Enhanced for News */
.space-1: 0.25rem      /* 4px - Tight spacing */
.space-2: 0.5rem       /* 8px - Small gaps */
.space-3: 0.75rem      /* 12px - Standard gaps */
.space-4: 1rem         /* 16px - Paragraph spacing */
.space-6: 1.5rem       /* 24px - Section spacing */
.space-8: 2rem         /* 32px - Component spacing */
.space-12: 3rem        /* 48px - Major sections */
.space-16: 4rem        /* 64px - Page sections */
.space-24: 6rem        /* 96px - Hero spacing */
```

### **Content Specific Spacing**
```css
/* Article Content */
--article-paragraph-spacing: 1.5rem    /* Between paragraphs */
--article-section-spacing: 3rem        /* Between sections */
--article-image-spacing: 2rem          /* Around images */

/* Layout Spacing */
--header-height: 4rem                  /* Navigation height */
--sidebar-width: 20rem                 /* Sidebar width */
--content-max-width: 45rem             /* Reading width */
--container-padding: 1.5rem            /* Mobile padding */
```

## 🔧 **COMPONENT DESIGN TOKENS**

### **Article Cards**
```css
/* Card Variants */
.card-hero {
  @apply bg-white rounded-lg shadow-lg overflow-hidden;
  @apply hover:shadow-xl transition-shadow duration-300;
  @apply border-l-4 border-news-red;
}

.card-featured {
  @apply bg-white rounded-lg shadow-md overflow-hidden;
  @apply hover:shadow-lg transition-all duration-300;
  @apply ring-1 ring-slate-200;
}

.card-standard {
  @apply bg-white rounded-md shadow-sm overflow-hidden;
  @apply hover:shadow-md transition-shadow duration-200;
  @apply border border-slate-200;
}

.card-minimal {
  @apply border-b border-slate-200 pb-4 mb-4;
  @apply hover:bg-slate-50 transition-colors duration-200;
}
```

### **Buttons & Interactive Elements**
```css
/* Primary Actions */
.btn-primary {
  @apply bg-news-primary-600 text-white px-6 py-3 rounded-md;
  @apply hover:bg-news-primary-700 active:bg-news-primary-800;
  @apply font-medium transition-colors duration-200;
  @apply focus:outline-none focus:ring-2 focus:ring-news-primary-500;
}

/* Secondary Actions */
.btn-secondary {
  @apply bg-white text-news-primary-600 px-6 py-3 rounded-md;
  @apply border border-news-primary-600 hover:bg-news-primary-50;
  @apply font-medium transition-colors duration-200;
}

/* Category Tags */
.tag-category {
  @apply inline-block px-3 py-1 rounded-full text-sm font-medium;
  @apply bg-slate-100 text-slate-700 hover:bg-slate-200;
  @apply transition-colors duration-200;
}
```

### **Navigation Components**
```css
/* Main Navigation */
.nav-main {
  @apply bg-white border-b border-slate-200 sticky top-0 z-50;
  @apply shadow-sm backdrop-blur-sm bg-white/95;
}

.nav-link {
  @apply text-slate-700 hover:text-news-primary-600;
  @apply font-medium px-4 py-2 rounded-md;
  @apply transition-colors duration-200;
}

.nav-link-active {
  @apply text-news-primary-600 bg-news-primary-50;
}

/* Breadcrumbs */
.breadcrumb {
  @apply flex items-center space-x-2 text-sm text-slate-500;
}

.breadcrumb-separator {
  @apply text-slate-300;
}
```

## 📱 **RESPONSIVE DESIGN BREAKPOINTS**

### **Screen Sizes - Mobile First**
```css
/* Breakpoints */
sm: 640px    /* Small tablets, large phones */
md: 768px    /* Tablets */
lg: 1024px   /* Small laptops */
xl: 1280px   /* Large laptops */
2xl: 1536px  /* Large desktops */

/* Content Widths */
.container-sm: max-width: 640px;    /* Mobile content */
.container-md: max-width: 768px;    /* Tablet content */
.container-lg: max-width: 1024px;   /* Desktop content */
.container-xl: max-width: 1280px;   /* Wide desktop */
```

### **Typography Responsive Scale**
```css
/* Headlines Scale */
.text-hero {
  @apply text-3xl lg:text-5xl xl:text-6xl;
  @apply leading-tight lg:leading-none;
}

.text-headline {
  @apply text-2xl lg:text-4xl xl:text-5xl;
  @apply leading-tight;
}

.text-title {
  @apply text-lg lg:text-xl xl:text-2xl;
  @apply leading-snug;
}

/* Content Padding */
.content-padding {
  @apply px-4 md:px-6 lg:px-8 xl:px-12;
}
```

## 🎯 **ACCESSIBILITY STANDARDS**

### **Color Contrast Requirements**
```css
/* WCAG AA Compliance */
text-on-white: contrast-ratio >= 4.5:1
text-on-dark: contrast-ratio >= 4.5:1
large-text: contrast-ratio >= 3:1

/* Focus States */
.focus-ring {
  @apply focus:outline-none focus:ring-2;
  @apply focus:ring-news-primary-500 focus:ring-offset-2;
}
```

### **Semantic HTML Patterns**
```html
<!-- Article Structure -->
<article role="article">
  <header>
    <h1>Headline</h1>
    <p role="doc-subtitle">Subheading</p>
    <time datetime="2024-01-15">Published Date</time>
  </header>
  <div role="main">Content</div>
</article>

<!-- Navigation -->
<nav role="navigation" aria-label="Main navigation">
  <ul role="menubar">
    <li role="menuitem"><a href="/">Home</a></li>
  </ul>
</nav>
```

## 📖 **READING EXPERIENCE OPTIMIZATION**

### **Content Layout**
```css
/* Optimal Reading Width */
.content-reading {
  @apply max-w-prose mx-auto;      /* ~65 characters per line */
  @apply text-lg leading-relaxed;   /* Comfortable reading */
  @apply text-slate-900;           /* High contrast */
}

/* Image Integration */
.content-image {
  @apply w-full rounded-lg shadow-md my-8;
  @apply max-w-4xl mx-auto;
}

.image-caption {
  @apply text-sm text-slate-600 mt-2 italic text-center;
}
```

### **Interactive Reading Features**
```css
/* Reading Progress */
.reading-progress {
  @apply fixed top-0 left-0 h-1 bg-news-primary-600;
  @apply transition-all duration-300 ease-out z-50;
}

/* Social Sharing */
.social-share {
  @apply sticky top-24 flex flex-col space-y-2;
  @apply opacity-75 hover:opacity-100 transition-opacity;
}

/* Back to Top */
.back-to-top {
  @apply fixed bottom-6 right-6 p-3 rounded-full;
  @apply bg-news-primary-600 text-white shadow-lg;
  @apply hover:bg-news-primary-700 transition-colors;
}
```

## 🌟 **ANIMATION & TRANSITIONS**

### **Micro-Interactions**
```css
/* Hover Effects */
.hover-lift {
  @apply transition-transform duration-200;
  @apply hover:-translate-y-1 hover:shadow-lg;
}

.hover-scale {
  @apply transition-transform duration-200;
  @apply hover:scale-105;
}

/* Loading States */
.loading-pulse {
  @apply animate-pulse bg-slate-200 rounded;
}

.loading-shimmer {
  @apply bg-gradient-to-r from-slate-200 via-slate-300 to-slate-200;
  @apply animate-pulse;
}

/* Page Transitions */
.page-enter {
  @apply opacity-0 translate-y-4;
}

.page-enter-active {
  @apply transition-all duration-300 ease-out;
}

.page-enter-to {
  @apply opacity-100 translate-y-0;
}
```

## 🎨 **COMPONENT SHOWCASE**

### **Article Card Layouts**
```html
<!-- Hero Article Card -->
<article class="card-hero group">
  <div class="relative h-64 overflow-hidden">
    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
    <div class="absolute top-4 left-4">
      <span class="tag-category bg-news-red text-white">Breaking</span>
    </div>
  </div>
  <div class="p-6">
    <h2 class="text-title font-bold mb-3 group-hover:text-news-primary-600">
      Article Headline
    </h2>
    <p class="text-body text-slate-600 mb-4">Article excerpt...</p>
    <div class="flex items-center justify-between text-small text-slate-500">
      <time>Jan 15, 2024</time>
      <span>5 min read</span>
    </div>
  </div>
</article>

<!-- Grid Article Card -->
<article class="card-standard group">
  <img class="w-full h-48 object-cover" />
  <div class="p-4">
    <span class="tag-category">Technology</span>
    <h3 class="text-lg font-semibold mt-2 mb-2 group-hover:text-news-primary-600">
      Article Title
    </h3>
    <p class="text-slate-600 text-sm">Brief excerpt...</p>
    <time class="text-xs text-slate-500 mt-2 block">2 hours ago</time>
  </div>
</article>
```

### **Navigation Header**
```html
<header class="nav-main">
  <div class="container mx-auto px-4">
    <div class="flex items-center justify-between h-16">
      <div class="flex items-center space-x-8">
        <h1 class="text-headline font-black text-news-primary-900">NewsPortal</h1>
        <nav class="hidden md:flex space-x-1">
          <a href="/" class="nav-link nav-link-active">Home</a>
          <a href="/politics" class="nav-link">Politics</a>
          <a href="/business" class="nav-link">Business</a>
          <a href="/tech" class="nav-link">Technology</a>
        </nav>
      </div>
      <div class="flex items-center space-x-4">
        <button class="btn-secondary">Subscribe</button>
        <button class="p-2 rounded-md hover:bg-slate-100">
          <svg class="w-5 h-5" fill="currentColor"><!-- Search icon --></svg>
        </button>
      </div>
    </div>
  </div>
</header>
```

## 🔍 **DESIGN PRINCIPLES**

### **Content First**
- Typography optimized for reading comfort
- Generous white space for content breathing
- Clear visual hierarchy for information scanning

### **Performance Focused**
- Lightweight animations and transitions
- Optimized images with proper aspect ratios
- Minimal CSS footprint with utility classes

### **Accessibility Champion**
- High contrast color combinations
- Focus states for keyboard navigation
- Semantic HTML structure throughout
- Screen reader friendly markup

### **Modern & Timeless**
- Clean, uncluttered layouts
- Professional color palette
- Scalable typography system
- Flexible component architecture

---

**This design system provides the foundation for building a world-class news reading experience that rivals the best publications while maintaining excellent performance and accessibility standards.** 