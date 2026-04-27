<template>
  <div class="h-full bg-slate-50 min-h-screen font-display">
    <div class="max-w-full mx-auto px-4 md:px-8 pt-6 pb-2">
      <breadcrumb :items="breadcrumbs" class="hidden md:block"></breadcrumb>
    </div>

    <div class="max-w-full mx-auto px-4 md:px-8 pb-16">

      <!-- Mobile Header Row (Back button + Title) -->
      <div class="flex items-center justify-between md:hidden mb-8">
        <div
          @click="goBack"
          class="inline-flex items-center gap-2 cursor-pointer text-teal-600 bg-white shadow-sm border border-teal-100 px-3 py-2 rounded-xl font-semibold text-sm"
        >
          <font-awesome-icon icon="arrow-left" />
          Kembali
        </div>
        <h1 class="text-xl font-semibold text-gray-900">Detail Pengajuan</h1>
        <div class="w-20"></div> <!-- Spacer for centering -->
      </div>

      <!-- Top Header Area - Desktop Only -->
      <div class="hidden md:flex relative flex-row items-center justify-center mb-10 gap-4">
        <!-- Back Button - Desktop Only -->
        <div class="absolute left-0">
          <div
            @click="goBack"
            class="text-teal-600 font-semibold flex items-center cursor-pointer hover:text-teal-700 transition-colors"
          >
            <font-awesome-icon icon="arrow-left" class="mr-2" />
            Kembali
          </div>
        </div>

        <h1 class="text-xl md:text-2xl font-bold text-slate-800 text-center">
          {{ form.unit_name || "KCU SMP 2 - Kanwil 10" }}
        </h1>
      </div>

      <!-- Header Info Card (Extracted Component) -->
      <detail-header :form="form" :getStatusStyle="getStatusStyle" />

      <!-- Main Content Container -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden mb-12">
        <div class="p-8 md:p-12 space-y-10">
          
          <!-- Summary Info Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-12">
            <div class="info-item">
              <label class="block text-sm font-semibold text-slate-600 mb-2">Tipe Pengajuan</label>
              <div class="bg-slate-50/80 rounded-lg px-4 py-3 border border-slate-100 text-slate-800 font-bold">
                {{ form.tipe_pengajuan || "-" }}
              </div>
            </div>

            <div class="info-item">
              <label class="block text-sm font-semibold text-slate-600 mb-2">Rentang Waktu Peminjaman</label>
              <div class="bg-slate-50/80 rounded-lg px-4 py-3 border border-slate-100 text-slate-800 font-bold">
                {{ formatDateRange }}
              </div>
            </div>

            <div class="info-item">
              <label class="block text-sm font-semibold text-slate-600 mb-2">Jam Mulai</label>
              <div class="bg-slate-50/80 rounded-lg px-4 py-3 border border-slate-100 text-slate-800 font-bold">
                {{ form.jam_mulai || "00:00" }}
              </div>
            </div>

            <div class="info-item">
              <label class="block text-sm font-semibold text-slate-600 mb-2">Jam Selesai</label>
              <div class="bg-slate-50/80 rounded-lg px-4 py-3 border border-slate-100 text-slate-800 font-bold">
                {{ form.jam_selesai || "00:00" }}
              </div>
            </div>

            <div class="info-item md:col-span-2">
              <label class="block text-sm font-semibold text-slate-600 mb-2">Keterangan Peminjaman</label>
              <div class="bg-slate-50/80 rounded-lg px-4 py-4 border border-slate-100 text-slate-700 font-medium leading-relaxed min-h-[100px]">
                {{ form.keterangan || "Tidak ada keterangan tambahan." }}
              </div>
            </div>
          </div>

          <!-- Room Selection Table Section -->
          <div class="pt-2">
             <div class="flex items-center gap-3 mb-6">
                <div class="w-1.5 h-6 bg-teal-500 rounded-full"></div>
                <h3 class="text-lg font-bold text-slate-800 tracking-tight">Detail Gedung & Ruangan</h3>
             </div>

             <!-- Desktop View (Table) -->
             <div class="hidden md:block border border-slate-100 rounded-2xl overflow-hidden shadow-sm">
                <table class="w-full text-left">
                  <thead>
                    <tr class="bg-slate-50/50 text-[11px] font-extrabold text-slate-400 uppercase tracking-widest border-b border-slate-100">
                      <th class="px-8 py-5 w-20">No</th>
                      <th class="px-4 py-5">Gedung</th>
                      <th class="px-8 py-5">Daftar Ruangan</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-50">
                    <tr v-for="(item, idx) in form.items" :key="idx" class="hover:bg-slate-50/30 transition-colors">
                      <td class="px-8 py-6 font-bold text-slate-400">{{ idx + 1 }}</td>
                      <td class="px-4 py-6 font-extrabold text-slate-700">{{ item.building_name }}</td>
                      <td class="px-8 py-6">
                        <div class="flex flex-wrap gap-2">
                          <span 
                            v-for="room in item.selected_rooms" 
                            :key="room.id"
                            class="px-3.5 py-1.5 bg-white border border-slate-200 text-slate-600 rounded-lg text-xs font-bold shadow-sm"
                          >
                            {{ room.name }}
                          </span>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
             </div>

             <!-- Mobile View (Cards) -->
             <div class="md:hidden space-y-4">
                <div v-for="(item, idx) in form.items" :key="idx" class="bg-slate-50 border border-slate-100 rounded-2xl p-6 space-y-5">
                   <div class="flex items-center justify-between border-b border-slate-200/50 pb-3">
                      <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-[0.2em]">Item {{ idx + 1 }}</span>
                      <div class="w-2 h-2 bg-teal-500 rounded-full animate-pulse"></div>
                   </div>
                   
                   <div class="space-y-1.5">
                      <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Gedung</label>
                      <p class="text-base font-extrabold text-slate-800">{{ item.building_name }}</p>
                   </div>

                   <div class="space-y-3">
                      <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Daftar Ruangan</label>
                      <div class="flex flex-wrap gap-2">
                        <span 
                          v-for="room in item.selected_rooms" 
                          :key="room.id"
                          class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-xl text-xs font-bold shadow-sm"
                        >
                          {{ room.name }}
                        </span>
                      </div>
                   </div>
                </div>
             </div>
          </div>

          <!-- Attachment Section -->
          <div class="space-y-4">
            <label class="block text-sm font-semibold text-slate-600">Lampiran</label>
            <div v-if="form.file_name" class="border-2 border-dashed border-slate-200 rounded-xl p-4 flex flex-col md:flex-row items-center justify-between gap-6 hover:border-teal-400 transition-colors bg-slate-50/30">
              <div class="flex items-center gap-6 overflow-hidden">
                <div class="w-14 h-14 bg-white rounded-lg flex items-center justify-center shadow-lg shadow-red-500/5 shrink-0 border border-slate-50">
                   <font-awesome-icon icon="file-pdf" class="text-3xl text-red-500" />
                </div>
                <div class="space-y-1 overflow-hidden">
                  <h4 class="text-sm font-bold text-slate-800 truncate">{{ form.file_name }}</h4>
                  <p class="text-[10px] text-gray-500 font-medium">
                     {{ moment(form.created_at).format("DD/MM/YYYY HH:mm:ss") }} - Diunggah Oleh {{ form.user?.name || "Anda" }}
                  </p>
                </div>
              </div>
              <div class="flex items-center gap-2 shrink-0">
                <button 
                  class="w-10 h-10 rounded-lg bg-indigo-400 hover:bg-indigo-500 text-white shadow-md shadow-indigo-500/10 flex items-center justify-center transition-all"
                  @click="viewUploadedPdf"
                  title="Lihat"
                >
                  <font-awesome-icon icon="eye" />
                </button>
                <button 
                  class="w-10 h-10 rounded-lg bg-indigo-400 hover:bg-indigo-500 text-white shadow-md shadow-indigo-500/10 flex items-center justify-center transition-all"
                  @click="downloadPdf"
                  title="Unduh"
                >
                  <font-awesome-icon icon="download" />
                </button>
              </div>
            </div>
            <p v-else class="text-xs text-slate-400 font-medium italic">Tidak ada lampiran terlampir.</p>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div v-if="showActionButtons" class="flex flex-col md:flex-row justify-center items-center gap-4 pb-10">
        <button 
          @click="showActionModal('tolak')"
          class="w-full md:w-56 h-12 rounded-xl border-2 border-red-500 text-red-500 font-bold hover:bg-red-50 transition-all flex justify-center items-center gap-2 shadow-lg shadow-red-500/5 active:scale-95"
        >
          Tolak
        </button>
        <button 
          @click="showActionModal('koreksi')"
          class="w-full md:w-56 h-12 rounded-xl border-2 border-amber-500 text-amber-500 font-bold hover:bg-amber-50 transition-all flex justify-center items-center gap-2 shadow-lg shadow-amber-500/5 active:scale-95"
        >
          Koreksi
        </button>
        <button 
          @click="showActionModal('setuju')"
          class="w-full md:w-56 h-12 rounded-xl bg-teal-500 text-white font-bold hover:bg-teal-600 transition-all shadow-lg shadow-teal-500/20 flex justify-center items-center gap-2 active:scale-95"
        >
          Setuju
        </button>
      </div>

      <!-- Confirmation Modal -->
      <confirm-modal 
        :show="actionModal.show" 
        :config="actionModal.config"
        @close="actionModal.show = false"
        @confirm="handleModalConfirm"
      />

      <!-- Success Modal -->
      <dialog :class="['modal modal-bottom sm:modal-middle z-[9999]', { 'modal-open': successModal.show }]">
        <div class="modal-box bg-white text-center rounded-3xl p-8 shadow-2xl relative overflow-hidden">
          <!-- Decorative Glow -->
          <div class="absolute -top-10 -right-10 w-32 h-32 bg-teal-50 rounded-full blur-3xl opacity-50"></div>
          
          <div class="flex justify-center mb-6 text-[#2DD4BF] relative animate-bounce">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 drop-shadow-lg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="font-extrabold text-3xl mb-2 text-slate-800 tracking-tight">Berhasil!</h3>
          <p class="py-2 text-slate-500 font-medium">Pengajuan telah berhasil disetujui.</p>
          <div class="modal-action justify-center mt-8">
            <button class="h-12 w-full rounded-xl font-extrabold text-sm transition-all text-white bg-[#2DD4BF] hover:bg-[#26bba8] active:scale-95 shadow-lg shadow-teal-500/20" @click="closeSuccessModal">
              Tutup & Kembali
            </button>
          </div>
        </div>
      </dialog>
    </div>
  </div>
