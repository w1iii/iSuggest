<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'
import logo from '@/assets/logo.png'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const suggestion = ref(null)
const loading = ref(true)
const editing = ref(false)
const saving = ref(false)
const deleting = ref(false)
const showDeleteConfirm = ref(false)

const editForm = ref({
  title: '',
  category: '',
  description: '',
})

const statusMap = {
  Pending: { label: 'In Review', class: 'bg-secondary-container text-on-secondary-container' },
  Approved: { label: 'Approved', class: 'bg-tertiary-fixed text-primary' },
  Rejected: { label: 'Declined', class: 'bg-error-container text-on-error-container' },
  Implemented: { label: 'Implemented', class: 'bg-surface-container-high text-on-surface-variant' },
}

const categories = ['Workplace', 'Technology', 'Process Improvement', 'Employee Welfare']

function isActive(path) {
  return route.path === path
}

function formatDate(dateStr) {
  const d = new Date(dateStr)
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

async function fetchSuggestion() {
  try {
    const res = await axios.get(`/api/v1/suggestions/${route.params.id}`)
    suggestion.value = res.data.data
    editForm.value = {
      title: res.data.data.title,
      category: res.data.data.category,
      description: res.data.data.description,
    }
  } catch (e) {
    if (e.response?.status === 404) {
      router.push('/my-submissions')
    }
    console.error('Failed to fetch suggestion', e)
  } finally {
    loading.value = false
  }
}

function startEditing() {
  editForm.value = {
    title: suggestion.value.title,
    category: suggestion.value.category,
    description: suggestion.value.description,
  }
  editing.value = true
}

function cancelEditing() {
  editing.value = false
}

async function saveEdit() {
  saving.value = true
  try {
    const res = await axios.put(`/api/v1/suggestions/${route.params.id}`, {
      title: editForm.value.title.trim(),
      category: editForm.value.category,
      description: editForm.value.description.trim(),
    })
    suggestion.value = res.data.data
    editing.value = false
  } catch (e) {
    console.error('Failed to update suggestion', e)
  } finally {
    saving.value = false
  }
}

async function handleDelete() {
  deleting.value = true
  try {
    await axios.delete(`/api/v1/suggestions/${route.params.id}`)
    router.push('/my-submissions')
  } catch (e) {
    console.error('Failed to delete suggestion', e)
  } finally {
    deleting.value = false
  }
}

async function handleLogout() {
  try {
    await authStore.logout()
  } finally {
    router.push('/login')
  }
}

onMounted(fetchSuggestion)
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
      <!-- Back Link -->
      <div class="mb-4">
        <router-link to="/my-submissions" class="inline-flex items-center gap-1 text-primary text-sm font-semibold hover:underline decoration-tertiary-fixed-dim decoration-2 underline-offset-4">
          <span class="material-symbols-outlined text-sm">arrow_back</span>
          Back to Submissions
        </router-link>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="flex items-center justify-center py-24">
        <span class="material-symbols-outlined text-4xl text-primary animate-spin">progress_activity</span>
      </div>

      <!-- Detail Card -->
      <div v-else-if="suggestion" class="bg-surface-container-lowest border-2 border-primary rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="p-6 border-b-2 border-primary bg-surface-container-low flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <h2 class="text-xl md:text-2xl text-primary font-bold">Suggestion Details</h2>
            <span
              class="px-3 py-0.5 rounded-full text-xs font-semibold border border-primary"
              :class="statusMap[suggestion.status]?.class || 'bg-surface-container-high text-on-surface-variant'"
            >
              {{ statusMap[suggestion.status]?.label || suggestion.status }}
            </span>
          </div>
          <div v-if="!editing" class="flex items-center gap-2">
            <button
              @click="startEditing"
              class="inline-flex items-center gap-1 px-4 py-2 bg-tertiary-fixed text-primary font-semibold text-sm rounded-lg hover:opacity-90 transition-all cursor-pointer"
            >
              <span class="material-symbols-outlined text-sm">edit</span>
              Edit
            </button>
            <button
              @click="showDeleteConfirm = true"
              class="inline-flex items-center gap-1 px-4 py-2 bg-error text-on-error font-semibold text-sm rounded-lg hover:opacity-90 transition-all cursor-pointer"
            >
              <span class="material-symbols-outlined text-sm">delete</span>
              Delete
            </button>
          </div>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
          <!-- View Mode -->
          <template v-if="!editing">
            <div>
              <h3 class="text-xs text-secondary uppercase tracking-wider mb-1">Title</h3>
              <p class="text-lg font-bold text-primary">{{ suggestion.title }}</p>
            </div>

            <div>
              <h3 class="text-xs text-secondary uppercase tracking-wider mb-1">Description</h3>
              <p class="text-sm text-on-surface whitespace-pre-wrap">{{ suggestion.description }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <h3 class="text-xs text-secondary uppercase tracking-wider mb-1">Category</h3>
                <span class="inline-block bg-secondary-container text-on-secondary-container text-xs px-3 py-1 rounded-full font-semibold">
                  {{ suggestion.category }}
                </span>
              </div>
              <div>
                <h3 class="text-xs text-secondary uppercase tracking-wider mb-1">Submitted</h3>
                <p class="text-sm text-on-surface">{{ formatDate(suggestion.created_at) }}</p>
              </div>
            </div>

            <div v-if="suggestion.admin_remarks" class="border-t-2 border-outline-variant/30 pt-4">
              <h3 class="text-xs text-secondary uppercase tracking-wider mb-2">Admin Remarks</h3>
              <p class="text-sm text-on-surface bg-surface-container-lowest p-4 rounded-lg border border-outline-variant/30 whitespace-pre-wrap">{{ suggestion.admin_remarks }}</p>
            </div>
          </template>

          <!-- Edit Mode -->
          <template v-else>
            <div>
              <label class="block text-xs text-secondary uppercase tracking-wider mb-1.5">Title</label>
              <input
                v-model="editForm.title"
                class="w-full h-11 px-4 rounded-lg border-2 border-primary bg-surface focus:ring-2 focus:ring-tertiary-fixed-dim focus:outline-none text-sm"
                placeholder="Give your suggestion a catchy name..."
                :disabled="saving"
              />
            </div>

            <div>
              <label class="block text-xs text-secondary uppercase tracking-wider mb-1.5">Category</label>
              <div class="relative">
                <select
                  v-model="editForm.category"
                  class="w-full h-11 px-4 rounded-lg border-2 border-primary bg-surface focus:ring-2 focus:ring-tertiary-fixed-dim focus:outline-none appearance-none text-sm"
                  :disabled="saving"
                >
                  <option disabled value="">Select a category</option>
                  <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                </select>
                <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]">expand_more</span>
              </div>
            </div>

            <div>
              <label class="block text-xs text-secondary uppercase tracking-wider mb-1.5">Description</label>
              <textarea
                v-model="editForm.description"
                class="w-full p-4 rounded-lg border-2 border-primary bg-surface focus:ring-2 focus:ring-tertiary-fixed-dim focus:outline-none text-sm resize-none"
                placeholder="Describe the problem, the solution, and the impact..."
                rows="5"
                :disabled="saving"
              ></textarea>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t-2 border-outline-variant/30">
              <button
                @click="saveEdit"
                :disabled="saving"
                class="inline-flex items-center gap-1 px-6 py-2 bg-primary text-surface font-semibold text-sm rounded-lg hover:opacity-90 disabled:opacity-50 transition-all cursor-pointer"
              >
                <span v-if="saving" class="material-symbols-outlined text-sm animate-spin">progress_activity</span>
                <span v-else class="material-symbols-outlined text-sm">check</span>
                Save Changes
              </button>
              <button
                @click="cancelEditing"
                :disabled="saving"
                class="px-6 py-2 border-2 border-outline-variant text-on-surface-variant font-semibold text-sm rounded-lg hover:border-primary disabled:opacity-50 transition-colors cursor-pointer"
              >
                Cancel
              </button>
            </div>
          </template>
        </div>
      </div>
    </main>

    <!-- Delete Confirmation Dialog -->
    <div
      v-if="showDeleteConfirm"
      class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4 animate-fade-in"
      @click="showDeleteConfirm = false"
    >
      <div class="bg-surface rounded-lg border-2 border-error max-w-md w-full p-6" @click.stop>
        <h3 class="font-bold text-lg text-primary mb-2">Delete Suggestion</h3>
        <p class="text-sm text-secondary mb-6">Are you sure you want to delete this suggestion? This action cannot be undone.</p>
        <div class="flex gap-3 justify-end">
          <button
            @click="showDeleteConfirm = false"
            :disabled="deleting"
            class="px-4 py-2 border-2 border-outline-variant text-on-surface-variant font-semibold text-sm rounded-lg hover:border-primary disabled:opacity-50 transition-colors cursor-pointer"
          >
            Cancel
          </button>
          <button
            @click="handleDelete"
            :disabled="deleting"
            class="px-4 py-2 bg-error text-on-error font-semibold text-sm rounded-lg hover:opacity-90 disabled:opacity-50 transition-all cursor-pointer flex items-center gap-2"
          >
            <span v-if="deleting" class="material-symbols-outlined text-sm animate-spin">progress_activity</span>
            <span v-else class="material-symbols-outlined text-sm">delete</span>
            Delete
          </button>
        </div>
      </div>
    </div>

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

<style scoped>
@keyframes fade-in {
  from { opacity: 0; }
  to { opacity: 1; }
}

.animate-fade-in {
  animation: fade-in 0.2s ease-in;
}
</style>
