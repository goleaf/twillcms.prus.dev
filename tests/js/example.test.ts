import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import { createApp } from 'vue'

describe('Basic Vue Test', () => {
  it('should create a Vue app', () => {
    const app = createApp({
      template: '<div>Hello World</div>'
    })
    expect(app).toBeDefined()
  })

  it('should mount a simple component', () => {
    const TestComponent = {
      template: '<div>{{ message }}</div>',
      data() {
        return {
          message: 'Hello Vue!'
        }
      }
    }

    const wrapper = mount(TestComponent)
    expect(wrapper.text()).toBe('Hello Vue!')
  })
})
