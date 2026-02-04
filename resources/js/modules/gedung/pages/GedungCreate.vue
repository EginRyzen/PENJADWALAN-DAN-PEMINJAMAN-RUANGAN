<template>
  <div class="p-6">
    <breadcrumb-bima :items="breadcrumbItems" class="mb-6" />
    <div class="bg-white rounded-lg shadow-sm mt-5 p-6">
      <div
        v-if="modal.error"
        class="fixed inset-0 z-[999] flex items-center justify-center p-4"
      >
        <div
          class="absolute inset-0 bg-black/50"
          @click="modal.error = false"
        ></div>
        <div
          class="relative bg-white rounded-2xl p-8 text-center max-w-sm w-full"
        >
          <font-awesome-icon
            icon="exclamation-circle"
            class="text-red-500 text-5xl mb-4"
          />
          <h3 class="text-lg font-bold mb-2">Form Belum Lengkap</h3>
          <p class="text-gray-600 mb-6">
            Mohon lengkapi semua data pada form sebelum menyimpan.
          </p>
          <button
            @click="modal.error = false"
            class="w-full py-2 bg-teal-500 text-white rounded-lg"
          >
            Tutup
          </button>
        </div>
      </div>

      <building-image-preview
        :show="modal.preview"
        :src="selectedImageSrc"
        @close="modal.preview = false"
      />

      <div class="flex items-center justify-between mb-8">
        <div
          class="flex items-center gap-2 text-gray-500 cursor-pointer"
          @click="$router.go(-1)"
        >
          <font-awesome-icon icon="arrow-left" /> Kembali
        </div>
        <h2 class="text-xl font-bold text-gray-800">
          Tambah Data Gedung & Ruangan
        </h2>
      </div>

      <div v-show="!isFacilityMode">
        <building-main-info-form
          ref="mainInfoForm"
          @preview-image="
            (src) => {
              selectedImageSrc = src;
              modal.preview = true;
            }
          "
        />

        <building-room-list-form
          ref="roomListForm"
          @open-facility="handleOpenFacility"
        />

        <div class="flex justify-center mt-10 pt-6 border-t border-gray-100">
          <button-app
            type="primary"
            color="teal"
            class="bg-teal-400 hover:bg-teal-500 text-white font-semibold px-12 py-2 rounded-lg shadow-md transition-all duration-200"
            @click="handleFinalSave"
          >
            Simpan Seluruh Data
          </button-app>
        </div>
      </div>

      <div v-if="isFacilityMode">
        <building-fasilitas-ruangan-form
          :room-name="currentRoom.room_name"
          :facilities="currentRoom.facilities"
          @back="isFacilityMode = false"
        />
      </div>
    </div>
  </div>
</template>

<script>
import BuildingMainInfoForm from "../components/BuildingMainInfoForm.vue";
import BuildingRoomListForm from "../components/BuildingRoomListForm.vue";
import BuildingFasilitasRuanganForm from "../components/BuildingFasilitasRuanganForm.vue";
import BuildingImagePreview from "../components/BuildingImagePreview.vue";
import BreadcrumbBima from "@/core/components/Breadcrumb.vue";
import ButtonApp from "@/core/components/Button.vue";

export default {
  components: {
    BuildingMainInfoForm,
    BuildingRoomListForm,
    BuildingFasilitasRuanganForm,
    BuildingImagePreview,
    BreadcrumbBima,
    ButtonApp,
  },
  data() {
    return {
      isFacilityMode: false,
      currentRoom: null,
      selectedImageSrc: null,
      modal: { error: false, preview: false },
      breadcrumbItems: [
        { text: "Dashboard", link: "/app/dashboard" },
        { text: "Gedung", link: "/app/gedung-list" },
        { text: "Buat", link: "#" },
      ],
    };
  },
  methods: {
    handleOpenFacility(index, room) {
      this.currentRoom = room;
      this.isFacilityMode = true;
    },
    handleFinalSave() {
      const isMainValid = this.$refs.mainInfoForm.validate();
      const isRoomValid = this.$refs.roomListForm.validate();

      if (isMainValid && isRoomValid) {
        const finalData = {
          ...this.$refs.mainInfoForm.getData(),
          rooms: this.$refs.roomListForm.getData(),
        };
        console.log("Data siap kirim API:", finalData);
        alert("Simpan Berhasil!");
      } else {
        this.modal.error = true;
      }
    },
  },
};
</script>
<style scoped>
:deep(input::-webkit-outer-spin-button),
:deep(input::-webkit-inner-spin-button) {
  -webkit-appearance: none;
  margin: 0;
}
:deep(input[type="number"]) {
  -moz-appearance: textfield;
}
</style>