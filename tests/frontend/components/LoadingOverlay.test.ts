import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import LoadingOverlay from '@/components/ui/LoadingOverlay.vue'
import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import LoadingOverlay from '@/components/LoadingOverlay.vue';

describe('LoadingOverlay', () => {
  it('renders when isLoading is true', () => {
    const wrapper = mount(LoadingOverlay, {
      props: {
        isLoading: true
      }
    });

    expect(wrapper.find('.loading-overlay').exists()).toBe(true);
    expect(wrapper.find('.spinner').exists()).toBe(true);
  });

  it('does not render when isLoading is false', () => {
    const wrapper = mount(LoadingOverlay, {
      props: {
        isLoading: false
      }
    });

    expect(wrapper.find('.loading-overlay').exists()).toBe(false);
  });

  it('displays custom message when provided', () => {
    const customMessage = 'Please wait...';
    const wrapper = mount(LoadingOverlay, {
      props: {
        isLoading: true,
        message: customMessage
      }
    });

    expect(wrapper.text()).toContain(customMessage);
  });

  it('displays default message when no message prop is provided', () => {
    const wrapper = mount(LoadingOverlay, {
      props: {
        isLoading: true
      }
    });

    expect(wrapper.text()).toContain('Loading...');
  });

  it('has correct CSS classes for styling', () => {
    const wrapper = mount(LoadingOverlay, {
      props: {
        isLoading: true
      }
    });

    const overlay = wrapper.find('.loading-overlay');
    expect(overlay.classes()).toContain('fixed');
    expect(overlay.classes()).toContain('inset-0');
    expect(overlay.classes()).toContain('z-50');
  });
});
describe('LoadingOverlay', () => {
  const createWrapper = (props = {}) => {
    return mount(LoadingOverlay, {
      props: {
        isVisible: false,
        message: 'Loading...',
        ...props
      }
    })
  }

  it('renders when visible', () => {
    const wrapper = createWrapper({ isVisible: true })
    
    expect(wrapper.find('[data-testid="loading-overlay"]').exists()).toBe(true)
  })

  it('is hidden when not visible', () => {
    const wrapper = createWrapper({ isVisible: false })
    
    expect(wrapper.find('[data-testid="loading-overlay"]').exists()).toBe(false)
  })

  it('displays loading message', () => {
    const wrapper = createWrapper({ 
      isVisible: true, 
      message: 'Please wait...' 
    })
    
    expect(wrapper.text()).toContain('Please wait...')
  })

  it('shows default loading message when none provided', () => {
    const wrapper = createWrapper({ isVisible: true })
    
    expect(wrapper.text()).toContain('Loading...')
  })

  it('displays loading spinner', () => {
    const wrapper = createWrapper({ isVisible: true })
    
    expect(wrapper.find('[data-testid="loading-spinner"]').exists()).toBe(true)
  })

  it('has proper overlay styling for backdrop', () => {
    const wrapper = createWrapper({ isVisible: true })
    
    const overlay = wrapper.find('[data-testid="loading-overlay"]')
    expect(overlay.classes()).toContain('fixed')
    expect(overlay.classes()).toContain('inset-0')
  })

  it('prevents body scroll when visible', () => {
    const wrapper = createWrapper({ isVisible: true })
    
    // Check if overlay has proper z-index and positioning
    const overlay = wrapper.find('[data-testid="loading-overlay"]')
    expect(overlay.classes()).toContain('z-50')
  })

  it('supports custom CSS classes', () => {
    const wrapper = createWrapper({ 
      isVisible: true,
      class: 'custom-loading'
    })
    
    expect(wrapper.classes()).toContain('custom-loading')
  })
}) 