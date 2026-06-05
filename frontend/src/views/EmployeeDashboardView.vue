<script setup>
import { ref } from 'vue'

const title = ref('')
const category = ref('')
const description = ref('')

function handleSubmit() {
  // Static — no backend yet
}

function handleSaveDraft() {
  // Static — no backend yet
}
</script>

<template>
  <div class="bg-surface text-on-surface font-body-md min-h-screen">
    <!-- Sidebar -->
    <nav class="fixed left-0 top-[66px] h-[calc(100vh-66px)] flex flex-col p-4 border-r-2 border-primary bg-surface hidden md:flex w-[200px] z-20">
      <div class="flex flex-col gap-1 flex-grow">
        <a class="flex items-center gap-2 p-2 rounded text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors text-sm font-medium active:scale-95" href="#">
          <span class="material-symbols-outlined text-[20px]">lightbulb</span>
          <span>Submit Suggestion</span>
        </a>
        <a class="flex items-center gap-2 p-2 rounded text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors text-sm font-medium active:scale-95" href="#">
          <span class="material-symbols-outlined text-[20px]">list_alt</span>
          <span>My Submissions</span>
        </a>
      </div>
      <div class="mt-auto space-y-4">
        <button class="w-full bg-primary text-surface py-3 rounded-full text-sm font-medium active:scale-90 flex items-center justify-center gap-1 cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">logout</span>
          Logout
        </button>
      </div>
    </nav>

    <!-- Top Nav -->
    <header class="flex justify-between items-center px-6 py-3 sticky top-0 z-30 bg-surface/80 backdrop-blur-md border-b-2 border-primary w-full left-0">
      <div class="flex items-center">
        <span class="font-bold text-primary text-lg">InnovationHub</span>
      </div>
      <div class="flex items-center gap-4">
        <div class="hidden lg:flex items-center gap-3">
          <a class="text-sm text-secondary hover:text-tertiary-container transition-all" href="#">Settings</a>
          <a class="text-sm text-secondary hover:text-tertiary-container transition-all" href="#">Support</a>
        </div>
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-primary cursor-pointer text-[22px]">notifications</span>
          <img
            alt="User Profile"
            class="w-8 h-8 rounded-full border-2 border-primary object-cover"
            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDyqql2psZptharucfNZGIrPwIypbnj2OVC6lr429hrbn7jFXNp1Pz_Bn9u3-SgQwrjbJxB_Ck9MjasSZAWPmVQ87nQsNHnZvF4cNE-BVkr_-Q85yABCmC_9ihHLBf5gOFRrVqaFwAZDau9aB66YIMSQfENzKUydkTKA_VDrez1agbWRCFMDewA_wOMi1IilAHtEs1ODlVHDXi5OmCaTOrNx8BXl-HDoa4zrMfUihTkAEPX2k8CbIX_RYT9mHnHodNgeJ31iEWOdao"
          />
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="md:ml-[200px] min-h-[calc(100vh-66px)] p-4 md:p-6 max-w-page mx-auto">
      <!-- Hero -->
      <section class="mb-6">
        <h2 class="text-2xl md:text-3xl text-primary font-bold">
          Share your <span class="scribble-highlight">brilliance</span>.
        </h2>
        <p class="text-sm text-secondary max-w-2xl mt-1">
          Every great innovation starts with a simple thought. Use this space to shape the future of our artisanal workplace.
        </p>
      </section>

      <!-- Form & Illustration -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Form -->
        <div class="lg:col-span-7 bg-surface-container-lowest p-6 rounded-lg border-2 border-primary">
          <form class="space-y-5" @submit.prevent="handleSubmit">
            <div>
              <label class="block text-sm font-medium text-primary mb-1.5" for="title">Title of your idea</label>
              <input
                id="title"
                v-model="title"
                class="w-full h-11 px-5 rounded-full border-2 border-primary bg-surface focus:ring-2 focus:ring-tertiary-fixed-dim focus:outline-none placeholder:opacity-50 transition-all text-sm"
                placeholder="Give your suggestion a catchy name..."
                type="text"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-primary mb-1.5" for="category">Category</label>
              <div class="relative">
                <select
                  id="category"
                  v-model="category"
                  class="w-full h-11 px-5 rounded-full border-2 border-primary bg-surface focus:ring-2 focus:ring-tertiary-fixed-dim focus:outline-none appearance-none text-sm"
                >
                  <option disabled value="">Select a category</option>
                  <option value="technology">Technology</option>
                  <option value="workplace">Workplace</option>
                  <option value="process">Process Improvement</option>
                  <option value="welfare">Employee Welfare</option>
                </select>
                <span class="material-symbols-outlined absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-[20px]">expand_more</span>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-primary mb-1.5" for="description">Detailed Description</label>
              <textarea
                id="description"
                v-model="description"
                class="w-full p-4 rounded-lg border-2 border-primary bg-surface focus:ring-2 focus:ring-tertiary-fixed-dim focus:outline-none placeholder:opacity-50 transition-all text-sm resize-none"
                placeholder="Describe the problem, the solution, and the impact..."
                rows="5"
              ></textarea>
            </div>
            <div class="flex items-center gap-3 pt-2">
              <button class="bg-primary-container text-primary-fixed px-6 py-3 rounded-full font-bold text-sm hover:opacity-90 active:scale-95 transition-all flex items-center gap-2 cursor-pointer" type="submit">
                <span>Submit Idea</span>
                <span class="material-symbols-outlined text-[18px]">send</span>
              </button>
              <button class="text-primary text-sm font-medium border-2 border-primary px-6 py-3 rounded-full hover:bg-surface-container transition-all cursor-pointer" type="button" @click="handleSaveDraft">
                Save as Draft
              </button>
            </div>
          </form>
        </div>

        <!-- Illustration & sidebar -->
        <div class="lg:col-span-5 space-y-6">
          <div class="relative p-6 rounded-lg border-2 border-primary bg-secondary-container/30">
            <div class="mb-4">
              <img
                alt="Human centric creative concept"
                class="w-full aspect-square object-cover rounded-lg border-2 border-primary mix-blend-multiply opacity-80"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuB7kUUsOWyUg1-whmc5gQV8ovjh0B7MhqqXwgrX37dS0LNT33iMd9M3S6gRB55bgHVQDymsolO6-0vHBDVI_B-uAD7rh20R9xETkeGdQkh-4x-UYzFbEeykZ2dlJQ0KUH0aEMnHdnp3EqOL8eAM_l8UGcwge_To_fpomogtuXyKQY0T8MFGmv2HCacQ23UXV7WnwR8j2APiNRDALOjj3mhsTh711ZLUQDg3xjq0weC2hWW0TwtBTJUJ_KGDYuuYnuaOpZSe7EDhLio"
              />
            </div>
            <h3 class="text-lg font-bold text-primary mb-3 scribble-circle inline-block">Why your idea matters</h3>
            <p class="text-sm text-on-secondary-container leading-relaxed">
              At <span class="font-bold">InnovationHub</span>, we believe that the most powerful solutions come from the people who live the experience every day. Your perspective is the craftsmanship behind our progress.
            </p>
            <div class="mt-6 flex justify-end">
              <span class="material-symbols-outlined text-5xl text-tertiary-fixed-dim rotate-12" style="font-variation-settings: 'wght' 200;">subdirectory_arrow_left</span>
            </div>
          </div>

          <!-- Stats -->
          <div class="grid grid-cols-2 gap-3">
            <div class="p-4 bg-surface border-2 border-primary rounded-lg">
              <p class="text-2xl font-extrabold text-primary">84%</p>
              <p class="text-xs text-secondary">Implementation Rate</p>
            </div>
            <div class="p-4 bg-tertiary-fixed-dim border-2 border-primary rounded-lg">
              <p class="text-2xl font-extrabold text-primary">12</p>
              <p class="text-xs text-primary">Active Pilots</p>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Mobile Bottom Nav -->
    <nav class="md:hidden fixed bottom-0 left-0 w-full bg-surface border-t-2 border-primary flex justify-around items-center py-3 z-50">
      <a class="flex flex-col items-center gap-1 text-secondary" href="#">
        <span class="material-symbols-outlined">dashboard</span>
        <span class="text-[10px] font-label-md">Home</span>
      </a>
      <a class="flex flex-col items-center gap-1 text-primary font-bold" href="#">
        <span class="material-symbols-outlined">lightbulb</span>
        <span class="text-[10px] font-label-md">Suggest</span>
      </a>
      <a class="flex flex-col items-center gap-1 text-secondary" href="#">
        <span class="material-symbols-outlined">list_alt</span>
        <span class="text-[10px] font-label-md">My Ideas</span>
      </a>
      <a class="flex flex-col items-center gap-1 text-secondary" href="#">
        <span class="material-symbols-outlined">person</span>
        <span class="text-[10px] font-label-md">Profile</span>
      </a>
    </nav>
  </div>
</template>
