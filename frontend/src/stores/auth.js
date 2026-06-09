import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

axios.defaults.withCredentials = true;

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null);
  const loading = ref(true);

  const isAuthenticated = computed(() => !!user.value);
  const isAdmin = computed(() => user.value?.role === 'Administrator' || user.value?.role === 'Super Administrator');

  async function fetchCsrfCookie() {
    await axios.get('/sanctum/csrf-cookie');
  }

  async function init() {
    loading.value = true;
    try {
      const response = await axios.get('/api/v1/user');
      user.value = response.data;
    } catch {
      user.value = null;
    } finally {
      loading.value = false;
    }
  }

  async function login(credentials) {
    await fetchCsrfCookie();
    const response = await axios.post('/api/v1/login', credentials);
    user.value = response.data.user;
  }

  async function register(data) {
    await fetchCsrfCookie();
    const response = await axios.post('/api/v1/register', data);
    user.value = response.data.user;
  }

  async function logout() {
    try {
      await axios.post('/api/v1/logout');
    } catch {
    }
    user.value = null;
  }

  async function updateProfile(profileData) {
    const formData = new FormData();
    if (profileData.name) formData.append('name', profileData.name);
    if (profileData.bio) formData.append('bio', profileData.bio);
    if (profileData.title) formData.append('title', profileData.title);
    if (profileData.field) formData.append('field', profileData.field);
    if (profileData.image) formData.append('image', profileData.image);

    const response = await axios.post('/api/v1/profile/update', formData);
    user.value = response.data.user;
    return response.data;
  }

  return { user, loading, isAuthenticated, isAdmin, init, fetchCsrfCookie, login, register, logout, updateProfile };
});
