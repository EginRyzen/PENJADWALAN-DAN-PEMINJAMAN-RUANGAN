<template>
  <div v-if="show" class="fixed inset-0 z-[1000] flex items-center justify-center p-4 lg:p-8">
    <div class="absolute inset-0 bg-black/90 backdrop-blur-sm" @click="$emit('close')"></div>
    
    <div class="relative max-w-5xl w-full flex items-center justify-center">
      <button 
        @click="$emit('close')" 
        class="absolute -top-12 right-0 lg:-right-12 text-white/70 hover:text-white transition-colors p-2"
      >
        <font-awesome-icon icon="times" class="text-3xl" />
      </button>
      
      <img 
        :src="src" 
        class="max-w-full max-h-[85vh] rounded-lg shadow-2xl object-contain bg-black/20" 
      />
    </div>
  </div>
</template>

<script>
export default {
  name: "BuildingImagePreview",
  props: {
    show: Boolean,
    src: String
  }
}
</script>