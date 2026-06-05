<template>
  <div class="p-6">
    <!-- Breadcrumb -->
    <breadcrumb-bima :items="breadcrumbItems" class="mb-6" />

    <div class="bg-white rounded-lg shadow-sm mt-5 p-6">
      <!-- Header -->
      <h2 class="text-xl font-bold text-gray-800 mb-4">Daftar Dosen</h2>

      <!-- Search + Tambah sejajar -->
      <div class="flex items-center justify-between gap-3 mb-4">
        <div class="flex-1 max-w-sm">
          <app-input
            v-model="search"
            placeholder="Cari dosen..."
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
        <div class="flex gap-2">
          <button-app type="primary" color="teal"
            class="bg-teal-400 hover:bg-teal-500 text-white font-semibold px-6 py-2 rounded-lg shadow-md transition-all duration-200"
            @click="handleTambah">
            <template #icon-left>
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
            </template>
            Tambah Dosen
          </button-app>

          <button-app 
            type="secondary" 
            color="teal"
            class="bg-white border border-teal-500 text-teal-600 hover:bg-teal-50 px-6 py-2 rounded-lg shadow-sm transition-all duration-200"
            @click="handleExport">
            <template #icon-left>
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M16 10l-4 4m0 0l-4-4m4 4V4" />
              </svg>
            </template>
            Export Excel
          </button-app>
        </div>
      </div>

      <!-- Table -->
      <table-app
        :items="filteredData"
        :headers="headers"
        :options="tableOptions"
        :server-side="true"
        :sort-order="sortOrder"
        @update:options="tableOptions = $event"
        @update:sort-order="handleSortOrder"
        :searchable="false"
        :show-pagination="true"
        :use-custom-row="true"
        not-found-label="Tidak ada data dosen."
      >
        <template #customrow="{ rows }">
          <tr
            v-for="(item, index) in rows"
            :key="item.id"
            class="bg-white hover:bg-gray-50 transition"
          >
            <td class="p-4 border-b text-gray-500 text-md">{{ startingIndex + index + 1 }}</td>
            <td class="p-4 border-b font-medium text-gray-700 text-md">{{ item.nidn }}</td>
            <td class="p-4 border-b text-gray-700 text-md">{{ item.nip || '-' }}</td>
            <td class="p-4 border-b text-gray-700 text-md">{{ item.nama }}</td>
            <td class="p-4 border-b text-gray-600 text-md">{{ item.program_studi ? item.program_studi.nama : '-' }}</td>
            <td class="p-4 border-b text-gray-600 text-md">{{ item.jabatan }}</td>
            <td class="p-4 border-b text-center">
              <span
                class="inline-block px-2 py-1 rounded-full text-xs font-semibold"
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
                <span class="cursor-pointer text-red-500 hover:text-red-600 transition" @click="handleDelete(item)">
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

    <!-- ===== MODAL TAMBAH / EDIT ===== -->
    <modal-app v-model="showModal" size="medium" :click-outside="true" @close="closeModal">
      <!-- Modal Header -->
      <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
        <h3 class="text-lg font-bold text-gray-800">
          {{ isEditMode ? 'Edit Dosen' : 'Tambah Dosen' }}
        </h3>
        <span class="cursor-pointer text-gray-400 hover:text-gray-600 transition" @click="closeModal">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </span>
      </div>

      <!-- Form Fields -->
      <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">NIDN <span class="text-red-500">*</span></label>
          <app-input v-model="form.nidn" type="number" placeholder="Contoh: 0012345678" label="" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">NIP (Opsional)</label>
          <app-input v-model="form.nip" placeholder="Contoh: 19800101..." label="" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan <span class="text-red-500">*</span></label>
          <select-auto-complete
            v-model="form.jabatan"
            :options="jabatanOptions"
            item-text="name"
            item-value="name"
            placeholder="Pilih Jabatan..."
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Program Studi <span class="text-red-500">*</span></label>
          <select-auto-complete
            v-model="form.program_studi_id"
            :options="programStudiOptions"
            item-text="nama"
            item-value="id"
            placeholder="Pilih Program Studi..."
            @search="handleSearchProdi"
          />
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
          <app-input v-model="form.nama" placeholder="Contoh: Dr. Budi Santoso, M.T." label="" />
        </div>
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
          <select-auto-complete
            v-model="form.status"
            :options="statusOptions"
            item-text="name"
            item-value="name"
            placeholder="Pilih Status..."
          />
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100">
        <button @click="closeModal"
          class="px-5 py-2 rounded-lg border border-gray-300 text-gray-600 text-sm font-medium hover:bg-gray-50 transition">
          Batal
        </button>
        <button @click="handleSimpan"
          class="px-6 py-2 rounded-lg bg-teal-400 hover:bg-teal-500 text-white text-sm font-semibold shadow-md transition-all duration-200">
          {{ isEditMode ? 'Simpan Perubahan' : 'Simpan' }}
        </button>
      </div>
    </modal-app>

    <!-- ===== MODAL CONFIRM ===== -->
    <modal-pop-up-confirm
      v-model="showConfirmModal"
      :title="confirmData.title"
      :description="confirmData.description"
      @confirm="confirmData.action"
    />

    <!-- ===== MODAL SUCCESS ===== -->
    <modal-pop-up-success
      v-model="showSuccessModal"
      :title="successData.title"
      :description="successData.description"
      :button-text="successData.buttonText"
      @close-action="successData.action"
    />
  </div>
