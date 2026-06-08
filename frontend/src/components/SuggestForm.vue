<script setup>
import { ref, reactive } from 'vue'
import axios from 'axios'

const emit = defineEmits(['submitted'])

const form = reactive({
  title: '',
  category: '',
  description: '',
})

const errors = reactive({
  title: '',
  category: '',
  description: '',
})

const submitting = ref(false)
const apiError = ref('')
const success = ref(false)

function validate() {
  let valid = true
  errors.title = ''
  errors.category = ''
  errors.description = ''

  if (!form.title.trim() || form.title.trim().length < 5) {
    errors.title = 'Title must be at least 5 characters'
    valid = false
  }
  if (!form.category) {
    errors.category = 'Please select a category'
    valid = false
  }
  if (!form.description.trim() || form.description.trim().length < 20) {
    errors.description = 'Description must be at least 20 characters'
    valid = false
  }
  return valid
}

async function handleSubmit() {
  apiError.value = ''
  success.value = false

  if (!validate()) return

  submitting.value = true
  try {
    const res = await axios.post('/api/v1/suggestions', {
      title: form.title.trim(),
      category: form.category,
      description: form.description.trim(),
    })
    success.value = true
    form.title = ''
    form.category = ''
    form.description = ''
    emit('submitted', res.data.data)
    setTimeout(() => { success.value = false }, 3000)
  } catch (e) {
    if (e.response?.data?.errors) {
      const serverErrors = e.response.data.errors
      if (serverErrors.title) errors.title = serverErrors.title[0]
      if (serverErrors.category) errors.category = serverErrors.category[0]
      if (serverErrors.description) errors.description = serverErrors.description[0]
    } else {
      apiError.value = e.response?.data?.message || 'Something went wrong. Please try again.'
    }
  } finally {
    submitting.value = false
  }
}

function resetForm() {
  form.title = ''
  form.category = ''
  form.description = ''
  errors.title = ''
  errors.category = ''
  errors.description = ''
  apiError.value = ''
  success.value = false
}
</script>

<template>
  <div class="bg-surface-container-lowest p-6 rounded-lg border-2 border-primary">
    <div v-if="apiError" class="mb-4 bg-error-container text-on-error-container rounded-full px-5 py-3 text-sm font-medium">
      {{ apiError }}
    </div>

    <div v-if="success" class="mb-4 bg-tertiary-fixed text-primary rounded-full px-5 py-3 text-sm font-medium flex items-center gap-2">
      <span class="material-symbols-outlined text-[18px]">check_circle</span>
      Suggestion submitted successfully!
    </div>

    <form class="space-y-5" @submit.prevent="handleSubmit">
      <div>
        <label class="block text-sm font-medium text-primary mb-1.5" for="suggest-title">Title of your idea</label>
        <input
          id="suggest-title"
          v-model="form.title"
          class="w-full h-11 px-5 rounded-full border-2 bg-surface focus:ring-2 focus:ring-tertiary-fixed-dim focus:outline-none placeholder:opacity-50 transition-all text-sm"
          :class="errors.title ? 'border-error' : 'border-primary'"
          placeholder="Give your suggestion a catchy name..."
          type="text"
          :disabled="submitting"
        />
        <p v-if="errors.title" class="mt-1 text-xs text-on-error-container font-medium">{{ errors.title }}</p>
      </div>

      <div>
        <label class="block text-sm font-medium text-primary mb-1.5" for="suggest-category">Category</label>
        <div class="relative">
          <select
            id="suggest-category"
            v-model="form.category"
            class="w-full h-11 px-5 rounded-full border-2 bg-surface focus:ring-2 focus:ring-tertiary-fixed-dim focus:outline-none appearance-none text-sm"
            :class="errors.category ? 'border-error' : 'border-primary'"
            :disabled="submitting"
          >
            <option disabled value="">Select a category</option>
            <option value="Technology">Technology</option>
            <option value="Workplace">Workplace</option>
            <option value="Process Improvement">Process Improvement</option>
            <option value="Employee Welfare">Employee Welfare</option>
          </select>
          <span class="material-symbols-outlined absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]">expand_more</span>
        </div>
        <p v-if="errors.category" class="mt-1 text-xs text-on-error-container font-medium">{{ errors.category }}</p>
      </div>

      <div>
        <label class="block text-sm font-medium text-primary mb-1.5" for="suggest-description">Detailed Description</label>
        <textarea
          id="suggest-description"
          v-model="form.description"
          class="w-full p-4 rounded-lg border-2 bg-surface focus:ring-2 focus:ring-tertiary-fixed-dim focus:outline-none placeholder:opacity-50 transition-all text-sm resize-none"
          :class="errors.description ? 'border-error' : 'border-primary'"
          placeholder="Describe the problem, the solution, and the impact..."
          rows="5"
          :disabled="submitting"
        ></textarea>
        <p v-if="errors.description" class="mt-1 text-xs text-on-error-container font-medium">{{ errors.description }}</p>
      </div>

      <div class="flex items-center gap-3 pt-2">
        <button
          class="bg-primary-container text-primary-fixed px-6 py-3 rounded-full font-bold text-sm hover:opacity-90 active:scale-95 transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
          type="submit"
          :disabled="submitting"
        >
          <span v-if="submitting" class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
          <span v-else class="material-symbols-outlined text-[18px]">send</span>
          <span>{{ submitting ? 'Submitting...' : 'Submit Idea' }}</span>
        </button>
        <button
          class="text-primary text-sm font-medium border-2 border-primary px-6 py-3 rounded-full hover:bg-surface-container transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
          type="button"
          :disabled="submitting"
          @click="resetForm"
        >
          Reset
        </button>
      </div>
    </form>
  </div>
</template>
