<template>
  <div class="mobile-container pb-20" ref="mobileContainer">
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
              @click="fetchData()"
              class="flex-2 py-2 px-4 rounded-lg bg-teal-500 text-white text-sm font-semibold hover:bg-teal-600 shadow-md shadow-teal-200 transition-all active:scale-95"
            >
              Terapkan
            </button>
          </div>
        </div>
      </transition>
    </div>

    <!-- Content Section -->
    <div class="p-4 space-y-4 mt-2 min-h-[400px] relative">
      <!-- Loading Overlay -->
      <div v-if="loading && pengajuans.length > 0" class="absolute inset-0 bg-white/50 backdrop-blur-[1px] z-10 flex items-center justify-center rounded-2xl">
        <div class="flex flex-col items-center gap-2">
          <font-awesome-icon icon="spinner" spin class="text-teal-500 text-3xl" />
          <span class="text-xs font-bold text-teal-600 uppercase tracking-widest">Memuat Data...</span>
        </div>
      </div>

      <div v-if="loading && pengajuans.length === 0" class="flex flex-col items-center justify-center py-20 text-gray-400">
        <font-awesome-icon icon="spinner" spin class="text-5xl mb-4 text-teal-500/20" />
        <p class="font-bold text-teal-600/40 uppercase tracking-widest text-xs">Sedang Mengambil Data...</p>
      </div>

      <div v-else-if="pengajuans.length === 0" class="flex flex-col items-center justify-center py-20 text-gray-400">
        <font-awesome-icon icon="exclamation-circle" class="text-5xl mb-4 opacity-20" />
        <p>Tidak ada data ditemukan</p>
      </div>

      <div 
        v-for="item in pengajuans" 
        :key="item.id"
        class="card-item bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow"
      >
        <!-- Header: No Pengajuan & Edit Button -->
        <div class="flex justify-between items-start mb-4">
          <div class="flex flex-col gap-2">
            <router-link 
              v-if="item.id"
              :to="{ name: 'peminjaman.workflow', params: { id: item.id } }" 
              class="active:scale-95 transition-transform inline-block group"
            >
              <h3 class="text-sm font-extrabold text-blue-600 group-hover:text-blue-800">
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
                <font-awesome-icon icon="clock" class="text-[9px]" />
                {{ formatDateData(item.created_at) }}
              </div>
            </div>
          </div>

          <router-link :to="{ name: 'peminjaman.detail', params: { id: item.id } }">
            <button 
              class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-orange-500 bg-gray-50 rounded-lg transition-colors border border-gray-100"
              title="Edit"
            >
              <font-awesome-icon icon="edit" class="text-sm" />
            </button>
          </router-link>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-2 gap-4 py-4 border-t border-gray-50">
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
            <div>
              <span class="text-[10px] font-bold text-teal-600 bg-teal-50 px-2 py-0.5 rounded-md border border-teal-100 inline-block">
                {{ item.tipe_pengajuan }}
              </span>
            </div>
          </div>
        </div>
        
        <!-- Waktu Peminjaman Info Box -->
        <div class="mt-1 p-3 bg-slate-50 rounded-xl border border-slate-100/50">
          <div class="flex flex-col gap-2">
            <span class="text-[10px] text-gray-400 uppercase font-bold tracking-tight">Jadwal Peminjaman</span>
            <div class="flex flex-col gap-2">
              <div class="flex items-center gap-2.5 text-xs font-bold text-gray-800">
                <div class="w-6 h-6 rounded-full bg-white flex items-center justify-center shadow-sm border border-slate-200">
                  <font-awesome-icon icon="calendar-alt" class="text-teal-500 text-[10px]" />
                </div>
                <span>{{ formatShortDate(item.tanggal_start_peminjaman) }} - {{ formatShortDate(item.tanggal_end_peminjaman) }}</span>
              </div>
              <div class="flex items-center gap-2.5 text-[11px] text-gray-600 font-semibold">
                <div class="w-6 h-6 rounded-full bg-white flex items-center justify-center shadow-sm border border-slate-200">
                  <font-awesome-icon icon="clock" class="text-teal-500 text-[10px]" />
                </div>
                <span>{{ item.jam_mulai }} - {{ item.jam_selesai }} WIB</span>
              </div>
            </div>
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
      loading: false,
    };
  },
  mounted() {
    this.fetchData();
    this.fetchBuildingOptions();
    this.$nextTick(() => {
      // Attach scroll to the parent scrollable container (from MasterLayout)
      const scrollEl = this.$el.closest('.overflow-y-auto');
      if (scrollEl) {
        this._scrollEl = scrollEl;
        scrollEl.addEventListener("scroll", this.handleScroll);
      } else {
        window.addEventListener("scroll", this.handleScroll);
      }
    });
    window.addEventListener("resize", this.onResize);
  },
  beforeDestroy() {
    if (this._scrollEl) {
      this._scrollEl.removeEventListener("scroll", this.handleScroll);
    } else {
      window.removeEventListener("scroll", this.handleScroll);
    }
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
    async fetchData(isAppend = false) {
      if (this.loading) return;
      
      // Reset page to 0 if we are doing a fresh search/filter
      if (!isAppend) {
        this.params.page = 0;
      }
      
      try {
        this.loading = true;
        this.$store.commit("SET_LOADING", !isAppend); // Show global loading only for initial load
        
        const params = {
          ...this.params,
          isAppend,
          search: this.searchQuery,
          tipe: this.filterTipe.map(t => t.id).join(","),
          buildings: this.filterGedung.map(b => b.id).join(","),
          start_date: this.filter.tanggal_mulai,
          end_date: this.filter.tanggal_selesai,
        };
        
        await this.$store.dispatch(DISPATCH.GET_LIST_PENGAJUAN, params);
        
        this.$store.commit("SET_LOADING", false);
        this.showFilters = false;
      } catch (error) {
        this.$store.commit("SET_LOADING", false);
        console.error("Gagal memuat data pengajuan:", error);
      } finally {
        this.loading = false;
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
    handleScroll() {
      // Check if user is near bottom (within 100px)
      const el = this._scrollEl || document.documentElement;
      const scrollHeight = el.scrollHeight;
      const scrollTop = el.scrollTop;
      const clientHeight = el.clientHeight;

      if (scrollTop + clientHeight >= scrollHeight - 100) {
        if (!this.loading && this.pengajuans.length < this.pagination.total_elements) {
          this.loadMore();
        }
      }
    },
    async loadMore() {
      this.params.page += 1;
      await this.fetchData(true);
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
      if (
        s.includes("VERIFIKASI") || 
        s.includes("VALIDASI") || 
        s.includes("PENGECEKAN") || 
        s.includes("PERSIAPAN") || 
        s.includes("MENUNGGU") ||
        s === "VALIDASI_KEMAHASISWAAN" ||
        s === "PENGECEKAN_RUANG_TU" ||
        s === "PERSIAPAN_SARPRAS"
      ) {
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
      return moment(date).format("DD MMM YYYY, HH:mm");
    },
    formatShortDate(date) {
      if (!date) return "-";
      return moment(date).format("DD/MM/YYYY");
    },
  },
};
</script>

<style scoped>
.mobile-container {
  width: 100%;
  overflow-x: hidden;
  background-color: #fcfcfc;
  min-height: 100%;
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
