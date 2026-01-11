<template>
  <div>
    <div class="flex h-screen antialiased text-gray-900 bg-gray-100 overflow-hidden">
      
      <div
        v-if="loading"
        class="fixed inset-0 z-50 flex items-center justify-center text-2xl font-semibold text-white bg-indigo-800"
      >
        Loading.....
      </div>

      <Transition
        enter-active-class="transition-opacity duration-300 ease-in-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-300 ease-in-out"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-show="isSidebarOpen"
          @click="isSidebarOpen = false"
          class="fixed inset-0 z-10 bg-indigo-800 lg:hidden"
          style="opacity: 0.5"
          aria-hidden="true"
        ></div>
      </Transition>

      <Transition
        enter-active-class="transition-all transform duration-300 ease-in-out"
        enter-from-class="-translate-x-full opacity-0"
        enter-to-class="translate-x-0 opacity-100"
        leave-active-class="transition-all transform duration-300 ease-in-out"
        leave-from-class="translate-x-0 opacity-100"
        leave-to-class="-translate-x-full opacity-0"
      >
        <AppSidebar
          v-show="isSidebarOpen"
          @open-settings="navigateToSettings"
        />
      </Transition>

      <div class="fixed flex items-center space-x-4 top-5 right-10 lg:hidden z-20">
        <button
          @click="isSidebarOpen = true"
          class="p-1 text-indigo-400 transition-colors duration-200 rounded-md bg-indigo-50 hover:text-indigo-600 hover:bg-indigo-100 focus:outline-none focus:ring"
        >
          <span class="sr-only">Toggle main menu</span>
          <span aria-hidden="true">
            <svg
              v-if="!isSidebarOpen"
              class="w-8 h-8"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
            </svg>
            <svg
              v-else
              class="w-8 h-8"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </span>
        </button>
      </div>

      <main class="flex-1 w-full bg-gray-100 py-10 px-5 overflow-hidden">
        
        <div
          class="flex flex-col flex-1 h-full shadow-xl bg-white rounded-3xl overflow-hidden"
        >
          
          <div class="flex-shrink-0 z-10 bg-white">
             <AppNavbar />
          </div>

          <div class="flex-1 overflow-x-hidden overflow-y-auto p-4">
            <router-view></router-view>
          </div>

        </div>
      </main>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import AppSidebar from "@/core/components/AppSidebar.vue";
import AppNavbar from "@/core/components/AppNavbar.vue";

const router = useRouter();

const loading = ref(true);
const isSidebarOpen = ref(window.innerWidth >= 1024);

const watchScreen = () => {
  if (window.innerWidth <= 1024) {
    isSidebarOpen.value = false;
  } else {
    isSidebarOpen.value = true;
  }
};

onMounted(() => {
  setTimeout(() => {
    loading.value = false;
  }, 500);

  window.addEventListener("resize", watchScreen);
});

onUnmounted(() => {
  window.removeEventListener("resize", watchScreen);
});

const navigateToSettings = () => {
  router.push("/app/settings");
};
</script>

<style>
:root {
  --light: #edf2f9;
  --dark: #152e4d;
  --darker: #12263f;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}
::-webkit-scrollbar-track {
  background: transparent; 
}
::-webkit-scrollbar-thumb {
  background: #cbd5e1; 
  border-radius: 4px;
}
::-webkit-scrollbar-thumb:hover {
  background: #94a3b8; 
}
</style>