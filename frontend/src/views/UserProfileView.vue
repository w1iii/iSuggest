<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'
import logo from '@/assets/logo.png'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const isEditing = ref(false)
const isLoading = ref(false)
const error = ref(null)
const userStats = ref({ total: 0, implemented: 0 })
const statsLoading = ref(true)

const form = reactive({
  name: authStore.user?.name || '',
  bio: authStore.user?.bio || '',
  title: authStore.user?.title || '',
  field: authStore.user?.field || '',
  image: null
})

function resetForm() {
  form.name = authStore.user?.name || ''
  form.bio = authStore.user?.bio || ''
  form.title = authStore.user?.title || ''
  form.field = authStore.user?.field || ''
  form.image = null
  error.value = null
}

function openEditModal() {
  resetForm()
  isEditing.value = true
}

function handleFileChange(event) {
  form.image = event.target.files[0]
}

async function handleUpdateProfile() {
  isLoading.value = true
  error.value = null
  try {
    await authStore.updateProfile(form)
    form.image = null
    isEditing.value = false
  } catch (e) {
    const msg = e.response?.data?.message || e.message || 'Upload failed'
    error.value = msg
    alert(msg)
  } finally {
    isLoading.value = false
  }
}

async function fetchUserStats() {
  try {
    const res = await axios.get('/api/v1/suggestions/user-stats')
    userStats.value = res.data
  } catch {
  } finally {
    statsLoading.value = false
  }
}

function isActive(path) {
  return route.path === path
}

async function handleLogout() {
  try {
    await authStore.logout()
  } finally {
    router.push('/login')
  }
}

onMounted(fetchUserStats)
</script>

