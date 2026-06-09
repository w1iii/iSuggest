<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import AdminLayout from '@/components/AdminLayout.vue'
import axios from 'axios'

const route = useRoute()

const employees = ref([])
const loading = ref(true)
const search = ref('')
const statusFilter = ref('')
const pagination = ref({ current_page: 1, last_page: 1, total: 0, from: 0, to: 0 })
const showModal = ref(false)
const form = ref({ name: '', email: '', password: '' })
const submitting = ref(false)
const error = ref('')
let debounceTimer = null

const statusOptions = [
  { value: '', label: 'All Status' },
  { value: 'active', label: 'Active' },
  { value: 'inactive', label: 'Inactive' },
]

function formatDate(dateStr) {
  const d = new Date(dateStr)
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

async function fetchEmployees(page = 1) {
  try {
    loading.value = true
    const params = { page, per_page: 15 }
    if (search.value) params.search = search.value
    if (statusFilter.value) params.status = statusFilter.value

    const res = await axios.get('/api/v1/admin/employees', { params })
    employees.value = res.data.data || []
    pagination.value = {
      current_page: res.data.current_page,
      last_page: res.data.last_page,
      total: res.data.total,
      from: res.data.from,
      to: res.data.to,
    }
  } catch (e) {
    console.error('Failed to load employees', e)
  } finally {
    loading.value = false
  }
}

function onSearchInput() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => fetchEmployees(), 300)
}

function changePage(page) {
  if (page < 1 || page > pagination.value.last_page) return
  fetchEmployees(page)
}

function openCreateModal() {
  form.value = { name: '', email: '', password: '' }
  error.value = ''
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  error.value = ''
}

async function handleCreate() {
  error.value = ''
  submitting.value = true
  try {
    await axios.post('/api/v1/admin/employees', form.value)
    closeModal()
    fetchEmployees()
  } catch (e) {
    error.value = e.response?.data?.message || e.message || 'Failed to create employee.'
  } finally {
    submitting.value = false
  }
}

function handleModalClick(e) {
  if (e.target === e.currentTarget) closeModal()
}

onMounted(() => fetchEmployees())

watch(() => statusFilter.value, () => fetchEmployees())
</script>

