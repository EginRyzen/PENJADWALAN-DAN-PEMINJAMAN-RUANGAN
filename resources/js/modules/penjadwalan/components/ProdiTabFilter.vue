<template>
  <div class="mb-4">
    <!-- Scrollable Tab Bar -->
    <div class="relative bg-white rounded-xl border border-gray-100 shadow-sm">
      <div class="overflow-x-auto scrollbar-hide">
        <div class="flex items-stretch min-w-max px-1 py-1 gap-1">
          <!-- Tab Semua -->
          <button
            @click="$emit('change', 'semua')"
            class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 whitespace-nowrap flex-shrink-0"
            :class="activeTab === 'semua'
              ? 'bg-teal-500 text-white shadow-sm'
              : 'text-gray-500 hover:text-teal-600 hover:bg-teal-50'"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            Semua
            <span
              class="text-xs px-1.5 py-0.5 rounded-full font-bold leading-none"
              :class="activeTab === 'semua' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600'"
            >{{ total }}</span>
          </button>

          <!-- Tab per Prodi -->
          <button
            v-for="p in prodiTabs"
            :key="p.id"
            @click="$emit('change', p.id)"
            class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 whitespace-nowrap flex-shrink-0"
            :class="activeTab === p.id
              ? 'bg-teal-500 text-white shadow-sm'
              : 'text-gray-500 hover:text-teal-600 hover:bg-teal-50'"
          >
            <span
              class="w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold leading-none flex-shrink-0"
              :class="activeTab === p.id ? 'bg-white/20 text-white' : 'bg-teal-100 text-teal-600'"
            >{{ p.kode.charAt(0) }}</span>
            {{ p.kode }}
            <span
              class="text-xs px-1.5 py-0.5 rounded-full font-bold leading-none"
              :class="activeTab === p.id ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600'"
            >{{ p.count }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ProdiTabFilter',
  props: {
    activeTab: { type: [String, Number], default: 'semua' },
    prodiTabs: { type: Array, default: () => [] },
    total:     { type: Number, default: 0 },
  },
  emits: ['change'],
};
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
