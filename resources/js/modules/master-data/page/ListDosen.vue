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
      </div>

      <!-- Table -->
      <table-app
        :items="filteredData"
        :headers="headers"
        :options="tableOptions"
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
            <td class="p-4 border-b text-gray-500 text-md">{{ index + 1 }}</td>
            <td class="p-4 border-b font-medium text-gray-700 text-md">{{ item.nidn }}</td>
            <td class="p-4 border-b text-gray-700 text-md">{{ item.nama }}</td>
            <td class="p-4 border-b text-gray-600 text-md">{{ item.prodi }}</td>
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
          <app-input v-model="form.nidn" placeholder="Contoh: 0012345678" label="" />
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
        <div class="sm:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
          <app-input v-model="form.nama" placeholder="Contoh: Dr. Budi Santoso, M.T." label="" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Program Studi <span class="text-red-500">*</span></label>
          <select-auto-complete
            v-model="form.prodi"
            :options="prodiOptions"
            item-text="name"
            item-value="name"
            placeholder="Pilih Program Studi..."
          />
        </div>
        <div>
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
  </div>
</template>

<script>
import BreadcrumbBima from "@/core/components/Breadcrumb.vue";
import ButtonApp from "@/core/components/Button.vue";
import AppInput from "@/core/components/AppInput.vue";
import TableApp from "@/core/components/Table.vue";
import ModalApp from "@/core/components/Modal.vue";
import SelectAutoComplete from "@/core/components/SelectAutoComplete.vue";

export default {
  name: "ListDosen",
  components: {
    BreadcrumbBima,
    ButtonApp,
    AppInput,
    TableApp,
    ModalApp,
    SelectAutoComplete,
  },
  data() {
    return {
      search: "",
      showModal: false,
      isEditMode: false,
      editId: null,
      form: {
        nidn:    "",
        nama:    "",
        prodi:   "",
        jabatan: "",
        status:  "",
      },
      jabatanOptions: [
        { name: "Asisten Ahli" },
        { name: "Lektor" },
        { name: "Lektor Kepala" },
        { name: "Guru Besar / Profesor" },
        { name: "Dosen Tetap" },
        { name: "Dosen Tidak Tetap" },
      ],
      prodiOptions: [
        { name: "Teknik Informatika" },
        { name: "Sistem Informasi" },
        { name: "Teknik Elektro" },
        { name: "Teknik Mesin" },
        { name: "Teknik Sipil" },
        { name: "Manajemen" },
        { name: "Akuntansi" },
        { name: "Ilmu Komunikasi" },
        { name: "Hukum" },
        { name: "Psikologi" },
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
        { text: "Nama",         value: "nama",    align: "start",  sortable: true  },
        { text: "Program Studi", value: "prodi",  align: "start",  sortable: true  },
        { text: "Jabatan",      value: "jabatan", align: "start",  sortable: false },
        { text: "Status",       value: "status",  align: "center", sortable: false },
        { text: "Aksi",         value: "aksi",    align: "center", sortable: false },
      ],
      tableOptions: {
        page: 1,
        itemsPerPage: 10,
      },
      // Data dummy — ganti dengan pemanggilan API sesuai kebutuhan
      dosenList: [
        { id: 1, nidn: "0011223344", nama: "Dr. Andi Wijaya, M.Kom.",      prodi: "Teknik Informatika", jabatan: "Lektor Kepala",  status: "Aktif"     },
        { id: 2, nidn: "0022334455", nama: "Prof. Siti Rahayu, Ph.D.",     prodi: "Sistem Informasi",   jabatan: "Guru Besar / Profesor", status: "Aktif" },
        { id: 3, nidn: "0033445566", nama: "Budi Santoso, M.T.",           prodi: "Teknik Elektro",     jabatan: "Lektor",         status: "Aktif"     },
        { id: 4, nidn: "0044556677", nama: "Dewi Lestari, S.E., M.M.",     prodi: "Manajemen",          jabatan: "Asisten Ahli",   status: "Aktif"     },
        { id: 5, nidn: "0055667788", nama: "Rizky Pratama, S.H., M.H.",    prodi: "Hukum",              jabatan: "Dosen Tetap",    status: "Non-Aktif" },
      ],
    };
  },
  computed: {
    filteredData() {
      const q = this.search.toLowerCase().trim();
      if (!q) return this.dosenList;
      return this.dosenList.filter(
        (item) =>
          item.nidn.toLowerCase().includes(q) ||
          item.nama.toLowerCase().includes(q) ||
          item.prodi.toLowerCase().includes(q)
      );
    },
  },
  methods: {
    handleTambah() {
      this.isEditMode = false;
      this.editId = null;
      this.form = { nidn: "", nama: "", prodi: "", jabatan: "", status: "" };
      this.showModal = true;
    },
    handleEdit(item) {
      this.isEditMode = true;
      this.editId = item.id;
      this.form = {
        nidn:    item.nidn,
        nama:    item.nama,
        prodi:   item.prodi,
        jabatan: item.jabatan,
        status:  item.status,
      };
      this.showModal = true;
    },
    handleDelete(item) {
      // TODO: konfirmasi & hapus data
      console.log("Delete:", item);
    },
    handleSimpan() {
      if (this.isEditMode) {
        const idx = this.dosenList.findIndex((d) => d.id === this.editId);
        if (idx !== -1) {
          this.dosenList[idx] = { id: this.editId, ...this.form };
        }
      } else {
        const newId = Date.now();
        this.dosenList.push({ id: newId, ...this.form });
      }
      this.closeModal();
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