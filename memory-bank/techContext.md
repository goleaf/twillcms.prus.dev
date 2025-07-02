# TWILL CMS TECHNICAL CONTEXT

## CORE ARCHITECTURAL DECISIONS

### FRONTEND ARCHITECTURE (CRITICAL RULE)
**🚨 MANDATORY: NO LARAVEL BLADES FOR FRONTEND**

**Architectural Decision**: This project uses **Vue.js 3 ONLY** for all frontend development. Laravel Blades are **COMPLETELY PROHIBITED** for user-facing frontend components.

**Enforcement Rules:**
- ✅ **ALWAYS USE**: Vue.js 3 Composition API with `<script setup>`
- ✅ **ALWAYS USE**: TypeScript for type safety
- ✅ **ALWAYS USE**: Context7 (`/vuejs/docs`) for Vue.js documentation
- ✅ **ALWAYS USE**: API-first approach for data fetching
- ✅ **ALWAYS USE**: Component-based architecture

- 🚫 **NEVER USE**: Laravel Blade views for frontend
- 🚫 **NEVER USE**: Server-side rendering for user-facing pages  
- 🚫 **NEVER USE**: Mixed Blade/Vue.js approaches
- 🚫 **NEVER USE**: CDN links in frontend components

**Rationale**: Maximum performance, modern development experience, clear separation of concerns, and optimal user experience.

## CURRENT TECHNOLOGY STACK

### Frontend (Vue.js SPA)
- **Framework**: Vue.js 3.x (Latest) with Composition API
- **Build Tool**: Vite with TypeScript support
- **Router**: Vue Router 4.x for client-side routing
- **State Management**: Pinia (modern Vuex alternative)
- **Styling**: TailwindCSS framework (NO Bootstrap)
- **Testing**: Vitest + Vue Test Utils
- **Bundle Size**: 196KB total, 37KB gzipped
- **Performance**: Code splitting + lazy loading enabled

### Backend (Laravel API)
- **Framework**: Laravel 10.x 
- **CMS**: Twill CMS for admin interface
- **Database**: SQLite for development
- **API**: RESTful API endpoints only
- **Admin Interface**: Twill Admin (Blade-based, separate from frontend)

### Development Tools
- **Package Manager**: npm
- **Code Quality**: Laravel Pint (PSR-12)
- **Static Analysis**: PHPStan
- **Testing**: Laravel Test Suite + Vue Test Utils

### Build & Deployment
- **Frontend Build**: `npm run build` (Vite)
- **Backend Setup**: `composer install` + `php artisan migrate`
- **Development**: Separate frontend/backend development servers
- **Performance**: Optimized builds with vendor chunking

## ARCHITECTURE PATTERNS

### API-First Design
- Laravel serves JSON API endpoints only
- Vue.js consumes APIs for all data
- Clean separation between frontend/backend
- RESTful API design principles

### Component Architecture
- Single-File Components (SFC) with `<script setup>`
- Composition API for logic reuse
- TypeScript for type safety
- Reusable component library approach

### State Management
- Pinia stores for global state
- Composables for shared logic
- Reactive data flow
- API state management patterns

## PERFORMANCE OPTIMIZATION

### Frontend Optimizations
- **Code Splitting**: Route-based and component-based
- **Lazy Loading**: Dynamic imports for components
- **Bundle Analysis**: Vendor chunking optimization
- **Asset Optimization**: Vite build optimizations

### Backend Optimizations
- **API Efficiency**: Optimized database queries
- **Caching**: Laravel caching strategies
- **Response Size**: Minimal JSON responses

## TESTING STRATEGY

### Frontend Testing
- **Unit Tests**: Vue component testing with Vue Test Utils
- **Integration Tests**: API integration testing
- **E2E Tests**: User workflow testing
- **Performance Tests**: Bundle size and load time monitoring

### Backend Testing
- **Feature Tests**: API endpoint testing
- **Unit Tests**: Business logic testing
- **Database Tests**: Model and migration testing

## DEVELOPMENT WORKFLOW

### 1. Context7 Integration
```bash
# Always use Context7 for Vue.js documentation
Library ID: /vuejs/docs
Trust Score: 9.7/10
Code Snippets: 1486 examples
```

### 2. Component Development
```vue
<script setup lang="ts">
// Always use Composition API with TypeScript
import { ref, computed } from 'vue'

const count = ref(0)
const doubleCount = computed(() => count.value * 2)
</script>

<template>
  <!-- Clean, semantic HTML -->
</template>

<style scoped>
/* TailwindCSS classes only */
</style>
```

### 3. API Integration
```typescript
// Use composables for API calls
export function useApiData() {
  const data = ref(null)
  const loading = ref(false)
  
  const fetchData = async () => {
    loading.value = true
    // API call logic
    loading.value = false
  }
  
  return { data, loading, fetchData }
}
```

## QUALITY STANDARDS

### Code Quality Commands
```bash
./vendor/bin/pint              # Auto-format all code
./vendor/bin/pint --test       # Check style compliance  
./vendor/bin/phpstan analyse   # Run static analysis
php artisan test --parallel    # Run comprehensive tests
npm run build                  # Frontend production build
npm run test                   # Frontend testing
```

### Performance Benchmarks
- **Bundle Size**: < 200KB total
- **Gzip Size**: < 50KB
- **Build Time**: < 5 seconds
- **API Response**: < 200ms
- **Page Load**: < 2 seconds

## MIGRATION STATUS

### ✅ Completed Migrations
- Laravel Blade → Vue.js SPA conversion
- Bootstrap → TailwindCSS migration  
- Mixed architecture → API-first design
- Manual routing → Vue Router implementation
- Legacy state → Pinia state management

### 🔄 Ongoing Optimizations
- Advanced component patterns
- Enhanced performance monitoring
- Progressive Web App features
- Advanced testing coverage

---

**Last Updated**: Current session - Architecture enforcement
**Next Review**: After major feature implementations
**Documentation Source**: Context7 Vue.js docs (`/vuejs/docs`) 