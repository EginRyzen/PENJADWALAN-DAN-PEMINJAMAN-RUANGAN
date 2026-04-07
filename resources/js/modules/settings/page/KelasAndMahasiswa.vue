<template>
  <div class="p-6">
    <!-- Breadcrumb -->
    <breadcrumb-bima :items="breadcrumbItems" class="mb-6" />

    <div class="bg-white rounded-lg shadow-sm mt-5 p-6">
      <!-- Header -->
      <h2 class="text-xl font-bold text-gray-800 mb-4">Pengaturan Kelas & Mahasiswa</h2>

      <!-- Search + Tambah sejajar -->
      <div class="flex items-center justify-between gap-3 mb-4">
        <div class="flex-1 max-w-sm">
          <app-input
            v-model="search"
            placeholder="Cari NIM atau Nama..."
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
          Tambah Data
        </button-app>
      </div>

      <!-- Table -->
      <table-app
        :items="filteredData"
        :headers="headers"
        :options="tableOptions"
        :server-side="false"
        :searchable="false"
        :show-pagination="true"
        :use-custom-row="true"
        not-found-label="Tidak ada data kelas & mahasiswa."
      >
        <template #customrow="{ rows }">
          <tr
            v-for="(item, index) in rows"
            :key="item.id"
            class="bg-white hover:bg-gray-50 transition"
          >
            <td class="p-4 border-b text-gray-500 text-md">{{ startingIndex + index + 1 }}</td>
            <td class="p-4 border-b font-medium text-gray-700 text-md">{{ item.nim }}</td>
            <td class="p-4 border-b text-gray-700 text-md">{{ item.nama }}</td>
            <td class="p-4 border-b text-gray-600 text-md text-center">{{ item.kelas }}</td>
            <td class="p-4 border-b text-gray-600 text-md">{{ item.program_studi }}</td>
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
          {{ isEditMode ? 'Edit Data' : 'Tambah Data' }}
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
          <label class="block text-sm font-medium text-gray-700 mb-1">NIM</label>
          <app-input v-model="form.nim" placeholder="Masukkan NIM..." label="" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nama Mahasiswa</label>
          <app-input v-model="form.nama" placeholder="Masukkan Nama..." label="" />
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
            <app-input v-model="form.kelas" placeholder="Contoh: IF-01" label="" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Program Studi</label>
            <app-input v-model="form.program_studi" placeholder="Contoh: Teknik Informatika" label="" />
          </div>
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
          Simpan
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

export default {
  name: "KelasAndMahasiswa",
  components: {
    BreadcrumbBima,
    ButtonApp,
    AppInput,
    TableApp,
    ModalApp,
  },
  data() {
    return {
      search: "",
      showModal: false,
      isEditMode: false,
      form: {
        id: null,
        nim: "",
        nama: "",
        kelas: "",
        program_studi: "",
      },
      breadcrumbItems: [
        { text: "Settings", link: "#" },
        { text: "Kelas & Mahasiswa", link: "/app/pengaturan-kelas-mahasiswa" },
      ],
      headers: [
        { text: "No",           value: "no",            align: "start",  sortable: false },
        { text: "NIM",          value: "nim",           align: "start",  sortable: true  },
        { text: "Nama Mahasiswa", value: "nama",        align: "start",  sortable: true  },
        { text: "Kelas",        value: "kelas",         align: "center", sortable: true  },
        { text: "Program Studi", value: "program_studi", align: "start",  sortable: true  },
        { text: "Aksi",         value: "aksi",          align: "center", sortable: false },
      ],
      tableOptions: {
        page: 1,
        itemsPerPage: 10,
        totalItems: 0,
      },
      // Hardcoded constant data
      allData: [
        { id: 1, nim: "20210001", nama: "Ahmad Fauzi", kelas: "IF-01", program_studi: "Teknik Informatika" },
        { id: 2, nim: "20210002", nama: "Siti Aminah", kelas: "IF-01", program_studi: "Teknik Informatika" },
        { id: 3, nim: "20210003", nama: "Budi Santoso", kelas: "IF-02", program_studi: "Sistem Informasi" },
        { id: 4, nim: "20210004", nama: "Dewi Lestari", kelas: "IF-02", program_studi: "Sistem Informasi" },
        { id: 5, nim: "20210005", nama: "Eko Prasetyo", kelas: "IF-01", program_studi: "Teknik Informatika" },
        { id: 6, nim: "20210006", nama: "Fitri Handayani", kelas: "IF-03", program_studi: "Teknik Elektro" },
        { id: 7, nim: "20210007", nama: "Guntur Saputra", kelas: "IF-03", program_studi: "Teknik Elektro" },
        { id: 8, nim: "20210008", nama: "Hana Pertiwi", kelas: "IF-01", program_studi: "Teknik Informatika" },
        { id: 9, nim: "20210009", nama: "Indra Wijaya", kelas: "IF-02", program_studi: "Sistem Informasi" },
        { id: 10, nim: "20210010", nama: "Joko Susilo", kelas: "IF-03", program_studi: "Teknik Elektro" },
      ]
    };
  },
  computed: {
    startingIndex() {
      return ((this.tableOptions.page ?? 1) - 1) * this.tableOptions.itemsPerPage;
    },
    filteredData() {
      if (!this.search) return this.allData;
      const lowerSearch = this.search.toLowerCase();
      return this.allData.filter(
        (item) =>
          item.nim.toLowerCase().includes(lowerSearch) ||
          item.nama.toLowerCase().includes(lowerSearch)
      );
    },
  },
  methods: {
    handleTambah() {
      this.isEditMode = false;
      this.form = { id: null, nim: "", nama: "", kelas: "", program_studi: "" };
      this.showModal = true;
    },
    handleEdit(item) {
      this.isEditMode = true;
      this.form = { ...item };
      this.showModal = true;
    },
    handleDelete(item) {
      if (confirm(`Apakah Anda yakin ingin menghapus data "${item.nama}"?`)) {
        this.allData = this.allData.filter(d => d.id !== item.id);
      }
    },
    handleSimpan() {
      if (this.isEditMode) {
        const index = this.allData.findIndex(d => d.id === this.form.id);
        if (index !== -1) {
          this.allData[index] = { ...this.form };
        }
      } else {
        const newId = this.allData.length > 0 ? Math.max(...this.allData.map(d => d.id)) + 1 : 1;
        this.allData.push({ ...this.form, id: newId });
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
</style>
