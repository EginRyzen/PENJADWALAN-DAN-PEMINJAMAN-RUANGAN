<template>
  <div class="bg-white rounded-lg shadow-md border border-gray-100 min-h-[600px]">
    <!-- Header with Action Button -->
    <div class="p-6 flex justify-center items-center">
      <h6 class="font-semibold text-3xl text-gray-800">Daftar Peminjaman Ruangan</h6>
    </div>

    <!-- Integrated Filter Section -->
    <div class="p-6 bg-gray-50/50">
      <div class="flex flex-col gap-6 mb-4">
        <!-- Search Row (Quarter Width) -->
        <div class="w-full md:w-1/4">
          <app-input
            v-model="searchQuery"
            placeholder="Search No. Pengajuan..."
            label="Pencarian"
          >
            <template #icon-right>
              <font-awesome-icon icon="search" class="text-gray-400" />
            </template>
          </app-input>
        </div>

        <!-- Bottom Row (Grid) -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
          <!-- Gedung Filter -->
          <div class="md:col-span-4">
            <autocomplete
              label="Gedung"
              :options="buildingOptions"
              item-value="id"
              item-text="name"
              placeholder="Pilih Gedung..."
              v-model="filterGedung"
              multiple
              show-select-all
            />
          </div>

          <!-- Tipe Pengajuan Filter -->
          <div class="md:col-span-4">
            <autocomplete
              label="Tipe Pengajuan"
              :options="typeOptions"
              item-value="id"
              item-text="name"
              placeholder="Pilih Tipe..."
              v-model="filterTipe"
              multiple
              show-select-all
            />
          </div>

          <!-- Buttons -->
          <div class="md:col-span-4 flex gap-3">
            <button-app
              color="teal"
              type="secondary"
              class="flex-1 font-semibold py-2 text-gray-500"
              @click="handleReset"
            >
              Reset
            </button-app>
            <button-app
              color="teal"
              class="flex-1 bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2"
              @click="fetchData"
            >
              Terapkan
            </button-app>
          </div>
        </div>
      </div>

      <div class="flex justify-between items-center">
        <div class="flex md:flex-row flex-col mt-4 py-3 gap-4">
          <div class="flex justify-center items-center">
            <h1 class="text-sm font-semibold">Waktu Pembuatan :</h1>
          </div>
          <button
            @click="modalDatePicker = !modalDatePicker"
            class="rounded-md border border-teal-500 text-teal-500 bg-white px-4 py-1.5 flex items-center gap-2"
          >
            <span class="icon-calendar" />

            <template v-if="filter.tanggal_mulai && filter.tanggal_selesai">
              <span>
                {{ filter.tanggal_mulai }} -
                {{ filter.tanggal_selesai }}
              </span>
              <span
                v-if="filter.tanggal_mulai && filter.tanggal_selesai"
                @click.stop="resetDatePicker"
                class="icon-x cursor-pointer"
                style="color: red"
              ></span>
            </template>
            <template v-else>
              <span>Pilih Tanggal</span>
            </template>
          </button>
          <button
            @click="downloadDataTable"
            class="w-32 h-10 bg-white border border-teal-500 text-teal-500 rounded-md flex justify-center items-center relative transition-colors hover:bg-teal-50"
          >
            <div class="tooltip flex justify-center items-center w-full h-full">
              <font-awesome-icon icon="download" class="text-lg" />
              <span class="tooltiptext">Download</span>
            </div>
          </button>
        </div>
        <router-link v-if="canCreatePengajuan" :to="{ name: 'peminjaman.create' }">
          <button-app
            color="teal"
            size="sm"
            class="bg-teal-500 hover:bg-teal-600 text-white font-semibold text-xs px-4 py-2"
          >
            <font-awesome-icon icon="plus" class="mr-2" />
            Buat Pengajuan
          </button-app>
        </router-link>
      </div>
      
      <ModalDatePicker
        v-if="modalDatePicker"
        :show="modalDatePicker"
        :date="datePicker"
        @close="modalDatePicker = false"
        @submit="submitDatePicker"
      />
    </div>

    <!-- Table Content -->
    <div class="p-6">
      <table-app
        :headers="headers"
        :items="pengajuans"
        :options="tableOptions"
        :searchable="false"
        server-side
        @pageChange="handlePageChange"
        @update:options="handleOptionsChange"
      >
        <template #no="{ slotProps }">
          {{ slotProps.index + 1 }}
        </template>

        <template #no_pengajuan="{ slotProps }">
          <router-link 
            v-if="slotProps.data.id"
            :to="{ name: 'peminjaman.workflow', params: { id: slotProps.data.id } }" 
            class="text-blue-600 hover:text-blue-800 hover:underline transition-all font-bold"
          >
            {{ slotProps.data.no_pengajuan }}
          </router-link>
          <span v-else class="font-semibold text-gray-400 italic">
            {{ slotProps.data.no_pengajuan }}
          </span>
        </template>

        <template #ruangan="{ slotProps }">
          {{ slotProps.data.ruangan?.room_name || "-" }}
        </template>

        <template #user="{ slotProps }">
          {{ slotProps.data.user?.name || "-" }}
        </template>

        <template #tipe_pengajuan="{ slotProps }">
          <span class="text-[11px] font-bold text-teal-600 bg-teal-50 px-2 py-0.5 rounded-full border border-teal-100">
            {{ slotProps.data.tipe_pengajuan }}
          </span>
        </template>

        <template #waktu_peminjaman="{ slotProps }">
          <div class="flex flex-col text-[11px] leading-tight min-w-[140px]">
            <div class="flex items-center gap-2 font-bold text-gray-700">
              <font-awesome-icon icon="calendar-alt" class="text-teal-500 w-3" />
              <span>{{ formatShortDate(slotProps.data.tanggal_start_peminjaman) }} - {{ formatShortDate(slotProps.data.tanggal_end_peminjaman) }}</span>
            </div>
            <div class="flex items-center gap-2 text-gray-500 mt-1 font-medium">
              <font-awesome-icon icon="clock" class="text-teal-500 w-3" />
              <span>{{ slotProps.data.jam_mulai }} - {{ slotProps.data.jam_selesai }}</span>
            </div>
          </div>
        </template>

        <template #created_at="{ slotProps }">
          <span class="text-xs font-medium text-gray-600">
            {{ formatDateData(slotProps.data.created_at) }}
          </span>
        </template>

        <template #status="{ slotProps }">
          <span
            class="px-3 py-1 rounded-md text-xs font-bold uppercase ring-1 ring-inset"
            :style="getStatusStyle(slotProps.data.status?.nama_status)"
          >
            {{ slotProps.data.status?.nama_status || "Pending" }}
          </span>
        </template>

        <template #aksi="{ slotProps }">
          <div class="flex justify-center w-full">
            <router-link :to="{ name: 'peminjaman.detail', params: { id: slotProps.data.id } }">
              <button
                class="text-gray-400 hover:text-orange-500 transition-colors mr-2 text-lg"
                title="Edit"
              >
                <font-awesome-icon icon="edit" />
              </button>
            </router-link>
          </div>
        </template>
      </table-app>
    </div>
  </div>
