<template>
  <div class="h-full">
    <breadcrumb :items="breadcrumbs"></breadcrumb>
    <div class="flex gap-4 mt-10">
      <div class="w-1/4">
        <h6 class="font-bold mb-5">Filter</h6>

        <content-filter></content-filter>

        <button-app
          expanded
          color="teal"
          class="mt-4 bg-teal-400 hover:bg-teal-500 text-white font-semibold px-4 py-2 rounded-lg shadow-md transition-colors duration-200"
          @click="$router.push({ name: 'gedung.create' })"
        >
          <template #icon-left>
            <font-awesome-icon icon="plus" />
          </template>
          Buat Data Gedung
        </button-app>

        <button-app
          expanded
          type="secondary"
          color="teal"
          class="mt-2 border border-teal-400 text-teal-400 hover:bg-teal-50"
        >
          <template #icon-left>
            <font-awesome-icon icon="download" />
          </template>
          Unduh Data Table
        </button-app>
      </div>

      <div class="w-3/4">
        <p class="font-normal text-sm mb-6 text-gray-600">
          Menampilkan 1 - {{ buildings.length }} dari 100 Daftar Gedung
        </p>

        <div class="flex flex-wrap gap-4 mb-6">
          <chip close>Contoh Filter</chip>
          <button class="text-teal-400 border-b-2 border-teal-400 text-sm">
            Hapus Filter
          </button>
        </div>

        <div
          class="flex flex-col bg-white p-6 rounded-lg shadow-md text-gray-900"
        >
          <div class="flex justify-between items-center">
            <h6 class="font-semibold flex-1 text-lg">Daftar Gedung</h6>

            <div class="relative flex-1 max-w-80">
              <div class="relative">
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Masukkan Nama Cabang/Kanwil"
                  class="w-full bg-white border border-teal-400 rounded-md py-2 pl-4 pr-10 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition-all duration-200 text-gray-500"
                />
                <div
                  class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"
                >
                  <font-awesome-icon icon="search" class="text-gray-500" />
                </div>
              </div>
            </div>
          </div>

          <div class="grid lg:grid-cols-3 gap-4 mt-6">
            <gedung-list-item
              v-for="(building, index) in buildings"
              :key="index"
              :building="building"
            ></gedung-list-item>
          </div>

          <div class="flex justify-end mt-8">
            <pagination></pagination>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Breadcrumb from "@/core/components/Breadcrumb.vue";
import Pagination from "@/core/components/Pagination.vue";
import ContentFilter from "@/core/components/ContentFilter.vue";
import GedungListItem from "@/modules/gedung/components/GedungListItem.vue";
import ButtonApp from "@/core/components/Button.vue";

export default {
  name: "GedungList",
  components: {
    Breadcrumb,
    Pagination,
    ContentFilter,
    GedungListItem,
    ButtonApp,
  },
  data() {
    return {
      breadcrumbs: [
        { text: "Gedung" },
        { text: "Profile Gedung", link: "/app/gedung-list" },
      ],
      // Menambahkan Data Dummy Gedung
      buildings: [
        {
          branch_code: "BCA01",
          suffix: "01",
          branch_name: "Gedung Menara BCA",
          regional_name: "Kanwil Jakarta Pusat",
          activation: true,
          url_image:
            "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=500",
          incomplete_image: false,
          incomplete_document: false,
        },
        {
          branch_code: "BCA02",
          suffix: "02",
          branch_name: "Gedung Wisma Asia",
          regional_name: "Kanwil Jakarta Barat",
          activation: false,
          url_image: null, // Akan memicu image default
          incomplete_image: true,
          incomplete_image_message: "Foto tampak depan belum diupload",
          incomplete_document: false,
        },
        {
          branch_code: "BCA03",
          suffix: "03",
          branch_name: "KCU Bandung",
          regional_name: "Kanwil Bandung",
          activation: true,
          url_image:
            "https://images.unsplash.com/photo-1577495508048-b635879837f1?q=80&w=500",
          incomplete_image: false,
          incomplete_document: true,
          incomplete_document_message: "Dokumen IMB belum lengkap",
        },
        {
          branch_code: "BCA04",
          suffix: "04",
          branch_name: "KCU Jakarta Selatan",
          regional_name: "Kanwil Jakarta Selatan",
          activation: true,
          url_image:
            "https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=500",
          incomplete_image: true,
          incomplete_image_message: "Kualitas foto terlalu rendah",
          incomplete_document: true,
          incomplete_document_message: "Sertifikat tanah kadaluarsa",
        },
        {
          branch_code: "BCA05",
          suffix: "05",
          branch_name: "KCU Malang",
          regional_name: "Kanwil Jawa Timur",
          activation: true,
          url_image: null,
          incomplete_image: false,
          incomplete_document: false,
        },
        {
          branch_code: "BCA06",
          suffix: "06",
          branch_name: "KCU Bogor",
          regional_name: "Kanwil Jawa Barat",
          activation: false,
          url_image:
            "https://images.unsplash.com/photo-1554435493-93422e8220c8?q=80&w=500",
          incomplete_image: false,
          incomplete_document: false,
        },
      ],
    };
  },
};
</script>