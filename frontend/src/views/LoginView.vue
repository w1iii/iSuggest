<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const role = ref('employee')
const email = ref('')
const password = ref('')
const remember = ref(false)

function toggleRole(r) {
  role.value = r
}

function handleSubmit() {
  // Static — no backend yet
}

let onMouseMove

onMounted(() => {
  onMouseMove = (e) => {
    const scribbles = document.querySelectorAll('.parallax-scribble')
    const x = (window.innerWidth - e.pageX * 2) / 100
    const y = (window.innerHeight - e.pageY * 2) / 100
    scribbles.forEach(s => {
      s.style.transform = `translateX(${x}px) translateY(${y}px)`
    })
  }
  document.addEventListener('mousemove', onMouseMove)
})

onUnmounted(() => {
  document.removeEventListener('mousemove', onMouseMove)
})
</script>

<template>
  <div class="bg-background text-on-background font-body-md min-h-screen flex flex-col overflow-x-hidden">
    <header class="w-full top-0 sticky z-50 bg-background border-b-2 border-primary">
      <div class="flex justify-between items-center h-20 px-gutter max-w-page mx-auto">
        <a class="font-headline-md text-headline-md font-bold text-primary flex items-center gap-2" href="#">
          <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">hub</span>
          InnovationHub
        </a>
        <nav class="hidden md:flex gap-8">
          <a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors duration-200" href="#">About Us</a>
          <a class="font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors duration-200" href="#">Support</a>
        </nav>
      </div>
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
              class="w-full h-14 bg-primary-container text-background font-label-md text-label-md rounded-full hover:bg-primary transition-all duration-300 active:scale-[0.98] flex items-center justify-center gap-2 group cursor-pointer"
              type="submit"
            >
              Sign In to Dashboard
              <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
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
