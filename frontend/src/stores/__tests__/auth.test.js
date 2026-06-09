import { describe, it, expect, beforeEach, vi } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'

vi.mock('axios')

describe('useAuthStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('starts with no user and loading true', () => {
    const store = useAuthStore()
    expect(store.user).toBeNull()
    expect(store.loading).toBe(true)
    expect(store.isAuthenticated).toBe(false)
    expect(store.isAdmin).toBe(false)
  })

  it('isAdmin returns true for Administrator role', () => {
    const store = useAuthStore()
    store.user = { role: 'Administrator' }
    expect(store.isAdmin).toBe(true)
  })

  it('isAdmin returns true for Super Administrator role', () => {
    const store = useAuthStore()
    store.user = { role: 'Super Administrator' }
    expect(store.isAdmin).toBe(true)
  })

  it('isAdmin returns false for Employee role', () => {
    const store = useAuthStore()
    store.user = { role: 'Employee' }
    expect(store.isAdmin).toBe(false)
  })

  it('login calls CSRF cookie then API and sets user', async () => {
    axios.get.mockResolvedValue({})
    axios.post.mockResolvedValue({
      data: {
        user: { id: 1, name: 'Test', role: 'Employee' },
      },
    })

    const store = useAuthStore()
    await store.login({ email: 'test@test.com', password: 'password' })

    expect(axios.get).toHaveBeenCalledWith('/sanctum/csrf-cookie')
    expect(axios.post).toHaveBeenCalledWith('/api/v1/login', {
      email: 'test@test.com',
      password: 'password',
    })
    expect(store.user.name).toBe('Test')
    expect(store.isAuthenticated).toBe(true)
  })

  it('logout clears user state', async () => {
    axios.post.mockResolvedValue({})
    const store = useAuthStore()
    store.user = { id: 1, name: 'Test', role: 'Employee' }

    await store.logout()

    expect(store.user).toBeNull()
    expect(store.isAuthenticated).toBe(false)
  })

  it('init sets user on success', async () => {
    axios.get.mockResolvedValue({
      data: { id: 1, name: 'Test', role: 'Employee' },
    })

    const store = useAuthStore()
    await store.init()

    expect(store.user.name).toBe('Test')
    expect(store.isAuthenticated).toBe(true)
    expect(store.loading).toBe(false)
  })

  it('init sets user to null on failure', async () => {
    axios.get.mockRejectedValue(new Error('Unauthorized'))

    const store = useAuthStore()
    store.user = { id: 1, name: 'Old', role: 'Employee' }
    await store.init()

    expect(store.user).toBeNull()
    expect(store.isAuthenticated).toBe(false)
    expect(store.loading).toBe(false)
  })

  it('register calls CSRF cookie then API and sets user', async () => {
    axios.get.mockResolvedValue({})
    axios.post.mockResolvedValue({
      data: {
        user: { id: 2, name: 'New', role: 'Employee' },
      },
    })

    const store = useAuthStore()
    await store.register({ name: 'New', email: 'new@test.com', password: 'password' })

    expect(axios.get).toHaveBeenCalledWith('/sanctum/csrf-cookie')
    expect(axios.post).toHaveBeenCalledWith('/api/v1/register', {
      name: 'New',
      email: 'new@test.com',
      password: 'password',
    })
    expect(store.user.name).toBe('New')
    expect(store.isAuthenticated).toBe(true)
  })

  it('updateProfile sends FormData and updates user', async () => {
    axios.post.mockResolvedValue({
      data: {
        user: { id: 1, name: 'Updated', role: 'Employee', bio: 'New bio' },
      },
    })

    const store = useAuthStore()
    store.user = { id: 1, name: 'Original', role: 'Employee' }

    await store.updateProfile({ name: 'Updated', bio: 'New bio' })

    expect(store.user.name).toBe('Updated')
    expect(store.user.bio).toBe('New bio')
  })
})
