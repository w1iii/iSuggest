<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'
import logo from '@/assets/logo.png'
import SuggestForm from '@/components/SuggestForm.vue'

const router = useRouter()
const authStore = useAuthStore()

const stats = ref(null)
const recentSubmissions = ref([])
const loading = ref(true)

const userName = computed(() => authStore.user?.name?.split(' ')[0] || 'there')

const statusMap = {
  Pending: { label: 'In Review', class: 'bg-secondary-container text-on-secondary-container' },
  Approved: { label: 'Approved', class: 'bg-tertiary-fixed text-primary' },
  Rejected: { label: 'Declined', class: 'bg-error-container text-on-error-container' },
  Implemented: { label: 'Implemented', class: 'bg-surface-container-high text-on-surface-variant' },
}

function formatDate(dateStr) {
  const d = new Date(dateStr)
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

async function fetchDashboardData() {
  try {
    const [statsRes, recentRes] = await Promise.all([
      axios.get('/api/v1/suggestions/user-stats'),
      axios.get('/api/v1/suggestions', { params: { limit: 5 } }),
    ])
    stats.value = statsRes.data
    recentSubmissions.value = recentRes.data.data?.data || recentRes.data.data || []
  } catch (e) {
    console.error('Failed to load dashboard', e)
  } finally {
    loading.value = false
  }
}

function onNewSubmission(submission) {
  if (!Array.isArray(recentSubmissions.value)) {
    recentSubmissions.value = [submission]
  } else {
    recentSubmissions.value.unshift(submission)
  }
  if (stats.value) {
    stats.value.total++
    stats.value.pending++
  }
}

async function handleLogout() {
  try {
    await authStore.logout()
  } finally {
    router.push('/login')
  }
}

onMounted(fetchDashboardData)
</script>

<template>
  <div class="bg-surface text-on-surface font-body-md min-h-screen">
    <!-- Sidebar -->
    <nav class="fixed left-0 top-20 h-[calc(100vh-80px)] flex flex-col p-4 border-r-2 border-primary bg-background hidden md:flex w-[200px] z-20">
      <div class="flex flex-col gap-1 flex-grow">
        <router-link class="flex items-center gap-2 p-2 rounded bg-surface-container text-primary transition-colors text-sm font-medium active:scale-95" to="/dashboard">
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

    <!-- Top Nav -->
    <header class="w-full top-0 sticky z-30 bg-background border-b-2 border-primary">
      <nav class="flex justify-between items-center h-20 px-gutter max-w-page mx-auto">
        <router-link to="/dashboard" class="flex items-center gap-3">
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
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDyqql2psZptharucfNZGIrPwIypbnj2OVC6lr429hrbn7jFXNp1Pz_Bn9u3-SgQwrjbJxB_Ck9MjasSZAWPmVQ87nQsNHnZvF4cNE-BVkr_-Q85yABCmC_9ihHLBf5gOFRrVqaFwAZDau9aB66YIMSQfENzKUydkTKA_VDrez1agbWRCFMDewA_wOMi1IilAHtEs1ODlVHDXi5OmCaTOrNx8BXl-HDoa4zrMfUihTkAEPX2k8CbIX_RYT9mHnHodNgeJ31iEWOdao"
              />
            </router-link>
          </div>
        </div>
      </nav>
    </header>

    <!-- Main Content -->
    <main class="md:ml-[232px] md:mr-[32px] min-h-[calc(100vh-80px)] p-4 md:p-6 max-w-page mx-auto">
      <!-- Hero -->
      <section class="mb-6">
        <h2 class="text-2xl md:text-3xl text-primary font-bold">
          Welcome back, <span class="scribble-highlight">{{ userName }}</span>.
        </h2>
        <p class="text-sm text-secondary max-w-2xl mt-1">
          Every great innovation starts with a simple thought. Use this space to shape the future of our artisanal workplace.
        </p>
      </section>

      <!-- Stats Row -->
      <div v-if="loading" class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        <div v-for="i in 4" :key="i" class="h-24 bg-surface-container-high rounded-lg border-2 border-primary animate-pulse"></div>
      </div>
      <div v-else-if="stats" class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        <div class="p-4 bg-surface border-2 border-primary rounded-lg">
          <p class="text-xs text-secondary uppercase tracking-wider mb-1">Total</p>
          <p class="text-2xl font-extrabold text-primary">{{ stats.total }}</p>
        </div>
        <div class="p-4 bg-secondary-container/30 border-2 border-primary rounded-lg">
          <p class="text-xs text-secondary uppercase tracking-wider mb-1">In Review</p>
          <p class="text-2xl font-extrabold text-primary">{{ stats.pending }}</p>
        </div>
        <div class="p-4 bg-tertiary-fixed-dim border-2 border-primary rounded-lg">
          <p class="text-xs text-primary uppercase tracking-wider mb-1">Approved</p>
          <p class="text-2xl font-extrabold text-primary">{{ stats.approved }}</p>
        </div>
        <div class="p-4 bg-surface-container-high border-2 border-primary rounded-lg">
          <p class="text-xs text-secondary uppercase tracking-wider mb-1">Implemented</p>
          <p class="text-2xl font-extrabold text-primary">{{ stats.implemented }}</p>
        </div>
      </div>

      <!-- Form & Recent Submissions -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Form -->
        <div class="lg:col-span-7">
          <SuggestForm @submitted="onNewSubmission" />
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-5 space-y-3">
          <!-- Recent Submissions -->
          <div class="bg-surface-container-lowest border-2 border-primary rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-sm font-bold text-primary">Recent Submissions</h3>
              <router-link class="text-xs text-primary font-semibold hover:underline" to="/my-submissions">View all</router-link>
            </div>

            <div v-if="loading" class="space-y-3">
              <div v-for="i in 3" :key="i" class="h-14 bg-surface-container-high rounded-lg animate-pulse"></div>
            </div>

            <div v-else-if="recentSubmissions.length === 0" class="py-6 text-center">
              <span class="material-symbols-outlined text-3xl text-tertiary-fixed-dim mb-2">lightbulb</span>
              <p class="text-xs text-secondary">No submissions yet. Submit your first idea above!</p>
            </div>

            <div v-else class="space-y-2">
              <div
                v-for="sub in recentSubmissions.slice(0, 5)"
                :key="sub.id"
                class="flex items-start gap-3 p-2 rounded-lg hover:bg-surface-container transition-colors"
              >
                <span class="material-symbols-outlined text-[18px] text-tertiary-fixed-dim mt-0.5">article</span>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-semibold text-primary truncate">{{ sub.title }}</p>
                  <p class="text-xs text-secondary">{{ formatDate(sub.created_at) }}</p>
                </div>
                <span
                  class="px-2 py-0.5 rounded-full text-[10px] font-semibold border border-primary shrink-0"
                  :class="statusMap[sub.status]?.class || 'bg-surface-container-high text-on-surface-variant'"
                >
                  {{ statusMap[sub.status]?.label || sub.status }}
                </span>
              </div>
            </div>
          </div>

          <!-- Why Your Idea Matters -->
          <div class="relative p-3 rounded-lg border-2 border-primary bg-secondary-container/30">
            <div class="flex gap-3 items-start">
              <img
                alt="Human centric creative concept"
                class="w-16 h-16 shrink-0 object-cover rounded-lg border-2 border-primary mix-blend-multiply opacity-80"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuB7kUUsOWyUg1-whmc5gQV8ovjh0B7MhqqXwgrX37dS0LNT33iMd9M3S6gRB55bgHVQDymsolO6-0vHBDVI_B-uAD7rh20R9xETkeGdQkh-4x-UYzFbEeykZ2dlJQ0KUH0aEMnHdnp3EqOL8eAM_l8UGcwge_To_fpomogtuXyKQY0T8MFGmv2HCacQ23UXV7WnwR8j2APiNRDALOjj3mhsTh711ZLUQDg3xjq0weC2hWW0TwtBTJUJ_KGDYuuYnuaOpZSe7EDhLio"
              />
              <div class="min-w-0">
                <h3 class="text-xs font-bold text-primary mb-1 scribble-circle inline-block">Why your idea matters</h3>
                <p class="text-xs text-on-secondary-container leading-relaxed">
                  At <span class="font-bold">InnovationHub</span>, we believe that the most powerful solutions come from the people who live the experience every day. Your perspective is the craftsmanship behind our progress.
                </p>
              </div>
            </div>
            <div class="mt-2 flex justify-end">
              <span class="material-symbols-outlined text-xl text-tertiary-fixed-dim rotate-12" style="font-variation-settings: 'wght' 200;">subdirectory_arrow_left</span>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Mobile Bottom Nav -->
    <nav class="md:hidden fixed bottom-0 left-0 w-full bg-surface border-t-2 border-primary flex justify-around items-center py-3 z-50">
      <router-link class="flex flex-col items-center gap-1 text-primary font-bold" to="/dashboard">
        <span class="material-symbols-outlined">dashboard</span>
        <span class="text-[10px] font-label-md">Home</span>
      </router-link>
      <router-link class="flex flex-col items-center gap-1 text-secondary" to="/dashboard">
        <span class="material-symbols-outlined">lightbulb</span>
        <span class="text-[10px] font-label-md">Suggest</span>
      </router-link>
      <router-link class="flex flex-col items-center gap-1 text-secondary" to="/my-submissions">
        <span class="material-symbols-outlined">list_alt</span>
        <span class="text-[10px] font-label-md">My Ideas</span>
      </router-link>
      <router-link class="flex flex-col items-center gap-1 text-secondary" to="/profile">
        <span class="material-symbols-outlined">person</span>
        <span class="text-[10px] font-label-md">Profile</span>
      </router-link>
    </nav>
  </div>
</template>
