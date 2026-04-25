<template>
  <div class="fixed bottom-0 left-0 right-0 z-40 border-t border-gray-200 bg-white/95 backdrop-blur-md shadow-2xl shadow-gray-300/50">
    <div class="max-w-full px-6 py-3 flex flex-col sm:flex-row items-center justify-between gap-3">

      <!-- Status Info -->
      <div class="flex items-center gap-3 flex-wrap">
        <div class="flex items-center gap-1.5 text-sm">
          <span class="w-2 h-2 rounded-full bg-green-500"></span>
          <span class="font-semibold text-gray-700">{{ stats.ok }}</span>
          <span class="text-gray-400 text-xs">OK</span>
        </div>
        <div class="flex items-center gap-1.5 text-sm" v-if="stats.edited > 0">
          <span class="w-2 h-2 rounded-full bg-amber-500"></span>
          <span class="font-semibold text-gray-700">{{ stats.edited }}</span>
          <span class="text-gray-400 text-xs">Diedit</span>
        </div>
        <div v-if="stats.conflict > 0" class="flex items-center gap-1.5">
          <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
          <span class="text-sm font-bold text-red-600">{{ stats.conflict }} konflik — selesaikan sebelum simpan permanen</span>
        </div>
        <div v-else-if="stats.ok > 0 && !isPermanen" class="flex items-center gap-1.5">
          <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
          </svg>
          <span class="text-sm font-semibold text-green-600">Semua jadwal siap disimpan</span>
        </div>

        <!-- Badge status permanen -->
        <div v-if="isPermanen" class="flex items-center gap-1.5 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-black uppercase tracking-wider">
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
          Jadwal Permanen (Locked)
        </div>

        <!-- Badge draft tersimpan -->
        <div v-if="lastDraftSavedAt && !isPermanen" class="flex items-center gap-1 text-xs bg-blue-50 text-blue-600 px-2.5 py-1 rounded-full font-medium">
          <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
          </svg>
          Draft: {{ formatTime(lastDraftSavedAt) }}
        </div>
      </div>

      <!-- Actions -->
      <div class="flex items-center gap-2">
        <!-- Reset & Generate Ulang -->
        <button
          @click="$emit('reset')"
          :disabled="isSaving || isSavingDraft || isPermanen"
          class="flex items-center gap-1.5 px-4 py-2 text-sm font-semibold border rounded-xl transition"
          :class="!isSaving && !isSavingDraft && !isPermanen
            ? 'text-gray-600 border-gray-200 hover:bg-gray-50 hover:border-gray-300'
            : 'text-gray-400 border-gray-100 bg-gray-50 cursor-not-allowed'"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Reset
        </button>

        <!-- Simpan Draft -->
        <button
          @click="handleSaveDraft"
          :disabled="isSavingDraft || isSaving || totalJadwal === 0 || isPermanen"
          class="flex items-center gap-2 px-5 py-2 text-sm font-bold rounded-xl border transition-all duration-200"
          :class="!isSavingDraft && !isSaving && totalJadwal > 0 && !isPermanen
            ? 'border-blue-300 text-blue-600 bg-blue-50 hover:bg-blue-100 hover:border-blue-400'
            : 'border-gray-200 text-gray-400 cursor-not-allowed bg-gray-50'"
        >
          <span v-if="isSavingDraft" class="w-4 h-4 border-2 border-blue-400 border-t-transparent rounded-full animate-spin"></span>
          <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
          </svg>
          {{ isSavingDraft ? 'Menyimpan Draft...' : 'Simpan Sebagai Draft' }}
        </button>

        <!-- Simpan Permanen -->
        <button
          @click="handleSavePermanen"
          :disabled="!canSavePermanen || isSaving || isSavingDraft || isPermanen"
          class="flex items-center gap-2 px-6 py-2 text-sm font-bold rounded-xl transition-all duration-200 shadow-sm"
          :class="canSavePermanen && !isSaving && !isSavingDraft && !isPermanen
            ? 'bg-teal-500 hover:bg-teal-600 text-white hover:shadow-md hover:shadow-teal-200'
            : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
        >
          <span v-if="isSaving" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
          <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          {{ isSaving ? 'Menyimpan...' : 'Simpan Permanen' }}
        </button>

        <!-- Info Mode Read Only -->
        <div v-if="isPermanen" class="text-xs text-gray-500 font-medium italic ml-2">
          Jadwal telah terkunci.
        </div>
      </div>

    </div>
  </div>
</template>

<script>
export default {
  name: 'ActionBar',
  props: {
    stats:            { type: Object,  default: () => ({ ok: 0, conflict: 0, edited: 0 }) },
    isSaving:         { type: Boolean, default: false },
    isSavingDraft:    { type: Boolean, default: false },
    lastDraftSavedAt: { type: String,  default: null },
    isPermanen:       { type: Boolean, default: false },
  },
  emits: ['save', 'save-draft', 'reset'],
  computed: {
    totalJadwal() {
      return (this.stats.ok || 0) + (this.stats.conflict || 0) + (this.stats.edited || 0);
    },
    canSavePermanen() {
      // Tidak bisa simpan permanen jika: ada konflik, total jadwal 0, sedang menyimpan, atau SUDAH permanen
      return this.stats.conflict === 0 && this.totalJadwal > 0 && !this.isPermanen;
    },
  },
  methods: {
    handleSaveDraft() {
      if (!this.isSavingDraft && !this.isSaving && this.totalJadwal > 0 && !this.isPermanen) {
        this.$emit('save-draft');
      }
    },
    handleSavePermanen() {
      if (this.canSavePermanen && !this.isSaving && !this.isSavingDraft && !this.isPermanen) {
        this.$emit('save');
      }
    },
    formatTime(ts) {
      if (!ts) return '';
      return new Date(ts).toLocaleTimeString('id-ID', {
        day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit',
      });
    },
  },
};
</script>