<template>
  <div class="bg-surface text-on-surface font-body-md min-h-screen">
    <aside v-if="authStore.isAdmin" class="fixed left-0 top-20 h-[calc(100vh-80px)] flex flex-col p-4 border-r-2 border-primary bg-background hidden md:flex w-[200px] z-20">
      <nav class="flex flex-col gap-1 flex-grow pt-4">
        <router-link class="flex items-center gap-2 p-2 rounded text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors text-sm font-medium active:scale-95" to="/admin/dashboard">
          <span class="material-symbols-outlined text-[20px]">dashboard</span>
          <span>Dashboard</span>
        </router-link>
        <router-link class="flex items-center gap-2 p-2 rounded text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors text-sm font-medium active:scale-95" to="/admin/suggestions">
          <span class="material-symbols-outlined text-[20px]">view_kanban</span>
          <span>Kanban</span>
        </router-link>
        <router-link 
          class="flex items-center gap-2 p-2 rounded text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors text-sm font-medium active:scale-95" 
          to="/admin/employees"
        >
          <span class="material-symbols-outlined text-[20px]">group</span>
          <span>Employees</span>
        </router-link>
        <router-link 
          class="flex items-center gap-2 p-2 rounded text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors text-sm font-medium active:scale-95" 
          to="/admin/analytics"
        >
          <span class="material-symbols-outlined text-[20px]">analytics</span>
          <span>Analytics</span>
        </router-link>
      </nav>
      <div class="mt-auto space-y-4">
        <button class="w-full bg-primary text-surface py-3 rounded-full text-sm font-medium active:scale-90 flex items-center justify-center gap-1 cursor-pointer" @click="handleLogout">
          <span class="material-symbols-outlined text-[18px]">logout</span>
          Logout
        </button>
      </div>
    </aside>

    <nav v-else class="fixed left-0 top-20 h-[calc(100vh-80px)] flex flex-col p-4 border-r-2 border-primary bg-background hidden md:flex w-[200px] z-20">
      <div class="flex flex-col gap-1 flex-grow">
        <router-link class="flex items-center gap-2 p-2 rounded text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors text-sm font-medium active:scale-95" to="/dashboard">
          <span class="material-symbols-outlined text-[20px]">lightbulb</span>
          <span>Submit Suggestion</span>
        </router-link>
        <router-link class="flex items-center gap-2 p-2 rounded text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors text-sm font-medium active:scale-95" to="/my-submissions">
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

    <header class="w-full top-0 sticky z-30 bg-background border-b-2 border-primary">
      <nav class="flex justify-between items-center h-20 px-gutter max-w-page mx-auto">
        <div class="flex items-center gap-2">
          <router-link :to="authStore.isAdmin ? '/admin/dashboard' : '/dashboard'" class="flex items-center gap-3">
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

    <main :class="authStore.isAdmin ? 'md:ml-[232px]' : 'md:ml-[232px] md:mr-[32px]'" class="min-h-[calc(100vh-80px)] p-4 md:p-6 max-w-page mx-auto pb-20 md:pb-6">
      <section class="mb-12 pt-8 relative">
        <div class="flex flex-col md:flex-row gap-8 items-start md:items-end relative z-10">
          <div class="relative group">
            <div class="absolute -top-4 -left-4 w-40 h-40 bg-tertiary-fixed-dim/30 rounded-full blur-2xl group-hover:bg-tertiary-fixed-dim/50 transition-all"></div>
            <div
              v-if="authStore.user?.profile_image_url"
              class="w-36 h-36 md:w-48 md:h-48 rounded-lg border-2 border-primary object-cover relative z-10 overflow-hidden"
            >
              <img
                :src="authStore.user.profile_image_url"
                :alt="authStore.user?.name"
                class="w-full h-full object-cover"
              />
            </div>
            <div
              v-else
              class="w-36 h-36 md:w-48 md:h-48 rounded-lg border-2 border-primary relative z-10 bg-primary-container flex items-center justify-center"
            >
              <span class="material-symbols-outlined text-6xl text-primary">account_circle</span>
            </div>
            <div class="absolute -bottom-2 -right-2 bg-tertiary-fixed text-primary px-4 py-1 rounded-full text-sm font-bold z-20 border-2 border-primary" v-if="authStore.user?.title">
              {{ authStore.user.title }}
            </div>
          </div>
          <div class="flex-grow">
            <div class="relative inline-block">
              <h1 class="text-3xl md:text-5xl text-primary font-extrabold relative z-10">{{ authStore.user?.name }}</h1>
              <svg class="absolute -top-2 -left-4 w-[110%] h-[120%] pointer-events-none opacity-80" preserveAspectRatio="none" viewBox="0 0 100 100">
                <path d="M10,50 Q25,10 50,10 Q85,10 90,50 Q90,90 50,90 Q15,90 10,50" fill="none" stroke="#bbd145" stroke-linecap="round" stroke-width="2"></path>
              </svg>
            </div>
            <p class="text-xl md:text-2xl text-on-primary-container font-semibold mt-2" v-if="authStore.user?.field">{{ authStore.user.field }}</p>
          </div>
          <div class="flex gap-4">
            <button @click="openEditModal" class="bg-secondary text-surface px-6 py-3 rounded-full text-sm font-medium hover:opacity-90 transition-all flex items-center gap-2 cursor-pointer">
              <span class="material-symbols-outlined text-[18px]">edit</span>
              Edit Profile
            </button>
          </div>
        </div>
      </section>

      <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        <div class="md:col-span-8 bg-surface-container-lowest p-6 md:p-8 rounded-lg border-2 border-primary relative overflow-hidden group">
          <h3 class="text-xl font-bold text-primary mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined text-[22px]">history_edu</span>
            Professional Bio
          </h3>
          <p class="text-base text-on-surface leading-relaxed">
            {{ authStore.user?.bio || 'No bio provided yet.' }}
          </p>
          <div class="absolute bottom-4 right-4 text-tertiary-fixed opacity-40 transform rotate-12 group-hover:rotate-0 transition-transform">
            <span class="material-symbols-outlined text-5xl">trending_flat</span>
          </div>
        </div>

        <div class="md:col-span-4 bg-primary-container p-6 md:p-8 rounded-lg border-2 border-primary text-surface relative overflow-hidden">
          <h3 class="text-xs font-semibold uppercase tracking-widest text-on-primary-container mb-8">Personal Impact</h3>
          <div v-if="statsLoading" class="space-y-8">
            <div class="h-16 bg-on-primary-container/10 rounded animate-pulse"></div>
            <div class="h-16 bg-on-primary-container/10 rounded animate-pulse"></div>
          </div>
          <div v-else class="space-y-8">
            <div>
              <div class="flex items-baseline gap-2">
                <span class="text-5xl font-extrabold text-tertiary-fixed">{{ userStats.total }}</span>
                <span class="text-tertiary-fixed-dim material-symbols-outlined text-2xl">auto_awesome</span>
              </div>
              <p class="text-xs font-semibold text-on-primary-container mt-1">Ideas Submitted</p>
            </div>
            <div class="relative">
              <div class="flex items-baseline gap-2">
                <span class="text-5xl font-extrabold text-tertiary-fixed">{{ userStats.implemented }}</span>
                <span class="text-tertiary-fixed-dim material-symbols-outlined text-2xl">task_alt</span>
              </div>
              <p class="text-xs font-semibold text-on-primary-container mt-1">Successfully Implemented</p>
              <div class="absolute bottom-0 left-0 w-24 h-2 bg-tertiary-fixed-dim/20 -rotate-1"></div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="isEditing" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-background/80 backdrop-blur-sm" @click="isEditing = false"></div>
        <div class="relative bg-surface w-full max-w-2xl rounded-lg border-2 border-primary shadow-xl overflow-hidden animate-in fade-in zoom-in duration-200">
          <div class="p-6 border-b-2 border-primary flex justify-between items-center bg-primary-container">
            <h2 class="text-2xl font-bold text-surface">Edit Profile</h2>
            <button @click="isEditing = false" class="text-surface hover:scale-110 transition-transform">
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>
          <form @submit.prevent="handleUpdateProfile" class="p-6 space-y-6">
            <div v-if="error" class="bg-error-container text-on-error-container p-4 rounded border-2 border-error text-sm font-medium">
              {{ error }}
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2">
                <label class="text-sm font-bold text-primary uppercase">Full Name</label>
                <input v-model="form.name" type="text" class="w-full bg-surface-container border-2 border-primary p-3 rounded text-on-surface focus:outline-none focus:bg-surface-container-high transition-colors" placeholder="e.g. Alex Rivera" />
              </div>
              <div class="space-y-2">
                <label class="text-sm font-bold text-primary uppercase">Job Title</label>
                <input v-model="form.title" type="text" class="w-full bg-surface-container border-2 border-primary p-3 rounded text-on-surface focus:outline-none focus:bg-surface-container-high transition-colors" placeholder="e.g. Lead Designer" />
              </div>
              <div class="space-y-2">
                <label class="text-sm font-bold text-primary uppercase">Field / Department</label>
                <input v-model="form.field" type="text" class="w-full bg-surface-container border-2 border-primary p-3 rounded text-on-surface focus:outline-none focus:bg-surface-container-high transition-colors" placeholder="e.g. Experience Strategy" />
              </div>
              <div class="space-y-2">
                <label class="text-sm font-bold text-primary uppercase">Profile Picture</label>
                <div class="relative group cursor-pointer">
                  <input 
                    type="file" 
                    @change="handleFileChange" 
                    accept="image/*" 
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" 
                  />
                  <div class="w-full bg-surface-container border-2 border-primary p-3 rounded text-on-surface flex items-center gap-3 group-hover:bg-surface-container-high transition-colors">
                    <span class="bg-primary text-surface px-4 py-1 rounded-full text-xs font-bold uppercase">
                      Choose File
                    </span>
                    <span class="text-sm truncate text-on-surface-variant font-medium">
                      {{ form.image ? form.image.name : 'No file chosen' }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="md:col-span-2 space-y-2">
                <label class="text-sm font-bold text-primary uppercase">Bio</label>
                <textarea v-model="form.bio" rows="4" class="w-full bg-surface-container border-2 border-primary p-3 rounded text-on-surface focus:outline-none focus:bg-surface-container-high transition-colors resize-none" placeholder="Tell us about yourself..."></textarea>
              </div>
            </div>
            <div class="flex justify-end gap-4 pt-4">
              <button type="button" @click="isEditing = false" class="px-6 py-2 rounded-full text-sm font-bold text-primary border-2 border-primary hover:bg-surface-container transition-colors">Cancel</button>
              <button type="submit" :disabled="isLoading" class="px-8 py-2 rounded-full text-sm font-bold bg-primary text-surface hover:opacity-90 transition-all disabled:opacity-50 flex items-center gap-2">
                <span v-if="isLoading" class="material-symbols-outlined animate-spin">sync</span>
                {{ isLoading ? 'Saving...' : 'Save Changes' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </main>

    <template v-if="authStore.isAdmin">
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
    </template>
    <template v-else>
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
    </template>
  </div>
</template>
