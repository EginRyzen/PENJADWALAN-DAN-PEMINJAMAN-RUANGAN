<template>
  <div class="mobile-container pb-20">
    <!-- Header Section -->
    <div class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-gray-100 p-4 shadow-sm">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold text-gray-800">Daftar Peminjaman</h1>
        <router-link :to="{ name: 'peminjaman.create' }">
          <button class="bg-teal-500 hover:bg-teal-600 text-white p-2.5 rounded-full shadow-lg transition-all active:scale-95">
            <font-awesome-icon icon="plus" />
          </button>
        </router-link>
      </div>

      <!-- Search Bar -->
      <div class="relative group">
        <app-input
          v-model="searchQuery"
          placeholder="Cari No. Pengajuan..."
          class="w-full"
        >
          <template #icon-right>
            <font-awesome-icon icon="search" class="text-gray-400 group-focus-within:text-teal-500 transition-colors" />
          </template>
        </app-input>
      </div>

      <!-- Filter Toggle -->
      <button 
        @click="showFilters = !showFilters"
        class="mt-3 flex items-center justify-center gap-2 w-full py-2 px-4 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors"
      >
        <font-awesome-icon icon="cog" :class="{'rotate-90': showFilters}" class="transition-transform duration-300" />
        {{ showFilters ? 'Sembunyikan Filter' : 'Tampilkan Filter' }}
      </button>

      <!-- Expandable Filter Section -->
      <transition 
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="transform -translate-y-4 opacity-0"
        enter-to-class="transform translate-y-0 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="transform translate-y-0 opacity-100"
        leave-to-class="transform -translate-y-4 opacity-0"
      >
        <div v-if="showFilters" class="mt-4 p-4 bg-gray-50 rounded-xl border border-gray-100 space-y-4 shadow-inner">
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

          <autocomplete
            label="Tipe"
            :options="typeOptions"
            item-value="id"
            item-text="name"
            placeholder="Pilih Tipe..."
            v-model="filterTipe"
            multiple
            show-select-all
          />

          <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Rentang Tanggal</label>
            <button
              @click="modalDatePicker = !modalDatePicker"
              class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 flex items-center justify-between text-sm text-gray-700"
            >
              <div class="flex items-center gap-2">
                <font-awesome-icon icon="calendar" class="text-teal-500" />
                <span v-if="filter.tanggal_mulai && filter.tanggal_selesai">
                  {{ formatDateRange }}
                </span>
                <span v-else class="text-gray-400">Pilih Tanggal</span>
              </div>
              <font-awesome-icon 
                v-if="filter.tanggal_mulai" 
                @click.stop="resetDatePicker" 
                icon="times" 
                class="text-red-400 hover:text-red-600" 
              />
            </button>
          </div>

          <div class="flex gap-2 pt-2">
            <button 
              @click="handleReset"
              class="flex-1 py-2 px-4 rounded-lg bg-white border border-gray-200 text-sm font-semibold text-gray-500 hover:bg-gray-100 transition-colors"
            >
              Reset
            </button>
            <button 
              @click="fetchData"
              class="flex-2 py-2 px-4 rounded-lg bg-teal-500 text-white text-sm font-semibold hover:bg-teal-600 shadow-md shadow-teal-200 transition-all active:scale-95"
            >
              Terapkan
            </button>
          </div>
        </div>
      </transition>
    </div>

    <!-- Content Section -->
    <div class="p-4 space-y-4 mt-2">
      <div v-if="pengajuans.length === 0" class="flex flex-col items-center justify-center py-20 text-gray-400">
        <font-awesome-icon icon="exclamation-circle" class="text-5xl mb-4 opacity-20" />
        <p>Tidak ada data ditemukan</p>
      </div>

      <div 
        v-for="item in pengajuans" 
        :key="item.id"
        class="card-item bg-white rounded-2xl p-4 border border-gray-100 shadow-sm hover:shadow-md transition-shadow"
      >
        <div class="mb-3">
          <div class="flex flex-col gap-1.5">
            <router-link 
              v-if="item.id"
              :to="{ name: 'peminjaman.workflow', params: { id: item.id } }" 
              class="active:scale-95 transition-transform inline-block group"
            >
              <h3 class="text-sm font-bold text-blue-600 group-hover:text-blue-800 group-hover:underline">
                {{ item.no_pengajuan }}
              </h3>
            </router-link>
            <span v-else class="text-sm font-bold text-gray-400 italic">
              {{ item.no_pengajuan }}
            </span>
            
            <div class="flex items-center gap-2">
              <span
                class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase ring-1 ring-inset whitespace-nowrap"
                :style="getStatusStyle(item.status?.nama_status)"
              >
                {{ item.status?.nama_status || "Pending" }}
              </span>
              <div class="flex items-center gap-1.5 text-[10px] text-gray-400 font-medium uppercase tracking-wider">
                <font-awesome-icon icon="clock" />
                {{ formatDateData(item.created_at) }}
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mt-3 pt-3 border-t border-gray-50">
          <div class="space-y-1">
            <span class="text-[10px] text-gray-400 uppercase font-bold tracking-tight">Ruangan</span>
            <p class="text-sm font-semibold text-gray-700 truncate">{{ item.ruangan?.room_name || "-" }}</p>
          </div>
          <div class="space-y-1">
            <span class="text-[10px] text-gray-400 uppercase font-bold tracking-tight">Peminjam</span>
            <p class="text-sm font-semibold text-gray-700 truncate">{{ item.user?.name || "-" }}</p>
          </div>
          <div class="space-y-1">
            <span class="text-[10px] text-gray-400 uppercase font-bold tracking-tight">Tipe</span>
            <p class="text-xs font-medium text-gray-600">{{ item.tipe_pengajuan }}</p>
          </div>
          <div class="flex justify-end items-end">
            <router-link :to="{ name: 'peminjaman.detail', params: { id: item.id } }">
              <button 
                class="p-2 text-gray-400 hover:text-orange-500 bg-gray-50 rounded-lg transition-colors"
                title="Edit"
              >
                <font-awesome-icon icon="edit" />
              </button>
            </router-link>
          </div>
        </div>
      </div>
      
      <!-- Load More / Pagination -->
      <div v-if="pagination.total_elements > pengajuans.length" class="flex justify-center pt-4">
        <button 
          @click="loadMore"
          class="px-6 py-2 rounded-full border border-teal-500 text-teal-600 text-sm font-bold hover:bg-teal-50 transition-colors"
        >
          Muat Lebih Banyak
        </button>
      </div>
    </div>

    <!-- Global Components -->
    <ModalDatePicker
      v-if="modalDatePicker && !isMobile"
      :show="modalDatePicker"
      :date="datePicker"
      @close="modalDatePicker = false"
      @submit="submitDatePicker"
    />

    <ModalDatePickerMobile
      v-if="modalDatePicker && isMobile"
      :show="modalDatePicker"
      :date="datePicker"
      @close="modalDatePicker = false"
      @submit="submitDatePicker"
    />
  </div>
