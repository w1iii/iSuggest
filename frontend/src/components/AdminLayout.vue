<script setup>
import { computed } from 'vue'
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
        <router-link to="/admin/dashboard" class="flex items-center gap-3">
          <img :src="logo" alt="iSuggest Logo" class="w-10 h-10 object-contain" />
          <span class="font-headline-md text-headline-md font-bold text-primary">iSuggest</span>
        </router-link>
        <div class="flex items-center gap-4">
          <div class="hidden lg:flex items-center gap-3">
            <a class="text-sm text-secondary hover:text-tertiary-container transition-all" href="#">Settings</a>
            <a class="text-sm text-secondary hover:text-tertiary-container transition-all" href="#">Support</a>
          </div>
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary cursor-pointer text-[22px]">notifications</span>
            <router-link to="/profile">
              <img
                alt="User Profile"
                class="w-8 h-8 rounded-full border-2 border-primary object-cover cursor-pointer"
                :src="authStore.user?.profile_image_url || 'https://lh3.googleusercontent.com/aida-public/AB6AXuDyqql2psZptharucfNZGIrPwIypbnj2OVC6lr429hrbn7jFXNp1Pz_Bn9u3-SgQwrjbJxB_Ck9MjasSZAWPmVQ87nQsNHnZvF4cNE-BVkr_-Q85yABCmC_9ihHLBf5gOFRrVqaFwAZDau9aB66YIMSQfENzKUydkTKA_VDrez1agbWRCFMDewA_wOMi1IilAHtEs1ODlVHDXi5OmCaTOrNx8BXl-HDoa4zrMfUihTkAEPX2k8CbIX_RYT9mHnHodNgeJ31iEWOdao'"
              />
            </router-link>
          </div>
        </div>
      </nav>
    </header>

    <!-- Main Content Slot -->
    <main class="md:ml-[232px] md:mr-[32px] min-h-[calc(100vh-80px)] p-4 md:p-6 max-w-page mx-auto">
      <slot />
    </main>
  </div>
</template>
