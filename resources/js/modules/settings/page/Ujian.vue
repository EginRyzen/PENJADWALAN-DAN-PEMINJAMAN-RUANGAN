<template>
  <div class="p-6">
    <!-- Breadcrumb -->
    <breadcrumb-bima :items="breadcrumbItems" class="mb-6" />

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
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
                * Durasi ini akan menjadi acuan perhitungan waktu ujian otomatis.
              </p>
            </div>

            <div class="pt-4">
              <button-app
                type="primary"
                color="teal"
                class="w-full bg-teal-400 hover:bg-teal-500 text-white font-semibold py-3 rounded-xl shadow-md transition-all duration-200"
                @click="handleSaveSks"
              >
                Simpan Durasi
              </button-app>
            </div>
          </div>
        </div>

        <div class="bg-teal-500 rounded-xl shadow-md p-6 text-white">
          <h4 class="font-bold mb-2 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Informasi
          </h4>
          <p class="text-sm text-teal-50  leading-relaxed">
            Pengaturan ini digunakan untuk validasi ketersediaan ruangan saat penjadwalan ujian dan peminjaman rutin.
          </p>
        </div>
      </div>

      <!-- Right Column: Daily Schedule -->
      <div class="lg:col-span-3">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-800">Jadwal Operasional & Istirahat</h3>
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
                    <span class="font-semibold text-gray-700">{{ day.name }}</span>
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
              class="bg-teal-500 hover:bg-teal-600 text-white font-semibold px-10 py-3 rounded-xl shadow-lg transition-all duration-200 flex items-center gap-2"
              @click="handleSaveSchedule"
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
  </div>
</template>

<script>
import BreadcrumbBima from "@/core/components/Breadcrumb.vue";
import ButtonApp from "@/core/components/Button.vue";
import AppInput from "@/core/components/AppInput.vue";
import TimeInput from "../components/TimeInput.vue";

export default {
  name: "UjianSettings",
  components: {
    BreadcrumbBima,
    ButtonApp,
    AppInput,
    TimeInput,
  },
  data() {
    return {
      sksDuration: 50,
      breadcrumbItems: [
        { text: "Settings", link: "#" },
        { text: "Ujian", link: "/app/pengaturan-ujian-ruangan" },
      ],
      dailySchedules: [
        { name: "Senin", start: "08:00", end: "17:00", breakStart: "12:00", breakEnd: "13:00", isActive: true },
        { name: "Selasa", start: "08:00", end: "17:00", breakStart: "12:00", breakEnd: "13:00", isActive: true },
        { name: "Rabu", start: "08:00", end: "17:00", breakStart: "12:00", breakEnd: "13:00", isActive: true },
        { name: "Kamis", start: "08:00", end: "17:00", breakStart: "12:00", breakEnd: "13:00", isActive: true },
        { name: "Jumat", start: "08:00", end: "17:00", breakStart: "11:30", breakEnd: "13:30", isActive: true },
        { name: "Sabtu", start: "09:00", end: "13:00", breakStart: "11:00", breakEnd: "12:00", isActive: false },
        { name: "Minggu", start: "09:00", end: "12:00", breakStart: "10:30", breakEnd: "11:00", isActive: false },
      ],
    };
  },
  methods: {
    handleSaveSks() {
      alert(`Berhasil menyimpan! 1 SKS sekarang setara dengan ${this.sksDuration} menit.`);
    },
    handleSaveSchedule() {
      alert("Konfigurasi jadwal operasional dan istirahat telah berhasil diperbarui!");
      console.log("Saving schedules:", this.dailySchedules);
    },
  },
};
</script>

<style scoped>
</style>
