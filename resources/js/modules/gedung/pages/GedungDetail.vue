<template>
  <div class="p-6">
    <breadcrumb :items="breadcrumbItems" class="mb-6" />

    <div class="bg-white rounded-lg shadow-sm mt-5 p-6">
      <div class="flex items-center justify-between mb-8">
        <div v-if="!isFacilityMode"
          class="flex items-center gap-2 text-gray-500 cursor-pointer hover:text-teal-500 transition-colors"
          @click="$router.go(-1)">
          <font-awesome-icon icon="arrow-left" /> Kembali
        </div>
        <h2 class="text-xl font-bold text-gray-800">Detail Gedung & Ruangan</h2>
      </div>

      <building-image-preview :show="modal.preview" :src="selectedImageSrc" @close="modal.preview = false" />

      <div v-show="!isFacilityMode">
        <building-detail-info ref="mainInfoForm" @preview-image="(src) => {
          selectedImageSrc = src;
          modal.preview = true;
        }" />

        <div class="mt-10">
          <div class="flex items-center gap-2 mb-6">
            <div class="w-1.5 h-6 bg-teal-500 rounded-full"></div>
            <h3 class="text-lg font-bold text-gray-800">Daftar Ruangan</h3>
          </div>

          <building-detail-room-table :rooms="rooms" @open-facility="handleOpenFacility" />

          <div class="flex justify-center mt-10 pt-6 border-t border-gray-100">
            <button-app type="primary" color="teal"
              class="bg-teal-400 hover:bg-teal-500 text-white font-semibold px-12 py-2 rounded-lg shadow-md transition-all duration-200"
              @click.stop.prevent="handleEditGedung">
              Edit Gedung
            </button-app>
          </div>
        </div>
      </div>

      <div v-if="isFacilityMode">
        <building-fasilitas-room-detail :room-name="currentRoom.room_name" :facilities="currentRoom.facilities"
          @back="isFacilityMode = false" />
      </div>
    </div>
  </div>
</template>

<script>
import BuildingDetailInfo from "../components/BuildingDetailInfo.vue";
import BuildingDetailRoomTable from "../components/BuildingDetailRoomTable.vue";
import BuildingFasilitasRoomDetail from "../components/BuildingFasilitasRoomDetail.vue";
import BuildingImagePreview from "../components/BuildingImagePreview.vue";
import Breadcrumb from "@/core/components/Breadcrumb.vue";
import ButtonApp from "@/core/components/Button.vue";
import DISPATCH from "@/core/plugins/constants/dispatches";

export default {
  name: "GedungDetail",
  components: {
    BuildingDetailInfo,
    BuildingDetailRoomTable,
    BuildingFasilitasRoomDetail,
    BuildingImagePreview,
    Breadcrumb,
    ButtonApp
  },
  data() {
    return {
      isFacilityMode: false,
      currentRoom: null,
      rooms: [],
      modal: {
        preview: false,
      },
      selectedImageSrc: null,
      breadcrumbItems: [
        { text: "Gedung", link: "/app/gedung-list" },
        { text: "Detail Gedung", link: "#" },
      ],
    };
  },
  mounted() {
    this.fetchDetailGedung();
  },
  methods: {
    async handleOpenFacility(index, room) {
      if (!room.id) return;

      this.$store.commit("SET_LOADING", true);
      try {
        const responseData = await this.$store.dispatch(DISPATCH.GET_ROOM_FACILITIES, room.id);

        const facilities = responseData.result || [];

        this.currentRoom = {
          ...room,
          facilities: facilities
        };

        this.isFacilityMode = true;
      } catch (error) {
        console.error("Gagal mengambil detail fasilitas ruangan:", error);
      } finally {
        this.$store.commit("SET_LOADING", false);
      }
    },
    async fetchDetailGedung() {
      const id = this.$route.params.id;
      if (!id) return;

      this.$store.commit("SET_LOADING", true);
      try {
        const response = await this.$store.dispatch(DISPATCH.GET_DETAIL_GEDUNG_DATA, id);

        if (this.$refs.mainInfoForm) {
          this.$refs.mainInfoForm.form = {
            building_name: response.building_name,
            building_code: response.building_code,
            building_location: response.building_location,
            building_status: response.building_status,
            building_image: null,
          };
          this.$refs.mainInfoForm.imagePreview = response.image ? `/storage/${response.image.file_path}` : null;
        }

        this.rooms = response.rooms || [];

      } catch (error) {
        console.error("Gagal mengambil detail gedung:", error);
      } finally {
        this.$store.commit("SET_LOADING", false);
      }
    },
    handleEditGedung() {
      const id = this.$route.params.id;
      if (id) {
        this.$router.push({ name: 'gedung.edit', params: { id } });
      }
    }
  },
};
</script>