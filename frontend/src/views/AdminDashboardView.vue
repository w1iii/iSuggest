<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'
import logo from '@/assets/logo.png'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const stats = ref(null)
const activities = ref([])
const categories = ref([])
const contributors = ref([])
const loading = ref(true)

const userName = computed(() => authStore.user?.name?.split(' ')[0] || 'Administrator')

const activityIcons = {
  new: { icon: 'draw', bg: 'bg-primary-container' },
  feedback: { icon: 'forum', bg: 'bg-secondary-container' },
  promoted: { icon: 'verified', bg: 'bg-tertiary-fixed-dim' },
  approved: { icon: 'check_circle', bg: 'bg-tertiary-fixed-dim' },
}

function timeAgo(dateStr) {
  const diff = Date.now() - new Date(dateStr).getTime()
  const mins = Math.floor(diff / 60000)
  if (mins < 1) return 'just now'
  if (mins < 60) return `${mins}m ago`
  const hrs = Math.floor(mins / 60)
  if (hrs < 24) return `${hrs}h ago`
  const days = Math.floor(hrs / 24)
  return `${days}d ago`
}

async function fetchDashboardData() {
  try {
    const [statsRes, activityRes, catsRes, contribsRes] = await Promise.all([
      axios.get('/api/v1/admin/dashboard/stats'),
      axios.get('/api/v1/admin/dashboard/activity'),
      axios.get('/api/v1/admin/dashboard/categories'),
      axios.get('/api/v1/admin/dashboard/contributors'),
    ])
    stats.value = statsRes.data
    activities.value = activityRes.data
    categories.value = catsRes.data
    contributors.value = contribsRes.data
  } catch (e) {
    console.error('Failed to load admin dashboard', e)
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

onMounted(fetchDashboardData)
</script>

<template>
  <div class="bg-surface text-on-surface font-body-md min-h-screen">
    <!-- Sidebar -->
    <aside class="fixed left-0 top-0 h-full flex flex-col p-gutter w-64 border-r-2 border-primary bg-surface z-50">
      <div class="mb-10">
        <router-link to="/admin/dashboard" class="flex items-center gap-2 mb-1">
          <img :src="logo" alt="InnovationHub" class="w-8 h-8 object-contain" />
          <h1 class="font-headline-md text-headline-md font-bold text-primary">InnovationHub</h1>
        </router-link>
        <p class="font-label-md text-label-md text-on-surface-variant opacity-70">Artisanal Insight Platform</p>
      </div>
      <nav class="flex-1 space-y-2">
        <router-link
          class="flex items-center gap-3 px-4 py-3 rounded-DEFAULT text-primary font-bold border-l-4 border-tertiary-fixed-dim bg-surface-container-high scale-95 transition-transform duration-150"
          to="/admin/dashboard"
        >
          <span class="material-symbols-outlined">dashboard</span>
          <span class="font-label-md text-label-md">Dashboard</span>
        </router-link>
        <router-link
          class="flex items-center gap-3 px-4 py-3 rounded-DEFAULT text-on-surface-variant hover:bg-surface-container hover:text-primary transition-all duration-150"
          to="/admin/suggestions"
        >
          <span class="material-symbols-outlined">view_kanban</span>
          <span class="font-label-md text-label-md">Kanban</span>
        </router-link>
        <router-link
          class="flex items-center gap-3 px-4 py-3 rounded-DEFAULT text-on-surface-variant hover:bg-surface-container hover:text-primary transition-all duration-150"
          to="/admin/analytics"
        >
          <span class="material-symbols-outlined">analytics</span>
          <span class="font-label-md text-label-md">Analytics</span>
        </router-link>
      </nav>
      <div class="mt-auto pt-6 border-t border-outline-variant">
        <button
          class="w-full py-3 bg-primary-container text-surface rounded-full font-label-md text-label-md flex items-center justify-center gap-2 hover:opacity-90 transition-opacity cursor-pointer"
          @click="handleLogout"
        >
          <span class="material-symbols-outlined">logout</span>
          Logout
        </button>
      </div>
    </aside>

    <!-- Top Nav -->
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
    <main class="ml-64 min-h-[calc(100vh-80px)] p-gutter max-w-page mx-auto">
      <!-- Welcome Header -->
      <section class="mb-12 pt-8">
        <h2 class="font-headline-lg text-headline-lg text-primary mb-2">
          Welcome back, <span class="scribble-underline">{{ userName }}</span>
        </h2>
        <p class="font-body-lg text-body-lg text-secondary">Here's what's happening with the InnovationHub ecosystem today.</p>
      </section>

      <!-- Loading Skeleton -->
      <template v-if="loading">
        <section class="grid grid-cols-1 md:grid-cols-3 gap-gutter mb-12">
          <div v-for="i in 3" :key="i" class="h-40 bg-surface-container-high rounded-lg border-2 border-primary animate-pulse"></div>
        </section>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter">
          <div class="lg:col-span-2 h-80 bg-surface-container-high rounded-lg border-2 border-primary animate-pulse"></div>
          <div class="h-80 bg-surface-container-high rounded-lg border-2 border-primary animate-pulse"></div>
        </div>
      </template>

      <!-- Dashboard Content -->
      <template v-if="!loading && stats">
        <!-- Summary Cards -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-gutter mb-12">
          <div class="border-2 border-primary p-8 bg-surface-container-low rounded-lg relative overflow-hidden group">
            <div class="flex justify-between items-start mb-4">
              <span class="material-symbols-outlined text-primary text-4xl">lightbulb</span>
              <span class="text-on-tertiary-container bg-tertiary-fixed px-3 py-1 rounded-full text-[12px] font-bold">+{{ stats.growth_pct }}%</span>
            </div>
            <h3 class="font-label-md text-label-md text-secondary uppercase tracking-wider mb-2">Total Ideas</h3>
            <p class="font-display-lg text-headline-lg text-primary">{{ stats.total_ideas }}</p>
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity pointer-events-none">
              <span class="material-symbols-outlined text-9xl">bolt</span>
            </div>
          </div>
          <div class="border-2 border-primary p-8 bg-secondary-container rounded-lg relative overflow-hidden group">
            <div class="flex justify-between items-start mb-4">
              <span class="material-symbols-outlined text-primary text-4xl">visibility</span>
              <span class="text-primary bg-surface px-3 py-1 rounded-full text-[12px] font-bold">Active</span>
            </div>
            <h3 class="font-label-md text-label-md text-secondary uppercase tracking-wider mb-2">Ideas in Review</h3>
            <p class="font-display-lg text-headline-lg text-primary">{{ stats.in_review }}</p>
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity pointer-events-none">
              <span class="material-symbols-outlined text-9xl">rate_review</span>
            </div>
          </div>
          <div class="border-2 border-primary p-8 bg-tertiary-fixed-dim rounded-lg relative overflow-hidden group">
            <div class="flex justify-between items-start mb-4">
              <span class="material-symbols-outlined text-primary text-4xl">check_circle</span>
              <span class="text-on-tertiary-fixed bg-surface px-3 py-1 rounded-full text-[12px] font-bold">Goal: {{ stats.goal }}</span>
            </div>
            <h3 class="font-label-md text-label-md text-secondary uppercase tracking-wider mb-2">Implemented</h3>
            <p class="font-display-lg text-headline-lg text-primary">{{ stats.implemented }}</p>
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
              <span class="material-symbols-outlined text-9xl">auto_awesome</span>
            </div>
          </div>
        </section>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter">
          <!-- Recent Activity -->
          <section class="lg:col-span-2 border-2 border-primary bg-surface p-8 rounded-lg">
            <div class="flex justify-between items-center mb-8">
              <h3 class="font-headline-md text-headline-md text-primary">Recent <span class="scribble-circle">Activity</span></h3>
            </div>
            <div v-if="activities.length === 0" class="py-12 text-center">
              <span class="material-symbols-outlined text-4xl text-tertiary-fixed-dim mb-2">history</span>
              <p class="text-secondary text-body-md">No recent activity yet.</p>
            </div>
            <div v-else class="space-y-6">
              <div
                v-for="act in activities"
                :key="act.id"
                class="flex gap-4 p-4 hover:bg-surface-container transition-colors rounded-DEFAULT group cursor-pointer border-l-2 border-transparent hover:border-tertiary-fixed-dim"
              >
                <div
                  class="w-12 h-12 rounded-full flex items-center justify-center shrink-0"
                  :class="activityIcons[act.type]?.bg || 'bg-surface-container-high'"
                >
                  <span class="material-symbols-outlined" :class="act.type === 'new' ? 'text-surface' : 'text-primary'">
                    {{ activityIcons[act.type]?.icon || 'article' }}
                  </span>
                </div>
                <div class="flex-1">
                  <div class="flex justify-between items-start mb-1">
                    <h4 class="font-label-md text-label-md text-primary">
                      <template v-if="act.type === 'new'">New Suggestion: "{{ act.title }}"</template>
                      <template v-else-if="act.type === 'feedback'">Feedback received on "{{ act.title }}"</template>
                      <template v-else-if="act.type === 'promoted'">Idea Promoted to "Implementation"</template>
                      <template v-else-if="act.type === 'approved'">Idea Approved: "{{ act.title }}"</template>
                      <template v-else>{{ act.title }}</template>
                    </h4>
                    <span class="text-[12px] text-outline shrink-0 ml-2">{{ timeAgo(act.created_at) }}</span>
                  </div>
                  <p class="text-body-md text-on-surface-variant line-clamp-1">{{ act.description }}</p>
                  <div class="mt-2 flex gap-2">
                    <span class="px-2 py-0.5 bg-secondary-container text-primary rounded-full text-[10px] font-bold uppercase tracking-tighter">{{ act.category }}</span>
                    <span
                      class="px-2 py-0.5 bg-surface-container-high text-secondary rounded-full text-[10px] font-bold uppercase tracking-tighter"
                    >{{ act.status }}</span>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- Category Insights -->
          <section class="border-2 border-primary bg-primary-container p-8 rounded-lg text-surface">
            <h3 class="font-headline-md text-headline-md mb-8">Category <span class="text-tertiary-fixed">Insights</span></h3>
            <div v-if="categories.length === 0" class="py-8 text-center">
              <p class="text-surface/70 text-body-md">No categories yet.</p>
            </div>
            <div v-else class="space-y-6">
              <div v-for="cat in categories" :key="cat.name" class="space-y-2">
                <div class="flex justify-between text-label-md font-label-md">
                  <span>{{ cat.name }}</span>
                  <span>{{ cat.percentage }}%</span>
                </div>
                <div class="w-full bg-surface/10 h-2 rounded-full overflow-hidden">
                  <div
                    class="h-full rounded-full"
                    :class="{
                      'bg-tertiary-fixed': cat.name === 'Workplace',
                      'bg-secondary-fixed': cat.name === 'Technology',
                      'bg-primary-fixed-dim': cat.name === 'Process Improvement',
                      'bg-error': cat.name === 'Employee Welfare',
                    }"
                    :style="{ width: cat.percentage + '%' }"
                  ></div>
                </div>
              </div>
            </div>
            <div class="mt-12 p-6 border-2 border-dashed border-surface/20 rounded-lg text-center">
              <p class="font-body-md text-body-md text-primary-fixed mb-4 italic">"True innovation starts with a single, handcrafted thought."</p>
              <button class="w-full py-2 bg-surface text-primary rounded-full font-label-md text-label-md hover:bg-tertiary-fixed transition-colors cursor-pointer">Download Report</button>
            </div>
          </section>
        </div>

        <!-- Top Contributors -->
        <section class="mt-12 mb-24 grid grid-cols-1 md:grid-cols-4 gap-gutter">
          <div class="md:col-span-1 flex flex-col justify-center">
            <h3 class="font-headline-md text-headline-md text-primary mb-4">Top Contributors</h3>
            <p class="text-body-md text-secondary">The minds behind our most impactful artisanal insights this quarter.</p>
          </div>
          <div class="md:col-span-3 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div v-for="c in contributors.slice(0, 3)" :key="c.id" class="border-2 border-primary bg-surface p-4 text-center rounded-lg hover:rotate-2 transition-transform cursor-pointer">
              <div class="w-16 h-16 rounded-full bg-surface-container-high mx-auto mb-3 border-2 border-primary flex items-center justify-center">
                <span class="material-symbols-outlined text-primary">person</span>
              </div>
              <p class="font-label-md text-label-md text-primary">{{ c.name }}</p>
              <p class="text-[10px] text-secondary uppercase font-bold">{{ c.ideas_count }} Ideas</p>
            </div>
            <div class="border-2 border-primary bg-surface p-4 text-center rounded-lg hover:-rotate-1 transition-transform cursor-pointer">
              <div class="w-16 h-16 rounded-full bg-surface-container-high mx-auto mb-3 border-2 border-dashed border-primary flex items-center justify-center">
                <span class="material-symbols-outlined text-primary">add</span>
              </div>
              <p class="font-label-md text-label-md text-secondary">Join Them</p>
            </div>
          </div>
        </section>
      </template>
    </main>
  </div>
</template>
