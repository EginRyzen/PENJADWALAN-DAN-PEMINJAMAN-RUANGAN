<template>
  <div class="h-full">
    <breadcrumb :items="breadcrumbs"></breadcrumb>
    <div class="mt-5">
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
          <button-app
            class="mt-4 bg-teal-400 hover:bg-teal-500 text-white font-semibold px-4 py-2 rounded-lg shadow-md transition-colors duration-200"
            >Mulai</button-app>
        </div>

        <div class="md:w-1/2 flex justify-end mt-6 md:mt-0">
          <img
            src="@/core/assets/dashboard_image.jpg"
            alt="Dashboard Illustration"
            class="w-full max-w-xs object-contain"
          />
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