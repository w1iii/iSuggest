<script setup>
import { useAuthStore } from '@/stores/auth'
import { useRouter, useRoute } from 'vue-router'
import AdminLayout from '@/components/AdminLayout.vue'
import SuggestionDetailModal from '@/components/SuggestionDetailModal.vue'
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import axios from 'axios'

const authStore = useAuthStore()
const router = useRouter()
const route = useRoute()

const suggestions = ref({
  Pending: [],
  'In Review': [],
  Approved: [],
  Implemented: [],
})

const loading = ref(true)
const draggedCard = ref(null)
const sourceColumn = ref(null)
const selectedSuggestion = ref(null)
const showModal = ref(false)
const activeTab = ref('Pending')
let abortController = null
let isMounted = false

const columns = [
  { key: 'Pending', label: 'Submitted' },
  { key: 'In Review', label: 'In Review' },
  { key: 'Approved', label: 'Approved' },
  { key: 'Implemented', label: 'Implemented' },
]

const statusMap = {
  Pending: 'Pending',
  'In Review': 'In Review',
  Approved: 'Approved',
  Implemented: 'Implemented',
}

const getColumnCount = (status) => suggestions.value[status]?.length || 0

async function fetchSuggestions() {
  try {
    // Cancel previous request if still pending
    if (abortController) {
      abortController.abort()
    }
    
    loading.value = true
    abortController = new AbortController()
    
    const res = await axios.get('/api/v1/admin/suggestions', {
      signal: abortController.signal
    })
    
    // Only update if component still mounted
    if (!isMounted) {
      return
    }
    
    const data = res.data.data || res.data

    // Group by status
    const grouped = {
      Pending: [],
      'In Review': [],
      Approved: [],
      Implemented: [],
    }

    if (Array.isArray(data)) {
      data.forEach(sugg => {
        if (grouped[sugg.status] !== undefined) {
          grouped[sugg.status].push(sugg)
        }
      })
    }

    suggestions.value = grouped
  } catch (e) {
    // Ignore abort errors
    if (e.code !== 'ERR_CANCELED') {
      console.error('[AdminSuggestions] Failed to load:', e)
    }
  } finally {
    if (isMounted) {
      loading.value = false
    }
  }
}

async function updateSuggestionStatus(suggestionId, newStatus) {
  try {
    await axios.patch(`/api/v1/admin/suggestions/${suggestionId}/status`, {
      status: newStatus,
    })
    return true
  } catch (e) {
    console.error('Failed to update suggestion status', e)
    return false
  }
}

const handleDragStart = (card, status) => {
  draggedCard.value = card
  sourceColumn.value = status
}

const handleDragOver = (e) => {
  e.preventDefault()
  e.dataTransfer.dropEffect = 'move'
}

const handleDrop = async (targetStatus) => {
  if (draggedCard.value && sourceColumn.value && sourceColumn.value !== targetStatus) {
    const sourceIdx = suggestions.value[sourceColumn.value].findIndex(c => c.id === draggedCard.value.id)
    if (sourceIdx !== -1) {
      // Move immediately (optimistic update)
      const card = suggestions.value[sourceColumn.value][sourceIdx]
      suggestions.value[sourceColumn.value].splice(sourceIdx, 1)
      suggestions.value[targetStatus].push(card)

      // Update DB status in background
      const success = await updateSuggestionStatus(card.id, targetStatus)
      if (!success) {
        // Rollback on error
        suggestions.value[targetStatus].splice(suggestions.value[targetStatus].length - 1, 1)
        suggestions.value[sourceColumn.value].splice(sourceIdx, 0, card)
      }
    }
  }
  draggedCard.value = null
  sourceColumn.value = null
}

const handleDragEnd = () => {
  draggedCard.value = null
  sourceColumn.value = null
}

const openSuggestionDetail = (card) => {
  selectedSuggestion.value = card
  showModal.value = true
}

const handleSuggestionUpdated = (updatedData) => {
  // Update card in appropriate column
  const oldStatus = selectedSuggestion.value.status
  const newStatus = updatedData.status

  if (oldStatus !== newStatus) {
    // Remove from old status
    const idx = suggestions.value[oldStatus].findIndex(s => s.id === updatedData.id)
    if (idx !== -1) {
      suggestions.value[oldStatus].splice(idx, 1)
    }
    // Add to new status
    suggestions.value[newStatus].push(updatedData)
  }

  // Update selected suggestion
  selectedSuggestion.value = updatedData
}

onMounted(() => {
  isMounted = true
  fetchSuggestions()
})

// Watch route to refetch when returning to suggestions page
watch(() => route.name, (newRoute) => {
  if (newRoute === 'admin-suggestions' && !loading.value) {
    fetchSuggestions()
  }
})

// Cleanup on component unmount
onBeforeUnmount(() => {
  isMounted = false
  // Cancel ongoing fetch
  if (abortController) {
    abortController.abort()
  }
  loading.value = false
  draggedCard.value = null
  sourceColumn.value = null
  selectedSuggestion.value = null
  showModal.value = false
})
</script>

