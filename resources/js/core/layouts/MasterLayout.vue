<template>
  <div class="flex h-screen antialiased text-gray-900 bg-gray-100 overflow-hidden">
    <AppSidebar
      class="hidden md:flex flex-shrink-0" 
      @open-settings="navigateToSettings"
    />

    <!-- Mobile Layout: full screen, no padding, no rounded corners -->
    <main class="flex-1 min-w-0 overflow-hidden transition-all duration-300
      bg-white md:bg-gray-100
      md:py-10 md:px-5">
      <div class="flex flex-col flex-1 h-full overflow-hidden
        md:shadow-xl md:bg-white md:rounded-3xl">
        
        <!-- Navbar: hidden on mobile, visible on desktop -->
        <div class="flex-shrink-0 z-10 bg-white hidden md:block">
          <AppNavbar />
        </div>

        <div class="flex-1 overflow-x-hidden overflow-y-auto
          px-0 py-0
          md:px-6 lg:px-10 md:py-4">
          <router-view></router-view>
        </div>
      </div>
    </main>

    <!-- Bottom menu for mobile -->
    <AppBottomNav />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import AppSidebar from "@/core/components/AppSidebar.vue";
import AppNavbar from "@/core/components/AppNavbar.vue";
import AppBottomNav from "@/core/components/AppBottomNav.vue";

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