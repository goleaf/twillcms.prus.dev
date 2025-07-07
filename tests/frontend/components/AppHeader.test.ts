import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createRouter, createWebHistory } from 'vue-router'
import { createPinia } from 'pinia'
import AppHeader from '@/components/layout/AppHeader.vue'

// Mock Heroicons
vi.mock('@heroicons/vue/24/outline', () => ({
  ChevronDownIcon: { name: 'ChevronDownIcon', template: '<svg data-testid="chevron-down-icon"></svg>' },
  MagnifyingGlassIcon: { name: 'MagnifyingGlassIcon', template: '<svg data-testid="search-icon"></svg>' },
  LanguageIcon: { name: 'LanguageIcon', template: '<svg data-testid="language-icon"></svg>' },
  Bars3Icon: { name: 'Bars3Icon', template: '<svg data-testid="bars-icon"></svg>' },
  XMarkIcon: { name: 'XMarkIcon', template: '<svg data-testid="x-mark-icon"></svg>' },
  HomeIcon: { name: 'HomeIcon', template: '<svg data-testid="home-icon"></svg>' },
  DocumentTextIcon: { name: 'DocumentTextIcon', template: '<svg data-testid="document-icon"></svg>' },
  FolderIcon: { name: 'FolderIcon', template: '<svg data-testid="folder-icon"></svg>' }
}))

// Mock stores
const mockSiteStore = {
  siteName: 'Test Blog'
}

const mockCategoryStore = {
  navigationCategories: [
    { id: 1, name: 'Technology', slug: 'technology', color: '#3B82F6', posts_count: 5 },
    { id: 2, name: 'Design', slug: 'design', color: '#10B981', posts_count: 3 }
  ]
}

// Mock translation function
const mockT = vi.fn((key: string) => {
  const translations: Record<string, string> = {
    'navigation.home': 'Home',
    'navigation.blog': 'Blog', 
    'navigation.categories': 'Categories',
    'navigation.search': 'Search',
    'navigation.language': 'Language',
    'navigation.menu': 'Menu',
    'categories.view_all_categories': 'View All Categories'
  }
  return translations[key] || key
})

// Create router for testing
const createTestRouter = () => {
  return createRouter({
    history: createWebHistory(),
    routes: [
      { path: '/', name: 'home', component: { template: '<div>Home</div>' } },
      { path: '/blog', name: 'blog', component: { template: '<div>Blog</div>' } },
      { path: '/categories', name: 'categories', component: { template: '<div>Categories</div>' } },
      { path: '/category/:slug', name: 'category', component: { template: '<div>Category</div>' } }
    ]
  })
}

