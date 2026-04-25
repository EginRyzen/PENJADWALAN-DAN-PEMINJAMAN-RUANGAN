<template>
  <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="$emit('update:modelValue', false)"></div>

    <!-- Modal Card -->
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 animate-modal-in">
      <!-- Icon -->
      <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-7 h-7 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
      </div>

      <!-- Title -->
      <h2 class="text-lg font-black text-gray-800 text-center mb-1">Simpan Jadwal Permanen?</h2>
      <p class="text-sm text-gray-500 text-center mb-5">
        Jadwal yang disimpan permanen <strong class="text-gray-700">tidak dapat diubah</strong> dan akan langsung mengirim notifikasi email ke semua dosen pengawas.
      </p>

      <!-- Summary -->
      <div class="bg-gray-50 rounded-xl px-4 py-3 mb-5 space-y-1.5">
        <div class="flex justify-between text-sm">
          <span class="text-gray-500">Total Jadwal</span>
          <span class="font-bold text-gray-800">{{ totalJadwal }} jadwal</span>
        </div>
        <div class="flex justify-between text-sm">
          <span class="text-gray-500">Tipe Ujian</span>
          <span class="font-bold text-teal-600 uppercase">{{ tipe }}</span>
        </div>
        <div class="flex justify-between text-sm">
          <span class="text-gray-500">Periode</span>
          <span class="font-bold text-gray-800">{{ periodeName }}</span>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex gap-3">
        <button
          @click="$emit('update:modelValue', false)"
          class="flex-1 py-2.5 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition"
        >
          Batal
        </button>
        <button
          @click="handleConfirm"
          class="flex-1 py-2.5 text-sm font-bold text-white bg-teal-500 hover:bg-teal-600 rounded-xl transition shadow-sm shadow-teal-200"
        >
          Ya, Simpan Permanen
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'KonfirmasiPermanenModal',
  props: {
    modelValue:  { type: Boolean, default: false },
    totalJadwal: { type: Number,  default: 0 },
    tipe:        { type: String,  default: '' },
    periodeName: { type: String,  default: '' },
  },
  emits: ['update:modelValue', 'confirm'],
  methods: {
    handleConfirm() {
      this.$emit('confirm');
      this.$emit('update:modelValue', false);
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
