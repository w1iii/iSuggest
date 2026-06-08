<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import logo from '@/assets/logo.png'

const router = useRouter()
const authStore = useAuthStore()

async function handleLogout() {
  try {
    await authStore.logout()
  } finally {
    router.push('/login')
  }
}
</script>

<template>
  <div class="bg-surface text-on-surface font-body-md min-h-screen">
    <!-- Sidebar -->
    <nav class="fixed left-0 top-20 h-[calc(100vh-80px)] flex flex-col p-4 border-r-2 border-primary bg-background hidden md:flex w-[200px] z-20">
      <div class="flex flex-col gap-1 flex-grow">
        <router-link class="flex items-center gap-2 p-2 rounded text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors text-sm font-medium active:scale-95" to="/dashboard">
          <span class="material-symbols-outlined text-[20px]">lightbulb</span>
          <span>Submit Suggestion</span>
        </router-link>
        <router-link class="flex items-center gap-2 p-2 rounded text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors text-sm font-medium active:scale-95" to="/my-submissions">
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
        <router-link to="/dashboard" class="flex items-center gap-3">
          <img :src="logo" alt="iSuggest Logo" class="w-10 h-10 object-contain" />
          <span class="font-headline-md text-headline-md font-bold text-primary">iSuggest</span>
        </router-link>
        <div class="flex items-center gap-4">
          <div class="hidden lg:flex items-center gap-3">
            <a class="text-sm text-secondary hover:text-tertiary-container transition-all" href="#">Settings</a>
            <a class="text-sm text-secondary hover:text-tertiary-container transition-all" href="#">Support</a>
          </div>
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary cursor-pointer text-[22px]">notifications</span>
            <router-link to="/profile">
              <img
                alt="User Profile"
                class="w-8 h-8 rounded-full border-2 border-primary object-cover cursor-pointer"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDyqql2psZptharucfNZGIrPwIypbnj2OVC6lr429hrbn7jFXNp1Pz_Bn9u3-SgQwrjbJxB_Ck9MjasSZAWPmVQ87nQsNHnZvF4cNE-BVkr_-Q85yABCmC_9ihHLBf5gOFRrVqaFwAZDau9aB66YIMSQfENzKUydkTKA_VDrez1agbWRCFMDewA_wOMi1IilAHtEs1ODlVHDXi5OmCaTOrNx8BXl-HDoa4zrMfUihTkAEPX2k8CbIX_RYT9mHnHodNgeJ31iEWOdao"
              />
            </router-link>
          </div>
        </div>
      </nav>
    </header>

    <!-- Main Content -->
    <main class="md:ml-[232px] md:mr-[32px] min-h-[calc(100vh-80px)] p-4 md:p-6 max-w-page mx-auto">
      <!-- Profile Header Section -->
      <section class="mb-12 relative">
        <div class="flex flex-col md:flex-row gap-8 items-start md:items-end relative z-10">
          <div class="relative group">
            <div class="absolute -top-4 -left-4 w-40 h-40 bg-tertiary-fixed-dim/30 rounded-full blur-2xl group-hover:bg-tertiary-fixed-dim/50 transition-all"></div>
            <img
              alt="Alex Rivera"
              class="w-36 h-36 md:w-48 md:h-48 rounded-lg border-2 border-primary object-cover relative z-10"
              src="https://lh3.googleusercontent.com/aida-public/AB6AXuBL3iWPRZZviRsIX-1wad6OKUAG-8Pg2zzn49UxO5arUGrFmuGEgXtXFfkm-jx_miOAColybQBav9oEbOb1swFd6tZdjg_nw-5H0FgmYBG51BOeIA-kr6w2r2TlR-zjqZFKrzYIhoj2kLYpuno-xIXlFJVK_uDgbSR9_AJjsO_bxyQehCBAP7cgeeOHJsfvsbBLSFhvUhbNuTWuQ5lmHhmCi1BU3-SA3Gilbz8GfIp_uGPMbOPBIe_C5Yj9QqgwyOEqSCLyeHgIce0"
            />
            <div class="absolute -bottom-2 -right-2 bg-tertiary-fixed text-primary px-4 py-1 rounded-full text-sm font-bold z-20 border-2 border-primary">
              Lead Designer
            </div>
          </div>
          <div class="flex-grow">
            <div class="relative inline-block">
              <h1 class="text-3xl md:text-5xl text-primary font-extrabold relative z-10">Alex Rivera</h1>
              <svg class="absolute -top-2 -left-4 w-[110%] h-[120%] pointer-events-none opacity-80" preserveAspectRatio="none" viewBox="0 0 100 100">
                <path d="M10,50 Q25,10 50,10 Q85,10 90,50 Q90,90 50,90 Q15,90 10,50" fill="none" stroke="#bbd145" stroke-linecap="round" stroke-width="2"></path>
              </svg>
            </div>
            <p class="text-xl md:text-2xl text-on-primary-container font-semibold mt-2">Product &amp; Experience Strategy</p>
          </div>
          <div class="flex gap-4">
            <button class="bg-secondary text-surface px-6 py-3 rounded-full text-sm font-medium hover:opacity-90 transition-all flex items-center gap-2 cursor-pointer">
              <span class="material-symbols-outlined text-[18px]">edit</span>
              Edit Profile
            </button>
          </div>
        </div>
      </section>

      <!-- Bento Grid Content -->
      <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        <!-- Bio Card -->
        <div class="md:col-span-8 bg-surface-container-lowest p-6 md:p-8 rounded-lg border-2 border-primary relative overflow-hidden group">
          <h3 class="text-xl font-bold text-primary mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined text-[22px]">history_edu</span>
            Professional Bio
          </h3>
          <p class="text-base text-on-surface leading-relaxed">
            Passionate about bridging the gap between raw data and human-centric design. Over the past 4 years at InnovationHub, I've focused on creating tools that empower artisans to scale their vision without losing the "bespoke" feel of their craft. I believe that every creative insight should be treated as a primary asset.
          </p>
          <div class="absolute bottom-4 right-4 text-tertiary-fixed opacity-40 transform rotate-12 group-hover:rotate-0 transition-transform">
            <span class="material-symbols-outlined text-5xl">trending_flat</span>
          </div>
        </div>

        <!-- Impact Stats Card -->
        <div class="md:col-span-4 bg-primary-container p-6 md:p-8 rounded-lg border-2 border-primary text-surface relative overflow-hidden">
          <h3 class="text-xs font-semibold uppercase tracking-widest text-on-primary-container mb-8">Personal Impact</h3>
          <div class="space-y-8">
            <div>
              <div class="flex items-baseline gap-2">
                <span class="text-5xl font-extrabold text-tertiary-fixed">24</span>
                <span class="text-tertiary-fixed-dim material-symbols-outlined text-2xl">auto_awesome</span>
              </div>
              <p class="text-xs font-semibold text-on-primary-container mt-1">Ideas Submitted</p>
            </div>
            <div class="relative">
              <div class="flex items-baseline gap-2">
                <span class="text-5xl font-extrabold text-tertiary-fixed">09</span>
                <span class="text-tertiary-fixed-dim material-symbols-outlined text-2xl">task_alt</span>
              </div>
              <p class="text-xs font-semibold text-on-primary-container mt-1">Successfully Implemented</p>
              <div class="absolute bottom-0 left-0 w-24 h-2 bg-tertiary-fixed-dim/20 -rotate-1"></div>
            </div>
          </div>
          <div class="absolute -bottom-10 -right-10 opacity-10">
            <svg fill="none" height="200" viewBox="0 0 200 200" width="200">
              <path d="M20 180C60 140 140 60 180 20" stroke="white" stroke-linecap="round" stroke-width="4"></path>
              <path d="M40 20C80 60 120 140 160 180" stroke="white" stroke-linecap="round" stroke-width="4"></path>
            </svg>
          </div>
        </div>

        <!-- Recent Contributions -->
        <div class="md:col-span-12 mt-2">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-primary">Recent Contributions</h3>
            <router-link class="text-primary text-sm font-semibold border-b-2 border-tertiary-fixed-dim hover:pb-1 transition-all" to="/my-submissions">View All Submissions</router-link>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Idea Card 1 -->
            <div class="bg-surface-container p-5 rounded-lg border-2 border-primary flex flex-col justify-between group cursor-pointer hover:bg-surface-container-high transition-colors">
              <div>
                <div class="flex justify-between items-start mb-4">
                  <span class="bg-secondary-container text-on-secondary-container px-3 py-1 rounded-full text-xs font-bold">UX STRATEGY</span>
                  <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary transition-colors text-[18px]">open_in_new</span>
                </div>
                <h4 class="text-sm font-bold text-primary mb-2">Automated Artisan Feedback Loops</h4>
                <p class="text-on-surface-variant text-sm leading-relaxed line-clamp-2">A proposal to integrate direct-to-studio messaging for real-time customer feedback.</p>
              </div>
              <div class="mt-5 flex items-center justify-between">
                <span class="text-xs text-outline font-medium">Aug 12, 2023</span>
                <span class="text-xs font-bold text-primary flex items-center gap-1">
                  <span class="material-symbols-outlined text-sm">check_circle</span>
                  IMPLEMENTED
                </span>
              </div>
            </div>
            <!-- Idea Card 2 -->
            <div class="bg-surface-container p-5 rounded-lg border-2 border-primary flex flex-col justify-between group cursor-pointer hover:bg-surface-container-high transition-colors">
              <div>
                <div class="flex justify-between items-start mb-4">
                  <span class="bg-tertiary-fixed-dim text-primary px-3 py-1 rounded-full text-xs font-bold">SUSTAINABILITY</span>
                  <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary transition-colors text-[18px]">open_in_new</span>
                </div>
                <h4 class="text-sm font-bold text-primary mb-2">Zero-Waste Packaging Initiative</h4>
                <p class="text-on-surface-variant text-sm leading-relaxed line-clamp-2">Redesigning the standard shipping kit to use 100% recycled workshop shavings.</p>
              </div>
              <div class="mt-5 flex items-center justify-between">
                <span class="text-xs text-outline font-medium">Sep 04, 2023</span>
                <span class="text-xs font-bold text-secondary flex items-center gap-1">
                  <span class="material-symbols-outlined text-sm">pending</span>
                  UNDER REVIEW
                </span>
              </div>
            </div>
            <!-- Idea Card 3 -->
            <div class="bg-surface-container p-5 rounded-lg border-2 border-primary flex flex-col justify-between group cursor-pointer hover:bg-surface-container-high transition-colors">
              <div>
                <div class="flex justify-between items-start mb-4">
                  <span class="bg-secondary-container text-on-secondary-container px-3 py-1 rounded-full text-xs font-bold">LOGISTICS</span>
                  <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary transition-colors text-[18px]">open_in_new</span>
                </div>
                <h4 class="text-sm font-bold text-primary mb-2">Artisan Rotation Program</h4>
                <p class="text-on-surface-variant text-sm leading-relaxed line-clamp-2">A scheme to allow office staff to spend 1 day a month in the physical studios.</p>
              </div>
              <div class="mt-5 flex items-center justify-between">
                <span class="text-xs text-outline font-medium">Oct 19, 2023</span>
                <span class="text-xs font-bold text-primary flex items-center gap-1">
                  <span class="material-symbols-outlined text-sm">check_circle</span>
                  IMPLEMENTED
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Mobile Bottom Nav -->
    <nav class="md:hidden fixed bottom-0 left-0 w-full bg-surface border-t-2 border-primary flex justify-around items-center py-3 z-50">
      <router-link class="flex flex-col items-center gap-1 text-secondary" to="/dashboard">
        <span class="material-symbols-outlined">dashboard</span>
        <span class="text-[10px] font-label-md">Home</span>
      </router-link>
      <router-link class="flex flex-col items-center gap-1 text-secondary" to="/dashboard">
        <span class="material-symbols-outlined">lightbulb</span>
        <span class="text-[10px] font-label-md">Suggest</span>
      </router-link>
      <router-link class="flex flex-col items-center gap-1 text-secondary" to="/my-submissions">
        <span class="material-symbols-outlined">list_alt</span>
        <span class="text-[10px] font-label-md">My Ideas</span>
      </router-link>
      <router-link class="flex flex-col items-center gap-1 text-primary font-bold" to="/profile">
        <span class="material-symbols-outlined">person</span>
        <span class="text-[10px] font-label-md">Profile</span>
      </router-link>
    </nav>
  </div>
</template>
