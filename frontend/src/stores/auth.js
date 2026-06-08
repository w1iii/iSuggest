// Ramos June 2, 2026 changed
import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(JSON.parse(localStorage.getItem('user')) || null);
  const token = ref(localStorage.getItem('access_token') || null);

  const isAuthenticated = computed(() => !!token.value);
  const isAdmin = computed(() => user.value?.role === 'Administrator' || user.value?.role === 'Super Administrator');

  async function login(credentials) {
    const response = await axios.post('/api/v1/login', credentials);
    setAuth(response.data);
  }

  async function register(data) {
    const response = await axios.post('/api/v1/register', data);
    setAuth(response.data);
  }

  async function logout() {
    try {
      await axios.post('/api/v1/logout');
    } catch {
      // proceed even if API call fails
    }
    token.value = null;
    user.value = null;
    localStorage.removeItem('access_token');
    localStorage.removeItem('user');
    delete axios.defaults.headers.common['Authorization'];
  }

  function setAuth(data) {
    token.value = data.access_token;
    user.value = data.user;
    localStorage.setItem('access_token', data.access_token);
    localStorage.setItem('user', JSON.stringify(data.user));
    axios.defaults.headers.common['Authorization'] = `Bearer ${data.access_token}`;
  }

  if (token.value) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
  }

  return { user, token, isAuthenticated, isAdmin, login, register, logout };
});
// Ramos June 2, 2026 changed
