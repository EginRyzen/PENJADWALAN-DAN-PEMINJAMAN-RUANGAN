<template>
  <modal-app :model-value="modelValue" size="medium" max-width="580px" :click-outside="true" @close="$emit('update:modelValue', false)">
    <!-- Header -->
    <div class="bg-gradient-to-r from-teal-500 to-teal-600 rounded-t-2xl px-6 py-4">
      <div class="flex items-start justify-between">
        <div>
          <div class="flex items-center gap-2 mb-1">
            <svg class="w-5 h-5 text-teal-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
            <h3 class="text-base font-bold text-white">Edit Jadwal Ujian</h3>
          </div>
          <p class="text-sm text-teal-100">{{ form.id }} — {{ form.mk_kode }} / {{ form.mk_nama }}</p>
        </div>
        <button @click="$emit('update:modelValue', false)" class="text-teal-200 hover:text-white transition p-1">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Body -->
    <div class="px-6 py-5 space-y-4">
      <!-- Conflict Alert -->
      <div v-if="form.status === 'conflict'" class="flex items-start gap-2.5 bg-red-50 border border-red-100 rounded-xl px-4 py-3">
        <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
          <p class="text-xs font-bold text-red-700 mb-0.5">Konflik Terdeteksi</p>
          <p class="text-xs text-red-600">{{ form.conflict_reason || 'Terdapat konflik pada jadwal ini. Silakan ubah ruangan atau waktu.' }}</p>
        </div>
      </div>

      <!-- MK Info Chips -->
      <div>
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Informasi Mata Kuliah</p>
        <div class="flex flex-wrap gap-2">
          <span class="text-xs font-bold bg-teal-50 text-teal-700 px-2.5 py-1 rounded-full">{{ form.mk_kode }}</span>
          <span class="text-xs font-medium bg-gray-50 text-gray-700 px-2.5 py-1 rounded-full">{{ form.mk_nama }}</span>
          <span class="text-xs font-medium bg-blue-50 text-blue-700 px-2.5 py-1 rounded-full">{{ form.prodi_kode }} / Kelas {{ form.kelas }}</span>
          <span class="text-xs font-bold bg-purple-50 text-purple-700 px-2.5 py-1 rounded-full">{{ form.sks }} SKS</span>
          <span class="text-xs font-medium bg-orange-50 text-orange-700 px-2.5 py-1 rounded-full">{{ form.durasi }} menit</span>
        </div>
      </div>

      <div class="border-t border-gray-100"></div>

      <!-- Form Fields -->
      <div class="grid grid-cols-2 gap-4">
        <!-- Tanggal -->
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Ujian <span class="text-red-500">*</span></label>
          <teal-date-picker
            v-model="form.tanggal"
            placeholder="Pilih tanggal ujian..."
          />
        </div>

        <!-- Dosen Pengawas -->
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Dosen Pengawas <span class="text-red-500">*</span></label>
          <select v-model="form.dosen_id" @change="onDosenChange" class="w-full h-10 border border-teal-300 rounded-lg px-3 text-sm text-gray-700 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-200 transition bg-white">
            <option v-for="d in dosenList" :key="d.id" :value="d.id">{{ d.nama }}</option>
          </select>
        </div>

        <!-- Jam Mulai -->
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jam Mulai <span class="text-red-500">*</span></label>
          <time-input
            v-model="form.jam_mulai"
            @update:modelValue="recalcJamSelesai"
          />
        </div>

        <!-- Jam Selesai (read-only, auto-calc) -->
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jam Selesai <span class="text-xs text-gray-400">(otomatis)</span></label>
          <div class="w-full h-10 border border-gray-200 rounded-lg px-3 flex items-center bg-gray-50">
            <span class="text-sm font-bold text-teal-700">{{ form.jam_selesai }}</span>
            <span class="ml-auto text-xs text-gray-400">{{ form.sks }} SKS × {{ sksDuration }} mnt</span>
          </div>
        </div>
      </div>

      <!-- Ruangan -->
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Ruangan <span class="text-red-500">*</span></label>
        <select-auto-complete
          v-model="form.ruangan_id"
          :options="ruanganList"
          item-text="nama"
          item-value="id"
          placeholder="Pilih Ruangan..."
          @search="handleSearchRuangan"
          @update:modelValue="onRuanganChange"
        />
        <p v-if="selectedRuangan && selectedRuangan.kapasitas < form.jumlah_peserta" class="text-xs text-orange-500 mt-1">
          ⚠ Kapasitas ruangan ({{ selectedRuangan.kapasitas }}) kurang dari peserta ({{ form.jumlah_peserta }})
        </p>
      </div>

      <!-- Duration Info -->
      <div class="flex items-start gap-2 bg-teal-50 border border-teal-100 rounded-xl px-4 py-2.5">
        <svg class="w-4 h-4 text-teal-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-xs text-teal-700">
          Durasi ujian dikunci <strong>{{ form.durasi }} menit</strong> ({{ form.sks }} SKS × {{ sksDuration }} mnt/SKS). Jam selesai dihitung otomatis dari jam mulai.
        </p>
      </div>
    </div>

    <!-- Footer -->
    <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100">
      <div class="text-xs text-gray-500">
        👥 {{ form.jumlah_peserta }} mahasiswa terdaftar
      </div>
      <div class="flex items-center gap-2">
        <button @click="$emit('update:modelValue', false)" class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
          Batal
        </button>
        <button @click="handleSave" class="px-5 py-2 text-sm font-bold text-white bg-teal-500 hover:bg-teal-600 rounded-xl transition shadow-sm hover:shadow-md flex items-center gap-1.5">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
          </svg>
          Terapkan Perubahan
        </button>
      </div>
    </div>
  </modal-app>
