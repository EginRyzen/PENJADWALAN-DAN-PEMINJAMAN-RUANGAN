<template>
  <modal
    :model-value="modelValue"
    size="medium"
    max-width="500px"
    @update:modelValue="$emit('update:modelValue', $event)"
  >
    <div class="p-6">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6 border-b pb-4">
        <div class="flex items-center gap-3">
          <div class="p-2 bg-teal-50 rounded-lg text-teal-600">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-gray-800">Filter Jadwal</h3>
            <p class="text-xs text-gray-400">Sesuaikan kriteria untuk menyaring hasil generate</p>
          </div>
        </div>
        <button 
          @click="$emit('update:modelValue', false)"
          class="text-gray-400 hover:text-gray-600 transition-colors"
        >
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Body -->
      <div class="space-y-5 py-2">
        <!-- Filter Prodi -->
        <div>
          <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Program Studi</label>
          <select-auto-complete
            v-model="localFilters.prodi"
            :options="prodiOptions"
            item-text="nama"
            item-value="id"
            placeholder="Pilih Program Studi..."
          />
        </div>

        <!-- Filter Kelas -->
        <div>
          <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kelas</label>
          <select-auto-complete
            v-model="localFilters.kelas"
            :options="kelasOptions"
            item-text="nama_kelas"
            item-value="id"
            placeholder="Pilih Kelas..."
          />
        </div>

        <!-- Filter Tanggal (Range via ModalDatePicker) -->
        <div>
          <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Rentang Tanggal</label>
          <button 
            @click="showDatePicker = true"
            class="w-full h-11 px-4 flex items-center justify-between rounded-lg border border-gray-200 bg-white hover:border-teal-400 transition-all text-sm group"
          >
            <span :class="localFilters.tanggalStart ? 'text-gray-700 font-medium' : 'text-gray-400'">
              {{ formattedDateRange }}
            </span>
            <svg class="w-4 h-4 text-gray-400 group-hover:text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </button>

          <modal-date-picker 
            v-model:show="showDatePicker"
            :date="dateValueForModal"
            @submit="handleDateSubmit"
          />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Filter Hari -->
          <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Hari</label>
            <select-auto-complete
              v-model="localFilters.hari"
              :options="hariOptions"
              item-text="label"
              item-value="value"
              placeholder="Pilih Hari..."
            />
          </div>

          <!-- Filter Ruangan -->
          <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Ruangan</label>
            <select-auto-complete
              v-model="localFilters.ruangan"
              :options="ruanganOptions"
              item-text="label"
              item-value="id"
              placeholder="Pilih Ruangan..."
              @search="$emit('search-ruangan', $event)"
            />
          </div>
        </div>

        <!-- Filter Dosen -->
        <div>
          <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Dosen Pengawas</label>
          <select-auto-complete
            v-model="localFilters.dosen"
            :options="dosenOptions"
            item-text="nama"
            item-value="id"
            placeholder="Pilih Dosen..."
            @search="$emit('search-dosen', $event)"
          />
        </div>
      </div>

      <!-- Footer -->
      <div class="mt-8 flex items-center justify-between gap-3 border-t pt-5">
        <button 
          @click="reset"
          class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-red-500 transition-colors flex items-center gap-2"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
          Reset Filter
        </button>
        <button 
          @click="apply"
          class="px-8 py-2.5 bg-teal-500 hover:bg-teal-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-teal-100 transition-all active:scale-95"
        >
          Terapkan Filter
        </button>
      </div>
    </div>
  </modal>
</template>

<script>
import Modal from "@/core/components/Modal.vue";
import SelectAutoComplete from "@/core/components/SelectAutoComplete.vue";
import TealDatePicker from "@/core/components/TealDatePicker.vue";
import ModalDatePicker from "@/core/components/ModalDatePicker.vue";

