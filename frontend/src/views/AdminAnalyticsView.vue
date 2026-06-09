<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import AdminLayout from '@/components/AdminLayout.vue'

const authStore = useAuthStore()
const router = useRouter()

const stats = ref({ total_ideas: 0, in_review: 0, implemented: 0, goal: 200, growth_pct: 0 })
const categories = ref([])
const topIdeas = ref([])
const trends = ref([])
const loading = ref(true)
const error = ref(null)

const topCategory = computed(() => categories.value[0]?.name || 'N/A')
const topCategoryPct = computed(() => categories.value[0]?.percentage || 0)

async function fetchAnalytics() {
  loading.value = true
  error.value = null
  try {
    const [statsRes, categoriesRes, topIdeasRes, trendsRes] = await Promise.all([
      axios.get('/api/v1/admin/dashboard/stats'),
      axios.get('/api/v1/admin/dashboard/categories'),
      axios.get('/api/v1/admin/dashboard/top-ideas'),
      axios.get('/api/v1/admin/dashboard/trends'),
    ])
    stats.value = statsRes.data
    categories.value = categoriesRes.data
    topIdeas.value = topIdeasRes.data
    trends.value = trendsRes.data
  } catch (e) {
    error.value = 'Failed to load analytics data'
    console.error(e)
  } finally {
    loading.value = false
  }
}

const categoryColors = ['primary', 'secondary-fixed-dim', 'tertiary-fixed-dim', 'on-primary-container']

function categoryColor(index) {
  return categoryColors[index % categoryColors.length]
}

const statusBadgeClass = (status) => {
  const map = {
    'Implemented': 'bg-tertiary-fixed text-primary',
    'Approved': 'bg-tertiary-fixed text-primary',
    'In Review': 'bg-secondary-container text-on-secondary-container',
    'Pending': 'bg-surface-container text-on-surface-variant',
    'Rejected': 'bg-error-container text-on-error-container',
  }
  return map[status] || 'bg-surface-container text-on-surface-variant'
}

function trendHeight(count) {
  if (!trends.value.length) return 0
  const max = Math.max(...trends.value.map(t => t.count), 1)
  return max > 0 ? (count / max) * 100 : 0
}

onMounted(fetchAnalytics)

async function downloadReport() {
  try {
    const res = await axios.get('/api/v1/admin/dashboard/report', { responseType: 'blob' })
    const url = URL.createObjectURL(new Blob([res.data], { type: 'application/pdf' }))
    const link = document.createElement('a')
    link.href = url
    link.download = 'analytics-report-' + new Date().toISOString().slice(0, 10) + '.pdf'
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    URL.revokeObjectURL(url)
  } catch (e) {
    console.error('Download failed', e)
  }
}
</script>