</template>

<script>
import BreadcrumbBima from "@/core/components/Breadcrumb.vue";
import ButtonApp from "@/core/components/Button.vue";
import AppInput from "@/core/components/AppInput.vue";
import TableApp from "@/core/components/Table.vue";
import ModalApp from "@/core/components/Modal.vue";
import SelectAutoComplete from "@/core/components/SelectAutoComplete.vue";
import ModalPopUpConfirm from "@/core/components/ModalPopUpConfirm.vue";
import ModalPopUpSuccess from "@/core/components/ModalPopUpSuccess.vue";
import DISPATCH from "@/core/plugins/constants/dispatches";

export default {
  name: "ListDosen",
  components: {
    BreadcrumbBima,
    ButtonApp,
    AppInput,
    TableApp,
    ModalApp,
    SelectAutoComplete,
    ModalPopUpConfirm,
    ModalPopUpSuccess,
  },
  data() {
    return {
      search: "",
      showModal: false,
      isEditMode: false,
      isSaving: false,
      editId: null,
      form: {
        nidn:             "",
        nip:              "",
        nama:             "",
        program_studi_id: "",
        jabatan:          "",
        status:           "",
      },
      // PopUp Confirm State
      showConfirmModal: false,
      confirmData: {
        title: "",
        description: "",
        action: () => {},
      },
      // PopUp Success State
      showSuccessModal: false,
      successData: {
        title: "",
        description: "",
        buttonText: "Oke",
        action: () => {},
      },
      jabatanOptions: [
        { name: "Asisten Ahli" },
        { name: "Lektor" },
        { name: "Lektor Kepala" },
        { name: "Guru Besar / Profesor" },
        { name: "Dosen Tetap" },
        { name: "Dosen Tidak Tetap" },
      ],
      statusOptions: [
        { name: "Aktif" },
        { name: "Non-Aktif" },
        { name: "Pensiun" },
      ],
      breadcrumbItems: [
        { text: "Master Data", link: "#" },
        { text: "Dosen", link: "/app/dosen-list" },
      ],
      headers: [
        { text: "No",           value: "no",      align: "start",  sortable: false },
        { text: "NIDN",         value: "nidn",    align: "start",  sortable: true  },
        { text: "NIP",          value: "nip",     align: "start",  sortable: true  },
        { text: "Nama",         value: "nama",    align: "start",  sortable: true  },
        { text: "Program Studi", value: "prodi",  align: "start",  sortable: true  },
        { text: "Jabatan",      value: "jabatan", align: "start",  sortable: false },
        { text: "Status",       value: "status",  align: "center", sortable: false },
        { text: "Aksi",         value: "aksi",    align: "center", sortable: false },
      ],
      tableOptions: {
        page: 1,
        itemsPerPage: 10,
        totalItems: 0,
      },
      sortOrder: [],
    };
  },
  computed: {
    dosenList() {
      return this.$store.state.masterData.dosenList;
    },
    pagination() {
      return this.$store.state.masterData.dosenPagination;
    },
    programStudiOptions() {
      return this.$store.state.masterData.programStudiList;
    },
    startingIndex() {
      return ((this.tableOptions.page ?? 1) - 1) * this.tableOptions.itemsPerPage;
    },
    filteredData() {
      return this.dosenList;
    },
  },
  mounted() {
    this.fetchData();
    this.fetchProgramStudi();
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
  methods: {
    async fetchData() {
      this.$store.commit("SET_LOADING", true);
      try {
        let sortBy = undefined;
        let sortDir = undefined;
        if (this.sortOrder && this.sortOrder.length > 0) {
          sortBy = this.sortOrder[0].field;
          sortDir = this.sortOrder[0].direction;
        }

        await this.$store.dispatch(DISPATCH.GET_DOSEN, {
          search: this.search || undefined,
          page: (this.tableOptions.page ?? 1) - 1,
          size: this.tableOptions.itemsPerPage,
          sort_by: sortBy,
          sort_dir: sortDir,
        });
        this.tableOptions = {
          ...this.tableOptions,
          totalItems: this.pagination.total_elements,
        };
      } catch (e) {
        console.error("Gagal memuat data dosen:", e);
      } finally {
        this.$store.commit("SET_LOADING", false);
      }
    },
    handleSortOrder(newSortOrder) {
      this.sortOrder = newSortOrder;
      this.fetchData();
    },
    async fetchProgramStudi(query) {
      try {
        await this.$store.dispatch(DISPATCH.GET_PROGRAM_STUDI, {
          search: query || undefined,
          page: 0,
          size: 10,
        });
      } catch (e) {
        console.error("Gagal memuat data program studi:", e);
      }
    },
    handleSearchProdi(query) {
      clearTimeout(this._prodiSearchTimer);
      this._prodiSearchTimer = setTimeout(() => {
        this.fetchProgramStudi(query);
      }, 500);
    },
    handleTambah() {
      this.isEditMode = false;
      this.editId = null;
      this.form = { nidn: "", nip: "", nama: "", program_studi_id: "", jabatan: "", status: "" };
      this.showModal = true;
    },
    handleEdit(item) {
      this.isEditMode = true;
      this.editId = item.id;
      this.form = {
        nidn:             item.nidn,
        nip:              item.nip || "",
        nama:             item.nama,
        program_studi_id: item.program_studi_id,
        jabatan:          item.jabatan,
        status:           item.status,
      };
      this.showModal = true;
    },
    async handleDelete(item) {
      this.confirmData = {
        title: "Hapus Dosen",
        description: `Apakah Anda yakin ingin menghapus dosen "${item.nama}"? Data yang dihapus tidak dapat dikembalikan.`,
        action: async () => {
          this.$store.commit("SET_LOADING", true);
          try {
            await this.$store.dispatch(DISPATCH.DELETE_DOSEN, item.id);
            this.successData = {
              title: "Berhasil Dihapus",
              description: `Dosen "${item.nama}" telah berhasil dihapus dari sistem.`,
              buttonText: "Oke",
              action: () => this.fetchData(),
            };
            this.showSuccessModal = true;
          } catch (e) {
            console.error("Gagal menghapus:", e);
          } finally {
            this.$store.commit("SET_LOADING", false);
          }
        },
      };
      this.showConfirmModal = true;
    },
    async handleSimpan() {
      if (this.isSaving) return;
      this.isSaving = true;
      this.$store.commit("SET_LOADING", true);
      try {
        const payload = { ...this.form };
        let message = "";
        if (this.isEditMode) {
          await this.$store.dispatch(DISPATCH.UPDATE_DOSEN, { id: this.editId, ...payload });
          message = `Perubahan pada dosen "${payload.nama}" berhasil disimpan.`;
        } else {
          await this.$store.dispatch(DISPATCH.CREATE_DOSEN, payload);
          message = `Dosen "${payload.nama}" berhasil ditambahkan ke sistem.`;
        }

        this.closeModal();

        this.successData = {
          title: this.isEditMode ? "Berhasil Diperbarui" : "Berhasil Ditambahkan",
          description: message,
          buttonText: "Selesai",
          action: () => {
            this.fetchData();
          },
        };
        this.showSuccessModal = true;
      } catch (e) {
        console.error("Gagal menyimpan:", e);
      } finally {
        this.isSaving = false;
        this.$store.commit("SET_LOADING", false);
      }
    },
    async handleExport() {
      this.$store.commit("SET_LOADING", true);
      try {
        const response = await this.$store.dispatch(DISPATCH.EXPORT_DOSEN, {
          search: this.search || undefined,
        });

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'daftar_dosen.xlsx');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      } catch (e) {
        console.error("Gagal mengekspor data dosen:", e);
      } finally {
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
:deep(input::-webkit-outer-spin-button),
:deep(input::-webkit-inner-spin-button) {
  -webkit-appearance: none;
  appearance: none;
  margin: 0;
}
:deep(input[type="number"]) {
  -moz-appearance: textfield;
  appearance: textfield;
}
</style>