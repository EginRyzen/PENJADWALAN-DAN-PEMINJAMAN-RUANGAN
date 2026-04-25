<template>
  <div class="grid grid-cols-3 gap-3 mb-4">
    <!-- OK -->
    <div 
      @click="$emit('filter-status', 'ok')"
      class="bg-white rounded-xl border border-green-100 shadow-sm p-4 flex items-center gap-3 hover:shadow-md hover:border-green-300 transition-all cursor-pointer group"
    >
      <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
        <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
        </svg>
      </div>
      <div>
        <p class="text-2xl font-black text-green-600 leading-tight">{{ stats.ok }}</p>
        <p class="text-xs font-semibold text-gray-500 leading-tight">Jadwal OK</p>
      </div>
      <div class="ml-auto w-1.5 h-10 rounded-full bg-green-400 opacity-30"></div>
    </div>

    <!-- Conflict -->
    <div 
      @click="$emit('filter-status', 'conflict')"
      class="bg-white rounded-xl border border-red-100 shadow-sm p-4 flex items-center gap-3 hover:shadow-md hover:border-red-300 transition-all cursor-pointer group"
    >
      <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
        <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
      </div>
      <div>
        <p class="text-2xl font-black text-red-500 leading-tight">{{ stats.conflict }}</p>
        <p class="text-xs font-semibold text-gray-500 leading-tight">Konflik</p>
      </div>
      <div class="ml-auto w-1.5 h-10 rounded-full bg-red-400 opacity-30"></div>
    </div>

    <!-- Edited -->
    <div 
      @click="$emit('filter-status', 'edited')"
      class="bg-white rounded-xl border border-amber-100 shadow-sm p-4 flex items-center gap-3 hover:shadow-md hover:border-amber-300 transition-all cursor-pointer group"
    >
      <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
        <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
        </svg>
      </div>
      <div>
        <p class="text-2xl font-black text-amber-500 leading-tight">{{ stats.edited }}</p>
        <p class="text-xs font-semibold text-gray-500 leading-tight">Diedit</p>
      </div>
      <div class="ml-auto w-1.5 h-10 rounded-full bg-amber-400 opacity-30"></div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'StatsRow',
  props: {
    stats: {
      type: Object,
      default: () => ({ ok: 0, conflict: 0, edited: 0 }),
    },
  },
};
</script>
