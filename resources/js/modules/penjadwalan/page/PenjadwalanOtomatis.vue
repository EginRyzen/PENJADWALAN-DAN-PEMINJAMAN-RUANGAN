<template>
  <div class="p-4 md:p-6 pb-24">
    <!-- Breadcrumb -->
    <breadcrumb-bima :items="breadcrumbItems" class="mb-5" />

    <!-- Page Title -->
    <div class="flex items-center gap-3 mb-6">
      <div class="p-2.5 bg-teal-500 rounded-xl shadow-lg shadow-teal-200">
        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
      </div>
      <div>
        <h1 class="text-xl font-black text-gray-800">Penjadwalan Ujian</h1>
        <p class="text-xs text-gray-500 mt-0.5">Generate & kelola jadwal ujian otomatis dengan CSP</p>
      </div>
    </div>

    <!-- Step 1: Filter Panel -->
    <filter-panel
      :is-generating="isGenerating"
      :hari-libur-list="hariLiburList"
      @generate="handleGenerate"
      @context-change="onContextChange"
    />

    <!-- Generating Skeleton -->
    <div v-if="isGenerating" class="space-y-4">
      <!-- Skeleton Stats -->
      <div class="grid grid-cols-3 gap-3">
        <div v-for="i in 3" :key="i" class="bg-white rounded-xl border border-gray-100 p-4 animate-pulse">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gray-100 rounded-xl"></div>
            <div class="space-y-2">
              <div class="w-10 h-5 bg-gray-100 rounded"></div>
              <div class="w-16 h-3 bg-gray-100 rounded"></div>
            </div>
          </div>
        </div>
      </div>
      <!-- Skeleton Table -->
      <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="h-14 bg-gray-50 border-b border-gray-100 animate-pulse"></div>
        <div v-for="i in 5" :key="i" class="h-14 border-b border-gray-50 px-4 flex items-center gap-4 animate-pulse">
          <div class="w-6 h-3 bg-gray-100 rounded"></div>
          <div class="flex-1 space-y-1">
            <div class="w-24 h-3 bg-gray-100 rounded"></div>
            <div class="w-40 h-2.5 bg-gray-100 rounded"></div>
          </div>
          <div class="w-20 h-3 bg-gray-100 rounded"></div>
          <div class="w-16 h-3 bg-gray-100 rounded"></div>
          <div class="w-16 h-6 bg-gray-100 rounded-full"></div>
        </div>
      </div>
      <p class="text-center text-sm text-teal-600 font-semibold animate-pulse py-2">
        ⏳ Sedang memproses CSP Engine — mohon tunggu...
      </p>
    </div>

    <!-- Results Section (after generate) -->
    <template v-if="!isGenerating && draftJadwal.length > 0">
      <!-- Prodi Tab Filter -->
      <prodi-tab-filter
        :active-tab="activeProdiTab"
        :prodi-tabs="prodiTabs"
        :total="draftJadwal.length"
        @change="setProdiTab"
      />

      <!-- Stats Row -->
      <stats-row :stats="stats" />

      <!-- Table: Desktop & Tablet (md+) -->
      <div class="hidden md:block">
        <jadwal-table :items="filteredJadwal" :ruangan-list="ruanganList" @edit="openEditModal" />
      </div>

      <!-- Card List: Mobile (<md) -->
      <div class="md:hidden">
        <jadwal-card :items="filteredJadwal" :ruangan-list="ruanganList" @edit="openEditModal" />
      </div>
    </template>

    <!-- Empty State (no generate yet) -->
    <div v-if="!isGenerating && draftJadwal.length === 0" class="bg-white rounded-xl border border-dashed border-gray-200 py-16 text-center">
      <div class="w-16 h-16 bg-teal-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
        </svg>
      </div>
      <h3 class="text-base font-bold text-gray-700 mb-1">Belum Ada Jadwal</h3>
      <p class="text-sm text-gray-400 max-w-xs mx-auto">Atur filter di atas, pilih tanggal mulai ujian, lalu klik <strong>Generate Jadwal Otomatis</strong>.</p>
    </div>

    <!-- Edit Modal -->
    <row-edit-modal
      v-model="showEditModal"
      :item="editingItem"
      :ruangan-list="ruanganList"
      :dosen-list="dosenList"
      :sks-duration="sksDuration"
      @save="handleRowSave"
    />

    <!-- Success Modal -->
    <modal-pop-up-success
      v-model="showSuccessModal"
      title="Jadwal Berhasil Disimpan"
      description="Jadwal ujian telah berhasil disimpan secara permanen ke dalam sistem."
      button-text="Selesai"
      @close-action="showSuccessModal = false"
    />

    <!-- Action Bar (sticky bottom, hanya muncul jika ada draft) -->
    <action-bar
      v-if="draftJadwal.length > 0 && !isGenerating"
      :stats="stats"
      :is-saving="isSaving"
      @save="handleSavePermanen"
      @reset="handleReset"
    />
  </div>
