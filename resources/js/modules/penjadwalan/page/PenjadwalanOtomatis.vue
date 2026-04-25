<template>
  <div class="p-4 md:p-6 pb-24">
    <!-- Breadcrumb (Hidden on Mobile) -->
    <breadcrumb-bima :items="breadcrumbItems" class="hidden md:block mb-5" />

    <!-- Page Title -->
    <div class="flex items-center gap-3 mb-6">
      <div class="p-2.5 bg-teal-500 rounded-xl shadow-lg shadow-teal-200">
        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
      </div>
      <div>
        <h1 class="text-xl font-black text-gray-800">Penjadwalan Ujian</h1>
        <p class="text-xs text-gray-500 mt-0.5">Generate &amp; kelola jadwal ujian otomatis dengan CSP</p>
      </div>
    </div>

    <!-- Step 1: Filter Panel -->
    <filter-panel
      :is-generating="isGenerating"
      :hari-libur-list="hariLiburList"
      :periode-list="periodeList"
      :prodi-list="programStudiList"
      :kelas-list="kelasList"
      :allowed-days="allowedDays"
      @generate="handleGenerate"
      @context-change="onContextChange"
      @periode-change="onPeriodeChange"
    />

    <!-- Generating Skeleton -->
    <div v-if="isGenerating" class="space-y-4">
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
      <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="h-14 bg-gray-50 border-b border-gray-100 animate-pulse"></div>
        <div v-for="i in 5" :key="i" class="h-14 border-b border-gray-50 px-4 flex items-center gap-4 animate-pulse">
          <div class="w-6 h-3 bg-gray-100 rounded"></div>
          <div class="flex-1 space-y-1">
            <div class="w-24 h-3 bg-gray-100 rounded"></div>
            <div class="w-40 h-2.5 bg-gray-100 rounded"></div>
          </div>
          <div class="w-20 h-3 bg-gray-100 rounded"></div>
          <div class="w-16 h-6 bg-gray-100 rounded-full"></div>
        </div>
      </div>
      <p class="text-center text-sm text-teal-600 font-semibold animate-pulse py-2">
        ⏳ Sedang memproses CSP Engine — mohon tunggu...
      </p>
    </div>

    <!-- Results Section (after generate) -->
    <template v-if="!isGenerating && draftJadwal.length > 0">
      <!-- Exam Date Range Info -->
      <div class="mb-4 bg-teal-50 border border-teal-100 rounded-xl px-4 py-3 flex items-center gap-3">
        <div class="p-2 bg-teal-500 rounded-lg shadow-sm">
          <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
        </div>
        <div>
          <h4 class="text-xs font-bold text-teal-800 uppercase tracking-wider">Rentang Pelaksanaan Ujian</h4>
          <p class="text-sm font-black text-teal-900">
            {{ formatFullDate(dateRange.start) }} <span class="font-normal mx-1">s/d</span> {{ formatFullDate(dateRange.end) }}
          </p>
        </div>
        <div class="ml-auto text-right hidden sm:block">
          <span class="text-[10px] font-bold text-teal-600 bg-white px-2 py-1 rounded-md border border-teal-200">
            {{ totalDays }} HARI KERJA
          </span>
        </div>
      </div>

      <!-- Stats Row -->
      <stats-row :stats="stats" @filter-status="handleFilterStatus" />

      <!-- Table: Desktop & Tablet (md+) -->
      <div class="hidden md:block">
        <jadwal-table
          ref="tableRef"
          :items="filteredJadwal"
          :ruangan-list="ruanganList"
          :prodi-list="programStudiList"
          :kelas-list="kelasList"
          @edit="openEditModal"
        />
      </div>

      <!-- Card List: Mobile (<md) -->
      <div class="md:hidden">
        <jadwal-card
          :items="filteredJadwal"
          :ruangan-list="ruanganList"
          :prodi-list="programStudiList"
          :kelas-list="kelasList"
          @edit="openEditModal"
        />
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
      <p class="text-sm text-gray-400 max-w-xs mx-auto">Atur filter di atas, pilih periode &amp; tanggal mulai ujian, lalu klik <strong>Generate Jadwal Otomatis</strong>.</p>
    </div>

    <!-- Edit Modal -->
    <row-edit-modal
      v-model="showEditModal"
      :item="editingItem"
      :ruangan-list="ruanganList"
      :dosen-list="dosenList"
      :sks-duration="sksDuration"
      :allowed-days="allowedDays"
      :disabled-dates="disabledDates"
      @save="handleRowSave"
    />

    <!-- Modal: Konfirmasi Simpan Permanen -->
    <konfirmasi-permanen-modal
      v-model="showKonfirmasiPermanen"
      :total-jadwal="draftJadwal.length"
      :tipe="context.type"
      :periode-name="selectedPeriodeName"
      @confirm="handleSavePermanen"
    />

    <!-- Modal: Konfirmasi Regenerate (ada draft lama) -->
    <regenerate-confirm-modal
      v-model="showRegenerateConfirm"
      :draft-count="existingDraftMeta.count"
      :draft-saved-at="existingDraftMeta.saved_at"
      @generate-ulang="doGenerateUlang"
      @lanjutkan-draft="doLanjutkanDraft"
    />

    <!-- Modal Sukses Draft -->
    <modal-pop-up-success
      v-model="showSuccessModalDraft"
      title="Draft Berhasil Disimpan"
      description="Draft jadwal ujian tersimpan. Anda dapat melanjutkan editing kapan saja."
      button-text="Lanjutkan"
      @close-action="showSuccessModalDraft = false"
    />

    <!-- Modal Sukses Permanen -->
    <modal-pop-up-success
      v-model="showSuccessModal"
      title="Jadwal Berhasil Disimpan Permanen"
      description="Jadwal ujian telah dikunci dan notifikasi email telah dikirim ke semua dosen pengawas."
      button-text="Selesai"
      @close-action="showSuccessModal = false"
    />

    <!-- Action Bar (sticky bottom) -->
    <action-bar
      v-if="draftJadwal.length > 0 && !isGenerating"
      :stats="stats"
      :is-saving="isSaving"
      :is-saving-draft="isSavingDraft"
      :last-draft-saved-at="lastDraftSavedAt"
      @save="openKonfirmasiPermanen"
      @save-draft="handleSaveDraft"
      @reset="handleReset"
    />
  </div>
