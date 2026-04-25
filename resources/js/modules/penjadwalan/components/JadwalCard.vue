<template>
  <div class="space-y-3">
    <!-- Search Bar (mobile) -->
    <div class="flex items-center gap-2">
      <div class="relative flex-1">
        <app-input
          v-model="search"
          placeholder="Cari mata kuliah, dosen, ruangan..."
          label=""
        >
          <template #icon-left>
            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </template>
        </app-input>
      </div>
      
      <!-- Filter Trigger -->
      <button 
        @click="showFilterModal = true"
        class="p-2.5 rounded-lg border transition-all flex items-center gap-1.5 font-bold text-xs"
        :class="hasActiveFilters ? 'bg-teal-50 border-teal-200 text-teal-600' : 'bg-white border-gray-200 text-gray-500'"
      >
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
        </svg>
        <span v-if="activeFilterCount > 0">{{ activeFilterCount }}</span>
      </button>

      <button 
        v-if="hasActiveFilters || search" 
        @click="resetFilters"
        class="p-2.5 bg-gray-50 text-gray-400 rounded-lg hover:bg-red-50 hover:text-red-500 transition-colors border border-gray-100"
        title="Reset Filter"
      >
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
      </button>
    </div>

    <!-- Filter Modal (Mobile) -->
    <jadwal-filter-modal
      v-model="showFilterModal"
      :filters="activeFilters"
      :prodi-list="prodiList"
      :kelas-list="kelasList"
      :ruangan-list="ruanganList"
      @apply="handleApplyFilters"
      @search-ruangan="handleSearchRuangan"
    />

    <!-- Empty State -->
    <div v-if="filteredItems.length === 0" class="bg-white rounded-xl border border-gray-100 py-12 text-center text-gray-400">
      <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
      </svg>
      <p class="text-sm font-medium">Belum ada jadwal</p>
      <p class="text-xs mt-1">Generate jadwal terlebih dahulu</p>
    </div>

    <!-- Card List -->
    <div
      v-for="item in filteredItems"
      :key="item.id"
      class="bg-white rounded-xl border shadow-sm overflow-hidden transition-all hover:shadow-md"
      :class="cardBorder(item.status)"
    >
      <!-- Card Header -->
      <div class="flex items-center justify-between px-4 py-2.5 border-b border-gray-50"
           :class="cardHeaderBg(item.status)">
        <div class="flex items-center gap-2">
          <span class="text-xs font-bold text-gray-400">{{ item.id }}</span>
          <span class="text-xs font-bold px-2 py-0.5 rounded-full" :class="statusBadge(item.status)">
            {{ statusLabel(item.status) }}
          </span>
        </div>
        <button
          @click="$emit('edit', item)"
          class="w-7 h-7 rounded-lg flex items-center justify-center transition-all"
          :class="item.status === 'conflict' ? 'bg-red-100 text-red-500' : 'bg-teal-100 text-teal-600'"
        >
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
          </svg>
        </button>
      </div>

      <!-- Card Body -->
      <div class="px-4 py-3">
        <!-- MK Info -->
        <div class="mb-2">
          <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-teal-600">{{ item.mk_kode }}</span>
            <span class="text-xs text-gray-400">|</span>
            <span class="text-xs text-gray-500">{{ item.sks }} SKS</span>
          </div>
          <h4 class="text-sm font-bold text-gray-800 leading-tight">{{ item.mk_nama }}</h4>
          <p class="text-xs text-teal-600 font-medium">{{ item.prodi_kode }} / Kelas {{ item.kelas }}</p>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-2 gap-x-4 gap-y-1.5">
          <div class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="text-xs text-gray-600">{{ item.hari }}, {{ formatTanggal(item.tanggal) }}</span>
          </div>
          <div class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span class="text-xs text-gray-600">{{ item.ruangan_nama }} (Kap. {{ item.kapasitas }})</span>
          </div>
          <div class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-xs font-semibold text-gray-700">{{ item.jam_mulai }} – {{ item.jam_selesai }} <span class="text-gray-400 font-normal">({{ item.durasi }} mnt)</span></span>
          </div>
          <div class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="text-xs text-gray-600">{{ item.jumlah_peserta }} / {{ item.kapasitas }} Peserta</span>
          </div>
        </div>

        <!-- Dosen -->
        <div class="mt-2 flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
          <span class="text-xs text-gray-600">{{ item.dosen_nama }}</span>
        </div>

        <!-- Conflict Reason -->
        <div v-if="item.status === 'conflict' && item.conflict_reason" class="mt-2 flex items-start gap-1.5 bg-red-50 rounded-lg px-2.5 py-2">
          <svg class="w-3.5 h-3.5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
          <span class="text-xs text-red-600 font-medium">{{ item.conflict_reason }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import AppInput from "@/core/components/AppInput.vue";
import SelectAutoComplete from "@/core/components/SelectAutoComplete.vue";
import JadwalFilterModal from "./JadwalFilterModal.vue";
import DISPATCH from "@/core/plugins/constants/dispatches";

export default {
  name: 'JadwalCard',
  components: { AppInput, SelectAutoComplete, JadwalFilterModal },
  props: {
    items: { type: Array, default: () => [] },
    ruanganList: { type: Array, default: () => [] },
    prodiList: { type: Array, default: () => [] },
    kelasList: { type: Array, default: () => [] },
  },
  emits: ['edit'],
  data() {
    return {
      search: '',
      activeFilters: {
        prodi: '',
        kelas: '',
        hari: '',
        ruangan: '',
      },
      showFilterModal: false,
    };
  },
  computed: {
    hasActiveFilters() {
      return this.activeFilterCount > 0;
    },
    activeFilterCount() {
      return Object.values(this.activeFilters).filter(v => !!v).length;
    },
    filteredItems() {
      let res = this.items;

      if (this.activeFilters.prodi) {
        res = res.filter(i => i.prodi_id == this.activeFilters.prodi);
      }
      if (this.activeFilters.kelas) {
        res = res.filter(i => i.kelas_id == this.activeFilters.kelas);
      }

      if (this.search) {
        const q = this.search.toLowerCase();
        res = res.filter(i =>
          i.mk_nama.toLowerCase().includes(q) ||
          i.mk_kode.toLowerCase().includes(q) ||
          i.ruangan_nama.toLowerCase().includes(q) ||
          i.dosen_nama.toLowerCase().includes(q)
        );
      }
      if (this.activeFilters.hari) {
        res = res.filter(i => i.hari === this.activeFilters.hari);
      }
      if (this.activeFilters.ruangan) {
        res = res.filter(i => i.ruangan_id == this.activeFilters.ruangan);
      }
      return res;
    },
  },
  mounted() {
    this.fetchRuangan();
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
    handleApplyFilters(filters) {
      this.activeFilters = { ...filters };
    },
    resetFilters() {
      this.activeFilters = {
        prodi: '',
        kelas: '',
        hari: '',
        ruangan: '',
      };
      this.search = '';
    },
    cardBorder(status) {
      return {
        'border-l-4 border-l-red-400 border-red-100': status === 'conflict',
        'border-l-4 border-l-amber-400 border-amber-100': status === 'edited',
        'border-l-4 border-l-green-400 border-gray-100': status === 'ok',
      };
    },
    cardHeaderBg(status) {
      return {
        'bg-red-50/50': status === 'conflict',
        'bg-amber-50/50': status === 'edited',
        'bg-gray-50/50': status === 'ok',
      };
    },
    statusBadge(status) {
      return {
        'bg-green-100 text-green-700': status === 'ok',
        'bg-red-100 text-red-600': status === 'conflict',
        'bg-amber-100 text-amber-600': status === 'edited',
      };
    },
    statusLabel(status) {
      return { ok: '✓ OK', conflict: '⚠ Bentrok', edited: '✎ Diedit' }[status] || status;
    },
    formatTanggal(d) {
      return new Date(d + 'T00:00:00').toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
    },
  },
};
</script>