export default {
  name: 'JadwalFilterModal',
  components: { Modal, SelectAutoComplete, ModalDatePicker },
  props: {
    modelValue: { type: Boolean, default: false },
    filters:    { type: Object,  default: () => ({ prodi: '', kelas: '', hari: '', ruangan: '', dosen: '', tanggalStart: '', tanggalEnd: '', status: '' }) },
    prodiList:  { type: Array,   default: () => [] },
    kelasList:  { type: Array,   default: () => [] },
    ruanganList:{ type: Array,   default: () => [] },
    dosenList:  { type: Array,   default: () => [] },
  },
  emits: ['update:modelValue', 'apply', 'search-ruangan', 'search-dosen'],
  data() {
    return {
      localFilters: { prodi: '', kelas: '', hari: '', ruangan: '', dosen: '', tanggalStart: '', tanggalEnd: '', status: '' },
      showDatePicker: false,
    };
  },
  computed: {
    formattedDateRange() {
      if (!this.localFilters.tanggalStart || !this.localFilters.tanggalEnd) return 'Pilih Rentang Tanggal...';
      const start = new Date(this.localFilters.tanggalStart + 'T00:00:00').toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
      const end = new Date(this.localFilters.tanggalEnd + 'T00:00:00').toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
      return `${start} - ${end}`;
    },
    dateValueForModal() {
      return {
        start: this.localFilters.tanggalStart ? new Date(this.localFilters.tanggalStart + 'T00:00:00') : null,
        end: this.localFilters.tanggalEnd ? new Date(this.localFilters.tanggalEnd + 'T00:00:00') : null,
      };
    },
    prodiOptions() {
      return this.prodiList.map(p => ({ id: p.id, nama: p.nama || p.prodi_nama }));
    },
    kelasOptions() {
      let list = this.kelasList || [];
      if (this.localFilters.prodi) {
        list = list.filter(k => k.program_studi_id == this.localFilters.prodi);
      }
      return list.map(k => ({ 
        id: k.id, 
        nama_kelas: k.nama_kelas || k.name || k.nama 
      }));
    },
    ruanganOptions() {
      return this.ruanganList.map(r => ({ 
        id: r.id, 
        label: `${r.nama || r.room_name} (Kap. ${r.kapasitas || r.room_capacity || '-'})` 
      }));
    },
    dosenOptions() {
      return this.dosenList.map(d => ({ id: d.id, nama: d.nama || d.name || '-' }));
    },
    hariOptions() {
      return [
        { value: '', label: 'Semua Hari' },
        { value: 'Senin', label: 'Senin' },
        { value: 'Selasa', label: 'Selasa' },
        { value: 'Rabu', label: 'Rabu' },
        { value: 'Kamis', label: 'Kamis' },
        { value: 'Jumat', label: 'Jumat' },
        { value: 'Sabtu', label: 'Sabtu' },
      ];
    },
  },
  watch: {
    modelValue(val) {
      if (val) {
        this.localFilters = { ...this.filters };
      }
    },
    'localFilters.prodi'(newVal, oldVal) {
      if (newVal !== oldVal) {
        this.localFilters.kelas = '';
      }
    }
  },
  methods: {
    handleDateSubmit(range) {
      if (range && range.start && range.end) {
        // Gunakan format YYYY-MM-DD yang aman untuk dibandingkan di JadwalTable
        const formatDate = (d) => {
          const date = new Date(d);
          const year = date.getFullYear();
          const month = String(date.getMonth() + 1).padStart(2, '0');
          const day = String(date.getDate()).padStart(2, '0');
          return `${year}-${month}-${day}`;
        };
        
        this.localFilters.tanggalStart = formatDate(range.start);
        this.localFilters.tanggalEnd = formatDate(range.end);
      }
    },
    apply() {
      this.$emit('apply', { ...this.localFilters });
      this.$emit('update:modelValue', false);
    },
    reset() {
      this.localFilters = {
        prodi: '',
        kelas: '',
        hari: '',
        ruangan: '',
        dosen: '',
        tanggalStart: '',
        tanggalEnd: '',
        status: '',
      };
      this.$emit('apply', { ...this.localFilters });
    }
  }
};
</script>
