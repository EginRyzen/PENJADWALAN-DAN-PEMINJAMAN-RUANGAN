<template>
  <div 
    v-if="isLoading" 
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/25 backdrop-blur-sm"
  >
    <div class="flex flex-col items-center bg-white p-6 rounded-lg shadow-xl">
      <div class="animate-spin rounded-full h-12 w-12 border-4 border-indigo-100 border-t-indigo-600 mb-3"></div>
      
      <span class="text-indigo-900 font-semibold text-lg">Mohon Tunggu...</span>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex';

export default {
  name: "GlobalLoader",
  computed: {
    ...mapState(['isLoading'])
  }
};
</script>

<style scoped>
/* Opsional: Animasi fade in/out */
</style>