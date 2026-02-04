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
import DISPATCH from "@/core/plugins/constants/dispatches";

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
    async handleFinalSave() {
      const isMainValid = this.$refs.mainInfoForm.validate(); //
      const isRoomValid = this.$refs.roomListForm.validate(); //

      if (isMainValid && isRoomValid) {
        const mainData = this.$refs.mainInfoForm.getData(); //
        const roomData = this.$refs.roomListForm.getData(); //

        // PEMBENTUKAN PAYLOAD DI SINI
        const formData = new FormData();
        formData.append("building_name", mainData.building_name);
        formData.append("building_code", mainData.building_code);
        formData.append("building_location", mainData.building_location);
        formData.append("building_status", "active"); // Status default

        if (mainData.building_image) {
          formData.append("building_image", mainData.building_image);
        }

        // Mapping Ruangan dan Fasilitas ke FormData
        roomData.forEach((room, index) => {
          formData.append(`rooms[${index}][room_name]`, room.room_name);
          formData.append(`rooms[${index}][room_code]`, room.room_code);
          formData.append(
            `rooms[${index}][room_location]`,
            mainData.building_location
          );
          formData.append(`rooms[${index}][room_status]`, room.room_status);
          formData.append(`rooms[${index}][room_capacity]`, room.room_capacity);
          formData.append(`rooms[${index}][room_purpose]`, room.room_purpose);

          if (room.facilities && room.facilities.length > 0) {
            room.facilities.forEach((facility, fIndex) => {
              formData.append(
                `rooms[${index}][facilities][${fIndex}][facility_id]`,
                facility.facility_id
              );
              formData.append(
                `rooms[${index}][facilities][${fIndex}][quantity]`,
                facility.quantity
              );
            });
          }
        });

        try {
          this.$store.commit("SET_LOADING", true);
          await this.$store.dispatch(DISPATCH.SAVE_GEDUNG_DATA, formData);

          alert("Data Gedung dan Ruangan berhasil disimpan!");
          this.$router.push({ name: "gedung.list" }); //
        } catch (error) {
          const errorMsg =
            error.response?.data?.message || "Terjadi kesalahan server";
          alert("Gagal menyimpan: " + errorMsg);
        } finally {
          this.$store.commit("SET_LOADING", false);
        }
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