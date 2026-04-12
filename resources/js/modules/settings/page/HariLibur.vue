<template>
  <div class="p-4 md:p-6">
    <!-- Breadcrumb -->
    <breadcrumb-bima :items="breadcrumbItems" class="mb-6" />

    <!-- Page Title -->
    <div class="flex items-center gap-3 mb-6">
      <div class="p-2.5 bg-red-500 rounded-xl shadow-lg shadow-red-200">
        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
      </div>
      <div>
        <h1 class="text-xl font-black text-gray-800">Hari Libur & Tanggal Merah</h1>
        <p class="text-xs text-gray-500 mt-0.5">Kelola hari libur nasional dan kampus — akan dilewati otomatis saat generate jadwal</p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left: Add Form -->
      <div class="lg:col-span-1 space-y-5">
        <!-- Add Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
          <div class="flex items-center gap-2 mb-5">
            <div class="p-1.5 bg-red-50 rounded-lg">
              <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
            </div>
            <h3 class="text-sm font-bold text-gray-800">Tambah Hari Libur</h3>
          </div>

          <div class="space-y-3">
            <!-- Tanggal -->
            <div>
              <teal-date-picker
                v-model="form.tanggal"
                label="Tanggal"
                placeholder="Pilih tanggal libur..."
                :required="true"
              />
            </div>

            <!-- Keterangan -->
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1.5">Keterangan <span class="text-red-500">*</span></label>
              <input
                type="text"
                v-model="form.keterangan"
                placeholder="cth: Maulid Nabi Muhammad SAW"
                class="w-full h-10 border border-gray-200 rounded-lg px-3 text-sm text-gray-700 focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition bg-white text-gray-800"
              />
            </div>

            <!-- Tipe -->
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-2">Tipe Libur</label>
              <div class="flex gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                  <div
                    @click="form.tipe = 'nasional'"
                    class="w-4 h-4 rounded-full border-2 flex items-center justify-center cursor-pointer"
                    :class="form.tipe === 'nasional' ? 'border-red-500 bg-red-500' : 'border-gray-300'"
                  >
                    <div v-if="form.tipe === 'nasional'" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                  </div>
                  <span class="text-sm text-gray-700">Libur Nasional</span>
                  <span class="w-2 h-2 rounded-full bg-red-500"></span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <div
                    @click="form.tipe = 'kampus'"
                    class="w-4 h-4 rounded-full border-2 flex items-center justify-center cursor-pointer"
                    :class="form.tipe === 'kampus' ? 'border-orange-500 bg-orange-500' : 'border-gray-300'"
                  >
                    <div v-if="form.tipe === 'kampus'" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                  </div>
                  <span class="text-sm text-gray-700">Libur Kampus</span>
                  <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                </label>
              </div>
            </div>

            <!-- Validation error -->
            <p v-if="formError" class="text-xs text-red-500 flex items-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/>
              </svg>
              {{ formError }}
            </p>

            <!-- Submit Button -->
            <button
              @click="handleTambah"
              :disabled="isSaving"
              class="w-full h-11 flex items-center justify-center gap-2 bg-red-500 hover:bg-red-600 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-bold rounded-xl transition-all duration-200 shadow-sm hover:shadow-md hover:shadow-red-200 mt-2"
            >
              <template v-if="isSaving">
                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menyimpan...
              </template>
              <template v-else>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Hari Libur
              </template>
            </button>
          </div>
        </div>

        <!-- Info Card -->
        <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl p-4 text-white">
          <h4 class="font-bold text-sm mb-1 flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Cara Kerja
          </h4>
          <p class="text-xs text-teal-100 leading-relaxed">
            Hari libur yang didaftarkan akan <strong class="text-white">otomatis dilewati</strong> oleh sistem saat generate jadwal ujian. Anda tidak perlu mengatur manual.
          </p>
          <div class="mt-3 space-y-1.5">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-red-300 flex-shrink-0"></span>
              <span class="text-xs text-teal-100">Libur Nasional — disertai keterangan resmi</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-orange-300 flex-shrink-0"></span>
              <span class="text-xs text-teal-100">Libur Kampus — kebijakan internal kampus</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Right: Calendar Preview + List -->
      <div class="lg:col-span-2 space-y-5">
        <!-- Mini Calendar Overview -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-800">Kalender Hari Libur {{ selectedYear }}</h3>
            <select v-model="selectedYear" class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none text-gray-600 bg-white">
              <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
            </select>
          </div>

          <!-- Month Grid -->
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <div v-for="month in 12" :key="month" class="border border-gray-100 rounded-lg p-2">
              <p class="text-xs font-bold text-gray-600 text-center mb-2">{{ monthName(month) }}</p>
              <div class="grid grid-cols-7 gap-0.5">
                <div v-for="d in ['M','S','S','R','K','J','S']" :key="d+'h'" class="text-center text-gray-400" style="font-size:8px">{{ d }}</div>
                <div v-for="blank in getFirstDayOfMonth(month)" :key="'b'+blank" class="h-4"></div>
                <div
                  v-for="day in getDaysInMonth(month)"
                  :key="day"
                  class="h-4 w-full flex items-center justify-center rounded text-center cursor-default"
                  :class="getDayClass(month, day)"
                  :title="getHolidayTooltip(month, day)"
                  style="font-size:9px"
                >{{ day }}</div>
              </div>
            </div>
          </div>

          <!-- Legend -->
          <div class="mt-4 flex flex-wrap gap-4 border-t border-gray-100 pt-3">
            <div class="flex items-center gap-1.5">
              <span class="w-4 h-4 rounded bg-red-100 border border-red-300 text-center text-red-600 font-bold" style="font-size:9px;line-height:16px">1</span>
              <span class="text-xs text-gray-500">Libur Nasional</span>
            </div>
            <div class="flex items-center gap-1.5">
              <span class="w-4 h-4 rounded bg-orange-100 border border-orange-300 text-center text-orange-600 font-bold" style="font-size:9px;line-height:16px">2</span>
              <span class="text-xs text-gray-500">Libur Kampus</span>
            </div>
            <div class="flex items-center gap-1.5">
              <span class="w-4 h-4 rounded text-center text-red-400 font-bold" style="font-size:9px;line-height:16px">7</span>
              <span class="text-xs text-gray-500">Minggu</span>
            </div>
          </div>
        </div>

        <!-- List Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-sm font-bold text-gray-800">
              Daftar Hari Libur
              <span class="text-xs font-normal text-gray-400 ml-1">({{ filteredLibur.length }} entri)</span>
            </h3>
            <div class="relative">
              <svg class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
              <input v-model="search" type="text" placeholder="Cari keterangan..." class="pl-7 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:border-teal-400 w-40 bg-white text-gray-800" />
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left">
              <thead>
                <tr class="border-b-2 border-teal-400">
                  <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                  <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Keterangan</th>
                  <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Tipe</th>
                  <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                <tr v-if="filteredLibur.length === 0">
                  <td colspan="4" class="px-4 py-8 text-center text-gray-400 text-sm">Belum ada hari libur terdaftar untuk {{ selectedYear }}.</td>
                </tr>
                <tr v-for="item in filteredLibur" :key="item.id" class="hover:bg-gray-50/60 transition-colors">
                  <td class="px-4 py-3">
                    <div class="text-sm font-semibold text-gray-800">{{ formatTanggal(item.tanggal) }}</div>
                    <div class="text-xs text-gray-400">{{ getDayName(item.tanggal) }}</div>
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-700">{{ item.keterangan }}</td>
                  <td class="px-4 py-3 text-center">
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full"
                      :class="item.tipe === 'nasional' ? 'bg-red-50 text-red-600' : 'bg-orange-50 text-orange-600'">
                      {{ item.tipe === 'nasional' ? '🔴 Nasional' : '🟠 Kampus' }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <button @click="handleHapus(item)" class="w-7 h-7 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center mx-auto transition">
                      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0a1 1 0 00-1-1h-4a1 1 0 00-1 1m6 0H7"/>
                      </svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="px-5 py-2 border-t border-gray-100 bg-gray-50/30">
            <p class="text-xs text-gray-400">Total: <strong class="text-gray-600">{{ filteredLibur.length }}</strong> hari libur terdaftar untuk tahun {{ selectedYear }}</p>
          </div>
        </div>
      </div>
    </div>

    <modal-pop-up-confirm
      v-model="showConfirm"
      title="Hapus Hari Libur?"
      :description="`Apakah Anda yakin ingin menghapus hari libur '${deletingItem ? deletingItem.keterangan : ''}' pada tanggal ${deletingItem ? formatTanggal(deletingItem.tanggal) : ''}?`"
      @confirm="confirmHapus"
    />

    <!-- Success Modal -->
    <modal-pop-up-success
      v-model="showSuccess"
      :title="successData.title"
      :description="successData.description"
      @close-action="showSuccess = false"
    />
  </div>
</template>

<script>
import BreadcrumbBima     from '@/core/components/Breadcrumb.vue';
import ModalPopUpConfirm  from '@/core/components/ModalPopUpConfirm.vue';
import ModalPopUpSuccess  from '@/core/components/ModalPopUpSuccess.vue';
import TealDatePicker     from '@/core/components/TealDatePicker.vue';
import DISPATCHES         from '@/core/plugins/constants/dispatches.js';

export default {
  name: 'HariLibur',
  components: { BreadcrumbBima, ModalPopUpConfirm, ModalPopUpSuccess, TealDatePicker },
  data() {
    const currentYear = new Date().getFullYear();
    return {
      selectedYear: currentYear,
      years: [currentYear - 1, currentYear, currentYear + 1],
      search: '',
      form: { tanggal: '', keterangan: '', tipe: 'nasional' },
      formError: '',
      showConfirm: false,
      showSuccess: false,
      isSaving: false,
      deletingItem: null,
      successData: {
        title: 'Berhasil!',
        description: 'Data hari libur telah berhasil diproses.',
      },
      breadcrumbItems: [
        { text: 'Settings', link: '#' },
        { text: 'Hari Libur & Tanggal Merah', link: '/app/pengaturan-hari-libur' },
      ],
    };
  },
  computed: {
    hariLiburList() {
      return this.$store.state.settings?.hariLiburList || [];
    },
    filteredLibur() {
      return this.hariLiburList
        .filter(h => {
          const year = new Date(h.tanggal + 'T00:00:00').getFullYear();
          const matchYear = year === this.selectedYear;
          const matchSearch = !this.search || h.keterangan.toLowerCase().includes(this.search.toLowerCase());
          return matchYear && matchSearch;
        })
        .sort((a, b) => a.tanggal.localeCompare(b.tanggal));
    },
  },
  mounted() {
    this.fetchHariLibur();
  },
  methods: {
    async fetchHariLibur() {
      this.$store.commit('SET_LOADING', true);
      try {
        await this.$store.dispatch(DISPATCHES.GET_HARI_LIBUR, {
          size: 500, // Ambil banyak agar kalender terisi
        });
      } catch (error) {
        console.error('Gagal mengambil data hari libur:', error);
      } finally {
        this.$store.commit('SET_LOADING', false);
      }
    },
    async handleTambah() {
      this.formError = '';
      if (!this.form.tanggal) { this.formError = 'Tanggal wajib diisi.'; return; }
      if (!this.form.keterangan.trim()) { this.formError = 'Keterangan wajib diisi.'; return; }
      // Check duplicate
      const exists = this.hariLiburList.some(h => h.tanggal === this.form.tanggal);
      if (exists) { this.formError = 'Tanggal ini sudah terdaftar sebagai hari libur.'; return; }

      this.isSaving = true;
      this.$store.commit('SET_LOADING', true);
      try {
        await this.$store.dispatch(DISPATCHES.CREATE_HARI_LIBUR, { ...this.form });
        this.successData = {
          title: 'Berhasil Ditambahkan',
          description: `Hari libur '${this.form.keterangan}' pada tanggal ${this.formatTanggal(this.form.tanggal)} telah disimpan.`,
        };
        this.form = { tanggal: '', keterangan: '', tipe: 'nasional' };
        this.showSuccess = true;
        this.fetchHariLibur(); // Refresh list
      } catch (error) {
        this.formError = error.response?.data?.message || 'Gagal menambahkan hari libur.';
      } finally {
        this.isSaving = false;
        this.$store.commit('SET_LOADING', false);
      }
    },
    handleHapus(item) {
      this.deletingItem = item;
      this.showConfirm = true;
    },
    async confirmHapus() {
      if (this.deletingItem) {
        this.$store.commit('SET_LOADING', true);
        try {
          await this.$store.dispatch(DISPATCHES.DELETE_HARI_LIBUR, this.deletingItem.id);
          this.successData = {
            title: 'Berhasil Dihapus',
            description: `Hari libur '${this.deletingItem.keterangan}' telah dihapus dari sistem.`,
          };
          this.deletingItem = null;
          this.showSuccess = true;
          this.fetchHariLibur(); // Refresh list
        } catch (error) {
          console.error('Gagal menghapus hari libur:', error);
        } finally {
          this.$store.commit('SET_LOADING', false);
        }
      }
    },
    formatTanggal(d) {
      if (!d) return '';
      return new Date(d + 'T00:00:00').toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    },
    getDayName(d) {
      return new Date(d + 'T00:00:00').toLocaleDateString('id-ID', { weekday: 'long' });
    },
    monthName(m) {
      return new Date(this.selectedYear, m - 1, 1).toLocaleDateString('id-ID', { month: 'long' });
    },
    getDaysInMonth(month) {
      return new Date(this.selectedYear, month, 0).getDate();
    },
    getFirstDayOfMonth(month) {
      let day = new Date(this.selectedYear, month - 1, 1).getDay();
      return day === 0 ? 6 : day - 1; // Convert to Mon-start
    },
    getDayClass(month, day) {
      const dateStr = `${this.selectedYear}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
      const holiday = this.hariLiburList.find(h => h.tanggal === dateStr);
      const isWeekend = new Date(dateStr + 'T00:00:00').getDay() === 0;

      if (holiday && holiday.tipe === 'nasional') return 'bg-red-100 text-red-700 font-bold rounded';
      if (holiday && holiday.tipe === 'kampus') return 'bg-orange-100 text-orange-700 font-bold rounded';
      if (isWeekend) return 'text-red-400 font-medium';
      return 'text-gray-600';
    },
    getHolidayTooltip(month, day) {
      const dateStr = `${this.selectedYear}-${String(month).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
      const h = this.hariLiburList.find(h => h.tanggal === dateStr);
      return h ? `${h.keterangan} (${h.tipe})` : '';
    },
  },
};
</script>
