<template>
  <div class="p-6">
    <!-- Breadcrumb -->
    <breadcrumb-bima :items="breadcrumbItems" class="mb-6" />

    <div class="bg-white rounded-lg shadow-sm mt-5 p-6">
      <!-- Header -->
      <h2 class="text-xl font-bold text-gray-800 mb-4">Pengaturan Periode Akademik</h2>

      <!-- Search + Tambah sejajar -->
      <div class="flex items-center justify-between gap-3 mb-4">
        <div class="flex-1 max-w-sm">
          <app-input
            v-model="search"
            placeholder="Cari nama periode..."
            label=""
          >
            <template #icon-left>
              <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
              </svg>
            </template>
          </app-input>
        </div>
        <button-app type="primary" color="teal"
          class="bg-teal-400 hover:bg-teal-500 text-white font-semibold px-6 py-2 rounded-lg shadow-md transition-all duration-200"
          @click="handleTambah">
          <template #icon-left>
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
          </template>
          Tambah Periode
        </button-app>
      </div>

      <!-- Table using TableApp -->
      <table-app
        :items="periodeList"
        :headers="headers"
        :options="tableOptions"
        :server-side="true"
        @update:options="tableOptions = $event"
        :searchable="false"
        :show-pagination="true"
        :use-custom-row="true"
        not-found-label="Tidak ada data periode."
      >
        <template #customrow="{ rows }">
          <tr
            v-for="(item, index) in rows"
            :key="item.id"
            class="bg-white hover:bg-gray-50 transition"
          >
            <td class="p-4 border-b text-gray-500 text-md">{{ startingIndex + index + 1 }}</td>
            <td class="p-4 border-b font-medium text-gray-700 text-md">
              <div class="font-bold">{{ item.nama }}</div>
            </td>
            <td class="p-4 border-b text-gray-600 text-md">
              <div class="flex items-center gap-2">
                <span>{{ formatTanggal(item.start_date) }}</span>
                <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
                <span>{{ formatTanggal(item.end_date) }}</span>
              </div>
            </td>
            <td class="p-4 border-b text-center">
              <span
                class="inline-block px-3 py-1 rounded-full text-xs font-semibold"
                :class="item.status === 'Aktif'
                  ? 'bg-teal-100 text-teal-700'
                  : 'bg-red-100 text-red-600'"
              >
                {{ item.status }}
              </span>
            </td>
            <td class="p-4 border-b text-center">
              <div class="flex items-center justify-center gap-4">
                <span class="cursor-pointer text-yellow-400 hover:text-yellow-500 transition" @click="handleEdit(item)">
                  <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a2 2 0 01-1.414.586H9v-1.414A2 2 0 019 13z" />
                  </svg>
                </span>
                <span class="cursor-pointer text-red-500 hover:text-red-600 transition" @click="handleHapus(item)">
                  <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0a1 1 0 00-1-1h-4a1 1 0 00-1 1m6 0H7" />
                  </svg>
                </span>
              </div>
            </td>
          </tr>
        </template>
      </table-app>
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
          :disabled="isSaving"
          class="px-8 py-2.5 bg-teal-500 hover:bg-teal-600 text-white font-black text-sm rounded-xl shadow-lg shadow-teal-100 transition-all duration-300 transform active:scale-95 disabled:opacity-50"
        >
          <span v-if="isSaving" class="flex items-center gap-2">
            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Menyimpan...
          </span>
          <span v-else>{{ isEditMode ? 'Simpan Perubahan' : 'Buat Periode' }}</span>
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
import DISPATCH from "@/core/plugins/constants/dispatches";
import TableApp from "@/core/components/Table.vue";

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
    TableApp,
  },
  data() {
    return {
      search: "",
      showModal: false,
      isEditMode: false,
      showConfirm: false,
      showSuccess: false,
      loading: false,
      isSaving: false,
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
      headers: [
        { text: "No",           value: "no",         align: "start",  sortable: false },
        { text: "Nama Periode",  value: "nama",       align: "start",  sortable: true  },
        { text: "Rentang Waktu", value: "rentang",    align: "start",  sortable: false },
        { text: "Status",       value: "status",     align: "center", sortable: false },
        { text: "Aksi",         value: "aksi",       align: "center", sortable: false },
      ],
      tableOptions: {
        page: 1,
        itemsPerPage: 10,
        totalItems: 0,
      },
    };
  },
  computed: {
    periodeList() {
      return this.$store.state.masterData.periodeList || [];
    },
    pagination() {
      return this.$store.state.masterData.periodePagination || {};
    },
    startingIndex() {
      return ((this.tableOptions.page ?? 1) - 1) * this.tableOptions.itemsPerPage;
    },
  },
  watch: {
    search(val) {
      clearTimeout(this._searchTimer);
      this._searchTimer = setTimeout(() => {
        this.tableOptions.page = 1;
        this.fetchData();
      }, 400);
    },
    tableOptions: {
      handler(newVal, oldVal) {
        if (oldVal && (newVal.page !== oldVal.page || newVal.itemsPerPage !== oldVal.itemsPerPage)) {
          this.fetchData();
        }
      },
      deep: true,
    },
  },
  mounted() {
    this.fetchData();
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
    async fetchData() {
      this.loading = true;
      this.$store.commit("SET_LOADING", true);
      try {
        await this.$store.dispatch(DISPATCH.GET_PERIODE, {
          search: this.search || undefined,
          page: (this.tableOptions.page ?? 1) - 1,
          size: this.tableOptions.itemsPerPage,
        });
        this.tableOptions = {
          ...this.tableOptions,
          totalItems: this.pagination.total_elements || 0,
        };
      } catch (error) {
        console.error("Gagal mengambil data periode:", error);
      } finally {
        this.loading = false;
        this.$store.commit("SET_LOADING", false);
      }
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
    async confirmHapus() {
      this.$store.commit("SET_LOADING", true);
      try {
        await this.$store.dispatch(DISPATCH.DELETE_PERIODE, this.selectedItem.id);
        this.showConfirm = false;
        this.successData = {
          title: "Data Dihapus",
          description: `Periode '${this.selectedItem.nama}' berhasil dihapus.`,
        };
        this.showSuccess = true;
        this.fetchData();
      } catch (error) {
        console.error("Gagal menghapus periode:", error);
      } finally {
        this.$store.commit("SET_LOADING", false);
      }
    },
    async handleSimpan() {
      if (!this.form.nama || !this.form.start_date || !this.form.end_date) {
        alert("Mohon lengkapi semua data wajib.");
        return;
      }

      this.isSaving = true;
      this.$store.commit("SET_LOADING", true);
      try {
        if (this.isEditMode) {
          await this.$store.dispatch(DISPATCH.UPDATE_PERIODE, this.form);
          this.successData.title = "Pembaruan Berhasil";
          this.successData.description = "Data periode akademik telah berhasil diperbarui.";
        } else {
          await this.$store.dispatch(DISPATCH.CREATE_PERIODE, this.form);
          this.successData.title = "Periode Dibuat";
          this.successData.description = "Periode akademik baru telah ditambahkan ke sistem.";
        }

        this.showModal = false;
        this.showSuccess = true;
        this.fetchData();
      } catch (error) {
        console.error("Gagal menyimpan periode:", error);
      } finally {
        this.isSaving = false;
        this.$store.commit("SET_LOADING", false);
      }
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
