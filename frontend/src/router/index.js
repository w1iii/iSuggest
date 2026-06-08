import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('../views/HomeView.vue'),
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue'),
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      meta: { requiresAuth: true },
      component: () => import('../views/EmployeeDashboardView.vue'),
    },
    {
      path: '/my-submissions',
      name: 'my-submissions',
      meta: { requiresAuth: true },
      component: () => import('../views/UserSubmissionsView.vue'),
    },
    {
      path: '/profile',
      name: 'profile',
      meta: { requiresAuth: true },
      component: () => import('../views/UserProfileView.vue'),
    },
    {
      path: '/admin/dashboard',
      name: 'admin-dashboard',
      meta: { requiresAuth: true, requiresAdmin: true },
      component: () => import('../views/AdminDashboardView.vue'),
    },
    {
      path: '/admin/suggestions',
      name: 'admin-suggestions',
      meta: { requiresAuth: true, requiresAdmin: true },
      component: () => import('../views/AdminSuggestionsView.vue'),
    },
    {
      path: '/admin/analytics',
      name: 'admin-analytics',
      meta: { requiresAuth: true, requiresAdmin: true },
      component: () => import('../views/AdminAnalyticsView.vue'),
    },
  ],
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  if (to.meta.requiresAuth && !authStore.token) {
    next('/login')
  } else if (to.meta.requiresAdmin && !authStore.isAdmin) {
    next('/dashboard')
  } else {
    next()
  }
})

export default router