</template>

<script>
import ButtonApp from "@/core/components/Button.vue";
import AppInput from "@/core/components/AppInput.vue";
import Autocomplete from "@/core/components/Autocomplete.vue";
import ModalDatePicker from "@/core/components/ModalDatePicker.vue";
import ModalDatePickerMobile from "@/core/components/ModalDatePickerMobile.vue";
import DISPATCH from "@/core/plugins/constants/dispatches";
import moment from "moment";
import _ from "lodash";

export default {
  name: "ListPeminjamanMobile",
  components: {
    ButtonApp,
    AppInput,
    Autocomplete,
    ModalDatePicker,
    ModalDatePickerMobile,
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
      showFilters: false,
      modalDatePicker: false,
      isMobile: window.innerWidth < 768,
      datePicker: {
        start: new Date(moment().startOf("month").format()),
        end: new Date(moment().endOf("month").format()),
      },
      params: {
        page: 0,
        size: 10,
      },
    };
  },
  mounted() {
    this.fetchData();
    this.fetchBuildingOptions();
    window.addEventListener("resize", this.onResize);
  },
  beforeDestroy() {
    window.removeEventListener("resize", this.onResize);
  },
  computed: {
    pengajuans() {
      return this.$store.state.listPengajuan.pengajuans || [];
    },
    pagination() {
      return this.$store.state.listPengajuan.pagination || { total_elements: 0 };
    },
    formatDateRange() {
      if (!this.filter.tanggal_mulai) return "";
      return `${moment(this.filter.tanggal_mulai).format("DD/MM")} - ${moment(this.filter.tanggal_selesai).format("DD/MM")}`;
    }
  },
  watch: {
    searchQuery: _.debounce(function () {
      this.params.page = 0;
      this.fetchData();
    }, 500),
  },
  methods: {
    onResize() {
      this.isMobile = window.innerWidth < 768;
    },
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
        await this.$store.dispatch("listPengajuan/getPengajuanData", params);
        this.$store.commit("SET_LOADING", false);
        this.showFilters = false; // Close filter panel after applying
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
    loadMore() {
      this.params.size += 10;
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
        // Don't fetch immediately, let user click Terapkan or handle as needed
      }
    },
    getStatusStyle(status) {
      if (!status) return {};
      const s = status.toLowerCase();

      if (s.includes("menunggu")) {
        return {
          backgroundColor: "#fff7ed",
          color: "#ea580c",
          borderColor: "#ffedd5",
        };
      } else if (s.includes("completed")) {
        return {
          backgroundColor: "#f0fdfa",
          color: "#0d9488",
          borderColor: "#ccfbf1",
        };
      } else if (s.includes("koreksi") || s.includes("tolak") || s.includes("rejected")) {
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
      return moment(date).format("DD MMM YYYY, HH:mm");
    },
  },
};
</script>

<style scoped>
.mobile-container {
  max-width: 100%;
  overflow-x: hidden;
  background-color: #fcfcfc;
  min-height: 100vh;
}

.card-item {
  animation: slideUp 0.4s ease-out forwards;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Glassmorphism effect for sticky header */
.sticky {
  -webkit-backdrop-filter: blur(8px);
}

.flex-2 {
  flex: 2;
}
</style>
