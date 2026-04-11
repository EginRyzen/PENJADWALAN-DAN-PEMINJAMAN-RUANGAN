<template>
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <!-- Table Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 py-3 border-b border-gray-100 bg-gray-50/50">
      <div class="flex items-center gap-2">
        <h3 class="text-sm font-bold text-gray-800">Hasil Generate</h3>
        <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">{{ items.length }} Jadwal</span>
        <span class="text-xs font-bold px-2 py-0.5 rounded-full" :class="hasDraftStatus ? 'text-amber-600 bg-amber-50' : 'text-green-600 bg-green-50'">
          {{ hasDraftStatus ? '🟡 DRAFT' : '🟢 SIAP SIMPAN' }}
        </span>
      </div>
      <div class="flex items-center gap-2">
        <!-- Search -->
        <div class="relative">
          <app-input
            v-model="search"
            placeholder="Cari MK, ruangan, dosen..."
            label=""
            class="w-48"
          >
            <template #icon-left>
              <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </template>
          </app-input>
        </div>
        <!-- Filter Hari -->
        <select-auto-complete
          v-model="filterHari"
          :options="hariOptions"
          item-text="label"
          item-value="value"
          placeholder="Semua Hari"
          class="w-[120px]"
        />
        <!-- Filter Ruangan -->
        <select-auto-complete
          v-model="filterRuangan"
          :options="ruanganList"
          item-text="nama"
          item-value="nama"
          placeholder="Semua Ruangan"
          class="w-[160px]"
          @search="handleSearchRuangan"
        />
      </div>
    </div>

    <!-- Table Scroll Container -->
    <div class="overflow-x-auto">
      <table class="w-full text-left min-w-[900px]">
        <thead>
          <tr class="bg-white border-b-2 border-teal-400">
            <th class="px-3 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-10">No</th>
            <th class="px-3 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Kode & Nama MK</th>
            <th class="px-3 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Prodi / Kelas</th>
            <th class="px-3 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Hari & Tanggal</th>
            <th class="px-3 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Waktu</th>
            <th class="px-3 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Durasi</th>
            <th class="px-3 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Ruangan</th>
            <th class="px-3 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Peserta</th>
            <th class="px-3 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Dosen Pengawas</th>
            <th class="px-3 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Status</th>
            <th class="px-3 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-if="filteredItems.length === 0">
            <td colspan="11" class="px-4 py-10 text-center text-gray-400 text-sm">
              <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
              </svg>
              Belum ada jadwal. Klik "Generate Jadwal Otomatis" untuk memulai.
            </td>
          </tr>

          <tr
            v-for="(item, idx) in paginatedItems"
            :key="item.id"
            class="hover:bg-gray-50/60 transition-colors group"
            :class="rowBg(item.status)"
          >
            <!-- No -->
            <td class="px-3 py-3 text-xs text-gray-400">{{ startIndex + idx + 1 }}</td>

            <!-- Kode & Nama MK -->
            <td class="px-3 py-3">
              <div class="font-bold text-xs text-teal-600">{{ item.mk_kode }}</div>
              <div class="text-sm font-medium text-gray-800 leading-tight">{{ item.mk_nama }}</div>
              <div class="text-xs text-gray-400 mt-0.5">{{ item.sks }} SKS</div>
            </td>

            <!-- Prodi / Kelas -->
            <td class="px-3 py-3">
              <div class="text-xs font-bold text-gray-700">{{ item.prodi_kode }}</div>
              <div class="text-xs text-gray-400">Kelas {{ item.kelas }}</div>
            </td>

            <!-- Hari & Tanggal -->
            <td class="px-3 py-3">
              <div class="text-sm font-semibold text-gray-800">{{ item.hari }}</div>
              <div class="text-xs text-gray-400">{{ formatTanggal(item.tanggal) }}</div>
            </td>

            <!-- Waktu -->
            <td class="px-3 py-3 text-center">
              <div class="text-sm font-bold text-gray-800">{{ item.jam_mulai }}</div>
              <div class="text-xs text-gray-400">s/d {{ item.jam_selesai }}</div>
            </td>

            <!-- Durasi -->
            <td class="px-3 py-3 text-center">
              <span class="text-xs font-semibold text-gray-600 bg-gray-100 px-2 py-0.5 rounded-full">{{ item.durasi }} mnt</span>
            </td>

            <!-- Ruangan -->
            <td class="px-3 py-3">
              <div class="text-sm font-medium text-gray-800">{{ item.ruangan_nama }}</div>
              <div class="text-xs text-gray-400">Kap. {{ item.kapasitas }}</div>
            </td>

            <!-- Peserta -->
            <td class="px-3 py-3 text-center">
              <span
                class="text-xs font-bold px-2 py-1 rounded-full"
                :class="item.jumlah_peserta / item.kapasitas > 0.9 ? 'bg-orange-50 text-orange-600' : 'bg-gray-50 text-gray-600'"
              >{{ item.jumlah_peserta }}/{{ item.kapasitas }}</span>
            </td>

            <!-- Dosen -->
            <td class="px-3 py-3">
              <div class="text-xs text-gray-700 max-w-[140px] leading-tight">{{ item.dosen_nama }}</div>
            </td>

            <!-- Status Badge -->
            <td class="px-3 py-3 text-center">
              <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full whitespace-nowrap" :class="statusBadge(item.status)">
                <span class="w-1.5 h-1.5 rounded-full" :class="statusDot(item.status)"></span>
                {{ statusLabel(item.status) }}
              </span>
              <div v-if="item.status === 'conflict' && item.conflict_reason" class="text-xs text-red-500 mt-1 max-w-[120px] text-left leading-tight">
                {{ item.conflict_reason }}
              </div>
            </td>

            <!-- Aksi -->
            <td class="px-3 py-3 text-center">
              <button
                @click="$emit('edit', item)"
                class="w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-150 mx-auto"
                :class="item.status === 'conflict'
                  ? 'bg-red-50 text-red-500 hover:bg-red-100'
                  : 'bg-teal-50 text-teal-600 hover:bg-teal-100'"
                title="Edit jadwal ini"
              >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="filteredItems.length > 0" class="px-4 py-3 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-2">
      <span class="text-xs text-gray-500">
        Menampilkan {{ startIndex + 1 }}–{{ Math.min(startIndex + pageSize, filteredItems.length) }} dari {{ filteredItems.length }} jadwal
      </span>
      <div class="flex items-center gap-1">
        <button @click="page--" :disabled="page <= 1" class="w-7 h-7 flex items-center justify-center rounded-lg border text-xs transition" :class="page <= 1 ? 'border-gray-100 text-gray-300 cursor-not-allowed' : 'border-gray-200 text-gray-600 hover:border-teal-400 hover:text-teal-600'">‹</button>
        <button
          v-for="p in totalPages" :key="p"
          @click="page = p"
          class="w-7 h-7 flex items-center justify-center rounded-lg border text-xs font-medium transition"
          :class="page === p ? 'bg-teal-500 border-teal-500 text-white' : 'border-gray-200 text-gray-600 hover:border-teal-400 hover:text-teal-600'"
        >{{ p }}</button>
        <button @click="page++" :disabled="page >= totalPages" class="w-7 h-7 flex items-center justify-center rounded-lg border text-xs transition" :class="page >= totalPages ? 'border-gray-100 text-gray-300 cursor-not-allowed' : 'border-gray-200 text-gray-600 hover:border-teal-400 hover:text-teal-600'">›</button>
      </div>
      <select v-model="pageSize" class="text-xs border border-gray-200 rounded-lg px-2 py-1 focus:outline-none text-gray-600 bg-white">
        <option :value="10">10 / halaman</option>
        <option :value="20">20 / halaman</option>
        <option :value="50">50 / halaman</option>
      </select>
    </div>
  </div>
