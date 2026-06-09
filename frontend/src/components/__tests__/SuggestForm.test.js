import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import SuggestForm from '@/components/SuggestForm.vue'
import axios from 'axios'

vi.mock('axios')

describe('SuggestForm', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('renders form fields', () => {
    const wrapper = mount(SuggestForm)
    expect(wrapper.find('#suggest-title').exists()).toBe(true)
    expect(wrapper.find('#suggest-category').exists()).toBe(true)
    expect(wrapper.find('#suggest-description').exists()).toBe(true)
    expect(wrapper.find('button[type="submit"]').exists()).toBe(true)
  })

  it('validates title min length', async () => {
    const wrapper = mount(SuggestForm)
    await wrapper.find('#suggest-title').setValue('AB')
    await wrapper.find('#suggest-description').setValue('This is a valid description that is long enough')
    await wrapper.find('#suggest-category').setValue('Technology')
    await wrapper.find('form').trigger('submit')

    expect(wrapper.text()).toContain('Title must be at least 5 characters')
  })

  it('validates category selection', async () => {
    const wrapper = mount(SuggestForm)
    await wrapper.find('#suggest-title').setValue('Valid Title')
    await wrapper.find('#suggest-description').setValue('This is a valid description that is long enough')
    await wrapper.find('form').trigger('submit')

    expect(wrapper.text()).toContain('Please select a category')
  })

  it('validates description min length', async () => {
    const wrapper = mount(SuggestForm)
    await wrapper.find('#suggest-title').setValue('Valid Title')
    await wrapper.find('#suggest-category').setValue('Technology')
    await wrapper.find('#suggest-description').setValue('Short')
    await wrapper.find('form').trigger('submit')

    expect(wrapper.text()).toContain('Description must be at least 20 characters')
  })

  it('submits form and emits submitted event', async () => {
    const mockSuggestion = {
      id: 1,
      title: 'Test Suggestion',
      category: 'Technology',
      description: 'This is a valid description that is long enough',
      status: 'Pending',
      created_at: '2025-01-01T00:00:00Z',
    }

    axios.post.mockResolvedValue({
      data: { data: mockSuggestion, success: true, message: 'Suggestion saved successfully.' },
    })

    const wrapper = mount(SuggestForm)
    await wrapper.find('#suggest-title').setValue('Test Suggestion')
    await wrapper.find('#suggest-category').setValue('Technology')
    await wrapper.find('#suggest-description').setValue('This is a valid description that is long enough')
    await wrapper.find('form').trigger('submit')

    await new Promise(process.nextTick)

    expect(axios.post).toHaveBeenCalledWith('/api/v1/suggestions', {
      title: 'Test Suggestion',
      category: 'Technology',
      description: 'This is a valid description that is long enough',
    })

    expect(wrapper.text()).toContain('Suggestion submitted successfully!')
  })

  it('shows API error message', async () => {
    axios.post.mockRejectedValue({
      response: {
        data: { message: 'Something went wrong. Please try again.' },
      },
    })

    const wrapper = mount(SuggestForm)
    await wrapper.find('#suggest-title').setValue('Test Suggestion')
    await wrapper.find('#suggest-category').setValue('Technology')
    await wrapper.find('#suggest-description').setValue('This is a valid description that is long enough')
    await wrapper.find('form').trigger('submit')

    await new Promise(process.nextTick)

    expect(wrapper.text()).toContain('Something went wrong')
  })

  it('reset button clears the form', async () => {
    const wrapper = mount(SuggestForm)
    await wrapper.find('#suggest-title').setValue('Test')
    await wrapper.find('button[type="button"]').trigger('click')

    expect(wrapper.find('#suggest-title').element.value).toBe('')
  })
})
