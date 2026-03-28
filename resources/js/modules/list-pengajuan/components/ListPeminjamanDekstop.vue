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
        <button-app
          color="teal"
          size="sm"
          class="bg-teal-500 hover:bg-teal-600 text-white font-semibold text-xs px-4 py-2"
        >
          <font-awesome-icon icon="plus" class="mr-2" />
          Buat Pengajuan
        </button-app>
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
      >
        <template #no="{ slotProps }">
          {{ slotProps.index + 1 }}
        </template>

        <template #no_pengajuan="{ slotProps }">
          <router-link
            to="#"
            class="font-semibold text-blue-500 hover:underline"
          >
            {{ slotProps.data.no_pengajuan }}
          </router-link>
        </template>

        <template #ruangan="{ slotProps }">
          {{ slotProps.data.ruangan?.room_name || "-" }}
        </template>

        <template #user="{ slotProps }">
          {{ slotProps.data.user?.name || "-" }}
        </template>

        <template #tipe_pengajuan="{ slotProps }">
          <span class="text-xs font-semibold text-gray-600">
            {{ slotProps.data.tipe_pengajuan }}
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

        <template #aksi>
          <div class="flex justify-center w-full">
            <button
              class="text-gray-400 hover:text-orange-500 transition-colors mr-2 text-lg"
              title="Edit"
            >
              <font-awesome-icon icon="edit" />
            </button>
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
        { text: "Status", value: "status", align: "start" },
        { text: "Aksi", value: "aksi", align: "center" },
      ],
    };
  },
  computed: {
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
          tipe: this.filterTipe.join(","),
          buildings: this.filterGedung.join(","),
          start_date: this.filter.tanggal_mulai,
          end_date: this.filter.tanggal_selesai,
        };
        await this.$store.dispatch("listPengajuan/getPengajuanData", params);
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
      const s = status.toLowerCase();

      if (s.includes("menunggu")) {
        return {
          backgroundColor: "#ffe6b6",
          color: "#f48c06",
          borderColor: "rgba(244, 140, 6, 0.2)",
        };
      } else if (s.includes("completed")) {
        return {
          backgroundColor: "#c0f7f2",
          color: "#46bebb",
          borderColor: "rgba(70, 190, 187, 0.2)",
        };
      } else if (s.includes("koreksi") || s.includes("tolak") || s.includes("rejected")) {
        return {
          backgroundColor: "#ffb3ad",
          color: "#900b09",
          borderColor: "rgba(144, 11, 9, 0.2)",
        };
      }

      return {
        backgroundColor: "#f3f4f6", // Default gray
        color: "#374151",
        borderColor: "rgba(55, 65, 81, 0.1)",
      };
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
