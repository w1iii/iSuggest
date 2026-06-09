<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'

const props = defineProps({
  modelValue: Boolean,
  suggestion: Object,
})

const emit = defineEmits(['update:modelValue', 'updated'])

const isOpen = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val),
})

const loading = ref(false)
const editingRemarks = ref(false)
const remarks = ref(props.suggestion?.admin_remarks || '')
const newStatus = ref(props.suggestion?.status || '')

const statusOptions = ['Pending', 'In Review', 'Approved', 'Rejected', 'Implemented']

const statusColors = {
  Pending: 'bg-secondary-container text-on-secondary-container',
  'In Review': 'bg-secondary-container text-on-secondary-container',
  Approved: 'bg-tertiary-fixed text-primary',
  Rejected: 'bg-error text-on-error',
  Implemented: 'bg-tertiary-fixed-dim text-on-tertiary-fixed',
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const handleStatusChange = async (status) => {
  if (status === props.suggestion.status) return
  
  loading.value = true
  try {
    const res = await axios.patch(`/api/v1/admin/suggestions/${props.suggestion.id}/status`, {
      status,
      admin_remarks: remarks.value,
    })
    
    newStatus.value = status
    emit('updated', res.data.data)
  } catch (e) {
    console.error('Failed to update status', e)
  } finally {
    loading.value = false
  }
}

const handleSaveRemarks = async () => {
  loading.value = true
  try {
    const res = await axios.patch(`/api/v1/admin/suggestions/${props.suggestion.id}/status`, {
      status: props.suggestion.status,
      admin_remarks: remarks.value,
    })
    
    editingRemarks.value = false
    emit('updated', res.data.data)
  } catch (e) {
    console.error('Failed to save remarks', e)
  } finally {
    loading.value = false
  }
}

const closeModal = () => {
  isOpen.value = false
  editingRemarks.value = false
  remarks.value = props.suggestion?.admin_remarks || ''
}
</script>

<template>
  <!-- Modal Backdrop -->
  <div
    v-if="isOpen"
    class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4 animate-fade-in"
    @click="closeModal"
  >
    <!-- Modal -->
    <div
      class="bg-surface rounded-lg border-2 border-primary max-w-2xl w-full max-h-[90vh] overflow-y-auto"
      @click.stop
    >
      <!-- Header -->
      <div class="sticky top-0 flex justify-between items-center p-6 border-b-2 border-primary bg-surface-container-low">
        <h2 class="font-headline-md text-headline-md text-primary">Suggestion Details</h2>
        <button
          @click="closeModal"
          class="material-symbols-outlined text-2xl text-on-surface-variant hover:text-primary transition-colors cursor-pointer"
        >
          close
        </button>
      </div>

      <!-- Content -->
      <div class="p-6 space-y-6">
        <!-- Title -->
        <div>
          <h3 class="font-label-md text-label-md text-secondary mb-2">Title</h3>
          <p class="font-headline-md text-headline-md text-primary">{{ suggestion.title }}</p>
        </div>

        <!-- Description -->
        <div>
          <h3 class="font-label-md text-label-md text-secondary mb-2">Description</h3>
          <p class="font-body-md text-body-md text-on-surface whitespace-pre-wrap">{{ suggestion.description }}</p>
        </div>

        <!-- Meta Info Grid -->
        <div class="grid grid-cols-2 gap-4">
          <!-- Category -->
          <div>
            <h3 class="font-label-md text-label-md text-secondary mb-2">Category</h3>
            <span class="inline-block bg-secondary-container text-on-secondary-container font-label-md text-xs px-3 py-1 rounded-full uppercase tracking-tighter">
              {{ suggestion.category }}
            </span>
          </div>

          <!-- Status -->
          <div>
            <h3 class="font-label-md text-label-md text-secondary mb-2">Status</h3>
            <span :class="statusColors[suggestion.status]" class="inline-block font-label-md text-xs px-3 py-1 rounded-full uppercase tracking-tighter">
              {{ suggestion.status }}
            </span>
          </div>

          <!-- Created Date -->
          <div>
            <h3 class="font-label-md text-label-md text-secondary mb-2">Created</h3>
            <p class="font-body-md text-body-md text-on-surface">{{ formatDate(suggestion.created_at) }}</p>
          </div>

          <!-- Updated Date -->
          <div>
            <h3 class="font-label-md text-label-md text-secondary mb-2">Updated</h3>
            <p class="font-body-md text-body-md text-on-surface">{{ formatDate(suggestion.updated_at) }}</p>
          </div>
        </div>

        <!-- Creator Info -->
        <div class="border-t-2 border-outline-variant/30 pt-4">
          <h3 class="font-label-md text-label-md text-secondary mb-3">Submitted By</h3>
          <div class="flex items-center gap-3">
            <div v-if="suggestion.user?.profile_picture" class="w-12 h-12 rounded-full border-2 border-primary overflow-hidden">
              <img :src="suggestion.user.profile_picture" :alt="suggestion.user.name" class="w-full h-full object-cover" />
            </div>
            <div v-else class="w-12 h-12 rounded-full border-2 border-primary bg-secondary-container flex items-center justify-center font-bold text-on-secondary-container text-lg">
              {{ suggestion.user?.name?.charAt(0) || '?' }}
            </div>
            <div>
              <p class="font-label-md text-label-md text-primary">{{ suggestion.user?.name }}</p>
              <p class="font-body-sm text-body-sm text-on-surface-variant">{{ suggestion.user?.email }}</p>
            </div>
          </div>
        </div>

        <!-- Admin Remarks -->
        <div class="border-t-2 border-outline-variant/30 pt-4 bg-surface-container-lowest p-4 rounded-lg">
          <div class="flex justify-between items-start mb-3">
            <h3 class="font-label-md text-label-md text-secondary">Admin Remarks</h3>
            <button
              v-if="!editingRemarks"
              @click="editingRemarks = true"
              class="material-symbols-outlined text-sm text-tertiary-fixed hover:text-tertiary-container transition-colors cursor-pointer"
            >
              edit
            </button>
          </div>

          <div v-if="!editingRemarks && suggestion.admin_remarks" class="font-body-md text-body-md text-on-surface whitespace-pre-wrap">
            {{ suggestion.admin_remarks }}
          </div>
          <p v-else-if="!editingRemarks" class="font-body-md text-body-md text-on-surface-variant italic">
            No remarks yet.
          </p>

          <div v-if="editingRemarks" class="space-y-3">
            <textarea
              v-model="remarks"
              placeholder="Add admin remarks..."
              class="w-full p-3 border-2 border-primary rounded-lg bg-surface-container-lowest font-body-md text-body-md text-on-surface placeholder-on-surface-variant resize-none focus:outline-none focus:ring-2 focus:ring-tertiary-fixed"
              rows="4"
            ></textarea>
            <div class="flex gap-2">
              <button
                @click="handleSaveRemarks"
                :disabled="loading"
                class="px-4 py-2 bg-tertiary-fixed text-primary font-label-md text-label-md rounded-lg hover:bg-tertiary-container disabled:opacity-50 transition-colors cursor-pointer"
              >
                Save
              </button>
              <button
                @click="editingRemarks = false"
                :disabled="loading"
                class="px-4 py-2 border-2 border-outline-variant text-on-surface-variant font-label-md text-label-md rounded-lg hover:border-primary disabled:opacity-50 transition-colors cursor-pointer"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>

        <!-- Status Actions -->
        <div class="border-t-2 border-outline-variant/30 pt-4">
          <h3 class="font-label-md text-label-md text-secondary mb-3">Change Status</h3>
          <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
            <button
              v-for="status in statusOptions"
              :key="status"
              @click="handleStatusChange(status)"
              :disabled="loading || status === suggestion.status"
              :class="[
                statusColors[status],
                status === suggestion.status ? 'ring-2 ring-offset-2 ring-primary' : '',
                'px-3 py-2 rounded-lg font-label-md text-xs uppercase tracking-tighter transition-all disabled:opacity-50 cursor-pointer hover:scale-105'
              ]"
            >
              {{ status }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.animate-fade-in {
  animation: fade-in 0.2s ease-in;
}
</style>