<template>
  <div>
    <AdminLayout>
      <section class="mb-8 pt-4">
        <div class="flex items-center justify-between mb-2">
          <h2 class="font-headline-md text-headline-md text-primary">
            Employees
          </h2>
          <button
            @click="openCreateModal"
            class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary text-surface rounded-full text-sm font-medium hover:opacity-90 active:scale-95 transition-all cursor-pointer"
          >
            <span class="material-symbols-outlined text-[18px]">add</span>
            Create Employee
          </button>
        </div>
        <p class="font-body-md text-body-md text-secondary max-w-2xl">
          Manage and view all registered employees.
        </p>
      </section>

      <!-- Search & Filter -->
      <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <div class="relative flex-1">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
          <input
            v-model="search"
            type="text"
            placeholder="Search by name or email..."
            class="w-full pl-10 pr-4 py-2.5 bg-surface-container-lowest border-2 border-primary rounded-lg text-sm text-primary placeholder:text-outline focus:outline-none focus:border-secondary-container transition-colors"
            @input="onSearchInput"
          />
        </div>
        <select
          v-model="statusFilter"
          class="px-4 py-2.5 bg-surface-container-lowest border-2 border-primary rounded-lg text-sm text-primary focus:outline-none focus:border-secondary-container transition-colors min-w-[140px]"
        >
          <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">
            {{ opt.label }}
          </option>
        </select>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="flex items-center justify-center py-24">
        <span class="material-symbols-outlined text-4xl text-primary animate-spin">progress_activity</span>
      </div>

      <!-- Empty -->
      <div v-else-if="employees.length === 0" class="bg-surface-container-lowest border-2 border-primary rounded-lg p-12 text-center">
        <span class="material-symbols-outlined text-5xl text-tertiary-fixed-dim mb-4">group_off</span>
        <h3 class="text-xl font-bold text-primary mb-2">No employees found</h3>
        <p class="text-sm text-secondary mb-6">Try adjusting your search or filter.</p>
      </div>

      <!-- Table -->
      <div v-else class="bg-surface-container-lowest border-2 border-primary rounded-lg overflow-hidden">
        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-outline-variant">
          <div
            v-for="emp in employees"
            :key="emp.id"
            class="p-4 space-y-2 hover:bg-surface-container-low transition-colors"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full border-2 border-primary bg-secondary-container flex items-center justify-center text-xs font-bold text-on-secondary-container shrink-0">
                  {{ emp.name?.charAt(0) || '?' }}
                </div>
                <span class="text-sm font-bold text-primary">{{ emp.name }}</span>
              </div>
              <span
                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold border shrink-0"
                :class="emp.status === 'active'
                  ? 'bg-tertiary-fixed text-primary border-primary'
                  : 'bg-error-container text-on-error-container border-error'"
              >
                <span class="w-1.5 h-1.5 rounded-full" :class="emp.status === 'active' ? 'bg-primary' : 'bg-on-error-container'"></span>
                {{ emp.status === 'active' ? 'Active' : 'Inactive' }}
              </span>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs text-secondary">
              <div>
                <span class="font-semibold text-on-surface-variant">Email:</span> {{ emp.email }}
              </div>
              <div>
                <span class="font-semibold text-on-surface-variant">Title:</span> {{ emp.title || '-' }}
              </div>
              <div>
                <span class="font-semibold text-on-surface-variant">Joined:</span> {{ formatDate(emp.created_at) }}
              </div>
            </div>
          </div>
        </div>
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-surface-container-high border-b-2 border-primary">
                <th class="p-4 text-sm font-semibold text-primary">Name</th>
                <th class="p-4 text-sm font-semibold text-primary">Email</th>
                <th class="p-4 text-sm font-semibold text-primary">Title</th>
                <th class="p-4 text-sm font-semibold text-primary">Status</th>
                <th class="p-4 text-sm font-semibold text-primary">Joined</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
              <tr v-for="emp in employees" :key="emp.id" class="group hover:bg-surface-container-low transition-colors">
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full border-2 border-primary bg-secondary-container flex items-center justify-center text-xs font-bold text-on-secondary-container">
                      {{ emp.name?.charAt(0) || '?' }}
                    </div>
                    <span class="text-sm font-bold text-primary">{{ emp.name }}</span>
                  </div>
                </td>
                <td class="p-4 text-sm text-secondary">{{ emp.email }}</td>
                <td class="p-4 text-sm text-secondary">{{ emp.title || '-' }}</td>
                <td class="p-4">
                  <span
                    class="inline-flex items-center gap-1 px-3 py-0.5 rounded-full text-xs font-semibold border"
                    :class="emp.status === 'active'
                      ? 'bg-tertiary-fixed text-primary border-primary'
                      : 'bg-error-container text-on-error-container border-error'"
                  >
                    <span class="w-1.5 h-1.5 rounded-full" :class="emp.status === 'active' ? 'bg-primary' : 'bg-on-error-container'"></span>
                    {{ emp.status === 'active' ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="p-4 text-sm text-secondary">{{ formatDate(emp.created_at) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 flex flex-col sm:flex-row items-center justify-between gap-3 border-t-2 border-primary bg-surface-container-low">
          <span class="text-sm font-semibold text-secondary">
            Showing {{ pagination.from }}-{{ pagination.to }} of {{ pagination.total }} employees
          </span>
          <div class="flex gap-2">
            <button
              class="h-9 w-9 border-2 border-primary rounded-full flex items-center justify-center hover:bg-tertiary-fixed transition-colors disabled:opacity-50"
              :disabled="pagination.current_page <= 1"
              @click="changePage(pagination.current_page - 1)"
            >
              <span class="material-symbols-outlined text-sm">chevron_left</span>
            </button>
            <button
              v-for="p in pagination.last_page"
              :key="p"
              class="h-9 w-9 border-2 border-primary rounded-full flex items-center justify-center text-sm font-semibold transition-colors"
              :class="p === pagination.current_page
                ? 'bg-primary text-on-primary'
                : 'hover:bg-tertiary-fixed'"
              @click="changePage(p)"
            >
              {{ p }}
            </button>
            <button
              class="h-9 w-9 border-2 border-primary rounded-full flex items-center justify-center hover:bg-tertiary-fixed transition-colors disabled:opacity-50"
              :disabled="pagination.current_page >= pagination.last_page"
              @click="changePage(pagination.current_page + 1)"
            >
              <span class="material-symbols-outlined text-sm">chevron_right</span>
            </button>
          </div>
        </div>
      </div>
    </AdminLayout>

    <!-- Create Employee Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4 animate-fade-in"
      @click="handleModalClick"
    >
      <div class="bg-surface rounded-lg border-2 border-primary max-w-md w-full" @click.stop>
        <div class="flex justify-between items-center p-6 border-b-2 border-primary bg-surface-container-low">
          <h2 class="font-headline-md text-headline-md text-primary">Create Employee</h2>
          <button
            @click="closeModal"
            class="material-symbols-outlined text-2xl text-on-surface-variant hover:text-primary transition-colors cursor-pointer"
          >
            close
          </button>
        </div>

        <form @submit.prevent="handleCreate" class="p-6 space-y-5">
          <div>
            <label class="font-label-md text-label-md text-secondary mb-1.5 block">Full Name</label>
            <input
              v-model="form.name"
              type="text"
              required
              placeholder="e.g. Jane Doe"
              class="w-full px-4 py-2.5 bg-surface-container-lowest border-2 border-primary rounded-lg text-sm text-primary placeholder:text-outline focus:outline-none focus:border-secondary-container transition-colors"
            />
          </div>

          <div>
            <label class="font-label-md text-label-md text-secondary mb-1.5 block">Email Address</label>
            <input
              v-model="form.email"
              type="email"
              required
              placeholder="e.g. jane@company.com"
              class="w-full px-4 py-2.5 bg-surface-container-lowest border-2 border-primary rounded-lg text-sm text-primary placeholder:text-outline focus:outline-none focus:border-secondary-container transition-colors"
            />
          </div>

          <div>
            <label class="font-label-md text-label-md text-secondary mb-1.5 block">Password</label>
            <input
              v-model="form.password"
              type="password"
              required
              minlength="8"
              placeholder="Min. 8 characters"
              class="w-full px-4 py-2.5 bg-surface-container-lowest border-2 border-primary rounded-lg text-sm text-primary placeholder:text-outline focus:outline-none focus:border-secondary-container transition-colors"
            />
          </div>

          <p v-if="error" class="text-sm text-on-error-container bg-error-container px-3 py-2 rounded-lg">{{ error }}</p>

          <div class="flex gap-3 pt-2">
            <button
              type="button"
              @click="closeModal"
              class="flex-1 px-4 py-2.5 border-2 border-outline-variant text-on-surface-variant font-label-md text-label-md rounded-lg hover:border-primary transition-colors cursor-pointer"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="submitting"
              class="flex-1 px-4 py-2.5 bg-primary text-surface font-label-md text-label-md rounded-lg hover:opacity-90 disabled:opacity-50 transition-all cursor-pointer inline-flex items-center justify-center gap-2"
            >
              <span v-if="submitting" class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
              {{ submitting ? 'Creating...' : 'Create Account' }}
            </button>
          </div>
        </form>
      </div>
    </div>
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
