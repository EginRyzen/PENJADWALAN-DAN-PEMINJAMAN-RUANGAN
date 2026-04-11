<template>
  <div class="p-6">
    <!-- Breadcrumb -->
    <breadcrumb-bima :items="breadcrumbItems" class="mb-6" />

    <!-- Tabs Filter -->
    <div class="flex items-center gap-1 bg-gray-100/50 p-1.5 rounded-2xl w-fit mb-8 border border-gray-100 backdrop-blur-sm">
      <button
        v-for="tab in tabs"
        :key="tab.value"
        @click="activeTab = tab.value"
        class="px-8 py-2.5 rounded-xl text-sm font-bold transition-all duration-300 flex items-center gap-2"
        :class="activeTab === tab.value
          ? 'bg-white text-teal-600 shadow-[0_4px_12px_rgba(20,184,166,0.12)] border border-teal-50 scale-100'
          : 'text-gray-400 hover:text-gray-600 hover:bg-white/50 border border-transparent scale-95'"
      >
        <span class="w-2 h-2 rounded-full" :class="activeTab === tab.value ? 'bg-teal-500' : 'bg-gray-300'"></span>
        {{ tab.label }}
      </button>
    </div>

    <div v-if="loading" class="flex flex-col items-center justify-center py-20 space-y-4">
      <div class="w-12 h-12 border-4 border-teal-200 border-t-teal-500 rounded-full animate-spin"></div>
      <p class="text-teal-600 font-bold animate-pulse">Menghubungkan ke pusat data...</p>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-4 gap-6 animate-fade-in">
      <!-- Left Column: SKS Duration & Info -->
      <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <div class="flex items-center gap-3 mb-6">
            <div class="p-2 bg-teal-50 rounded-lg">
              <svg class="w-6 h-6 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Durasi SKS</h3>
          </div>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-600 mb-2">1 SKS Setara Dengan</label>
              <div class="flex items-center gap-3">
                <div class="w-24">
                  <app-input
                    v-model="sksDuration"
                    type="number"
                    placeholder="50"
                    label=""
                    class="text-center font-bold text-lg"
                  />
                </div>
                <span class="text-gray-500 font-medium">Menit</span>
              </div>
              <p class="mt-2 text-xs text-gray-400 italic">
                * Durasi ini akan menjadi acuan perhitungan waktu otomatis untuk kategori {{ currentTabLabel }}.
              </p>
            </div>

            <div class="pt-4">
              <button-app
                type="primary"
                color="teal"
                :loading="isSaving"
                class="w-full bg-teal-400 hover:bg-teal-500 text-white font-semibold py-3 rounded-xl shadow-md transition-all duration-200"
                @click="handleSave"
              >
                Simpan Konfigurasi
              </button-app>
            </div>
          </div>
        </div>

        <div class="bg-teal-500 rounded-xl shadow-md p-6 text-white overflow-hidden relative group">
          <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-all duration-500"></div>
          <h4 class="font-bold mb-2 flex items-center gap-2 relative z-10">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Informasi
          </h4>
          <p class="text-sm text-teal-50 leading-relaxed relative z-10">
            Pengaturan ini khusus untuk kategori <strong>{{ currentTabLabel }}</strong>. Pastikan durasi dan jadwal sudah sesuai dengan kebijakan akademik.
          </p>
        </div>
      </div>

      <!-- Right Column: Daily Schedule -->
      <div class="lg:col-span-3">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-800">Jadwal Operasional {{ currentTabLabel }}</h3>
            <div class="flex items-center gap-2">
               <span class="w-3 h-3 bg-teal-400 rounded-full animate-pulse"></span>
               <span class="text-xs font-bold text-teal-600 uppercase tracking-wider">Sistem Aktif</span>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-white border-b border-gray-100">
                  <th rowspan="2" class="p-4 text-xs font-bold text-gray-400 uppercase tracking-widest pl-8 border-r border-gray-50">Hari</th>
                  <th colspan="2" class="p-4 text-xs font-bold text-teal-500 uppercase tracking-widest text-center border-r border-gray-50 bg-teal-50/30">Jam Operasional</th>
                  <th colspan="2" class="p-4 text-xs font-bold text-orange-400 uppercase tracking-widest text-center border-r border-gray-50 bg-orange-50/30">Jam Istirahat</th>
                  <th rowspan="2" class="p-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-center">Status</th>
                </tr>
                <tr class="bg-white border-b border-gray-100">
                  <th class="p-2 text-[10px] font-bold text-gray-400 uppercase text-center border-r border-gray-50">Mulai</th>
                  <th class="p-2 text-[10px] font-bold text-gray-400 uppercase text-center border-r border-gray-50">Selesai</th>
                  <th class="p-2 text-[10px] font-bold text-gray-400 uppercase text-center border-r border-gray-50">Mulai</th>
                  <th class="p-2 text-[10px] font-bold text-gray-400 uppercase text-center border-r border-gray-50">Selesai</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                <tr v-for="(day, index) in dailySchedules" :key="index" class="hover:bg-gray-50/30 transition">
                  <td class="p-4 pl-8 border-r border-gray-50">
                    <span class="font-semibold text-gray-700 capitalize">{{ day.name }}</span>
                  </td>
                  <td class="p-3 border-r border-gray-50 w-32">
                    <time-input v-model="day.start" :disabled="!day.isActive" />
                  </td>
                  <td class="p-3 border-r border-gray-50 w-32">
                    <time-input v-model="day.end" :disabled="!day.isActive" />
                  </td>
                  <td class="p-3 border-r border-gray-50 w-32">
                    <time-input v-model="day.breakStart" :disabled="!day.isActive" />
                  </td>
                  <td class="p-3 border-r border-gray-50 w-32">
                    <time-input v-model="day.breakEnd" :disabled="!day.isActive" />
                  </td>
                  <td class="p-4">
                    <div class="flex justify-center">
                      <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" v-model="day.isActive" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-400"></div>
                      </label>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="p-6 bg-gray-50 border-t border-gray-100 flex justify-end">
            <button-app
              type="primary"
              color="teal"
              :loading="isSaving"
              class="bg-teal-500 hover:bg-teal-600 text-white font-semibold px-10 py-3 rounded-xl shadow-lg transition-all duration-200 flex items-center gap-2"
              @click="handleSave"
            >
              <svg class="w-5 h-5 font-bold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
              </svg>
              Simpan Konfigurasi
            </button-app>
          </div>
        </div>
      </div>
    </div>

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
import TimeInput from "../components/TimeInput.vue";
import ModalPopUpSuccess from "@/core/components/ModalPopUpSuccess.vue";
import DISPATCH from "@/core/plugins/constants/dispatches";