</template>

<script>
import ButtonApp from "@/core/components/Button.vue";
import TableApp from "@/core/components/Table.vue";
import AppInput from "@/core/components/AppInput.vue";
import Autocomplete from "@/core/components/Autocomplete.vue";
import ModalDatePicker from "@/core/components/ModalDatePicker.vue";
import DISPATCH from "@/core/plugins/constants/dispatches";
import moment from "moment";
import _ from "lodash";

export default {
  name: "ListPeminjamanDekstop",
  components: {
    ButtonApp,
    TableApp,
    AppInput,
    Autocomplete,
    ModalDatePicker,
  },
  data() {
    return {
      searchQuery: "",
      filterTipe: [],
      typeOptions: [
        { id: "PEMBELAJARAN", name: "Pembelajaran" },
        { id: "EVENT", name: "Event" },
      ],
      filterGedung: [],
      buildingOptions: [],
      filter: {
        tanggal_mulai: "",
        tanggal_selesai: "",
      },
      modalDatePicker: false,
      datePicker: {
        start: new Date(moment().startOf("month").format()),
        end: new Date(moment().endOf("month").format()),
      },
      params: {
        page: 0,
        size: 10,
      },
      headers: [
        { text: "No", value: "no", align: "start", width: "w-12" },
        { text: "No. Pengajuan", value: "no_pengajuan", align: "start" },
        { text: "Ruangan", value: "ruangan", align: "start" },
        { text: "Peminjam", value: "user", align: "start" },
        { text: "Tipe", value: "tipe_pengajuan", align: "start" },
        { text: "Waktu Peminjaman", value: "waktu_peminjaman", align: "start" },
        { text: "Waktu Pembuatan", value: "created_at", align: "start" },
        { text: "Status", value: "status", align: "start" },
        { text: "Aksi", value: "aksi", align: "center" },
      ],
    };
  },
  computed: {
    canCreatePengajuan() {
      // Coba ambil dari Vuex state terlebih dahulu
      const user = this.$store.state.auth.user;
      let roles = [];

      if (user && user.roles) {
        roles = user.roles;
      } else {
        // Fallback: Ambil dari localStorage jika user belum ter-load dari API akibat refresh
        const savedRoles = localStorage.getItem('user_roles');
        if (savedRoles) {
          try {
            roles = JSON.parse(savedRoles);
          } catch (e) {
            roles = [];
          }
        }
      }

      return roles.includes('MAHASISWA') || roles.includes('DOSEN');
    },
    pengajuans() {
      return this.$store.state.listPengajuan.pengajuans;
    },
    pagination() {
      return this.$store.state.listPengajuan.pagination;
    },
    tableOptions() {
      return {
        page: this.params.page + 1,
        itemsPerPage: this.params.size,
        totalItems: this.pagination.total_elements,
      };
    },
  },
  watch: {
    searchQuery: _.debounce(function () {
      this.params.page = 0;
      this.fetchData();
    }, 500),
  },
  methods: {
    async fetchData() {
      try {
        this.$store.commit("SET_LOADING", true);
        const params = {
          ...this.params,
          search: this.searchQuery,
          tipe: this.filterTipe.map(t => t.id).join(","),
          buildings: this.filterGedung.map(b => b.id).join(","),
          start_date: this.filter.tanggal_mulai,
          end_date: this.filter.tanggal_selesai,
        };
        await this.$store.dispatch(DISPATCH.GET_LIST_PENGAJUAN, params);
        this.$store.commit("SET_LOADING", false);
      } catch (error) {
        this.$store.commit("SET_LOADING", false);
        console.error("Gagal memuat data pengajuan:", error);
      }
    },
    async fetchBuildingOptions() {
      try {
        const data = await this.$store.dispatch(DISPATCH.GET_BUILDINGS_ONLY, {
          active: "active",
        });

        this.buildingOptions = data.map((item) => ({
          id: item.id,
          name: item.building_code,
        }));
      } catch (error) {
        console.error("Gagal memuat filter gedung:", error);
      }
    },
    handlePageChange(page) {
      this.params.page = page - 1;
      this.fetchData();
    },
    handleOptionsChange(options) {
      if (this.params.size !== options.itemsPerPage) {
        this.params.size = options.itemsPerPage;
        this.params.page = 0;
        this.fetchData();
      }
    },
    handleReset() {
      this.searchQuery = "";
      this.filterTipe = [];
      this.filterGedung = [];
      this.filter.tanggal_mulai = "";
      this.filter.tanggal_selesai = "";
      this.datePicker = {
        start: new Date(moment().startOf("month").format()),
        end: new Date(moment().endOf("month").format()),
      };
      this.params.page = 0;
      this.fetchData();
    },
    resetDatePicker() {
      this.filter.tanggal_mulai = "";
      this.filter.tanggal_selesai = "";
      this.datePicker = {
        start: new Date(moment().startOf("month").format()),
        end: new Date(moment().endOf("month").format()),
      };
      this.params.page = 0;
      this.fetchData();
    },
    submitDatePicker(date) {
      if (date && date.start && date.end) {
        this.filter.tanggal_mulai = moment(date.start).format("YYYY-MM-DD");
        this.filter.tanggal_selesai = moment(date.end).format("YYYY-MM-DD");
        this.datePicker = { ...date };
        this.modalDatePicker = false;
        this.params.page = 0;
        this.fetchData();
      }
    },
    downloadDataTable() {
      console.log("Download data triggered");
    },
    getStatusStyle(status) {
      if (!status) return {};
      const s = status.toUpperCase();

      // Drafts (Gray/Slate)
      if (s.includes("DRAFT")) {
        return {
          backgroundColor: "#f1f5f9",
          color: "#475569",
          borderColor: "#e2e8f0",
        };
      }
      
      // Approved / Final (Teal/Emerald)
      if (s === "DISETUJUI" || s.includes("PENGESAHAN") || s.includes("COMPLETED")) {
        return {
          backgroundColor: "#f0fdfa",
          color: "#0d9488",
          borderColor: "#ccfbf1",
        };
      }

      // Verification / Process (Amber/Orange)
      if (s.includes("VERIFIKASI") || s.includes("VALIDASI") || s.includes("PENGECEKAN") || s.includes("PERSIAPAN") || s.includes("MENUNGGU")) {
        return {
          backgroundColor: "#fff7ed",
          color: "#ea580c",
          borderColor: "#ffedd5",
        };
      }

      // Rejected / Correction (Red)
      if (s.includes("KOREKSI") || s.includes("TOLAK") || s.includes("REJECTED")) {
        return {
          backgroundColor: "#fef2f2",
          color: "#dc2626",
          borderColor: "#fee2e2",
        };
      }

      return {
        backgroundColor: "#f9fafb",
        color: "#4b5563",
        borderColor: "#f3f4f6",
      };
    },
    formatDateData(date) {
      if (!date) return "-";
      return moment(date).format("DD/MM/YYYY HH:mm");
    },
    formatShortDate(date) {
      if (!date) return "-";
      return moment(date).format("DD/MM/YYYY");
    },
  },
  mounted() {
    this.fetchData();
    this.fetchBuildingOptions();
  },
};
</script>

<style scoped>
</style>