</template>

<script>
import Breadcrumb from "@/core/components/Breadcrumb.vue";
import DetailHeader from "../components/DetailHeader.vue";
import ConfirmModal from "@/core/components/ConfirmModal.vue";
import DISPATCH from "@/core/plugins/constants/dispatches";
import moment from "moment";

export default {
  name: "PeminjamanRuanganDetail",
  components: {
    Breadcrumb,
    DetailHeader,
    ConfirmModal
  },
  data() {
    return {
      moment,
      breadcrumbs: [
        { text: "Gedung", link: "#" },
        { text: "List Peminjaman Ruangan", link: "/app/list-peminjaman-ruangan" },
        { text: "Detail Pengajuan", link: "#" },
      ],
      form: {
        id: "",
        no_pengajuan: "LOADING...",
        unit_name: "KCU SMP 2 - Kanwil 10",
        tipe_pengajuan: "",
        tanggal_start: "",
        tanggal_end: "",
        jam_mulai: "",
        jam_selesai: "",
        keterangan: "",
        file_name: "",
        file_url: "",
        created_at: "",
        user: { 
          name: "-",
          username: "-"
        },
        status: { nama_status: "" },
        items: [],
      },
      actionModal: {
        show: false,
        type: "",
        config: {}
      },
      successModal: {
        show: false
      }
    };
  },
  computed: {
    formatDateRange() {
      if (!this.form.tanggal_start || !this.form.tanggal_end) return "-";
      return `${moment(this.form.tanggal_start).format("DD/MM/YYYY")} - ${moment(this.form.tanggal_end).format("DD/MM/YYYY")}`;
    },
    showActionButtons() {
      // 1. Ambil data role user login
      const user = this.$store.state.auth.user;
      let rawRoles = [];
      if (user && user.roles) {
        rawRoles = user.roles;
      } else {
        const savedRoles = localStorage.getItem('user_roles');
        if (savedRoles) {
          try {
            rawRoles = JSON.parse(savedRoles);
          } catch (e) {
            rawRoles = [];
          }
        }
      }

      // Map roles ke format string (antisipasi format object)
      const roles = rawRoles.map(r => typeof r === 'object' ? r.name_role : r);
      const currentStatus = this.form.status?.nama_status;

      const isTenagaTU = roles.includes('TENAGA_TU');
      const isUnitKemahasiswaan = roles.includes('UNIT_KEMAHASISWAAN');
      const isBagianSarpras = roles.includes('BAGIAN_SARPRAS');
      const isKabagUmum = roles.includes('KABAG_UMUM');

      // Kondisi Logik per Role dan Status
      
      // 1. TENAGA_TU (Menangani Verifikasi & Pengecekan Ruang)
      if (isTenagaTU && (currentStatus === 'VERIFIKASI_TU' || currentStatus === 'PENGECEKAN_RUANG_TU')) {
        return true;
      }

      // 2. UNIT_KEMAHASISWAAN (Menangani Validasi Khusus Event Mahasiswa)
      if (isUnitKemahasiswaan && currentStatus === 'VALIDASI_KEMAHASISWAAN') {
        return true;
      }

      // 3. BAGIAN_SARPRAS (Menangani Persiapan Sarpras)
      if (isBagianSarpras && currentStatus === 'PERSIAPAN_SARPRAS') {
        return true;
      }

      // 4. KABAG_UMUM (Menangani Pengesahan Akhir)
      if (isKabagUmum && currentStatus === 'PENGESAHAN_KABAG_UMUM') {
        return true;
      }

      return false;
    }
  },
  mounted() {
    this.fetchDetailData();
  },
  methods: {
    setting_confirm_reject() {
      return {
        confirmType: "reject",
        textConfirmationTitle: "Tolak Pengajuan",
        textConfirmationBody: "Apakah Anda yakin untuk menolak pengajuan peminjaman ruangan ini?",
        showKomentarConfirmation: true,
        labelKomentarConfirmation: "Alasan Penolakan",
        placeholderKomentarConfirmation: "Masukkan alasan penolakan...",
        labelKomentarConfirmationError: "Alasan Penolakan Wajib Diisi",
        maxChar: 100,
        showCharCount: true,
        komentarConfirmation: "",
        komentarConfirmationRequired: true,
        komentarConfirmationCharacterCheck: true,
      };
    },
    setting_confirm_koreksi() {
      return {
        confirmType: "koreksi",
        textConfirmationTitle: "Koreksi Pengajuan",
        textConfirmationBody: "Apakah Anda yakin untuk mengoreksi pengajuan peminjaman ruangan ini?",
        showKomentarConfirmation: true,
        labelKomentarConfirmation: "Alasan Koreksi",
        placeholderKomentarConfirmation: "Masukkan alasan koreksi...",
        labelKomentarConfirmationError: "Alasan Koreksi Wajib Diisi",
        maxChar: 100,
        showCharCount: true,
        komentarConfirmation: "",
        komentarConfirmationRequired: true,
        komentarConfirmationCharacterCheck: true,
      };
    },
    setting_confirm_approve() {
      return {
        confirmType: "approve",
        textConfirmationTitle: "Setujui Pengajuan",
        textConfirmationBody: "Apakah Anda yakin menyetujui pengajuan peminjaman ruangan ini?",
        showKomentarConfirmation: false,
        komentarConfirmationRequired: false,
      };
    },
    async fetchDetailData() {
      const id = this.$route.params.id;
      this.$store.commit("SET_LOADING", true);
      
      try {
        const result = await this.$store.dispatch(DISPATCH.GET_DETAIL_PENGAJUAN, id);
        
        if (result) {
          // Group items by building
          const rawItems = result.items || [];
          const buildingGroups = {};

          rawItems.forEach(item => {
            const building = item.ruangan?.building;
            if (!building) return;

            if (!buildingGroups[building.id]) {
              buildingGroups[building.id] = {
                building_name: building.building_code || building.name,
                selected_rooms: []
              };
            }

            buildingGroups[building.id].selected_rooms.push({
              id: item.ruangan.id,
              name: item.ruangan.room_name
            });
          });

          this.form = {
            ...this.form,
            ...result,
            unit_name: "Layanan Peminjaman Ruangan",
            tanggal_start: result.tanggal_start_peminjaman,
            tanggal_end: result.tanggal_end_peminjaman,
            keterangan: result.alasan,
            file_name: result.dokumen_pendukung?.file_name || "",
            file_url: result.dokumen_pendukung?.file_path ? `/storage/${result.dokumen_pendukung.file_path}` : "",
            created_at: result.created_at,
            user: { 
              ...result.user,
              role: result.user?.role || { name: "Pengaju" }
            },
            status: result.status || { nama_status: "" },
            items: Object.values(buildingGroups)
          };
        }
      } catch (error) {
        console.error("Gagal memuat detail pengajuan:", error);
      } finally {
        this.$store.commit("SET_LOADING", false);
      }
    },
    goBack() {
      this.$router.push({ name: 'peminjaman.list' });
    },
    viewUploadedPdf() {
      if (!this.form.file_url) {
        alert("File tidak tersedia");
        return;
      }
      window.open(this.form.file_url, '_blank');
    },
    downloadPdf() {
      if (!this.form.file_url) {
        alert("File tidak tersedia");
        return;
      }
      const link = document.createElement('a');
      link.href = this.form.file_url;
      link.download = this.form.file_name || 'dokumen_peminjaman.pdf';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    },
    showActionModal(type) {
      this.actionModal.type = type;
      if (type === 'setuju') {
        this.actionModal.config = this.setting_confirm_approve();
      } else if (type === 'tolak') {
        this.actionModal.config = this.setting_confirm_reject();
      } else if (type === 'koreksi') {
        this.actionModal.config = this.setting_confirm_koreksi();
      }
      this.actionModal.show = true;
    },
    async handleModalConfirm(comment) {
      const action = this.actionModal.type;
      
      this.$store.commit("SET_LOADING", true);
      this.actionModal.show = false;

      try {
        if (action === 'setuju') {
          // Panggil API endpoint untuk Approve
          await this.$store.dispatch(DISPATCH.APPROVE_PENGAJUAN, {
            pengajuan_id: this.form.id,
            catatan: comment || ''
          });
          
          // Tampilkan Modal Success
          this.successModal.show = true;
        } else {
          // Untuk Tolak dan Koreksi (Belum ada endpoint backend-nya di scope ini)
          alert(`Fitur ${action} sedang dalam tahap pengembangan.`);
        }
      } catch (error) {
        // Error sudah ditangani secara global oleh interceptor Api.js (muncul sebagai Toast popup)
        console.error("Proses approval gagal:", error);
      } finally {
        this.$store.commit("SET_LOADING", false);
      }
    },
    closeSuccessModal() {
      this.successModal.show = false;
      this.goBack();
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
  },
};
</script>

<style scoped>
.tracking-tighter {
  letter-spacing: -0.02em;
}
</style>
