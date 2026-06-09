<script setup>
import logo from '@/assets/logo.png'
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const cursor = ref(null)
const role = ref('employee')
const email = ref('')
const password = ref('')
const remember = ref(false)
const error = ref('')
const loading = ref(false)

function toggleRole(r) {
  role.value = r
}

async function handleSubmit() {
  error.value = ''
  loading.value = true
  try {
    await authStore.login({ email: email.value, password: password.value, role: role.value })
    if (authStore.isAdmin) {
      router.push('/admin/dashboard')
    } else {
      router.push('/dashboard')
    }
  } catch (err) {
    const errors = err.response?.data?.errors
    error.value = errors ? Object.values(errors).flat()[0] : (err.response?.data?.message || 'Login failed. Please check your credentials.')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  const cursorEl = cursor.value
  if (!cursorEl) return

  const onMouseMove = (e) => {
    cursorEl.style.left = e.clientX + 'px'
    cursorEl.style.top = e.clientY + 'px'
  }
  document.addEventListener('mousemove', onMouseMove)

  const interactiveEls = document.querySelectorAll('a, button')
  const onEnter = () => {
    cursorEl.style.transform = 'scale(2.5)'
    cursorEl.style.backgroundColor = 'rgba(6, 22, 15, 0.1)'
  }
  const onLeave = () => {
    cursorEl.style.transform = 'scale(1)'
    cursorEl.style.backgroundColor = 'transparent'
  }
  interactiveEls.forEach(el => {
    el.addEventListener('mouseenter', onEnter)
    el.addEventListener('mouseleave', onLeave)
  })

  onUnmounted(() => {
    document.removeEventListener('mousemove', onMouseMove)
    interactiveEls.forEach(el => {
      el.removeEventListener('mouseenter', onEnter)
      el.removeEventListener('mouseleave', onLeave)
    })
  })
})
</script>

