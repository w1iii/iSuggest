<script setup>
import logo from '@/assets/logo.png'
import { ref, onMounted, onUnmounted } from 'vue'

const cursor = ref(null)

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

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('opacity-100')
        entry.target.classList.remove('translate-y-10')
      }
    })
  }, { threshold: 0.1 })

  document.querySelectorAll('section').forEach(section => {
    section.classList.add('transition-all', 'duration-1000', 'translate-y-10', 'opacity-0')
    observer.observe(section)
  })

  onUnmounted(() => {
    document.removeEventListener('mousemove', onMouseMove)
    interactiveEls.forEach(el => {
      el.removeEventListener('mouseenter', onEnter)
      el.removeEventListener('mouseleave', onLeave)
    })
    observer.disconnect()
  })
})
</script>

<template>
  <div class="bg-background text-on-background font-body-md selection:bg-tertiary-fixed min-h-screen">
    <div ref="cursor" class="custom-cursor hidden md:block"></div>

    <header class="w-full top-0 sticky z-50 bg-background border-b-2 border-primary">
      <nav class="flex justify-between items-center h-20 px-gutter max-w-page mx-auto">
        <router-link to="/" class="flex items-center gap-3">
  <img
    :src="logo"
    alt="iSuggest Logo"
    class="w-10 h-10 object-contain"
  />
      <span class="font-headline-md text-headline-md font-bold text-primary">
       iSuggest
      </span>
      </router-link>
        <div class="hidden md:flex space-x-8 items-center">
          <a class="text-on-surface-variant font-body-md text-body-md cursor-pointer hover:text-primary transition-colors duration-200 active:opacity-80" href="#features">Features</a>
          <a class="text-on-surface-variant font-body-md text-body-md cursor-pointer hover:text-primary transition-colors duration-200 active:opacity-80" href="#how-it-works">How It Works</a>
          <a class="text-on-surface-variant font-body-md text-body-md cursor-pointer hover:text-primary transition-colors duration-200 active:opacity-80" href="#about">About Us</a>
        </div>
        <router-link to="/login" class="bg-primary text-background px-8 py-3 rounded-full font-label-md text-label-md hover:bg-primary-container transition-all active:scale-95">
          Sign In
        </router-link>
      </nav>
    </header>

    <main>
      <section class="relative py-section-padding-mobile md:py-section-padding-desktop overflow-hidden">
        <div class="max-w-page mx-auto px-gutter grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
          <div class="lg:col-span-7 z-10">
            <h1 class="font-display-lg-mobile md:font-display-lg text-display-lg-mobile md:text-display-lg text-primary mb-8">
              The future is built on <span class="scribble-highlight">iSuggest</span>.
            </h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant mb-12 max-w-2xl">
              "The future of workplace engagement begins with iSuggest. We connect innovative thinking with strategic action, transforming everyday suggestions into opportunities for continuous improvement."
            </p>
            <div class="flex flex-wrap gap-4">
              <router-link to="/login" class="bg-primary text-background px-10 py-4 rounded-full font-headline-md text-body-lg hover:scale-105 transition-transform active:opacity-90">
                Get Started
              </router-link>
      
            </div>
          </div>
          <div class="lg:col-span-5 relative">
            <div class="relative w-full aspect-square bg-surface-container-high rounded overflow-hidden border-2 border-primary rotate-2 hover:rotate-0 transition-transform duration-500">
              <img
                alt="Innovation process"
                class="w-full h-full object-cover"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDkfAHVHYaws3hohlApj_klTsXcMdNiHlDTsoW096uYpbYWUvlxIEn0lB3ylPSKxXtU4vFH_m4T-gyEu0Vzu8LXE9jvKdwuhhQ2xzB0NOkZyg5p3FQIXrj4SOMeJnfA0vC_zaCKxtVThkNozy71eUagYGeb1QOAjCVTwFH5huEeEiNdsbzZAzjaUV__wC-6bgT1HKbpfdkE7cAZrmbdidM2ojENSuBsmMiOutxV3XKwwHZPcj9DFZ9SIjEmXwbaBCX3kyEHb1gO19o"
              />
            </div>
            <div class="absolute -top-12 -right-12 w-32 h-32 opacity-20 pointer-events-none">
              <svg class="w-full h-full text-primary" viewBox="0 0 100 100">
                <path d="M10,50 Q25,10 50,50 T90,50" fill="none" stroke="currentColor" stroke-width="2" />
              </svg>
            </div>
          </div>
        </div>
      </section>

      <section id="features" class="bg-surface-container-lowest py-section-padding-mobile md:py-section-padding-desktop">
        <div class="max-w-page mx-auto px-gutter">
          <div class="text-center mb-16">
            <h2 class="font-headline-lg text-headline-lg text-primary mb-4">Choose Your Path</h2>
            <div class="w-24 h-1 bg-tertiary-fixed mx-auto"></div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="group relative bg-background border-2 border-primary p-12 rounded hover:bg-primary-fixed transition-colors duration-300">
              <div class="mb-8">
                <span class="material-symbols-outlined text-[48px] text-primary">lightbulb</span>
              </div>
              <h3 class="font-headline-md text-headline-md text-primary mb-4">For Employees</h3>
              <p class="font-body-md text-body-md text-on-surface-variant mb-8">
                Turn your "what-ifs" into reality. Submit ideas, collaborate with peers, and watch your creative vision transform into tangible impact.
              </p>
              <a class="inline-flex items-center gap-2 font-bold text-primary hover:gap-4 transition-all" href="#">
                Submit an Idea
                <span class="material-symbols-outlined">arrow_forward</span>
              </a>
              <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                <svg fill="none" height="40" viewBox="0 0 40 40" width="40">
                  <path d="M5 20 C 5 5, 35 5, 35 20 C 35 35, 5 35, 5 20" stroke="#06160f" stroke-dasharray="4 4" stroke-width="2" />
                </svg>
              </div>
            </div>
            <div class="group relative bg-secondary-container border-2 border-primary p-12 rounded hover:bg-secondary-fixed transition-colors duration-300">
              <div class="mb-8">
                <span class="material-symbols-outlined text-[48px] text-primary" style="font-variation-settings: 'FILL' 1;">dashboard_customize</span>
              </div>
              <h3 class="font-headline-md text-headline-md text-primary mb-4">For Administrators</h3>
              <p class="font-body-md text-body-md text-on-surface-variant mb-8">
                Manage the iSuggest pipeline with precision. Access deep insights, track project health, and nurture the organization's growth.
              </p>
              <a class="inline-flex items-center gap-2 font-bold text-primary hover:gap-4 transition-all" href="#">
                Access Dashboard
                <span class="material-symbols-outlined">arrow_forward</span>
              </a>
            </div>
          </div>
        </div>
      </section>

      <section id="how-it-works" class="py-section-padding-mobile md:py-section-padding-desktop">
        <div class="max-w-page mx-auto px-gutter">
          <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <div class="md:col-span-8 bg-surface-container border-2 border-primary rounded p-8 relative overflow-hidden">
              <div class="flex flex-col h-full justify-between">
                <div>
                  <h4 class="font-headline-md text-headline-md text-primary mb-4">The iSuggest Lifecycle</h4>
                  <p class="font-body-md text-body-md text-on-surface-variant max-w-md">
                    Our platform tracks every stage of growth—from a raw scribble to a polished, professional deployment.
                  </p>
                </div>
                <div class="mt-8 flex gap-4">
                  <div class="bg-background border-2 border-primary px-4 py-2 rounded-full text-label-md">Phase 1: Spark</div>
                  <div class="bg-background border-2 border-primary px-4 py-2 rounded-full text-label-md">Phase 2: Refine</div>
                  <div class="bg-background border-2 border-primary px-4 py-2 rounded-full text-label-md">Phase 3: Launch</div>
                </div>
              </div>
              <span class="material-symbols-outlined absolute -bottom-8 -right-8 text-[160px] opacity-5 pointer-events-none">eco</span>
            </div>
            <div class="md:col-span-4 bg-primary text-background rounded p-8 flex flex-col justify-center items-center text-center">
              <div class="text-display-lg-mobile font-display-lg text-tertiary-fixed mb-2">92%</div>
              <div class="font-label-md text-label-md uppercase tracking-widest text-primary-fixed">Success Rate</div>
              <p class="mt-4 text-primary-fixed-dim text-body-md">On projects graduating from Hub to production.</p>
            </div>
            <div class="md:col-span-4 aspect-square rounded border-2 border-primary overflow-hidden">
              <img
                class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-700"
                alt="Professionals collaborating"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBaKY1zgBmX45a5mYwiAwAi6CFiEzH8OKAnaexRyncXd86TkBBtuR1_KYbzDfjN-IWGYJtblPlhNiU-947u2aYHR9zgAsaba4JB9IYZ8HmoQSzKUmDCsLJPOyGeIxxsYKvfqgYMxZNtGDrDUpcKF0fzvwkMjbKtlM3-gTYgby_VG2LPLunYIru7SPefhdbBqHI7Jql0YdcS8FnfH0pEdeIF6xWebclgsEMpx0ItJGEQ2nZ6mJIdf5cNDE3i-EGrFgOECB5ncnO0ACk"
              />
            </div>
            <div class="md:col-span-8 bg-tertiary-fixed border-2 border-primary rounded p-8 flex items-center justify-between">
              <div>
                <h4 class="font-headline-md text-headline-md text-primary">Human-Centric Insights</h4>
                <p class="text-primary opacity-80 mt-2">Data is just numbers until a human tells its story.</p>
              </div>
              <button class="bg-primary text-background w-16 h-16 rounded-full flex items-center justify-center hover:scale-110 transition-transform">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
              </button>
            </div>
          </div>
        </div>
      </section>
      <section id="about" class="bg-surface-container-lowest py-section-padding-mobile md:py-section-padding-desktop">
        <div class="max-w-page mx-auto px-gutter">
          <div class="text-center mb-16">
            <h2 class="font-headline-lg text-headline-lg text-primary mb-4">About iSuggest</h2>
            <div class="w-24 h-1 bg-tertiary-fixed mx-auto"></div>
          </div>
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-16">
            <div class="relative">
              <div class="w-full aspect-square bg-surface-container-high rounded overflow-hidden border-2 border-primary -rotate-1 hover:rotate-0 transition-transform duration-500">
                  <img
                    alt="Team collaboration"
                    class="w-full h-full object-cover"
                    src="https://images.pexels.com/photos/3183183/pexels-photo-3183183.jpeg"
                  />
              </div>
              <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-primary/10 rounded-full blur-2xl pointer-events-none"></div>
            </div>
            <div class="space-y-6">
              <h3 class="font-headline-md text-headline-md text-primary">Our Story</h3>
              <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
                iSuggest was born from a simple belief: the best ideas come from the people on the ground. 
                We built this platform to bridge the gap between frontline employees and decision-makers, 
                transforming workplace suggestions into actionable innovations.
              </p>
              <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
                Every feature is designed to amplify your voice, streamline feedback, and create a culture 
                where every suggestion matters — regardless of role or rank.
              </p>
              <div class="grid grid-cols-3 gap-4 pt-4">
                <div class="text-center">
                  <div class="text-display-lg-mobile font-bold text-primary">500+</div>
                  <div class="text-label-md text-on-surface-variant">Ideas Submitted</div>
                </div>
                <div class="text-center">
                  <div class="text-display-lg-mobile font-bold text-primary">84%</div>
                  <div class="text-label-md text-on-surface-variant">Implementation</div>
                </div>
                <div class="text-center">
                  <div class="text-display-lg-mobile font-bold text-primary">1K+</div>
                  <div class="text-label-md text-on-surface-variant">Active Users</div>
                </div>
              </div>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-background border-2 border-primary rounded-lg p-8 text-center hover:bg-primary-fixed transition-colors duration-300">
              <span class="material-symbols-outlined text-[40px] text-primary mb-4">visibility</span>
              <h4 class="font-headline-md text-headline-md text-primary mb-2">Our Mission</h4>
              <p class="font-body-md text-body-md text-on-surface-variant">Empower every employee to shape the future of their workplace through transparent, impactful idea sharing.</p>
            </div>
            <div class="bg-background border-2 border-primary rounded-lg p-8 text-center hover:bg-primary-fixed transition-colors duration-300">
              <span class="material-symbols-outlined text-[40px] text-primary mb-4">favorite</span>
              <h4 class="font-headline-md text-headline-md text-primary mb-2">Our Values</h4>
              <p class="font-body-md text-body-md text-on-surface-variant">Inclusion, transparency, and continuous improvement drive everything we build and believe in.</p>
            </div>
            <div class="bg-background border-2 border-primary rounded-lg p-8 text-center hover:bg-primary-fixed transition-colors duration-300">
              <span class="material-symbols-outlined text-[40px] text-primary mb-4">groups</span>
              <h4 class="font-headline-md text-headline-md text-primary mb-2">Our People</h4>
              <p class="font-body-md text-body-md text-on-surface-variant">A diverse team of engineers, designers, and workplace advocates committed to human-centric innovation.</p>
            </div>
          </div>
        </div>
      </section>
    </main>

    <footer class="w-full bg-surface-container border-t-2 border-primary">
      <div class="flex flex-col md:flex-row justify-between items-center py-section-padding-mobile px-gutter max-w-page mx-auto gap-8">
        <div class="flex flex-col items-center md:items-start gap-4">
          <div class="font-headline-md text-headline-md font-bold text-primary">iSuggest</div>
          <p class="font-label-md text-label-md text-on-surface-variant max-w-xs text-center md:text-left">
            © 2026 iSuggest. Handcrafted for Human Progress.
          </p>
        </div>
        <div class="flex flex-wrap justify-center gap-8">
          <a class="text-on-surface-variant font-label-md text-label-md transition-all duration-300 hover:underline decoration-tertiary-container decoration-4" href="#">Privacy Policy</a>
          <a class="text-on-surface-variant font-label-md text-label-md transition-all duration-300 hover:underline decoration-tertiary-container decoration-4" href="#">Terms of Service</a>
          <a class="text-on-surface-variant font-label-md text-label-md transition-all duration-300 hover:underline decoration-tertiary-container decoration-4" href="#">Contact Support</a>
        </div>
        <div class="flex gap-4">
          <div class="w-10 h-10 rounded-full border-2 border-primary flex items-center justify-center hover:bg-tertiary-fixed transition-colors cursor-pointer">
            <span class="material-symbols-outlined text-[20px]">share</span>
          </div>
          <div class="w-10 h-10 rounded-full border-2 border-primary flex items-center justify-center hover:bg-tertiary-fixed transition-colors cursor-pointer">
            <span class="material-symbols-outlined text-[20px]">alternate_email</span>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>
