<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-4">
    <!-- Header -->
    <div class="flex items-center gap-2 mb-4">
      <div class="p-1.5 bg-teal-50 rounded-lg">
        <svg class="w-5 h-5 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
        </svg>
      </div>
      <h3 class="text-base font-bold text-gray-800">Konfigurasi Generate Jadwal</h3>
      <span class="ml-auto text-xs font-bold text-teal-600 bg-teal-50 px-2 py-1 rounded-full">{{ localContext.type.toUpperCase() }} Mode</span>
    </div>

    <!-- Filter Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
      <!-- Tipe Ujian -->
      <div>
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Tipe Ujian</label>
        <div class="flex rounded-lg border border-gray-200 overflow-hidden h-11">
          <button
            v-for="t in ['uas', 'pembelajaran', 'uts']"
            :key="t"
            @click="t === 'uas' ? setType(t) : null"
            :disabled="t !== 'uas'"
            class="flex-1 text-[10px] sm:text-sm font-bold transition-all duration-200"
            :class="[
              localContext.type === t
                ? 'bg-teal-500 text-white'
                : 'bg-white text-gray-500 hover:bg-teal-50 hover:text-teal-600',
              t !== 'uas' ? 'opacity-50 cursor-not-allowed grayscale-[0.5]' : ''
            ]"
          >
            {{ t === 'pembelajaran' ? 'BLJR' : t.toUpperCase() }}
          </button>
        </div>
      </div>

      <!-- Periode dari API (Swapped to Left) -->
      <div>
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Periode</label>
        <select
          v-model="localContext.periode_id"
          class="w-full h-11 border border-gray-200 rounded-lg px-3 text-sm text-gray-700 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-200 transition bg-white cursor-pointer hover:border-teal-400"
          @change="onPeriodeChange"
        >
          <option value="">Pilih Periode...</option>
          <option v-for="p in periodeList" :key="p.id" :value="p.id">{{ p.nama }}</option>
        </select>
      </div>

      <!-- Tanggal Mulai (Swapped to Right) -->
      <div class="relative">
        <teal-date-picker
          v-model="localContext.start_date"
          label="Mulai Ujian"
          placeholder="Pilih tanggal mulai..."
          :required="true"
          :min-date="today"
          :disabled="!localContext.periode_id || !!scheduleStatus"
          :hari-libur-list="hariLiburList"
          :allowed-days="allowedDays"
          @change="emitContext"
        />
      </div>
    </div>

    <!-- Generate Button Row -->
    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
      <!-- Chips info -->
      <div class="flex flex-wrap gap-2">
        <span v-if="localContext.start_date" class="inline-flex items-center gap-1 text-xs bg-teal-50 text-teal-700 px-2.5 py-1 rounded-full font-medium">
          📅 Mulai: {{ formatDate(localContext.start_date) }}
        </span>
        <span v-if="localContext.start_date && isHoliday(localContext.start_date)" class="inline-flex items-center gap-1 text-xs bg-amber-50 text-amber-700 px-2.5 py-1 rounded-full font-medium">
          ⚠ Tanggal ini hari libur!
        </span>
        <span v-if="!localContext.start_date && !scheduleStatus" class="text-xs text-gray-400 italic">
          Pilih tanggal mulai ujian untuk mengaktifkan generate
        </span>
        <span v-if="scheduleStatus === 'permanen'" class="inline-flex items-center gap-1 text-xs bg-blue-50 text-blue-700 px-2.5 py-1 rounded-full font-medium">
          ✅ Jadwal Permanen Terdeteksi
        </span>
        <span v-if="scheduleStatus === 'draft'" class="inline-flex items-center gap-1 text-xs bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-full font-medium">
          📝 Draft Jadwal Terdeteksi
        </span>
      </div>

      <div class="flex items-center gap-2">
        <!-- Download Placeholder Button (Visible if schedule exists) -->
        <button
          v-if="scheduleStatus"
          type="button"
          @click="$emit('download')"
          class="flex items-center justify-center w-11 h-11 rounded-xl bg-gray-50 text-gray-500 hover:bg-gray-100 border border-gray-200 transition-all duration-200 active:scale-95"
          title="Download Jadwal (Excel/PDF)"
        >
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
        </button>

        <button
          @click="$emit('generate')"
          :disabled="!canGenerateAction || isGenerating"
          class="flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 shadow-md flex-shrink-0 ml-1"
          :class="canGenerateAction && !isGenerating
            ? (scheduleStatus ? 'bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white shadow-indigo-200 hover:shadow-lg hover:shadow-indigo-200 hover:-translate-y-0.5' : 'bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 text-white shadow-teal-200 hover:shadow-lg hover:shadow-teal-200 hover:-translate-y-0.5')
            : 'bg-gray-200 text-gray-400 cursor-not-allowed shadow-none'"
        >
          <span v-if="isGenerating" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
          <template v-else>
            <svg v-if="scheduleStatus" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <svg v-else class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
          </template>
          {{ isGenerating ? 'Generating...' : (scheduleStatus ? 'Tampilkan Jadwal Terdeteksi' : 'Generate Jadwal Otomatis') }}
        </button>
      </div>
    </div>

    <!-- Holiday info strip -->
    <div v-if="hariLiburList.length > 0" class="mt-3 flex items-start gap-2 text-xs text-orange-700 bg-orange-50 rounded-lg px-3 py-2">
      <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
      </svg>
      <span>Terdapat <strong>{{ hariLiburList.length }} hari libur</strong> yang akan dilewati otomatis saat generate jadwal.</span>
    </div>
  </div>
</template>

<script>
import TealDatePicker from '@/core/components/TealDatePicker.vue';
import SelectAutoComplete from '@/core/components/SelectAutoComplete.vue';

export default {
  name: 'FilterPanel',
  components: { TealDatePicker, SelectAutoComplete },
  props: {
    isGenerating:   { type: Boolean, default: false },
    hariLiburList:  { type: Array,   default: () => [] },
    periodeList:    { type: Array,   default: () => [] },
    prodiList:      { type: Array,   default: () => [] },
    kelasList:      { type: Array,   default: () => [] },
    allowedDays:    { type: Array,   default: null },
    scheduleStatus: { type: String,  default: null }, // 'draft' | 'permanen' | null
    context:        { type: Object,  default: () => ({}) },
  },
  emits: ['generate', 'context-change', 'periode-change'],
  data() {
    return {
      today: new Date().toISOString().split('T')[0],
      localContext: {
        type:       'uas',
        start_date: '',
        periode_id: '',
        prodi_id:   '',
        kelas_id:   '',
      },
    };
  },
  watch: {
    context: {
      deep: true,
      immediate: true,
      handler(val) {
        if (val) this.localContext = { ...this.localContext, ...val };
      }
    }
  },
  computed: {
    canGenerateAction() {
      // Jika sudah ada jadwal, minimal periode harus terisi untuk "Tampilkan"
      if (this.scheduleStatus) return !!this.localContext.periode_id;
      // Jika belum ada, butuh start_date
      return !!this.localContext.start_date && !!this.localContext.periode_id;
    },
  },
  methods: {
    setType(t) {
      this.localContext.type = t;
      this.emitContext();
    },
    emitContext() {
      this.$emit('context-change', { ...this.localContext });
    },
    onPeriodeChange() {
      this.emitContext();
      this.$emit('periode-change', this.localContext.periode_id);
    },
    formatDate(d) {
      if (!d) return '';
      return new Date(d + 'T00:00:00').toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    },
    isHoliday(date) {
      return this.hariLiburList.some(h => h.tanggal === date);
    },
  },
};
</script>