describe('AppHeader', () => {
  let router: any
  let pinia: any

  beforeEach(() => {
    router = createTestRouter()
    pinia = createPinia()
  })
import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import AppHeader from '@/components/AppHeader.vue';

describe('AppHeader', () => {
  it('renders properly', () => {
    const wrapper = mount(AppHeader, {
      props: {
        title: 'Test App'
      }
    });

    expect(wrapper.text()).toContain('Test App');
  });

  it('emits toggle-search when search button is clicked', async () => {
    const wrapper = mount(AppHeader);

    const searchButton = wrapper.find('.search-button');
    await searchButton.trigger('click');

    expect(wrapper.emitted('toggle-search')).toBeTruthy();
  });

  it('displays default title when no title prop is provided', () => {
    const wrapper = mount(AppHeader);

    expect(wrapper.text()).toContain('My App');
  });

  it('renders navigation slot', () => {
    const wrapper = mount(AppHeader, {
      slots: {
        navigation: '<a href="/home">Home</a>'
      }
    });

    expect(wrapper.html()).toContain('<a href="/home">Home</a>');
  });
});
  const createWrapper = async (routePath = '/') => {
    router.push(routePath)
    await router.isReady()

    return mount(AppHeader, {
      global: {
        plugins: [router, pinia],
        mocks: {
          t: mockT,
          siteStore: mockSiteStore,
          categoryStore: mockCategoryStore,
          currentLocale: 'en',
          availableLocales: ['en', 'lt']
        },
        stubs: {
          'router-link': {
            template: '<a><slot /></a>',
            props: ['to']
          }
        }
      }
    })
  }

  it('renders site name correctly', async () => {
    const wrapper = await createWrapper()
    
    expect(wrapper.text()).toContain('Test Blog')
  })

  it('displays navigation links', async () => {
    const wrapper = await createWrapper()
    
    expect(wrapper.text()).toContain('Home')
    expect(wrapper.text()).toContain('Blog')
    expect(wrapper.text()).toContain('Categories')
  })

  it('shows search button with correct icon', async () => {
    const wrapper = await createWrapper()
    
    const searchButton = wrapper.find('[aria-label="Search"]')
    expect(searchButton.exists()).toBe(true)
    expect(searchButton.find('[data-testid="search-icon"]').exists()).toBe(true)
  })

  it('toggles categories dropdown on click', async () => {
    const wrapper = await createWrapper()
    
    // Initially dropdown should be hidden
    expect(wrapper.find('.absolute.right-0.mt-2').exists()).toBe(false)
    
    // Click categories button
    const categoriesButton = wrapper.find('button').filter(btn => 
      btn.text().includes('Categories')
    )[0]
    await categoriesButton.trigger('click')
    
    // Wait for next tick
    await wrapper.vm.$nextTick()
    
    // Dropdown should be visible
    expect(wrapper.text()).toContain('View All Categories')
    expect(wrapper.text()).toContain('Technology')
    expect(wrapper.text()).toContain('Design')
  })

  it('shows category posts count', async () => {
    const wrapper = await createWrapper()
    
    // Open categories dropdown
    const categoriesButton = wrapper.find('button').filter(btn => 
      btn.text().includes('Categories')
    )[0]
    await categoriesButton.trigger('click')
    await wrapper.vm.$nextTick()
    
    expect(wrapper.text()).toContain('5') // Technology posts count
    expect(wrapper.text()).toContain('3') // Design posts count
  })

  it('displays language switcher', async () => {
    const wrapper = await createWrapper()
    
    const languageButton = wrapper.find('[aria-label="Language"]')
    expect(languageButton.exists()).toBe(true)
    expect(languageButton.find('[data-testid="language-icon"]').exists()).toBe(true)
  })

  it('shows mobile menu button on mobile', async () => {
    const wrapper = await createWrapper()
    
    const mobileButton = wrapper.find('[aria-label="Menu"]')
    expect(mobileButton.exists()).toBe(true)
    expect(mobileButton.find('[data-testid="bars-icon"]').exists()).toBe(true)
  })

  it('toggles mobile menu correctly', async () => {
    const wrapper = await createWrapper()
    
    // Mobile menu should be hidden initially
    expect(wrapper.find('.md\\:hidden.border-t').exists()).toBe(false)
    
    // Click mobile menu button
    const mobileButton = wrapper.find('[aria-label="Menu"]')
    await mobileButton.trigger('click')
    await wrapper.vm.$nextTick()
    
    // Should show X mark icon when menu is open
    expect(mobileButton.find('[data-testid="x-mark-icon"]').exists()).toBe(true)
  })

  it('renders navigation with proper accessibility attributes', async () => {
    const wrapper = await createWrapper()
    
    // Check aria-labels are present
    expect(wrapper.find('[aria-label="Search"]').exists()).toBe(true)
    expect(wrapper.find('[aria-label="Language"]').exists()).toBe(true)
    expect(wrapper.find('[aria-label="Menu"]').exists()).toBe(true)
  })

  it('applies active class to current route', async () => {
    const wrapper = await createWrapper('/')
    
    // Should have active styling on home route
    const homeLink = wrapper.findAll('router-link-stub').find(link => 
      link.text().includes('Home')
    )
    expect(homeLink?.classes()).toContain('text-blue-600')
  })

  it('calls translation function for all text content', async () => {
    await createWrapper()
    
    expect(mockT).toHaveBeenCalledWith('navigation.home')
    expect(mockT).toHaveBeenCalledWith('navigation.blog')
    expect(mockT).toHaveBeenCalledWith('navigation.categories')
    expect(mockT).toHaveBeenCalledWith('navigation.search')
    expect(mockT).toHaveBeenCalledWith('navigation.language')
    expect(mockT).toHaveBeenCalledWith('navigation.menu')
  })

  it('has proper semantic HTML structure', async () => {
    const wrapper = await createWrapper()
    
    // Should have header element
    expect(wrapper.find('header').exists()).toBe(true)
    
    // Should have nav element
    expect(wrapper.find('nav').exists()).toBe(true)
    
    // Should have proper heading hierarchy
    expect(wrapper.find('h1, h2, h3').exists()).toBe(false) // Navigation shouldn't have headings
  })

  it('handles keyboard navigation', async () => {
    const wrapper = await createWrapper()
    
    // Categories dropdown should be keyboard accessible
    const categoriesButton = wrapper.find('button').filter(btn => 
      btn.text().includes('Categories')
    )[0]
    
    // Test Enter key
    await categoriesButton.trigger('keydown.enter')
    await wrapper.vm.$nextTick()
    
    expect(wrapper.text()).toContain('View All Categories')
  })
}) 