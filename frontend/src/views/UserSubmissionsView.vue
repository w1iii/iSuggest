<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'
import logo from '@/assets/logo.png'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const submissions = ref([])
const stats = ref({ pending: 0, approved: 0, total: 0 })
const loading = ref(true)

const statusMap = {
  Pending: { label: 'In Review', class: 'bg-secondary-container text-on-secondary-container' },
  Approved: { label: 'Approved', class: 'bg-tertiary-fixed text-primary' },
  Rejected: { label: 'Declined', class: 'bg-error-container text-on-error-container' },
  Implemented: { label: 'Implemented', class: 'bg-surface-container-high text-on-surface-variant' },
}

function isActive(path) {
  return route.path === path
}

function formatDate(dateStr) {
  const d = new Date(dateStr)
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

async function fetchData() {
  try {
    const [subRes, statsRes] = await Promise.all([
      axios.get('/api/v1/suggestions'),
      axios.get('/api/v1/suggestions/user-stats'),
    ])
    submissions.value = subRes.data.data || []
    stats.value = statsRes.data
  } catch (e) {
    console.error('Failed to fetch submissions', e)
  } finally {
    loading.value = false
  }
}

async function handleLogout() {
  try {
    await authStore.logout()
  } finally {
    router.push('/login')
  }
}

onMounted(fetchData)
</script>

<template>
  <div class="bg-surface text-on-surface font-body-md min-h-screen">
    <!-- Sidebar (Desktop) -->
    <nav class="fixed left-0 top-20 h-[calc(100vh-80px)] flex flex-col p-4 border-r-2 border-primary bg-background hidden md:flex w-[200px] z-20">
      <div class="flex flex-col gap-1 flex-grow">
        <router-link class="flex items-center gap-2 p-2 rounded text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors text-sm font-medium active:scale-95" to="/dashboard">
          <span class="material-symbols-outlined text-[20px]">lightbulb</span>
          <span>Submit Suggestion</span>
        </router-link>
        <router-link class="flex items-center gap-2 p-2 rounded bg-surface-container text-primary transition-colors text-sm font-medium active:scale-95" to="/my-submissions">
          <span class="material-symbols-outlined text-[20px]">list_alt</span>
          <span>My Submissions</span>
        </router-link>
      </div>
      <div class="mt-auto space-y-4">
        <button class="w-full bg-primary text-surface py-3 rounded-full text-sm font-medium active:scale-90 flex items-center justify-center gap-1 cursor-pointer" @click="handleLogout">
          <span class="material-symbols-outlined text-[18px]">logout</span>
          Logout
        </button>
      </div>
    </nav>

    <!-- Top Nav -->
    <header class="w-full top-0 sticky z-30 bg-background border-b-2 border-primary">
      <nav class="flex justify-between items-center h-20 px-gutter max-w-page mx-auto">
        <div class="flex items-center gap-2">
          <router-link to="/dashboard" class="flex items-center gap-3">
            <img :src="logo" alt="iSuggest Logo" class="w-10 h-10 object-contain" />
            <span class="font-headline-md text-headline-md font-bold text-primary hidden sm:inline">iSuggest</span>
          </router-link>
        </div>
        <div class="flex items-center gap-4">
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary cursor-pointer text-[22px]">notifications</span>
            <router-link to="/profile">
              <img
                v-if="authStore.user?.profile_image_url"
                :src="authStore.user.profile_image_url"
                alt="User Profile"
                class="w-8 h-8 rounded-full border-2 border-primary object-cover cursor-pointer"
              />
              <span
                v-else
                class="material-symbols-outlined text-primary cursor-pointer text-[28px]"
              >account_circle</span>
            </router-link>
            <div class="hidden max-md:flex items-center">
              <button
                class="material-symbols-outlined text-primary cursor-pointer text-[22px]"
                @click="handleLogout"
              >logout</button>
            </div>
          </div>
        </div>
      </nav>
    </header>

    <!-- Main Content -->
    <main class="md:ml-[232px] md:mr-[32px] min-h-[calc(100vh-80px)] p-4 md:p-6 max-w-page mx-auto pb-20 md:pb-6">
      <!-- Page Header -->
      <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-2xl md:text-3xl text-primary font-bold scribble-underline">
            My Submissions
          </h2>
          <svg class="text-tertiary-fixed-dim hidden lg:block w-16 h-16 flex-shrink-0" viewBox="0 0 100 100">
            <path d="M10 10 Q 50 10 90 90 M 90 90 L 70 85 M 90 90 L 85 70" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="4"></path>
          </svg>
        </div>
        <p class="text-sm text-secondary max-w-2xl mt-1">
          Review and track the progress of your artisanal insights. Every idea is a stitch in our collective tapestry.
        </p>
      </div>

      <!-- Dashboard Stats / Filters -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-12">
        <div class="p-4 bg-surface border-2 border-primary rounded-lg">
          <p class="text-xs text-secondary uppercase tracking-wider mb-2">Active</p>
          <p class="text-2xl font-extrabold text-primary">{{ stats.pending }} Ideas</p>
        </div>
        <div class="p-4 bg-tertiary-fixed-dim border-2 border-primary rounded-lg">
          <p class="text-xs text-primary uppercase tracking-wider mb-2">Approved</p>
          <p class="text-2xl font-extrabold text-primary">{{ stats.approved }} Total</p>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-24">
        <span class="material-symbols-outlined text-4xl text-primary animate-spin">progress_activity</span>
      </div>

      <!-- Empty State -->
      <div v-else-if="submissions.length === 0" class="bg-surface-container-lowest border-2 border-primary rounded-lg p-12 text-center">
        <span class="material-symbols-outlined text-5xl text-tertiary-fixed-dim mb-4">lightbulb</span>
        <h3 class="text-xl font-bold text-primary mb-2">No submissions yet</h3>
        <p class="text-sm text-secondary mb-6">Your first big idea is waiting to be born.</p>
        <router-link class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-surface rounded-full font-bold text-sm hover:opacity-90 active:scale-95 transition-all" to="/dashboard">
          <span>Submit Your First Idea</span>
          <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
        </router-link>
      </div>

      <!-- Submissions List / Table -->
      <div v-else class="bg-surface-container-lowest border-2 border-primary rounded-lg overflow-hidden">
        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-outline-variant">
          <div
            v-for="sub in submissions"
            :key="sub.id"
            class="p-4 space-y-2 hover:bg-surface-container-low transition-colors"
          >
            <div class="flex items-start justify-between gap-2">
              <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-primary truncate">{{ sub.title }}</p>
                <p class="text-xs text-secondary">{{ sub.category }}</p>
              </div>
              <span
                class="px-3 py-0.5 rounded-full text-[10px] font-semibold border border-primary shrink-0"
                :class="statusMap[sub.status]?.class || 'bg-surface-container-high text-on-surface-variant'"
              >
                {{ statusMap[sub.status]?.label || sub.status }}
              </span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-xs text-secondary">{{ formatDate(sub.created_at) }}</span>
              <a class="inline-flex items-center gap-1 text-primary text-xs font-semibold" href="#">
                View Details
                <span class="material-symbols-outlined text-xs">arrow_forward</span>
              </a>
            </div>
          </div>
        </div>
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-surface-container-high border-b-2 border-primary">
                <th class="p-6 text-sm font-semibold text-primary">Title</th>
                <th class="p-6 text-sm font-semibold text-primary">Date Submitted</th>
                <th class="p-6 text-sm font-semibold text-primary">Status</th>
                <th class="p-6 text-sm font-semibold text-primary text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
              <tr v-for="sub in submissions" :key="sub.id" class="group hover:bg-surface-container-low transition-colors">
                <td class="p-6">
                  <div class="flex flex-col">
                    <span class="text-sm font-bold text-primary">{{ sub.title }}</span>
                    <span class="text-xs text-secondary mt-1">{{ sub.category }}</span>
                  </div>
                </td>
                <td class="p-6 text-sm text-secondary">{{ formatDate(sub.created_at) }}</td>
                <td class="p-6">
                  <span class="px-4 py-1 rounded-full text-xs font-semibold border border-primary" :class="statusMap[sub.status]?.class || 'bg-surface-container-high text-on-surface-variant'">
                    {{ statusMap[sub.status]?.label || sub.status }}
                  </span>
                </td>
                <td class="p-6 text-right">
                  <a class="inline-flex items-center gap-1 text-primary text-sm font-semibold hover:underline decoration-tertiary-fixed-dim decoration-2 underline-offset-4" href="#">
                    View Details
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="p-6 flex flex-col sm:flex-row items-center justify-between gap-3 border-t-2 border-primary bg-surface-container-low">
          <span class="text-sm font-semibold text-secondary">Showing 1-{{ submissions.length }} of {{ stats.total }} submissions</span>
          <div class="flex gap-2">
            <button class="h-10 w-10 border-2 border-primary rounded-full flex items-center justify-center hover:bg-tertiary-fixed transition-colors disabled:opacity-50" disabled>
              <span class="material-symbols-outlined">chevron_left</span>
            </button>
            <button class="h-10 w-10 border-2 border-primary rounded-full flex items-center justify-center bg-primary text-on-primary text-sm font-semibold">1</button>
            <button v-if="stats.total > submissions.length" class="h-10 w-10 border-2 border-primary rounded-full flex items-center justify-center hover:bg-tertiary-fixed transition-colors text-sm font-semibold">2</button>
            <button class="h-10 w-10 border-2 border-primary rounded-full flex items-center justify-center hover:bg-tertiary-fixed transition-colors">
              <span class="material-symbols-outlined">chevron_right</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Inspiration Footer -->
      <div class="mt-24 p-gutter border-2 border-primary rounded-xl relative overflow-hidden bg-primary-container text-on-primary-container">
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
          <div>
            <h3 class="text-xl font-bold text-on-primary-container mb-2">Need a spark?</h3>
            <p class="text-sm opacity-80">Explore trending community challenges to fuel your next submission.</p>
          </div>
          <button class="px-8 py-4 bg-tertiary-fixed text-primary rounded-full font-bold hover:scale-105 active:scale-95 transition-transform whitespace-nowrap">
            Explore Challenges
          </button>
        </div>
      </div>
    </main>

    <!-- Mobile Bottom Nav -->
    <nav class="md:hidden fixed bottom-0 left-0 w-full bg-surface border-t-2 border-primary flex justify-around items-center py-3 z-50">
      <router-link
        class="flex flex-col items-center gap-1"
        :class="isActive('/dashboard') ? 'text-primary font-bold' : 'text-secondary'"
        to="/dashboard"
      >
        <span class="material-symbols-outlined">dashboard</span>
        <span class="text-[10px] font-label-md">Home</span>
      </router-link>
      <router-link
        class="flex flex-col items-center gap-1"
        :class="isActive('/my-submissions') ? 'text-primary font-bold' : 'text-secondary'"
        to="/my-submissions"
      >
        <span class="material-symbols-outlined">list_alt</span>
        <span class="text-[10px] font-label-md">My Ideas</span>
      </router-link>
      <router-link
        class="flex flex-col items-center gap-1"
        :class="isActive('/profile') ? 'text-primary font-bold' : 'text-secondary'"
        to="/profile"
      >
        <span class="material-symbols-outlined">person</span>
        <span class="text-[10px] font-label-md">Profile</span>
      </router-link>
    </nav>
  </div>
</template>
