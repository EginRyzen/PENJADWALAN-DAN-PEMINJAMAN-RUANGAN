<template>
  <div class="flex h-screen antialiased text-gray-900 bg-gray-100 overflow-hidden">
    <AppSidebar
      class="hidden md:flex flex-shrink-0" 
      @open-settings="navigateToSettings"
    />

    <main class="flex-1 min-w-0 bg-gray-100 py-10 px-5 overflow-hidden transition-all duration-300">
      <div class="flex flex-col flex-1 h-full shadow-xl bg-white rounded-3xl overflow-hidden">
        
        <div class="flex-shrink-0 z-10 bg-white">
          <AppNavbar />
        </div>

        <div class="flex-1 overflow-x-hidden overflow-y-auto px-6 lg:px-10 py-4">
          <router-view></router-view>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import AppSidebar from "@/core/components/AppSidebar.vue";
import AppNavbar from "@/core/components/AppNavbar.vue";

const router = useRouter();
const loading = ref(true);

onMounted(() => {
  setTimeout(() => {
    loading.value = false;
  }, 500);
});

const navigateToSettings = () => {
  router.push("/app/settings");
};
</script>