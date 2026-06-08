<script setup>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import logo from '@/assets/logo.png'

const authStore = useAuthStore()
const router = useRouter()

async function handleLogout() {
  try {
    await authStore.logout()
  } finally {
    router.push('/login')
  }
}
</script>

<template>
  <div class="bg-surface text-on-surface font-body-md min-h-screen">
    <aside class="fixed left-0 top-0 h-full flex flex-col p-gutter w-64 border-r-2 border-primary bg-surface z-50">
      <div class="mb-10">
        <router-link to="/admin/dashboard" class="flex items-center gap-2 mb-1">
          <img :src="logo" alt="InnovationHub" class="w-8 h-8 object-contain" />
          <h1 class="font-headline-md text-headline-md font-bold text-primary">InnovationHub</h1>
        </router-link>
        <p class="font-label-md text-label-md text-on-surface-variant opacity-70">Artisanal Insight Platform</p>
      </div>
      <nav class="flex-1 space-y-2">
        <router-link class="flex items-center gap-3 px-4 py-3 rounded-DEFAULT text-on-surface-variant hover:bg-surface-container hover:text-primary transition-all duration-150" to="/admin/dashboard">
          <span class="material-symbols-outlined">dashboard</span>
          <span class="font-label-md text-label-md">Dashboard</span>
        </router-link>
        <router-link class="flex items-center gap-3 px-4 py-3 rounded-DEFAULT text-primary font-bold border-l-4 border-tertiary-fixed-dim bg-surface-container-high scale-95 transition-transform duration-150" to="/admin/suggestions">
          <span class="material-symbols-outlined">view_kanban</span>
          <span class="font-label-md text-label-md">Kanban</span>
        </router-link>
        <router-link class="flex items-center gap-3 px-4 py-3 rounded-DEFAULT text-on-surface-variant hover:bg-surface-container hover:text-primary transition-all duration-150" to="/admin/analytics">
          <span class="material-symbols-outlined">analytics</span>
          <span class="font-label-md text-label-md">Analytics</span>
        </router-link>
      </nav>
      <div class="mt-auto pt-6 border-t border-outline-variant">
        <button class="w-full py-3 bg-primary-container text-surface rounded-full font-label-md text-label-md flex items-center justify-center gap-2 hover:opacity-90 transition-opacity cursor-pointer" @click="handleLogout">
          <span class="material-symbols-outlined">logout</span>
          Logout
        </button>
      </div>
    </aside>

    <header class="ml-64 w-full top-0 sticky z-40 bg-surface/80 backdrop-blur-md border-b-2 border-primary">
      <nav class="flex justify-between items-center h-20 px-gutter max-w-page mx-auto">
        <router-link to="/admin/dashboard" class="flex items-center gap-3">
          <img :src="logo" alt="iSuggest Logo" class="w-10 h-10 object-contain" />
          <span class="font-headline-md text-headline-md font-bold text-primary">iSuggest</span>
        </router-link>
        <div class="flex items-center gap-4">
          <div class="hidden lg:flex items-center gap-3">
            <a class="text-sm text-secondary hover:text-tertiary-container transition-all" href="#">Settings</a>
            <a class="text-sm text-secondary hover:text-tertiary-container transition-all" href="#">Support</a>
          </div>
          <span class="material-symbols-outlined text-primary cursor-pointer text-[22px]">notifications</span>
        </div>
      </nav>
    </header>

    <main class="ml-64 min-h-[calc(100vh-80px)] p-gutter max-w-page mx-auto flex items-center justify-center">
      <div class="text-center">
        <span class="material-symbols-outlined text-7xl text-tertiary-fixed-dim mb-6">view_kanban</span>
        <h2 class="font-headline-md text-headline-md text-primary mb-2">Kanban Board</h2>
        <p class="text-body-lg text-secondary">Suggestion management board coming soon.</p>
      </div>
    </main>
  </div>
</template>