<template>
  <AdminLayout>
    <!-- Main Content Canvas -->
    <main class="pt-4 min-h-screen pb-8">
      <!-- Hero Header with Hand-Drawn Style -->
      <section class="relative overflow-hidden rounded-xl bg-surface-container-low border-2 border-primary mb-8 p-6 md:p-8 flex flex-col items-center justify-center text-center min-h-[300px]">
        <div class="absolute inset-0 z-0 opacity-20 pointer-events-none overflow-hidden">
          <img class="w-full h-full object-cover grayscale" alt="Stylized minimalist illustration background featuring clean hand-drawn line art depicting messy creative thoughts, tangled strings, and artistic chaos. Lo-fi handcrafted style using monochromatic deep green and cream color palette." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCvtyhZA1VoiqunkK4gc_52je6iF8eM0cfOjPYI2Z7mZXtTKV8_KEF3P4kC20fNJe_-KhX025J3BqTNGFIHweNk-yKBkAtaOWnNZYzLR9-AeLpcS3azzI0oNUFwo2fCL4EGdA-kE6CfZl6e3anx2ODI9RF-F3V4zo9r6TIA4sakINVvTSnWkhz7pPwPoG7yB9SEWgQM_vf7fzb3O0OqdU-yt4I1Xnlkn15Kw_JkGAAZ9X9_3O0dci6f5EpG7Cju0lmxdwCoJhtN-nY" />
        </div>
        <!-- Scribble Accents -->
        <svg class="scribble-accent top-10 left-10 w-24 h-24 text-secondary-fixed-dim opacity-50 absolute" viewBox="0 0 100 100">
          <path d="M10,50 Q40,10 90,50" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2"></path>
        </svg>
        <div class="relative z-10 max-w-2xl">
          <h1 class="font-headline-md text-headline-md text-primary mb-3">
            Innovation <span class="relative">Metrics<svg class="absolute -bottom-2 left-0 w-full h-4 text-tertiary-fixed" preserveAspectRatio="none" viewBox="0 0 100 10"><path d="M0,5 Q50,0 100,5" fill="none" stroke="currentColor" stroke-width="4"></path></svg></span> &amp; Insights
          </h1>
          <p class="font-body-md text-body-md text-on-surface-variant max-w-xl mx-auto text-sm">
            Tracking the human creative engine behind every breakthrough idea. Real-time data interpreted through a bespoke lens.
          </p>
        </div>
        <div v-if="loading" class="relative z-10 mt-6">
          <span class="text-on-surface-variant text-xs">Loading metrics...</span>
        </div>
        <div v-else class="relative z-10 mt-6 flex flex-wrap justify-center gap-2">
          <div class="bg-surface border-2 border-primary px-4 py-2 rounded-full flex flex-col items-center">
            <span class="text-xs text-on-surface-variant">Active Ideas</span>
            <span class="text-label-md font-bold text-primary">{{ stats.total_ideas }}</span>
          </div>
          <div class="bg-surface border-2 border-primary px-4 py-2 rounded-full flex flex-col items-center">
            <span class="text-xs text-on-surface-variant">Implemented</span>
            <span class="text-label-md font-bold text-primary">{{ stats.implemented }}</span>
          </div>
          <div class="bg-surface border-2 border-primary px-4 py-2 rounded-full flex flex-col items-center">
            <span class="text-xs text-on-surface-variant">In Review</span>
            <span class="text-label-md font-bold text-primary">{{ stats.in_review }}</span>
          </div>
          <div class="bg-surface border-2 border-primary px-4 py-2 rounded-full flex flex-col items-center">
            <span class="text-xs text-on-surface-variant">Growth</span>
            <span class="text-label-md font-bold text-primary">{{ stats.growth_pct }}%</span>
          </div>
        </div>
      </section>

      <!-- Analytics Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-8">
        <!-- Submission Trends -->
        <div class="lg:col-span-8 p-4 scribble-border bg-surface rounded-lg border-2 border-primary">
          <div class="flex justify-between items-end mb-4">
            <div>
              <h3 class="font-label-md text-label-md text-primary">Submission Trends</h3>
              <p class="text-xs text-on-surface-variant">Idea volume over the last 30 days</p>
            </div>
            <div class="flex gap-1">
              <span class="w-2 h-2 rounded-full bg-primary mt-0.5"></span>
              <span class="text-xs font-bold">New Entries</span>
            </div>
          </div>
          <div v-if="loading" class="h-48 flex items-center justify-center">
            <span class="text-on-surface-variant text-xs">Loading trends...</span>
          </div>
          <div v-else-if="trends.length === 0" class="h-48 flex items-center justify-center">
            <span class="text-on-surface-variant text-xs">No submission data yet</span>
          </div>
          <div v-else class="h-48 flex items-end justify-between gap-1 px-2 relative">
            <div class="absolute inset-0 border-b-2 border-outline-variant opacity-30 flex flex-col justify-between py-2">
              <div class="border-t border-dashed border-outline-variant w-full"></div>
              <div class="border-t border-dashed border-outline-variant w-full"></div>
              <div class="border-t border-dashed border-outline-variant w-full"></div>
            </div>
            <div
              v-for="(day, i) in trends"
              :key="day.date"
              class="relative flex-1 bg-primary/10 rounded-t-lg transition-colors"
              :class="day.count > 0 ? 'hover:bg-primary/20' : ''"
              :style="{ height: day.count > 0 ? trendHeight(day.count) + '%' : '4%' }"
              :title="day.date + ': ' + day.count + ' submissions'"
            ></div>
          </div>
          <div v-if="trends.length > 0" class="flex justify-between mt-2 text-[8px] font-bold text-on-surface-variant px-1">
            <span>{{ trends[0]?.day_label }} {{ trends[0]?.date?.slice(5) }}</span>
            <span>{{ trends[Math.floor(trends.length/3)]?.day_label }} {{ trends[Math.floor(trends.length/3)]?.date?.slice(5) }}</span>
            <span>{{ trends[Math.floor(2*trends.length/3)]?.day_label }} {{ trends[Math.floor(2*trends.length/3)]?.date?.slice(5) }}</span>
            <span>{{ trends[trends.length-1]?.day_label }} {{ trends[trends.length-1]?.date?.slice(5) }}</span>
          </div>
        </div>

        <!-- Category Distribution -->
        <div class="lg:col-span-4 p-4 scribble-border bg-surface rounded-lg border-2 border-primary flex flex-col items-center justify-center">
          <h3 class="font-label-md text-label-md text-primary mb-1 self-start">Category Distribution</h3>
          <p class="text-xs text-on-surface-variant mb-4 self-start">Engagement by sector</p>
          <div v-if="loading" class="w-40 h-40 flex items-center justify-center">
            <span class="text-on-surface-variant text-xs">Loading...</span>
          </div>
          <div v-else-if="categories.length === 0" class="w-40 h-40 flex items-center justify-center">
            <span class="text-on-surface-variant text-xs">No data yet</span>
          </div>
          <template v-else>
            <div class="relative w-40 h-40 flex items-center justify-center">
              <div class="absolute inset-0 rounded-full border-[16px] border-secondary-fixed-dim"></div>
              <div
                class="absolute inset-0 rounded-full border-[16px] border-primary border-t-transparent border-l-transparent border-r-transparent"
                :style="{ transform: 'rotate(' + (categories[0]?.percentage || 0) * 3.6 + 'deg)' }"
              ></div>
              <div class="text-center">
                <span class="block font-label-md text-label-md text-primary">{{ categories[0]?.percentage || 0 }}%</span>
                <span class="text-[8px] font-bold uppercase tracking-widest text-on-surface-variant">{{ categories[0]?.name || 'N/A' }}</span>
              </div>
            </div>
            <div class="mt-4 space-y-2 w-full">
              <div
                v-for="(cat, i) in categories"
                :key="cat.name"
                class="flex items-center justify-between text-xs"
              >
                <div class="flex items-center gap-1">
                  <span class="w-2 h-2 rounded-full" :class="'bg-' + categoryColor(i)"></span>
                  <span>{{ cat.name }}</span>
                </div>
                <span class="font-bold">{{ cat.percentage }}%</span>
              </div>
            </div>
          </template>
        </div>
      </div>

      <!-- Strategic Insight Section -->
      <section v-if="!loading && categories.length > 0" class="mb-8 relative">
        <div class="bg-primary-container text-on-primary-container p-6 md:p-8 rounded-xl overflow-hidden relative border-2 border-primary">
          <div class="absolute top-0 right-0 p-2 opacity-10">
            <span class="material-symbols-outlined text-7xl" style="font-variation-settings: 'FILL' 1;">lightbulb</span>
          </div>
          <div class="relative z-10 max-w-3xl">
            <div class="inline-flex items-center gap-1 bg-tertiary-fixed text-primary px-3 py-0.5 rounded-full text-xs font-bold mb-4">
              <span class="material-symbols-outlined text-sm">auto_awesome</span>
              STRATEGIC INSIGHT
            </div>
            <h2 class="font-label-md text-label-md text-background mb-4 leading-tight">
              <span class="text-tertiary-fixed">{{ topCategory }}</span> is your most active category with {{ topCategoryPct }}% of all ideas.
            </h2>
            <p class="font-body-md text-body-md text-on-primary-container/80 mb-4 text-sm">
              {{ stats.total_ideas }} total ideas submitted, {{ stats.implemented }} implemented, and {{ stats.in_review }} currently in review.
              Keep the momentum going — encourage cross-team collaboration to boost adoption.
            </p>
            <div class="flex flex-wrap gap-2">
              <button @click="downloadReport" class="bg-background text-primary font-bold py-2 px-4 rounded-full text-sm hover:bg-secondary-fixed transition-colors">Download Report</button>
            </div>
          </div>
          <svg class="absolute top-1/2 left-1/3 -translate-x-1/2 -translate-y-1/2 w-80 h-40 opacity-20 pointer-events-none" viewBox="0 0 400 200">
            <path d="M50,100 C50,50 350,50 350,100 C350,150 50,150 50,100 Z" fill="none" stroke="#d7ee5f" stroke-dasharray="10 5" stroke-width="4"></path>
          </svg>
        </div>
      </section>

      <!-- Top Performing Ideas Table -->
      <section class="scribble-border bg-surface rounded-xl overflow-hidden border-2 border-primary">
        <div class="p-4 border-b-2 border-primary flex justify-between items-center">
          <h3 class="font-label-md text-label-md text-primary">Top Performing Ideas</h3>
          <router-link to="/admin/suggestions" class="text-xs font-bold text-primary flex items-center gap-0.5 hover:underline">
            View All <span class="material-symbols-outlined text-sm">arrow_forward</span>
          </router-link>
        </div>
        <div v-if="loading" class="p-8 text-center text-on-surface-variant text-xs">Loading ideas...</div>
        <div v-else-if="topIdeas.length === 0" class="p-8 text-center text-on-surface-variant text-xs">No ideas submitted yet</div>
        <div v-else class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead>
              <tr class="bg-surface-container-low">
                <th class="px-4 py-3 font-bold text-xs text-on-surface-variant">PROJECT NAME</th>
                <th class="px-4 py-3 font-bold text-xs text-on-surface-variant">CREATOR</th>
                <th class="px-4 py-3 font-bold text-xs text-on-surface-variant">CATEGORY</th>
                <th class="px-4 py-3 font-bold text-xs text-on-surface-variant">SCORE</th>
                <th class="px-4 py-3 font-bold text-xs text-on-surface-variant">STATUS</th>
                <th class="px-4 py-3 font-bold text-xs text-on-surface-variant text-right">ACTION</th>
              </tr>
            </thead>
            <tbody class="divide-y-2 divide-primary/5">
              <tr
                v-for="idea in topIdeas"
                :key="idea.id"
                class="hover:bg-secondary-container/20 transition-colors"
              >
                <td class="px-4 py-3">
                  <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-background text-sm">
                      <span class="material-symbols-outlined">lightbulb</span>
                    </div>
                    <div>
                      <p class="font-bold text-xs text-primary">{{ idea.title }}</p>
                      <p class="text-[10px] text-on-surface-variant">{{ idea.description?.slice(0, 40) }}{{ idea.description?.length > 40 ? '...' : '' }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-1">
                    <div class="w-5 h-5 rounded-full bg-secondary-fixed-dim"></div>
                    <span class="text-xs">{{ idea.user_name }}</span>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <span class="text-xs text-on-surface-variant">{{ idea.category }}</span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-1">
                    <div class="w-12 h-1.5 bg-surface-container rounded-full overflow-hidden">
                      <div class="h-full bg-tertiary-fixed-dim" :style="{ width: idea.score + '%' }"></div>
                    </div>
                    <span class="font-bold text-xs">{{ idea.score }}</span>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <span
                    class="px-2 py-0.5 rounded-full text-[8px] font-bold uppercase tracking-wider"
                    :class="statusBadgeClass(idea.status)"
                  >{{ idea.status }}</span>
                </td>
                <td class="px-4 py-3 text-right">
                  <button class="material-symbols-outlined text-sm text-on-surface-variant hover:text-primary transition-colors">more_horiz</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </AdminLayout>
</template>
