<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AdminLayout from '@/components/AdminLayout.vue'
import axios from 'axios'

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

onMounted(fetchDashboardData)
</script>

<template>
  <AdminLayout>
    <!-- Welcome Header -->
    <section class="mb-8 pt-4">
      <h2 class="font-headline-md text-headline-md text-primary mb-1">
        Welcome back, <span class="scribble-underline">{{ userName }}</span>
      </h2>
      <p class="font-body-md text-body-md text-secondary">Here's what's happening with the InnovationHub ecosystem today.</p>
    </section>

    <!-- Loading Skeleton -->
    <template v-if="loading">
      <section class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div v-for="i in 3" :key="i" class="h-32 bg-surface-container-high rounded-lg border-2 border-primary animate-pulse"></div>
      </section>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 h-64 bg-surface-container-high rounded-lg border-2 border-primary animate-pulse"></div>
        <div class="h-64 bg-surface-container-high rounded-lg border-2 border-primary animate-pulse"></div>
      </div>
    </template>

    <!-- Dashboard Content -->
    <template v-if="!loading && stats">
      <!-- Summary Cards -->
      <section class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="border-2 border-primary p-4 bg-surface-container-low rounded-lg relative overflow-hidden group">
          <div class="flex justify-between items-start mb-3">
            <span class="material-symbols-outlined text-primary text-2xl">lightbulb</span>
            <span class="text-on-tertiary-container bg-tertiary-fixed px-2 py-0.5 rounded-full text-xs font-bold">+{{ stats.growth_pct }}%</span>
          </div>
          <h3 class="font-label-md text-xs text-secondary uppercase tracking-wider mb-1">Total Ideas</h3>
          <p class="font-headline-md text-headline-md text-primary">{{ stats.total_ideas }}</p>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity pointer-events-none">
            <span class="material-symbols-outlined text-7xl">bolt</span>
          </div>
        </div>
        <div class="border-2 border-primary p-4 bg-secondary-container rounded-lg relative overflow-hidden group">
          <div class="flex justify-between items-start mb-3">
            <span class="material-symbols-outlined text-primary text-2xl">visibility</span>
            <span class="text-primary bg-surface px-2 py-0.5 rounded-full text-xs font-bold">Active</span>
          </div>
          <h3 class="font-label-md text-xs text-secondary uppercase tracking-wider mb-1">Ideas in Review</h3>
          <p class="font-headline-md text-headline-md text-primary">{{ stats.in_review }}</p>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity pointer-events-none">
            <span class="material-symbols-outlined text-7xl">rate_review</span>
          </div>
        </div>
        <div class="border-2 border-primary p-4 bg-tertiary-fixed-dim rounded-lg relative overflow-hidden group">
          <div class="flex justify-between items-start mb-3">
            <span class="material-symbols-outlined text-primary text-2xl">check_circle</span>
            <span class="text-on-tertiary-fixed bg-surface px-2 py-0.5 rounded-full text-xs font-bold">Goal: {{ stats.goal }}</span>
          </div>
          <h3 class="font-label-md text-xs text-secondary uppercase tracking-wider mb-1">Implemented</h3>
          <p class="font-headline-md text-headline-md text-primary">{{ stats.implemented }}</p>
          <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
            <span class="material-symbols-outlined text-7xl">auto_awesome</span>
          </div>
        </div>
      </section>

      <!-- Two Column Layout -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Recent Activity -->
        <section class="lg:col-span-2 border-2 border-primary bg-surface p-4 rounded-lg">
          <div class="flex justify-between items-center mb-4">
            <h3 class="font-label-md text-label-md text-primary">Recent <span class="scribble-circle">Activity</span></h3>
          </div>
          <div v-if="activities.length === 0" class="py-8 text-center">
            <span class="material-symbols-outlined text-2xl text-tertiary-fixed-dim mb-2">history</span>
            <p class="text-secondary text-body-sm">No recent activity yet.</p>
          </div>
          <div v-else class="space-y-3">
            <div
              v-for="act in activities"
              :key="act.id"
              class="flex gap-3 p-3 hover:bg-surface-container transition-colors rounded-DEFAULT group cursor-pointer border-l-2 border-transparent hover:border-tertiary-fixed-dim"
            >
              <div
                class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 text-lg"
                :class="activityIcons[act.type]?.bg || 'bg-surface-container-high'"
              >
                <span class="material-symbols-outlined" :class="act.type === 'new' ? 'text-surface' : 'text-primary'">
                  {{ activityIcons[act.type]?.icon || 'article' }}
                </span>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex justify-between items-start mb-0.5">
                  <h4 class="font-label-md text-xs text-primary line-clamp-1">
                    <template v-if="act.type === 'new'">New: "{{ act.title }}"</template>
                    <template v-else-if="act.type === 'feedback'">Feedback: "{{ act.title }}"</template>
                    <template v-else-if="act.type === 'promoted'">Promoted: "{{ act.title }}"</template>
                    <template v-else-if="act.type === 'approved'">Approved: "{{ act.title }}"</template>
                    <template v-else>{{ act.title }}</template>
                  </h4>
                  <span class="text-xs text-outline shrink-0 ml-2">{{ timeAgo(act.created_at) }}</span>
                </div>
                <p class="text-body-sm text-on-surface-variant line-clamp-1">{{ act.description }}</p>
                <div class="mt-1.5 flex gap-1.5">
                  <span class="px-1.5 py-0.5 bg-secondary-container text-primary rounded-full text-[8px] font-bold uppercase tracking-tighter">{{ act.category }}</span>
                  <span
                    class="px-1.5 py-0.5 bg-surface-container-high text-secondary rounded-full text-[8px] font-bold uppercase tracking-tighter"
                  >{{ act.status }}</span>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Category Insights -->
        <section class="border-2 border-primary bg-primary-container p-4 rounded-lg text-surface">
          <h3 class="font-label-md text-label-md mb-4">Category <span class="text-tertiary-fixed">Insights</span></h3>
          <div v-if="categories.length === 0" class="py-6 text-center">
            <p class="text-surface/70 text-body-sm">No categories yet.</p>
          </div>
          <div v-else class="space-y-4">
            <div v-for="cat in categories" :key="cat.name" class="space-y-1">
              <div class="flex justify-between text-label-md font-label-md text-xs">
                <span>{{ cat.name }}</span>
                <span>{{ cat.percentage }}%</span>
              </div>
              <div class="w-full bg-surface/10 h-1.5 rounded-full overflow-hidden">
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
          <div class="mt-6 p-3 border-2 border-dashed border-surface/20 rounded-lg text-center">
            <p class="font-body-sm text-surface text-xs mb-2 italic">"True innovation starts with a single, handcrafted thought."</p>
            <button class="w-full py-1.5 bg-surface text-primary rounded-full font-label-md text-xs hover:bg-tertiary-fixed transition-colors cursor-pointer" @click="router.push('/admin/analytics')">Download Report</button>
          </div>
        </section>
      </div>

      <!-- Top Contributors -->
      <section class="mt-8 mb-16 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-1 flex flex-col justify-center">
          <h3 class="font-label-md text-label-md text-primary mb-2">Top Contributors</h3>
          <p class="text-body-sm text-secondary">The minds behind our most impactful artisanal insights this quarter.</p>
        </div>
        <div class="md:col-span-3 grid grid-cols-2 md:grid-cols-4 gap-3">
          <div v-for="c in contributors.slice(0, 3)" :key="c.id" class="border-2 border-primary bg-surface p-3 text-center rounded-lg hover:rotate-2 transition-transform cursor-pointer">
            <div class="w-14 h-14 rounded-full bg-surface-container-high mx-auto mb-2 border-2 border-primary flex items-center justify-center text-lg">
              <span class="material-symbols-outlined text-primary">person</span>
            </div>
            <p class="font-label-md text-xs text-primary">{{ c.name }}</p>
            <p class="text-[9px] text-secondary uppercase font-bold">{{ c.ideas_count }} Ideas</p>
          </div>
          <div class="border-2 border-primary bg-surface p-3 text-center rounded-lg hover:-rotate-1 transition-transform cursor-pointer">
            <div class="w-14 h-14 rounded-full bg-surface-container-high mx-auto mb-2 border-2 border-dashed border-primary flex items-center justify-center text-lg">
              <span class="material-symbols-outlined text-primary">add</span>
            </div>
            <p class="font-label-md text-xs text-secondary">Join Them</p>
          </div>
        </div>
      </section>
    </template>
  </AdminLayout>
</template>