</template>

<script>
import ModalApp from '@/core/components/Modal.vue';
import TealDatePicker from '@/core/components/TealDatePicker.vue';
import TimeInput from '../../settings/components/TimeInput.vue';
import SelectAutoComplete from "@/core/components/SelectAutoComplete.vue";
import DISPATCH from "@/core/plugins/constants/dispatches";
export default {
  name: 'RowEditModal',
  components: { ModalApp, TealDatePicker, TimeInput, SelectAutoComplete },
  props: {
    modelValue:  { type: Boolean, default: false },
    item:        { type: Object, default: null },
    ruanganList: { type: Array, default: () => [] },
    dosenList:   { type: Array, default: () => [] },
    sksDuration: { type: Number, default: 50 },
  },
  emits: ['update:modelValue', 'save'],
  data() {
    return {
      form: {
        id: '', mk_kode: '', mk_nama: '', prodi_kode: '', kelas: '',
        sks: 0, durasi: 0, tanggal: '', jam_mulai: '', jam_selesai: '',
        ruangan_id: null, ruangan_nama: '', kapasitas: 0,
        jumlah_peserta: 0, dosen_id: null, dosen_nama: '',
        status: 'ok', conflict_reason: null,
      },
    };
  },
  computed: {
    selectedRuangan() {
      return this.ruanganList.find(r => r.id === this.form.ruangan_id);
    },
  },
  watch: {
    item: {
      handler(v) {
        if (v) this.form = { ...v };
      },
      immediate: true,
    },
  },
  methods: {
    recalcJamSelesai() {
      if (!this.form.jam_mulai) return;
      const [h, m] = this.form.jam_mulai.split(':').map(Number);
      const totalMin = h * 60 + m + this.form.durasi;
      this.form.jam_selesai = `${String(Math.floor(totalMin / 60)).padStart(2, '0')}:${String(totalMin % 60).padStart(2, '0')}`;
    },
    async fetchRuangan(query) {
      try {
        await this.$store.dispatch(DISPATCH.GET_ROOMS, {
          search: query || undefined,
        });
      } catch (e) {
        console.error("Gagal memuat data ruangan:", e);
      }
    },
    handleSearchRuangan(query) {
      clearTimeout(this._ruanganSearchTimer);
      this._ruanganSearchTimer = setTimeout(() => {
        this.fetchRuangan(query);
      }, 500);
    },
    onRuanganChange() {
      const r = this.ruanganList.find(r => r.id === this.form.ruangan_id);
      if (r) { this.form.ruangan_nama = r.nama; this.form.kapasitas = r.kapasitas; }
    },
    onDosenChange() {
      const d = this.dosenList.find(d => d.id === this.form.dosen_id);
      if (d) this.form.dosen_nama = d.nama;
    },
    handleSave() {
      this.$emit('save', { ...this.form });
      this.$emit('update:modelValue', false);
    },
  },
};
</script>
