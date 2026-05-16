<template>
  <div class="p-6">
    <!-- Breadcrumb -->
    <breadcrumb-bima :items="breadcrumbItems" class="mb-6" />

    <div class="bg-white rounded-lg shadow-sm mt-5 p-6">
      <!-- Header -->
      <h2 class="text-xl font-bold text-gray-800 mb-4">Plotting Kelas Mata Kuliah</h2>

      <!-- Search + Tambah sejajar -->
      <div class="flex items-center justify-between gap-3 mb-4">
        <div class="flex-1 max-w-sm">
          <app-input
            v-model="search"
            placeholder="Cari kelas atau mata kuliah..."
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
            Tambah Plotting
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
        @update:options="tableOptions = $event"
        :searchable="false"
        :show-pagination="true"
        :use-custom-row="true"
        not-found-label="Tidak ada data plotting."
      >
        <template #customrow="{ rows }">
          <tr
            v-for="(item, index) in rows"
            :key="item.id"
            class="bg-white hover:bg-gray-50 transition"
          >
            <td class="p-4 border-b text-gray-500 text-md">{{ startingIndex + index + 1 }}</td>
            <td class="p-4 border-b font-medium text-gray-700 text-md">{{ item.kelas ? item.kelas.nama_kelas : '-' }}</td>
            <td class="p-4 border-b text-gray-700 text-md">
                <div class="font-bold">{{ item.mata_kuliah ? item.mata_kuliah.nama : '-' }}</div>
                <div class="text-xs text-gray-400">{{ item.mata_kuliah ? item.mata_kuliah.kode : '' }}</div>
            </td>
            <td class="p-4 border-b text-gray-600 text-md text-center">{{ item.semester }}</td>
            <td class="p-4 border-b text-gray-600 text-md text-center">
                {{ item.mata_kuliah && item.mata_kuliah.program_studi ? item.mata_kuliah.program_studi.nama : '-' }}
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
          {{ isEditMode ? 'Edit Plotting' : 'Tambah Plotting' }}
        </h3>
        <span class="cursor-pointer text-gray-400 hover:text-gray-600 transition" @click="closeModal">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </span>
      </div>

      <!-- Form Fields -->
      <div class="px-6 py-5 grid grid-cols-1 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Kelas <span class="text-red-500">*</span></label>
          <select-auto-complete
            v-model="form.kelas_id"
            :options="kelasOptions"
            item-text="displayText"
            item-value="id"
            placeholder="Pilih Kelas..."
            @search="handleSearchKelas"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Mata Kuliah <span class="text-red-500">*</span></label>
          <select-auto-complete
            v-model="form.mata_kuliah_id"
            :options="mataKuliahOptions"
            item-text="displayText"
            item-value="id"
            placeholder="Pilih Mata Kuliah..."
            @search="handleSearchMK"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Semester <span class="text-red-500">*</span></label>
          <app-input v-model="form.semester" type="number" placeholder="Contoh: 2" label="" />
        </div>
      </div>

      <!-- MODAL FOOTER -->
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
  name: "ListKelasMataKuliah",
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
        kelas_id: "",
        mata_kuliah_id: "",
        semester: "",
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
      breadcrumbItems: [
        { text: "Master Data", link: "#" },
        { text: "Akademik", link: "#" },
        { text: "Kelas Mata Kuliah", link: "/app/kelas-mata-kuliah" },
      ],
      headers: [
        { text: "No",              value: "no",       align: "start",  sortable: false },
        { text: "Nama Kelas",      value: "kelas",    align: "start",  sortable: true  },
        { text: "Mata Kuliah",     value: "matkul",   align: "start",  sortable: true  },
        { text: "Semester",        value: "semester", align: "center", sortable: false },
        { text: "Program Studi",   value: "prodi",    align: "center", sortable: false },
        { text: "Aksi",            value: "aksi",     align: "center", sortable: false },
      ],
      tableOptions: {
        page: 1,
        itemsPerPage: 10,
        totalItems: 0,
      },
    };
  },
  computed: {
    kmList() {
      return this.$store.state.masterData.kmList;
    },
    pagination() {
      return this.$store.state.masterData.kmPagination;
    },
    kelasOptions() {
      return (this.$store.state.settings.kelasList || []).map(item => ({
        ...item,
        displayText: `${item.nama_kelas} - ${item.program_studi?.nama || '-'}`
      }));
    },
    mataKuliahOptions() {
      return (this.$store.state.masterData.mataKuliahList || []).map(item => ({
        ...item,
        displayText: `${item.nama} - Semester ${item.semester || '-'}`
      }));
    },
    startingIndex() {
      return ((this.tableOptions.page ?? 1) - 1) * this.tableOptions.itemsPerPage;
    },
    filteredData() {
      return this.kmList;
    },
  },
  mounted() {
    this.fetchData();
    this.fetchKelas();
    this.fetchMataKuliah();
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
        await this.$store.dispatch(DISPATCH.GET_KELAS_MATA_KULIAH, {
          search: this.search || undefined,
          page: (this.tableOptions.page ?? 1) - 1,
          size: this.tableOptions.itemsPerPage,
        });
        if (this.tableOptions.totalItems !== (this.pagination?.total_elements ?? 0)) {
          this.tableOptions = {
            ...this.tableOptions,
            totalItems: this.pagination?.total_elements ?? 0,
          };
        }
      } catch (e) {
        console.error("Gagal memuat data plotting:", e);
      } finally {
        this.$store.commit("SET_LOADING", false);
      }
    },
    async fetchKelas(query) {
      try {
        await this.$store.dispatch(DISPATCH.GET_KELAS, {
          search: query || undefined,
          page: 0,
          size: 10,
        });
      } catch (e) {
        console.error("Gagal memuat data kelas:", e);
      }
    },
    async fetchMataKuliah(query) {
      try {
        await this.$store.dispatch(DISPATCH.GET_MATA_KULIAH, {
          search: query || undefined,
          page: 0,
          size: 10,
        });
      } catch (e) {
        console.error("Gagal memuat data mata kuliah:", e);
      }
    },
    handleSearchKelas(query) {
      clearTimeout(this._kelasSearchTimer);
      this._kelasSearchTimer = setTimeout(() => {
        this.fetchKelas(query);
      }, 500);
    },
    handleSearchMK(query) {
      clearTimeout(this._mkSearchTimer);
      this._mkSearchTimer = setTimeout(() => {
        this.fetchMataKuliah(query);
      }, 500);
    },
    handleTambah() {
      this.isEditMode = false;
      this.editId = null;
      this.form = { kelas_id: "", mata_kuliah_id: "", semester: "" };
      this.showModal = true;
    },
    handleEdit(item) {
      this.isEditMode = true;
      this.editId = item.id;
      this.form = {
        kelas_id:       item.kelas_id,
        mata_kuliah_id: item.mata_kuliah_id,
        semester:       item.semester,
      };
      this.showModal = true;
    },
    async handleDelete(item) {
      this.confirmData = {
        title: "Hapus Plotting",
        description: `Apakah Anda yakin ingin menghapus plotting mata kuliah "${item.mata_kuliah?.nama}" dari kelas "${item.kelas?.nama_kelas}"?`,
        action: async () => {
          this.$store.commit("SET_LOADING", true);
          try {
            await this.$store.dispatch(DISPATCH.DELETE_KELAS_MATA_KULIAH, item.id);
            this.successData = {
              title: "Berhasil Dihapus",
              description: `Plotting telah berhasil dihapus dari sistem.`,
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
          await this.$store.dispatch(DISPATCH.UPDATE_KELAS_MATA_KULIAH, { id: this.editId, ...payload });
          message = `Plotting berhasil diperbarui.`;
        } else {
          await this.$store.dispatch(DISPATCH.CREATE_KELAS_MATA_KULIAH, payload);
          message = `Plotting berhasil ditambahkan.`;
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
        const response = await this.$store.dispatch(DISPATCH.EXPORT_KELAS_MATA_KULIAH, {
          search: this.search || undefined,
        });

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'plotting_kelas_mata_kuliah.xlsx');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      } catch (e) {
        console.error("Gagal mengekspor data plotting:", e);
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
