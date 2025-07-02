import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createRouter, createWebHistory } from 'vue-router'
import SearchOverlay from '@/components/ui/SearchOverlay.vue'

// Mock XMarkIcon
vi.mock('@heroicons/vue/24/outline', () => ({
  XMarkIcon: { name: 'XMarkIcon', template: '<svg data-testid="close-icon"></svg>' }
}))

describe('SearchOverlay', () => {
  let router: any

  beforeEach(() => {
    router = createRouter({
      history: createWebHistory(),
      routes: [
        { path: '/', name: 'home', component: { template: '<div>Home</div>' } },
        { path: '/search', name: 'search', component: { template: '<div>Search</div>' } }
      ]
    })
  })

  const createWrapper = (props = {}) => {
    return mount(SearchOverlay, {
      props: {
        isVisible: false,
        ...props
      },
      global: {
        plugins: [router],
        stubs: {
          'router-link': {
            template: '<a><slot /></a>',
            props: ['to']
          }
        }
      }
    })
  }

  it('renders when visible', () => {
    const wrapper = createWrapper({ isVisible: true })
    
    expect(wrapper.find('[data-testid="search-overlay"]').exists()).toBe(true)
  })

  it('is hidden when not visible', () => {
    const wrapper = createWrapper({ isVisible: false })
    
    expect(wrapper.find('[data-testid="search-overlay"]').exists()).toBe(false)
  })

  it('displays search input when visible', () => {
    const wrapper = createWrapper({ isVisible: true })
    
    const searchInput = wrapper.find('input[type="text"]')
    expect(searchInput.exists()).toBe(true)
    expect(searchInput.attributes('placeholder')).toContain('Search')
  })

  it('shows close button', () => {
    const wrapper = createWrapper({ isVisible: true })
    
    const closeButton = wrapper.find('[data-testid="close-icon"]')
    expect(closeButton.exists()).toBe(true)
  })

  it('emits close event when close button clicked', async () => {
    const wrapper = createWrapper({ isVisible: true })
    
    const closeButton = wrapper.find('button')
    await closeButton.trigger('click')
    
    expect(wrapper.emitted('close')).toBeTruthy()
  })

  it('emits search event when form submitted', async () => {
    const wrapper = createWrapper({ isVisible: true })
    
    const form = wrapper.find('form')
    const searchInput = wrapper.find('input[type="text"]')
    
    await searchInput.setValue('test query')
    await form.trigger('submit')
    
    expect(wrapper.emitted('search')).toBeTruthy()
    expect(wrapper.emitted('search')[0]).toEqual(['test query'])
  })

  it('focuses search input when overlay becomes visible', async () => {
    const wrapper = createWrapper({ isVisible: false })
    
    // Mock focus method
    const focusSpy = vi.fn()
    const searchInput = wrapper.find('input[type="text"]')
    searchInput.element.focus = focusSpy
    
    await wrapper.setProps({ isVisible: true })
    await wrapper.vm.$nextTick()
    
    expect(focusSpy).toHaveBeenCalled()
  })

  it('closes overlay when ESC key is pressed', async () => {
    const wrapper = createWrapper({ isVisible: true })
    
    await wrapper.trigger('keydown.esc')
    
    expect(wrapper.emitted('close')).toBeTruthy()
  })

  it('closes overlay when clicking outside the search box', async () => {
    const wrapper = createWrapper({ isVisible: true })
    
    const overlay = wrapper.find('[data-testid="search-overlay"]')
    await overlay.trigger('click')
    
    expect(wrapper.emitted('close')).toBeTruthy()
  })

  it('does not close when clicking inside search box', async () => {
    const wrapper = createWrapper({ isVisible: true })
    
    const searchBox = wrapper.find('.search-box')
    await searchBox.trigger('click')
    
    expect(wrapper.emitted('close')).toBeFalsy()
  })

  it('has proper accessibility attributes', () => {
    const wrapper = createWrapper({ isVisible: true })
    
    expect(wrapper.find('[role="dialog"]').exists()).toBe(true)
    expect(wrapper.find('[aria-label]').exists()).toBe(true)
  })
}) 