</template>

<script>
import BreadcrumbBima    from '@/core/components/Breadcrumb.vue';
import ModalPopUpSuccess from '@/core/components/ModalPopUpSuccess.vue';

import FilterPanel    from '../components/FilterPanel.vue';
import ProdiTabFilter from '../components/ProdiTabFilter.vue';
import StatsRow       from '../components/StatsRow.vue';
import JadwalTable    from '../components/JadwalTable.vue';
import JadwalCard     from '../components/JadwalCard.vue';
import RowEditModal   from '../components/RowEditModal.vue';
import ActionBar      from '../components/ActionBar.vue';

export default {
  name: 'PenjadwalanOtomatis',
  components: {
    BreadcrumbBima,
    ModalPopUpSuccess,
    FilterPanel,
    ProdiTabFilter,
    StatsRow,
    JadwalTable,
    JadwalCard,
    RowEditModal,
    ActionBar,
  },
  data() {
    return {
      showEditModal: false,
      showSuccessModal: false,
      editingItem: null,
      breadcrumbItems: [
        { text: 'Dashboard', link: '#' },
        { text: 'Penjadwalan', link: '#' },
        { text: 'Ujian Tengah Semester (UTS)', link: '/app/penjadwalan' },
      ],
    };
  },
  computed: {
    isGenerating()   { return this.$store.state.penjadwalan.isGenerating; },
    isSaving()       { return this.$store.state.penjadwalan.isSaving; },
    draftJadwal()    { return this.$store.state.penjadwalan.draftJadwal; },
    stats()          { return this.$store.state.penjadwalan.stats; },
    activeProdiTab() { return this.$store.state.penjadwalan.activeProdiTab; },
    ruanganList()    { return this.$store.state.penjadwalan.ruanganList; },
    dosenList()      { return this.$store.state.penjadwalan.dosenList; },
    hariLiburList()  { return this.$store.state.penjadwalan.hariLiburList; },
    sksDuration()    { return 50; }, // TODO: ambil dari settings store
    prodiTabs()      { return this.$store.getters['penjadwalan/prodiTabs']; },
    filteredJadwal() { return this.$store.getters['penjadwalan/filteredJadwal']; },
  },
  methods: {
    async handleGenerate() {
      await this.$store.dispatch('penjadwalan/generateJadwal');
    },
    onContextChange(ctx) {
      this.$store.commit('penjadwalan/SET_CONTEXT', ctx);
    },
    setProdiTab(id) {
      this.$store.commit('penjadwalan/SET_ACTIVE_PRODI_TAB', id);
    },
    openEditModal(item) {
      this.editingItem = { ...item };
      this.showEditModal = true;
    },
    handleRowSave(row) {
      this.$store.dispatch('penjadwalan/updateJadwalRow', row);
    },
    async handleSavePermanen() {
      await this.$store.dispatch('penjadwalan/saveJadwal');
      this.showSuccessModal = true;
    },
    handleReset() {
      this.$store.commit('penjadwalan/SET_DRAFT_JADWAL', []);
    },
  },
};
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.4s ease-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}
</style>