<template>
  <div class="bg-background text-on-background font-body-md selection:bg-tertiary-fixed min-h-screen flex flex-col overflow-x-hidden">
    <div ref="cursor" class="custom-cursor hidden md:block"></div>
    <header class="w-full top-0 sticky z-50 bg-background border-b-2 border-primary">
      <nav class="flex justify-between items-center h-20 px-gutter max-w-page mx-auto">
        <router-link to="/" class="flex items-center gap-3">
          <img :src="logo" alt="iSuggest Logo" class="w-10 h-10 object-contain" />
          <span class="font-headline-md text-headline-md font-bold text-primary">iSuggest</span>
        </router-link>
        <div class="hidden md:flex space-x-8 items-center">
          <a class="text-on-surface-variant font-body-md text-body-md cursor-pointer hover:text-primary transition-colors duration-200 active:opacity-80" href="/#features">Features</a>
          <a class="text-on-surface-variant font-body-md text-body-md cursor-pointer hover:text-primary transition-colors duration-200 active:opacity-80" href="/#how-it-works">How It Works</a>
          <a class="text-on-surface-variant font-body-md text-body-md cursor-pointer hover:text-primary transition-colors duration-200 active:opacity-80" href="/#about">About Us</a>
        </div>
        <router-link to="/login" class="bg-primary text-background px-8 py-3 rounded-full font-label-md text-label-md hover:bg-primary-container transition-all active:scale-95">Sign In</router-link>
      </nav>
    </header>

    <main class="flex-grow flex items-center justify-center px-gutter py-section-padding-mobile md:py-section-padding-desktop">
      <div class="w-full max-w-md">
        <div class="text-center mb-10 relative">
          <h1 class="font-headline-lg text-headline-lg-mobile md:text-headline-lg text-primary mb-4">
            Welcome <span class="relative inline-block"><span class="scribble-underline">Back</span></span>
          </h1>
          <p class="font-body-lg text-body-lg text-on-surface-variant">Enter your credentials to access the hub.</p>
          <div class="absolute -top-6 -right-4 hidden md:block opacity-60 parallax-scribble">
            <svg fill="none" height="60" viewBox="0 0 60 60" width="60" xmlns="http://www.w3.org/2000/svg">
              <path d="M10 10C25 5 45 15 50 30C55 45 35 55 20 50C5 45 15 25 30 20" stroke="#d7ee5f" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
              <path d="M45 40L50 30L40 25" stroke="#d7ee5f" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
            </svg>
          </div>
        </div>

        <div class="bg-surface-container-lowest border-2 border-primary rounded-lg p-8 md:p-10">
          <div class="relative flex bg-surface-container rounded-full p-1 mb-8">
            <div
              class="absolute top-1 left-1 w-[calc(50%-4px)] h-[calc(100%-8px)] bg-primary-container rounded-full transition-transform duration-300"
              :style="{ transform: role === 'admin' ? 'translateX(100%)' : 'translateX(0%)' }"
            ></div>
            <button
              class="relative z-10 flex-1 py-3 text-label-md font-label-md transition-colors duration-300"
              :class="role === 'employee' ? 'text-on-primary' : 'text-on-surface-variant'"
              @click="toggleRole('employee')"
            >
              Employee Portal
            </button>
            <button
              class="relative z-10 flex-1 py-3 text-label-md font-label-md transition-colors duration-300"
              :class="role === 'admin' ? 'text-on-primary' : 'text-on-surface-variant'"
              @click="toggleRole('admin')"
            >
              Admin Portal
            </button>
          </div>

          <div v-if="error" class="bg-error-container text-on-error-container px-4 py-3 rounded-full text-label-md font-label-md mb-4 text-center">
            {{ error }}
          </div>
          <form class="space-y-6" @submit.prevent="handleSubmit">
            <div class="space-y-2">
              <label class="font-label-md text-label-md text-primary ml-2" for="email">Email Address</label>
              <input
                id="email"
                v-model="email"
                class="w-full h-14 px-6 rounded-full border-2 border-primary bg-background focus:ring-2 focus:ring-tertiary-fixed outline-none transition-all placeholder:opacity-50 text-primary font-body-md"
                placeholder="name@innovationhub.com"
                type="email"
              />
            </div>

            <div class="space-y-2">
              <div class="flex justify-between items-center ml-2">
                <label class="font-label-md text-label-md text-primary" for="password">Password</label>
                <a class="text-label-md font-label-md text-secondary hover:underline" href="#">Forgot?</a>
              </div>
              <div class="relative">
                <input
                  id="password"
                  v-model="password"
                  class="w-full h-14 px-6 rounded-full border-2 border-primary bg-background focus:ring-2 focus:ring-tertiary-fixed outline-none transition-all placeholder:opacity-50 text-primary font-body-md"
                  placeholder="••••••••"
                  type="password"
                />
                <span class="material-symbols-outlined absolute right-6 top-1/2 -translate-y-1/2 text-on-surface-variant cursor-pointer">visibility</span>
              </div>
            </div>

            <div class="flex items-center gap-3 ml-2">
              <input
                id="remember"
                v-model="remember"
                class="w-5 h-5 rounded border-2 border-primary text-primary focus:ring-0 cursor-pointer"
                type="checkbox"
              />
              <label class="font-label-md text-label-md text-on-surface-variant cursor-pointer" for="remember">Keep me signed in</label>
            </div>

            <button
              class="w-full h-14 bg-primary-container text-background font-label-md text-label-md rounded-full hover:bg-primary transition-all duration-300 active:scale-[0.98] flex items-center justify-center gap-2 group cursor-pointer disabled:opacity-50"
              type="submit"
              :disabled="loading"
            >
              <template v-if="loading">
                <span class="inline-block w-5 h-5 border-2 border-background border-t-transparent rounded-full animate-spin"></span>
                Signing In...
              </template>
              <template v-else>
                Sign In to Dashboard
                <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
              </template>
            </button>
          </form>

          <div class="mt-8 pt-8 border-t border-outline-variant text-center">
            <p class="font-body-md text-body-md text-on-surface-variant">
              New to the hub? <a class="text-primary font-bold hover:underline" href="#">Contact HR</a>
            </p>
          </div>
        </div>
      </div>
    </main>

    <footer class="w-full bg-surface-container border-t-2 border-primary mt-auto">
      <div class="flex flex-col md:flex-row justify-between items-center py-10 px-gutter max-w-page mx-auto gap-6">
        <div class="flex items-center gap-2">
          <span class="font-headline-md text-headline-md font-bold text-primary">InnovationHub</span>
        </div>
        <p class="font-label-md text-label-md text-on-surface-variant">© 2024 InnovationHub. Handcrafted for Human Progress.</p>
        <div class="flex gap-6">
          <a class="font-label-md text-label-md text-on-surface-variant hover:underline decoration-tertiary-container decoration-4 transition-all duration-300" href="#">Privacy Policy</a>
          <a class="font-label-md text-label-md text-on-surface-variant hover:underline decoration-tertiary-container decoration-4 transition-all duration-300" href="#">Terms of Service</a>
        </div>
      </div>
    </footer>
  </div>
</template>
