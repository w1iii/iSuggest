<script setup>
import { ref } from 'vue'
import axios from 'axios'

const title = ref('')
const description = ref('')
const category = ref('')

const loading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const handleSubmit = async () => {
  loading.value = true
  successMessage.value = ''
  errorMessage.value = ''

  try {
    const token = localStorage.getItem('token')

    const response = await axios.post(
      'http://127.0.0.1:8000/api/v1/suggestions',
      {
        title: title.value,
        description: description.value,
        category: category.value
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: 'application/json'
        }
      }
    )

    successMessage.value = response.data.message

    title.value = ''
    description.value = ''
    category.value = ''
  } catch (error) {
    console.error(error)

    if (error.response) {
      errorMessage.value =
        error.response.data.message ||
        'Submission failed.'
    } else {
      errorMessage.value =
        'Cannot connect to server.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="max-w-2xl mx-auto p-6">

    <h1 class="text-3xl font-bold mb-6">
      Submit a Suggestion
    </h1>

    <form @submit.prevent="handleSubmit">

      <div class="mb-4">
        <label class="block mb-2">
          Title
        </label>

        <input
          v-model="title"
          type="text"
          class="w-full border rounded p-3"
          placeholder="Enter suggestion title"
          required
        />
      </div>

      <div class="mb-4">
        <label class="block mb-2">
          Category
        </label>

        <select
          v-model="category"
          class="w-full border rounded p-3"
          required
        >
          <option value="">Select Category</option>
          <option value="Operations">Operations</option>
          <option value="Safety">Safety</option>
          <option value="Customer Service">Customer Service</option>
          <option value="Technology">Technology</option>
        </select>
      </div>

      <div class="mb-4">
        <label class="block mb-2">
          Description
        </label>

        <textarea
          v-model="description"
          rows="6"
          class="w-full border rounded p-3"
          placeholder="Describe your suggestion"
          required
        ></textarea>
      </div>

      <button
        type="submit"
        :disabled="loading"
        class="bg-green-700 text-white px-6 py-3 rounded"
      >
        {{ loading ? 'Submitting...' : 'Submit Suggestion' }}
      </button>

    </form>

    <div
      v-if="successMessage"
      class="mt-4 text-green-600"
    >
      {{ successMessage }}
    </div>

    <div
      v-if="errorMessage"
      class="mt-4 text-red-600"
    >
      {{ errorMessage }}
    </div>

  </div>
</template>