export default {
  name: "UjianSettings",
  components: {
    BreadcrumbBima,
    ButtonApp,
    AppInput,
    TimeInput,
    ModalPopUpSuccess,
  },
  data() {
    return {
      activeTab: "uts",
      tabs: [
        { label: "Ujian Tengah Semester (UTS)", value: "uts" },
        { label: "Ujian Akhir Semester (UAS)", value: "uas" },
        { label: "Mata Kuliah / Pelajaran", value: "pelajaran" },
      ],
      sksDuration: 50,
      dailySchedules: [],
      loading: false,
      isSaving: false,
      breadcrumbItems: [
        { text: "Settings", link: "#" },
        { text: "Ujian", link: "/app/pengaturan-ujian-ruangan" },
      ],
      // Success Modal State
      showSuccessModal: false,
      successData: {
        title: "",
        description: "",
        buttonText: "Oke",
        action: () => {},
      },
    };
  },
  computed: {
    sksSetting() {
      return this.$store.state.settings.sksSetting;
    },
    currentTabLabel() {
      return this.tabs.find(t => t.value === this.activeTab)?.label || "";
    }
  },
  watch: {
    activeTab: {
      handler: "fetchData",
      immediate: true,
    },
    sksSetting: {
      handler(newVal) {
        if (newVal) {
          this.sksDuration = newVal.duration_minutes;
          this.mapSchedules(newVal.operationalSchedules);
        }
      },
      deep: true,
    }
  },
  methods: {
    async fetchData() {
      this.loading = true;
      try {
        await this.$store.dispatch(DISPATCH.GET_SKS_SETTING, { type: this.activeTab });
      } catch (e) {
        console.error("Gagal mengambil data SKS:", e);
      } finally {
        this.loading = false;
      }
    },
    mapSchedules(schedules) {
      if (!schedules || schedules.length === 0) {
        this.dailySchedules = [];
        return;
      }
      this.dailySchedules = schedules.map(s => ({
        id: s.id,
        name: s.day,
        start: s.start_time.substring(0, 5),
        end: s.end_time.substring(0, 5),
        breakStart: s.break_start.substring(0, 5),
        breakEnd: s.break_end.substring(0, 5),
        isActive: s.status === 'aktif',
      }));
    },
    async handleSave() {
      if (this.isSaving) return;
      this.isSaving = true;
      this.$store.commit("SET_LOADING", true);
      try {
        const payload = {
          id: this.sksSetting.id,
          duration_minutes: this.sksDuration,
          schedules: this.dailySchedules.map(s => ({
            id: s.id,
            start_time: s.start,
            end_time: s.end,
            break_start: s.breakStart,
            break_end: s.breakEnd,
            status: s.isActive ? 'aktif' : 'non-aktif',
          }))
        };
        
        await this.$store.dispatch(DISPATCH.UPDATE_SKS_SETTING, payload);
        
        this.successData = {
          title: "Pembaruan Berhasil",
          description: `Konfigurasi untuk ${this.currentTabLabel} telah berhasil disimpan ke dalam sistem.`,
          buttonText: "Selesai",
          action: () => { this.fetchData(); },
        };
        this.showSuccessModal = true;
      } catch (e) {
        console.error("Gagal menyimpan:", e);
      } finally {
        this.isSaving = false;
        this.$store.commit("SET_LOADING", false);
      }
    },
  },
};
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.5s ease-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