<template>
  <div>
    <AdminLayout>
      <!-- Header -->
      <section class="mb-8 pt-4">
      <h2 class="font-headline-md text-headline-md text-primary mb-1">
        Kanban <span class="scribble-underline">Workflow</span>
      </h2>
      <p class="font-body-md text-body-md text-secondary max-w-2xl">Overview and manage the suggestion workflow across stages.</p>
    </section>

    <!-- Mobile Tab Bar -->
    <div class="md:hidden flex overflow-x-auto gap-1 mb-4 pb-2 scrollbar-hide">
      <button
        v-for="column in columns"
        :key="column.key"
        @click="activeTab = column.key"
        class="shrink-0 px-4 py-2 rounded-full text-xs font-semibold border-2 border-primary transition-all whitespace-nowrap"
        :class="activeTab === column.key
          ? 'bg-primary text-surface'
          : 'bg-surface text-primary hover:bg-surface-container'"
      >
        {{ column.label }}
        <span class="ml-1.5 inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-bold"
          :class="activeTab === column.key ? 'bg-surface text-primary' : 'bg-secondary-container text-on-secondary-container'"
        >
          {{ getColumnCount(column.key) }}
        </span>
      </button>
    </div>

    <!-- Kanban Board -->
    <div v-if="!loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-start">
      <!-- Columns -->
      <div v-for="column in columns" :key="column.key" class="flex flex-col gap-3" :class="activeTab !== column.key ? 'hidden md:flex' : 'flex'">
        <!-- Column Header -->
        <div class="flex items-center justify-between px-0 mb-2">
          <h3 class="font-label-md text-label-md text-primary flex items-center gap-1">
            {{ column.label }}
            <span class="bg-secondary-container text-on-secondary-container px-2 py-0.5 rounded-full text-xs font-bold">
              {{ getColumnCount(column.key) }}
            </span>
          </h3>
          <span class="material-symbols-outlined text-outline text-lg cursor-pointer hover:text-primary transition-colors">more_horiz</span>
        </div>

        <!-- Drop Zone -->
        <div
          class="flex flex-col gap-3 min-h-[200px] md:min-h-[400px] p-4 rounded-lg bg-surface-container-lowest border-2 border-primary transition-all hover:border-secondary-container"
          @dragover="handleDragOver"
          @drop="handleDrop(column.key)"
          @dragleave="e => e.preventDefault()"
        >
          <!-- Cards -->
          <div
            v-for="card in suggestions[column.key]"
            :key="card.id"
            draggable="true"
            class="bg-surface-container-lowest border-2 border-primary rounded-lg transition-all cursor-grab active:cursor-grabbing hover:-rotate-1 p-4 group"
            :class="{ 'opacity-50': draggedCard?.id === card.id, 'bg-surface-container opacity-60': column.key === 'Implemented' }"
            @dragstart="handleDragStart(card, column.key)"
            @dragend="handleDragEnd"
            @click="openSuggestionDetail(card)"
          >
            <div class="flex justify-between items-start mb-3">
              <span class="bg-secondary-container text-on-secondary-container font-label-md text-xs px-2 py-0.5 rounded-full uppercase tracking-tighter">
                {{ card.category }}
              </span>
              <span v-if="column.key === 'Implemented'" class="material-symbols-outlined text-on-tertiary-fixed-variant text-lg" style="font-variation-settings: 'FILL' 1;">check_circle</span>
              <span v-else-if="column.key === 'Approved'" class="material-symbols-outlined text-on-tertiary-fixed-variant text-lg">check_circle</span>
            </div>
            <h4 :class="{ 'line-through': column.key === 'Implemented' }" class="font-label-md text-label-md text-primary mb-3 line-clamp-2">{{ card.title }}</h4>
            <div class="flex items-center gap-2 pt-3 border-t border-outline-variant/30">
              <img v-if="card.user?.profile_picture" :alt="card.user.name" class="w-5 h-5 rounded-full border border-primary object-cover" :src="card.user.profile_picture" />
              <div v-else class="w-5 h-5 rounded-full border border-primary bg-secondary-container flex items-center justify-center text-xs font-bold text-on-secondary-container">
                {{ card.user?.name?.charAt(0) || '?' }}
              </div>
              <span class="font-label-md text-xs text-on-surface-variant truncate">{{ card.user?.name || 'Unknown' }}</span>
            </div>
          </div>

          <!-- Empty State -->
          <button v-if="suggestions[column.key].length === 0" class="w-full border-2 border-dashed border-outline-variant p-4 rounded-lg font-label-md text-xs text-on-surface-variant hover:border-primary hover:text-primary transition-all flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-lg">add</span>
            Add
          </button>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-for="i in 4" :key="i" class="flex flex-col gap-3">
        <div class="h-8 bg-surface-container-high rounded animate-pulse"></div>
        <div class="space-y-2">
          <div v-for="j in 3" :key="j" class="h-32 bg-surface-container-low rounded-lg border-2 border-primary animate-pulse"></div>
        </div>
      </div>
    </div>
    </AdminLayout>

    <!-- Suggestion Detail Modal -->
    <SuggestionDetailModal
      v-model="showModal"
      :suggestion="selectedSuggestion"
      @updated="handleSuggestionUpdated"
    />
  </div>
</template>
