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
    return response.data;
  }

  async function register(data) {
    const response = await axios.post('/api/v1/register', data);
    setAuth(response.data);
  }

  async function logout() {
    try {
      await axios.post('/api/v1/logout');
    } catch {
    }
    token.value = null;
    user.value = null;
    localStorage.removeItem('access_token');
    localStorage.removeItem('user');
    delete axios.defaults.headers.common['Authorization'];
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
    localStorage.setItem('user', JSON.stringify(response.data.user));
    return response.data;
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

  return { user, token, isAuthenticated, isAdmin, login, register, logout, updateProfile, setAuth };
});