</template>

<script>
import AppInput from "@/core/components/AppInput.vue";
import SelectAutoComplete from "@/core/components/SelectAutoComplete.vue";
import DISPATCH from "@/core/plugins/constants/dispatches";

export default {
  name: 'JadwalTable',
  components: { AppInput, SelectAutoComplete },
  props: {
    items: { type: Array, default: () => [] },
    ruanganList: { type: Array, default: () => [] },
  },
  emits: ['edit'],
  data() {
    return {
      search: '',
      filterHari: '',
      filterRuangan: '',
      page: 1,
      pageSize: 10,
      hariOptions: [
        { label: 'Senin', value: 'Senin' },
        { label: 'Selasa', value: 'Selasa' },
        { label: 'Rabu', value: 'Rabu' },
        { label: 'Kamis', value: 'Kamis' },
        { label: 'Jumat', value: 'Jumat' },
        { label: 'Sabtu', value: 'Sabtu' },
      ],
    };
  },
  computed: {
    hasDraftStatus() {
      return this.items.some(i => i.status === 'conflict');
    },
    filteredItems() {
      let res = this.items;
      if (this.search) {
        const q = this.search.toLowerCase();
        res = res.filter(i =>
          i.mk_nama.toLowerCase().includes(q) ||
          i.mk_kode.toLowerCase().includes(q) ||
          i.ruangan_nama.toLowerCase().includes(q) ||
          i.dosen_nama.toLowerCase().includes(q)
        );
      }
      if (this.filterHari) {
        res = res.filter(i => i.hari === this.filterHari);
      }
      if (this.filterRuangan) {
        res = res.filter(i => i.ruangan_nama === this.filterRuangan);
      }
      return res;
    },
    totalPages() {
      return Math.max(1, Math.ceil(this.filteredItems.length / this.pageSize));
    },
    startIndex() {
      return (this.page - 1) * this.pageSize;
    },
    paginatedItems() {
      return this.filteredItems.slice(this.startIndex, this.startIndex + this.pageSize);
    },
  },
  mounted() {
    this.fetchRuangan();
  },
  watch: {
    filteredItems() { this.page = 1; },
  },
  methods: {
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
    rowBg(status) {
      return {
        'border-l-4 border-l-red-400 bg-red-50/20': status === 'conflict',
        'border-l-4 border-l-amber-400 bg-amber-50/10': status === 'edited',
        'border-l-4 border-l-green-400': status === 'ok',
      };
    },
    statusBadge(status) {
      return {
        'bg-green-50 text-green-700': status === 'ok',
        'bg-red-50 text-red-600': status === 'conflict',
        'bg-amber-50 text-amber-600': status === 'edited',
      };
    },
    statusDot(status) {
      return {
        'bg-green-500': status === 'ok',
        'bg-red-500': status === 'conflict',
        'bg-amber-500': status === 'edited',
      };
    },
    statusLabel(status) {
      return { ok: '✓ OK', conflict: '⚠ Bentrok', edited: '✎ Diedit' }[status] || status;
    },
    formatTanggal(d) {
      return new Date(d + 'T00:00:00').toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    },
  },
};
</script>
