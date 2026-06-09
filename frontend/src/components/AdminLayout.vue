<script setup>
import { computed, ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import logo from '@/assets/logo.png'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const userName = computed(() => authStore.user?.name?.split(' ')[0] || 'Administrator')

const isActive = (path) => route.path === path

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
    <!-- Sidebar -->
    <aside class="fixed left-0 top-20 h-[calc(100vh-80px)] flex flex-col p-4 border-r-2 border-primary bg-background hidden md:flex w-[200px] z-20">
      <nav class="flex flex-col gap-1 flex-grow pt-4">
        <router-link 
          :class="[
            'flex items-center gap-2 p-2 rounded transition-colors text-sm font-medium active:scale-95',
            isActive('/admin/dashboard') 
              ? 'bg-surface-container text-primary' 
              : 'text-on-surface-variant hover:bg-surface-container hover:text-primary'
          ]"
          to="/admin/dashboard"
        >
          <span class="material-symbols-outlined text-[20px]">dashboard</span>
          <span>Dashboard</span>
        </router-link>
        <router-link 
          :class="[
            'flex items-center gap-2 p-2 rounded transition-colors text-sm font-medium active:scale-95',
            isActive('/admin/suggestions') 
              ? 'bg-surface-container text-primary' 
              : 'text-on-surface-variant hover:bg-surface-container hover:text-primary'
          ]"
          to="/admin/suggestions"
        >
          <span class="material-symbols-outlined text-[20px]">view_kanban</span>
          <span>Kanban</span>
        </router-link>
        <router-link 
          :class="[
            'flex items-center gap-2 p-2 rounded transition-colors text-sm font-medium active:scale-95',
            isActive('/admin/employees') 
              ? 'bg-surface-container text-primary' 
              : 'text-on-surface-variant hover:bg-surface-container hover:text-primary'
          ]"
          to="/admin/employees"
        >
          <span class="material-symbols-outlined text-[20px]">group</span>
          <span>Employees</span>
        </router-link>
        <router-link 
          :class="[
            'flex items-center gap-2 p-2 rounded transition-colors text-sm font-medium active:scale-95',
            isActive('/admin/analytics') 
              ? 'bg-surface-container text-primary' 
              : 'text-on-surface-variant hover:bg-surface-container hover:text-primary'
          ]"
          to="/admin/analytics"
        >
          <span class="material-symbols-outlined text-[20px]">analytics</span>
          <span>Analytics</span>
        </router-link>
      </nav>
      <div class="mt-auto space-y-4">
        <button 
          class="w-full bg-primary text-surface py-3 rounded-full text-sm font-medium active:scale-90 flex items-center justify-center gap-1 cursor-pointer" 
          @click="handleLogout"
        >
          <span class="material-symbols-outlined text-[18px]">logout</span>
          Logout
        </button>
      </div>
    </aside>

    <!-- Top Nav -->
    <header class="w-full top-0 sticky z-30 bg-background border-b-2 border-primary">
      <nav class="flex justify-between items-center h-20 px-gutter max-w-page mx-auto">
        <div class="flex items-center gap-2">
          <router-link to="/admin/dashboard" class="flex items-center gap-3">
            <img :src="logo" alt="iSuggest Logo" class="w-10 h-10 object-contain" />
            <span class="font-headline-md text-headline-md font-bold text-primary hidden sm:inline">iSuggest</span>
          </router-link>
        </div>
        <div class="flex items-center gap-4">
          <div class="hidden lg:flex items-center gap-3">
            <a class="text-sm text-secondary hover:text-tertiary-container transition-all" href="#">Settings</a>
            <a class="text-sm text-secondary hover:text-tertiary-container transition-all" href="#">Support</a>
          </div>
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

    <!-- Main Content Slot -->
    <main class="md:ml-[232px] md:mr-[32px] min-h-[calc(100vh-80px)] p-4 md:p-6 max-w-page mx-auto pb-20 md:pb-6">
      <slot />
    </main>

    <!-- Mobile Bottom Nav -->
    <nav class="md:hidden fixed bottom-0 left-0 w-full bg-surface border-t-2 border-primary flex justify-around items-center py-3 z-50">
      <router-link
        class="flex flex-col items-center gap-1"
        :class="isActive('/admin/dashboard') ? 'text-primary font-bold' : 'text-secondary'"
        to="/admin/dashboard"
      >
        <span class="material-symbols-outlined">dashboard</span>
        <span class="text-[10px] font-label-md">Home</span>
      </router-link>
      <router-link
        class="flex flex-col items-center gap-1"
        :class="isActive('/admin/suggestions') ? 'text-primary font-bold' : 'text-secondary'"
        to="/admin/suggestions"
      >
        <span class="material-symbols-outlined">view_kanban</span>
        <span class="text-[10px] font-label-md">Kanban</span>
      </router-link>
      <router-link
        class="flex flex-col items-center gap-1"
        :class="isActive('/admin/employees') ? 'text-primary font-bold' : 'text-secondary'"
        to="/admin/employees"
      >
        <span class="material-symbols-outlined">group</span>
        <span class="text-[10px] font-label-md">People</span>
      </router-link>
      <router-link
        class="flex flex-col items-center gap-1"
        :class="isActive('/admin/analytics') ? 'text-primary font-bold' : 'text-secondary'"
        to="/admin/analytics"
      >
        <span class="material-symbols-outlined">analytics</span>
        <span class="text-[10px] font-label-md">Insights</span>
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
