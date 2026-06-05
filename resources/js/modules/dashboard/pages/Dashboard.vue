<template>
  <div class="h-full">
    <breadcrumb :items="breadcrumbs" class="hidden md:block"></breadcrumb>
    <div class="mt-10">
      <h1 class="text-3xl font-bold mb-6">Dashboard Admin</h1>

      <div
        class="mt-10 bg-indigo-50 rounded-2xl p-8 flex flex-col md:flex-row items-center justify-between"
      >
        <div class="md:w-1/2">
          <h2 class="text-2xl font-bold text-indigo-900 mb-2">
            Halo, Selamat Datang!
          </h2>
          <p class="text-indigo-700">
            Selamat datang di halaman admin yang terlindungi. Di sini Anda dapat
            mengelola seluruh operasional sistem dengan mudah dan efisien.
          </p>
        </div>

        <div class="md:w-1/2 flex justify-end mt-6 md:mt-0">
          <img
            src="@/core/assets/dashboard_image.jpg"
            alt="Dashboard Illustration"
            class="w-full max-w-xs object-contain"
          />
        </div>
      </div>

      <!-- Panduan Peminjaman Ruangan -->
      <div class="mt-8 bg-white rounded-2xl p-6 md:p-8 border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)]">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
          <div>
            <h3 class="text-xl font-black text-gray-800">Panduan Peminjaman Ruangan</h3>
            <p class="text-sm text-gray-500 mt-1">Langkah mudah untuk mengajukan peminjaman ruangan</p>
          </div>
          <router-link to="/app/list-peminjaman-ruangan" class="w-full md:w-auto px-6 py-3 bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-teal-200 transition-all duration-300 hover:-translate-y-0.5 flex items-center justify-center gap-2 group">
            Mulai Pinjam Sekarang
            <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
          </router-link>
        </div>
        
        <div class="grid grid-cols-1 gap-3">
          <!-- Step 1 -->
          <div class="flex items-start gap-4 p-4 rounded-xl border border-gray-100 bg-gray-50/30 hover:bg-white hover:border-indigo-200 hover:shadow-md transition-all duration-300 group cursor-default">
            <div class="w-10 h-10 rounded-full bg-white border border-indigo-100 text-indigo-600 flex items-center justify-center font-black flex-shrink-0 shadow-sm group-hover:bg-indigo-500 group-hover:text-white group-hover:border-indigo-500 transition-colors duration-300">1</div>
            <div>
              <h4 class="font-bold text-gray-800">Pilih Ruangan</h4>
              <p class="text-sm text-gray-500 mt-0.5">Buka menu Peminjaman dan pilih ruangan yang berstatus <span class="font-semibold text-teal-600">Tersedia</span>.</p>
            </div>
          </div>

          <!-- Step 2 -->
          <div class="flex items-start gap-4 p-4 rounded-xl border border-gray-100 bg-gray-50/30 hover:bg-white hover:border-indigo-200 hover:shadow-md transition-all duration-300 group cursor-default">
            <div class="w-10 h-10 rounded-full bg-white border border-indigo-100 text-indigo-600 flex items-center justify-center font-black flex-shrink-0 shadow-sm group-hover:bg-indigo-500 group-hover:text-white group-hover:border-indigo-500 transition-colors duration-300">2</div>
            <div>
              <h4 class="font-bold text-gray-800">Isi Formulir</h4>
              <p class="text-sm text-gray-500 mt-0.5">Lengkapi form pengajuan dengan data diri, waktu peminjaman, serta deskripsi kegiatan Anda.</p>
            </div>
          </div>

          <!-- Step 3 -->
          <div class="flex items-start gap-4 p-4 rounded-xl border border-gray-100 bg-gray-50/30 hover:bg-white hover:border-amber-200 hover:shadow-md transition-all duration-300 group cursor-default">
            <div class="w-10 h-10 rounded-full bg-white border border-amber-100 text-amber-500 flex items-center justify-center font-black flex-shrink-0 shadow-sm group-hover:bg-amber-500 group-hover:text-white group-hover:border-amber-500 transition-colors duration-300">3</div>
            <div>
              <h4 class="font-bold text-gray-800">Proses Approval</h4>
              <p class="text-sm text-gray-500 mt-0.5">Sistem otomatis meneruskan permohonan ke pihak berwenang untuk diverifikasi secara berjenjang.</p>
            </div>
          </div>

          <!-- Step 4 -->
          <div class="flex items-start gap-4 p-4 rounded-xl border border-gray-100 bg-gray-50/30 hover:bg-white hover:border-teal-200 hover:shadow-md transition-all duration-300 group cursor-default">
            <div class="w-10 h-10 rounded-full bg-white border border-teal-100 text-teal-500 flex items-center justify-center font-black flex-shrink-0 shadow-sm group-hover:bg-teal-500 group-hover:text-white group-hover:border-teal-500 transition-colors duration-300">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
            </div>
            <div>
              <h4 class="font-bold text-gray-800">Selesai</h4>
              <p class="text-sm text-gray-500 mt-0.5">Jika disetujui, ruangan <span class="font-semibold text-teal-600">Siap Digunakan</span>. Notifikasi akan dikirimkan ke email Anda.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Breadcrumb from "@/core/components/Breadcrumb.vue";
import ButtonApp from "@/core/components/Button.vue";

export default {
  name: "DashboardPage",
  components: {
    Breadcrumb,
    ButtonApp,
  },
  data() {
    return {
      breadcrumbs: [
        {
          text: "Home",
          link: "/app/dashboard",
        },
        {
          text: "Dashboard",
          link: "/app/dashboard",
        },
      ],
    };
  },
};
// Anda bisa menambahkan logic dashboard di sini
</script>