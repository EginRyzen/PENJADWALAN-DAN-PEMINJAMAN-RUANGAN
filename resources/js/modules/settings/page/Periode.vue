<template>
  <div class="p-6">
    <!-- Breadcrumb -->
    <breadcrumb-bima :items="breadcrumbItems" class="mb-6" />

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
      <div class="flex items-center gap-3">
        <div class="p-3 bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl shadow-lg shadow-teal-100">
          <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>
        <div>
          <h1 class="text-2xl font-black text-gray-800 tracking-tight">Manajemen Periode Akademik</h1>
          <p class="text-sm text-gray-500 font-medium">Kelola rentang waktu semester dan tahun ajaran aktif</p>
        </div>
      </div>
      
      <button-app
        type="primary"
        color="teal"
        class="bg-teal-500 hover:bg-teal-600 text-white font-bold px-6 py-3 rounded-xl shadow-lg shadow-teal-100 transition-all duration-300 flex items-center gap-2 group"
        @click="handleTambah"
      >
        <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Periode Baru
      </button-app>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in">
      <!-- Table Filter Bar -->
      <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-2">
          <span class="text-sm font-bold text-gray-700">Daftar Periode</span>
          <span class="px-2 py-0.5 bg-teal-50 text-teal-600 text-xs font-bold rounded-full border border-teal-100">{{ filteredItems.length }} Data</span>
        </div>
        
        <div class="relative w-full sm:w-64">
          <app-input
            v-model="search"
            placeholder="Cari nama periode..."
            label=""
          >
            <template #icon-left>
              <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </template>
          </app-input>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead>
            <tr class="bg-white border-b-2 border-teal-500">
              <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">No</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Nama Periode</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Rentang Waktu</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-center">Status</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="filteredItems.length === 0">
              <td colspan="5" class="px-6 py-20 text-center">
                <div class="flex flex-col items-center">
                  <div class="p-4 bg-gray-50 rounded-full mb-4">
                    <svg class="w-12 h-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                  </div>
                  <p class="text-gray-400 font-bold">Data tidak ditemukan</p>
                  <p class="text-xs text-gray-300">Coba kata kunci pencarian lain</p>
                </div>
              </td>
            </tr>
            
            <tr
              v-for="(item, index) in filteredItems"
              :key="item.id"
              class="hover:bg-gray-50/50 transition-colors group"
            >
              <td class="px-6 py-4">
                <span class="text-sm font-bold text-gray-400 group-hover:text-teal-500 transition-colors">{{ index + 1 }}</span>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm font-black text-gray-800 leading-tight">{{ item.nama }}</div>
                <div class="text-[10px] font-bold text-teal-600 uppercase tracking-tighter mt-0.5">Akademik Terpadu</div>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <div class="text-sm font-semibold text-gray-700">{{ formatTanggal(item.start_date) }}</div>
                  <svg class="w-3 h-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                  </svg>
                  <div class="text-sm font-semibold text-gray-700">{{ formatTanggal(item.end_date) }}</div>
                </div>
              </td>
              <td class="px-6 py-4 text-center">
                <span
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border transition-all duration-300"
                  :class="item.status === 'Aktif'
                    ? 'bg-teal-50 text-teal-700 border-teal-100 shadow-sm shadow-teal-50'
                    : 'bg-gray-50 text-gray-400 border-gray-100'"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="item.status === 'Aktif' ? 'bg-teal-500 animate-pulse' : 'bg-gray-300'"></span>
                  {{ item.status }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                  <button
                    @click="handleEdit(item)"
                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-amber-50 text-amber-500 hover:bg-amber-100 transition-colors"
                    title="Edit Data"
                  >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15.000a2 2 0 01-1.061.553l-3.500.500.500-3.500a2 2 0 01.553-1.061l1.414-9.414z" />
                    </svg>
                  </button>
                  <button
                    @click="handleHapus(item)"
                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition-colors"
                    title="Hapus Data"
                  >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0a1 1 0 00-1-1h-4a1 1 0 00-1 1m6 0H7" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Info Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
      <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-3xl p-6 text-white shadow-lg shadow-blue-100 overflow-hidden relative group">
        <div class="absolute -right-4 -bottom-4 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-700"></div>
        <div class="relative z-10">
          <div class="p-2 bg-white/20 w-fit rounded-lg mb-4">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="font-black text-lg mb-2">Penjadwalan Otomatis</h3>
          <p class="text-xs text-blue-50 leading-relaxed font-medium">Periode aktif akan menjadi basis penanggalan untuk seluruh jadwal UTS & UAS yang digenerate oleh sistem.</p>
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-3xl p-6 text-white shadow-lg shadow-orange-100 overflow-hidden relative group">
        <div class="absolute -right-4 -bottom-4 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-700"></div>
        <div class="relative z-10">
          <div class="p-2 bg-white/20 w-fit rounded-lg mb-4">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
          </div>
          <h3 class="font-black text-lg mb-2">Satu Periode Aktif</h3>
          <p class="text-xs text-amber-50 leading-relaxed font-medium">Sistem hanya memperbolehkan satu periode aktif dalam satu waktu untuk menjaga integritas data akademik.</p>
        </div>
      </div>

      <div class="bg-white rounded-3xl p-6 border border-gray-100 flex items-center justify-center text-center">
        <div>
          <div class="w-16 h-16 bg-teal-50 text-teal-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-teal-100">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
          </div>
          <h3 class="font-black text-gray-800 tracking-tight">Data Terlindungi</h3>
          <p class="text-xs text-gray-400 mt-1 font-medium">Seluruh perubahan riwayat periode tersimpan dan dapat diaudit secara real-time.</p>
        </div>
      </div>
    </div>

    <!-- MODAL TAMBAH / EDIT -->
    <modal-app v-model="showModal" size="medium" :click-outside="true" @close="closeModal">
      <div class="bg-gradient-to-r from-teal-500 to-teal-600 px-8 py-6">
        <div class="flex items-center gap-3">
          <div class="p-2 bg-white/20 rounded-xl backdrop-blur-md">
            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
          </div>
          <h3 class="text-xl font-black text-white tracking-tight">
            {{ isEditMode ? 'Perbarui Periode' : 'Tambah Periode Baru' }}
          </h3>
        </div>
      </div>

      <div class="p-8 space-y-6">
        <div>
          <label class="block text-sm font-bold text-gray-700 mb-2">Nama Periode <span class="text-red-500">*</span></label>
          <app-input v-model="form.nama" placeholder="Contoh: 2024/2025 Ganjil" label="" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
            <teal-date-picker v-model="form.start_date" placeholder="Pilih tanggal..." />
          </div>
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Selesai <span class="text-red-500">*</span></label>
            <teal-date-picker v-model="form.end_date" placeholder="Pilih tanggal..." />
          </div>
        </div>

        <div>
          <label class="block text-sm font-bold text-gray-700 mb-2">Status Aktif</label>
          <div class="flex items-center gap-4">
            <button
              @click="form.status = 'Aktif'"
              class="flex-1 py-3 rounded-2xl border-2 transition-all duration-300 font-bold text-sm flex items-center justify-center gap-2"
              :class="form.status === 'Aktif'
                ? 'bg-teal-50 border-teal-500 text-teal-700 shadow-sm'
                : 'bg-white border-gray-100 text-gray-400 hover:border-teal-200'"
            >
              <span class="w-2.5 h-2.5 rounded-full" :class="form.status === 'Aktif' ? 'bg-teal-500 shadow-[0_0_8px_rgba(20,184,166,0.6)]' : 'bg-gray-300'"></span>
              Aktif
            </button>
            <button
              @click="form.status = 'Non-Aktif'"
              class="flex-1 py-3 rounded-2xl border-2 transition-all duration-300 font-bold text-sm flex items-center justify-center gap-2"
              :class="form.status === 'Non-Aktif'
                ? 'bg-gray-50 border-gray-400 text-gray-700 shadow-sm'
                : 'bg-white border-gray-100 text-gray-400 hover:border-teal-200'"
            >
              <span class="w-2.5 h-2.5 rounded-full" :class="form.status === 'Non-Aktif' ? 'bg-gray-400 font-black shadow-sm' : 'bg-gray-300'"></span>
              Non-Aktif
            </button>
          </div>
        </div>
      </div>

      <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100 flex justify-end gap-3">
        <button
          @click="closeModal"
          class="px-6 py-2.5 rounded-xl border-2 border-transparent text-gray-500 font-bold text-sm hover:bg-gray-100 transition-all duration-300"
        >
          Batalkan
        </button>
        <button
          @click="handleSimpan"
          class="px-8 py-2.5 bg-teal-500 hover:bg-teal-600 text-white font-black text-sm rounded-xl shadow-lg shadow-teal-100 transition-all duration-300 transform active:scale-95"
        >
          {{ isEditMode ? 'Simpan Perubahan' : 'Buat Periode' }}
        </button>
      </div>
    </modal-app>

    <!-- CONFIRM DELETE -->
    <modal-pop-up-confirm
      v-model="showConfirm"
      title="Hapus Periode Akademik?"
      :description="`Apakah Anda yakin ingin menghapus '${selectedItem?.nama}'? Seluruh data yang terkait dengan periode ini akan diarsipkan.`"
      confirm-text="Ya, Hapus Permanen"
      @confirm="confirmHapus"
    />

    <!-- SUCCESS ALERT -->
    <modal-pop-up-success
      v-model="showSuccess"
      :title="successData.title"
      :description="successData.description"
      @close-action="showSuccess = false"
    />
  </div>
</template>

<script>
import BreadcrumbBima from "@/core/components/Breadcrumb.vue";
import ButtonApp from "@/core/components/Button.vue";
import AppInput from "@/core/components/AppInput.vue";
import ModalApp from "@/core/components/Modal.vue";
import ModalPopUpConfirm from "@/core/components/ModalPopUpConfirm.vue";
import ModalPopUpSuccess from "@/core/components/ModalPopUpSuccess.vue";
import TealDatePicker from "@/modules/penjadwalan/components/TealDatePicker.vue";

export default {
  name: "PeriodeSettings",
  components: {
    BreadcrumbBima,
    ButtonApp,
    AppInput,
    ModalApp,
    ModalPopUpConfirm,
    ModalPopUpSuccess,
    TealDatePicker,
  },
  data() {
    return {
      search: "",
      showModal: false,
      isEditMode: false,
      showConfirm: false,
      showSuccess: false,
      selectedItem: null,
      form: {
        id: null,
        nama: "",
        start_date: "",
        end_date: "",
        status: "Aktif",
      },
      successData: {
        title: "Berhasil!",
        description: "Periode akademik telah diperbarui.",
      },
      breadcrumbItems: [
        { text: "Settings", link: "#" },
        { text: "Periode", link: "/app/pengaturan-periode" },
      ],
      // DUMMY DATA
      periodeList: [
        { id: 1, nama: "2023/2024 Genap", start_date: "2024-02-01", end_date: "2024-07-31", status: "Non-Aktif" },
        { id: 2, nama: "2024/2025 Ganjil", start_date: "2024-08-01", end_date: "2025-01-31", status: "Aktif" },
        { id: 3, nama: "2024/2025 Genap", start_date: "2025-02-01", end_date: "2025-07-31", status: "Non-Aktif" },
      ],
    };
  },
  computed: {
    filteredItems() {
      if (!this.search) return this.periodeList;
      return this.periodeList.filter((item) =>
        item.nama.toLowerCase().includes(this.search.toLowerCase())
      );
    },
  },
  methods: {
    formatTanggal(d) {
      if (!d) return "-";
      return new Date(d + "T00:00:00").toLocaleDateString("id-ID", {
        day: "numeric",
        month: "short",
        year: "numeric",
      });
    },
    handleTambah() {
      this.isEditMode = false;
      this.form = { id: null, nama: "", start_date: "", end_date: "", status: "Aktif" };
      this.showModal = true;
    },
    handleEdit(item) {
      this.isEditMode = true;
      this.form = { ...item };
      this.showModal = true;
    },
    handleHapus(item) {
      this.selectedItem = item;
      this.showConfirm = true;
    },
    confirmHapus() {
      this.periodeList = this.periodeList.filter((i) => i.id !== this.selectedItem.id);
      this.showConfirm = false;
      this.successData = {
        title: "Data Dihapus",
        description: `Periode '${this.selectedItem.nama}' berhasil dihapus.`,
      };
      this.showSuccess = true;
    },
    handleSimpan() {
      if (!this.form.nama || !this.form.start_date || !this.form.end_date) {
        alert("Mohon lengkapi semua data wajib.");
        return;
      }

      if (this.isEditMode) {
        const idx = this.periodeList.findIndex((i) => i.id === this.form.id);
        if (idx !== -1) {
          this.periodeList.splice(idx, 1, { ...this.form });
        }
        this.successData.title = "Pembaruan Berhasil";
        this.successData.description = "Data periode akademik telah berhasil diperbarui.";
      } else {
        const newId = Math.max(0, ...this.periodeList.map((i) => i.id)) + 1;
        this.periodeList.unshift({ ...this.form, id: newId });
        this.successData.title = "Periode Dibuat";
        this.successData.description = "Periode akademik baru telah ditambahkan ke sistem.";
      }

      // Logic: Hanya boleh 1 aktif
      if (this.form.status === "Aktif") {
        this.periodeList.forEach((item) => {
          if (item.id !== (this.form.id || Math.max(...this.periodeList.map(i => i.id)))) {
            item.status = "Non-Aktif";
          }
        });
      }

      this.showModal = false;
      this.showSuccess = true;
    },
    closeModal() {
      this.showModal = false;
    },
  },
};
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

:deep(.modal-container) {
  border-radius: 2rem !important;
  overflow: hidden;
}
</style>