</template>

<script>
import BreadcrumbBima        from '@/core/components/Breadcrumb.vue';
import ModalPopUpSuccess     from '@/core/components/ModalPopUpSuccess.vue';
import DISPATCH              from '@/core/plugins/constants/dispatches';

import FilterPanel           from '../components/FilterPanel.vue';
import ProdiTabFilter        from '../components/ProdiTabFilter.vue';
import StatsRow              from '../components/StatsRow.vue';
import JadwalTable           from '../components/JadwalTable.vue';
import JadwalCard            from '../components/JadwalCard.vue';
import RowEditModal          from '../components/RowEditModal.vue';
import ActionBar             from '../components/ActionBar.vue';
import KonfirmasiPermanenModal  from '../components/KonfirmasiPermanenModal.vue';
import RegenerateConfirmModal   from '../components/RegenerateConfirmModal.vue';

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
    KonfirmasiPermanenModal,
    RegenerateConfirmModal,
  },
  data() {
    return {
      showEditModal:          false,
      showSuccessModal:       false,
      showSuccessModalDraft:  false,
      showKonfirmasiPermanen: false,
      showRegenerateConfirm:  false,
      editingItem:            null,
      existingDraftMeta:      { count: 0, saved_at: null }, // info draft lama
      context:                { type: 'uas', start_date: '', periode_id: '' },
      breadcrumbItems: [
        { text: 'Dashboard', link: '#' },
        { text: 'Penjadwalan', link: '#' },
        { text: 'Penjadwalan Otomatis', link: '/app/penjadwalan' },
      ],
    };
  },
  computed: {
    isGenerating()      { return this.$store.state.penjadwalan.isGenerating; },
    isSaving()          { return this.$store.state.penjadwalan.isSaving; },
    isSavingDraft()     { return this.$store.state.penjadwalan.isSavingDraft; },
    draftJadwal()       { return this.$store.state.penjadwalan.draftJadwal; },
    stats()             { return this.$store.state.penjadwalan.stats; },
    activeProdiTab()    { return this.$store.state.penjadwalan.activeProdiTab; },
    ruanganList()       { return this.$store.state.penjadwalan.ruanganList; },
    dosenList()         { return this.$store.state.penjadwalan.dosenList; },
    hariLiburList()     { return this.$store.state.penjadwalan.hariLiburList; },
    periodeList()       { return this.$store.state.penjadwalan.periodeList; },
    programStudiList()  { return this.$store.state.masterData.programStudiList; },
    kelasList()         { return this.$store.state.settings.kelasList; },
    lastDraftSavedAt()  { return this.$store.state.penjadwalan.lastDraftSavedAt; },
    operasionalScheduleList() { return this.$store.state.settings.operasionalScheduleList; },
    allowedDays() {
      // Ambil nama hari yang aktif dari operasionalScheduleList
      const mapping = {
        'monday': 'Senin', 'tuesday': 'Selasa', 'wednesday': 'Rabu', 'thursday': 'Kamis', 'friday': 'Jumat', 'saturday': 'Sabtu', 'sunday': 'Minggu',
        'senin': 'Senin', 'selasa': 'Selasa', 'rabu': 'Rabu', 'kamis': 'Kamis', 'jumat': 'Jumat', 'sabtu': 'Sabtu', 'minggu': 'Minggu'
      };

      return this.operasionalScheduleList
        .filter(s => {
          const status = (s.status || '').toLowerCase();
          return status === 'aktif' || status === 'active' || s.is_active || s.is_open;
        })
        .map(s => {
          const rawDay = (s.day || s.hari || '').toLowerCase();
          return mapping[rawDay] || rawDay.charAt(0).toUpperCase() + rawDay.slice(1);
        });
    },
    sksDuration()       { return 50; },
    prodiTabs()         { return this.$store.getters['penjadwalan/prodiTabs']; },
    filteredJadwal()    { return this.$store.getters['penjadwalan/filteredJadwal']; },
    selectedPeriodeName() {
      const p = this.periodeList.find(p => p.id === this.context.periode_id);
      return p ? p.nama : '-';
    },
    dateRange() {
      if (this.draftJadwal.length === 0) return { start: '', end: '' };
      const dates = this.draftJadwal.filter(j => j.tanggal).map(j => j.tanggal).sort();
      if (dates.length === 0) return { start: '', end: '' };
      return { start: dates[0], end: dates[dates.length - 1] };
    },
    totalDays() {
      if (!this.dateRange.start || !this.dateRange.end) return 0;
      const start = new Date(this.dateRange.start);
      const end   = new Date(this.dateRange.end);
      return Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
    },
  },
  mounted() {
    // Muat daftar periode saat halaman pertama dibuka
    this.$store.dispatch('penjadwalan/getPeriode');
    this.$store.dispatch(DISPATCH.GET_PROGRAM_STUDI, { all: true });
    this.$store.dispatch(DISPATCH.GET_KELAS, { all: true });
    
    // Fetch operasional khusus untuk ujian (UAS secara default)
    this.fetchOperasionalSchedule();
    
    this.$store.dispatch('penjadwalan/getDosen', { size: 1000 });
  },
  methods: {
    async fetchOperasionalSchedule() {
      this.$store.commit('SET_LOADING', true);
      this.$store.commit('SET_LOADING_MESSAGE', 'Singkronisasi jadwal operasional...');
      try {
        // Kita pakai type dari context, mapping pembelajaran -> pelajaran untuk API
        const apiType = this.context.type === 'pembelajaran' ? 'pelajaran' : this.context.type;
        await this.$store.dispatch(DISPATCH.GET_OPERASIONAL_SCHEDULE, { type: apiType });
      } catch (e) {
        console.error('Gagal fetch operasional schedule:', e);
      } finally {
        this.$store.commit('SET_LOADING', false);
      }
    },
    onContextChange(newContext) {
      const oldType = this.context.type;
      this.context = { ...newContext };
      
      // Jika tipe ujian berubah, re-fetch operasional schedule-nya
      if (oldType !== this.context.type) {
        this.fetchOperasionalSchedule();
      }
      this.$store.commit('penjadwalan/SET_CONTEXT', newContext);
    },

    async onPeriodeChange(periodeId) {
      if (!periodeId) return;
      // Muat hari libur untuk periode yang dipilih
      await this.$store.dispatch(DISPATCH.GET_HARI_LIBUR, { periode_id: periodeId });
    },

    // ── Klik tombol Generate ──────────────────────────────────────
    async handleGenerate() {
      if (!this.context.periode_id || !this.context.start_date) return;

      // Cek apakah ada draft lama untuk periode + tipe ini
      const existing = await this.$store.dispatch(DISPATCH.GET_JADWAL_DRAFT);

      if (existing && existing.exists) {
        // Ada draft lama → tampilkan modal pilihan
        this.existingDraftMeta = { count: existing.count, saved_at: existing.saved_at };
        this.showRegenerateConfirm = true;
      } else {
        // Tidak ada draft lama → langsung generate
        await this.doGenerateUlang();
      }
    },

    // ── Generate Ulang (hapus draft lama dulu) ───────────────────
    async doGenerateUlang() {
      this.$store.commit('SET_LOADING', true);
      this.$store.commit('SET_LOADING_MESSAGE', 'Sedang memproses algoritma CSP...');
      try {
        // Hapus draft lama jika ada
        await this.$store.dispatch(DISPATCH.DELETE_JADWAL_DRAFT);
        await this.$store.dispatch(DISPATCH.GENERATE_JADWAL);
      } catch (e) {
        console.error('Gagal generate:', e);
      } finally {
        this.$store.commit('SET_LOADING', false);
      }
    },

    // ── Lanjutkan Draft Lama ─────────────────────────────────────
    async doLanjutkanDraft() {
      this.showRegenerateConfirm = false;
      const existing = await this.$store.dispatch(DISPATCH.GET_JADWAL_DRAFT);
      if (existing && existing.items) {
        this.$store.dispatch('penjadwalan/loadDraftItems', existing.items);
      }
    },

    setProdiTab(id) {
      this.$store.commit('penjadwalan/SET_ACTIVE_PRODI_TAB', id);
    },

    openEditModal(item) {
      this.editingItem  = { ...item };
      this.showEditModal = true;
    },

    async handleRowSave(row) {
      this.$store.commit('SET_LOADING', true);
      this.$store.commit('SET_LOADING_MESSAGE', 'Memvalidasi bentrok jadwal...');
      try {
        // Simpan perubahannya dulu
        await this.$store.dispatch(DISPATCH.UPDATE_JADWAL_ROW, row);
        
        // Lalu panggil generate (validasi) dengan membawa seluruh draft saat ini
        // Agar backend bisa mengecek apakah perubahan ini menyebabkan bentrok dengan item lain
        await this.$store.dispatch(DISPATCH.GENERATE_JADWAL, this.draftJadwal);
      } catch (e) {
        console.error('Gagal validasi bentrok:', e);
      } finally {
        this.$store.commit('SET_LOADING', false);
      }
    },

    // ── Simpan Draft ─────────────────────────────────────────────
    async handleSaveDraft() {
      this.$store.commit('SET_LOADING', true);
      this.$store.commit('SET_LOADING_MESSAGE', 'Menyimpan draft jadwal...');
      try {
        await this.$store.dispatch(DISPATCH.SAVE_JADWAL_DRAFT);
        this.showSuccessModalDraft = true;
      } catch (e) {
        console.error('Gagal simpan draft:', e);
      } finally {
        this.$store.commit('SET_LOADING', false);
      }
    },

    // ── Buka modal konfirmasi permanen ───────────────────────────
    openKonfirmasiPermanen() {
      this.showKonfirmasiPermanen = true;
    },

    // ── Simpan Permanen (dipanggil setelah konfirmasi) ───────────
    async handleSavePermanen() {
      this.$store.commit('SET_LOADING', true);
      this.$store.commit('SET_LOADING_MESSAGE', 'Sedang mengunci jadwal & mengirim notifikasi...');
      try {
        await this.$store.dispatch(DISPATCH.SAVE_JADWAL_PERMANEN);
        this.showSuccessModal = true;
      } catch (e) {
        console.error('Gagal simpan permanen:', e);
      } finally {
        this.$store.commit('SET_LOADING', false);
      }
    },

    // ── Reset & bersihkan tabel ──────────────────────────────────
    handleReset() {
      this.$store.commit('penjadwalan/SET_DRAFT_JADWAL', []);
      this.$store.commit('penjadwalan/SET_DRAFT_META', { savedAt: null });
    },
    handleFilterStatus(status) {
      if (this.$refs.tableRef) {
        this.$refs.tableRef.setStatusFilter(status);
      }
    },
    formatFullDate(d) {
      if (!d) return '-';
      return new Date(d + 'T00:00:00').toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
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
