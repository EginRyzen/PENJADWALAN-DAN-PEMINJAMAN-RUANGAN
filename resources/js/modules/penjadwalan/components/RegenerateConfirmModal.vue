<template>
  <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

    <!-- Modal Card -->
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 animate-modal-in">
      <!-- Icon -->
      <div class="w-14 h-14 bg-teal-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-7 h-7 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
      </div>

      <!-- Title -->
      <h2 class="text-lg font-black text-gray-800 text-center mb-1">Generate Ulang?</h2>
      <p class="text-sm text-gray-500 text-center mb-4">
        Ditemukan <strong class="text-teal-600">draft jadwal tersimpan</strong> untuk periode dan tipe ini.
        Jika melanjutkan, draft lama akan dihapus dan digantikan dengan jadwal baru.
      </p>

      <!-- Info draft -->
      <div class="bg-teal-50 border border-teal-100 rounded-xl px-4 py-3 mb-5 flex items-start gap-3">
        <svg class="w-4 h-4 mt-0.5 text-teal-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
        </svg>
        <div>
          <div class="text-sm font-bold text-teal-700">Draft Ditemukan</div>
          <div class="text-xs text-teal-600 font-medium">{{ draftCount }} jadwal — Disimpan {{ formatTime(draftSavedAt) }}</div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex gap-3">
        <button
          @click="$emit('lanjutkan-draft')"
          class="flex-1 py-2.5 text-sm font-bold text-white bg-teal-500 hover:bg-teal-600 rounded-xl transition shadow-lg shadow-teal-100 active:scale-95"
        >
          Lanjutkan Draft
        </button>
        <button
          @click="handleGenerateUlang"
          class="flex-1 py-2.5 text-sm font-semibold text-gray-500 border border-gray-200 rounded-xl hover:bg-gray-50 transition active:scale-95"
        >
          Generate Ulang
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RegenerateConfirmModal',
  props: {
    modelValue:   { type: Boolean, default: false },
    draftCount:   { type: Number,  default: 0 },
    draftSavedAt: { type: String,  default: null },
  },
  emits: ['update:modelValue', 'generate-ulang', 'lanjutkan-draft'],
  methods: {
    handleGenerateUlang() {
      this.$emit('generate-ulang');
      this.$emit('update:modelValue', false);
    },
    formatTime(ts) {
      if (!ts) return '-';
      return new Date(ts).toLocaleString('id-ID', {
        day: 'numeric', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit',
      });
    },
  },
};
</script>

<style scoped>
@keyframes modalIn {
  from { opacity: 0; transform: scale(0.95) translateY(8px); }
  to   { opacity: 1; transform: scale(1) translateY(0); }
}
.animate-modal-in { animation: modalIn 0.2s ease-out; }
</style>
