import { describe, it, expect, beforeEach, vi } from 'vitest'
import { createPinia, setActivePinia } from 'pinia'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'

vi.mock('axios')

const localStorageMock = (() => {
  let store = {}
  return {
    getItem: (key) => store[key] ?? null,
    setItem: (key, value) => { store[key] = String(value) },
    removeItem: (key) => { delete store[key] },
    clear: () => { store = {} },
    get length() { return Object.keys(store).length },
    key: (i) => Object.keys(store)[i] ?? null,
  }
})()

Object.defineProperty(global, 'localStorage', { value: localStorageMock })

describe('useAuthStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    localStorage.clear()
  })

  it('starts with no user or token', () => {
    const store = useAuthStore()
    expect(store.user).toBeNull()
    expect(store.token).toBeNull()
    expect(store.isAuthenticated).toBe(false)
    expect(store.isAdmin).toBe(false)
  })

  it('setAuth stores user and token', () => {
    const store = useAuthStore()
    store.setAuth({
      access_token: 'test-token',
      user: { id: 1, name: 'Test', role: 'Employee' },
    })

    expect(store.token).toBe('test-token')
    expect(store.user.name).toBe('Test')
    expect(store.isAuthenticated).toBe(true)
    expect(store.isAdmin).toBe(false)
    expect(localStorage.getItem('access_token')).toBe('test-token')
  })

  it('isAdmin returns true for Administrator role', () => {
    const store = useAuthStore()
    store.setAuth({
      access_token: 'token',
      user: { role: 'Administrator' },
    })
    expect(store.isAdmin).toBe(true)
  })

  it('isAdmin returns true for Super Administrator role', () => {
    const store = useAuthStore()
    store.setAuth({
      access_token: 'token',
      user: { role: 'Super Administrator' },
    })
    expect(store.isAdmin).toBe(true)
  })

  it('isAdmin returns false for Employee role', () => {
    const store = useAuthStore()
    store.setAuth({
      access_token: 'token',
      user: { role: 'Employee' },
    })
    expect(store.isAdmin).toBe(false)
  })

  it('login calls API and sets auth', async () => {
    const mockResponse = {
      data: {
        access_token: 'login-token',
        user: { id: 1, name: 'Test', role: 'Employee' },
      },
    }
    axios.post.mockResolvedValue(mockResponse)

    const store = useAuthStore()
    await store.login({ email: 'test@test.com', password: 'password' })

    expect(axios.post).toHaveBeenCalledWith('/api/v1/login', {
      email: 'test@test.com',
      password: 'password',
    })
    expect(store.token).toBe('login-token')
  })

  it('logout clears auth state', async () => {
    axios.post.mockResolvedValue({})
    const store = useAuthStore()
    store.setAuth({
      access_token: 'token',
      user: { id: 1, name: 'Test', role: 'Employee' },
    })

    await store.logout()

    expect(store.token).toBeNull()
    expect(store.user).toBeNull()
    expect(store.isAuthenticated).toBe(false)
  })

  it('restores auth from localStorage on init', () => {
    localStorage.setItem('access_token', 'stored-token')
    localStorage.setItem('user', JSON.stringify({ id: 1, name: 'Stored', role: 'Administrator' }))

    const store = useAuthStore()
    expect(store.token).toBe('stored-token')
    expect(store.user.name).toBe('Stored')
    expect(store.isAuthenticated).toBe(true)
    expect(store.isAdmin).toBe(true)
  })

  it('register calls API with correct payload', async () => {
    axios.post.mockResolvedValue({
      data: {
        access_token: 'reg-token',
        user: { id: 2, name: 'New', role: 'Employee' },
      },
    })

    const store = useAuthStore()
    await store.register({ name: 'New', email: 'new@test.com', password: 'password' })

    expect(axios.post).toHaveBeenCalledWith('/api/v1/register', {
      name: 'New',
      email: 'new@test.com',
      password: 'password',
    })
    expect(store.token).toBe('reg-token')
  })

  it('updateProfile sends FormData and updates user', async () => {
    axios.post.mockResolvedValue({
      data: {
        user: { id: 1, name: 'Updated', role: 'Employee', bio: 'New bio' },
      },
    })

    const store = useAuthStore()
    store.setAuth({
      access_token: 'token',
      user: { id: 1, name: 'Original', role: 'Employee' },
    })

    await store.updateProfile({ name: 'Updated', bio: 'New bio' })

    expect(store.user.name).toBe('Updated')
    expect(store.user.bio).toBe('New bio')
  })
})
