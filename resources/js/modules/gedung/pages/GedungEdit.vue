<template>
    <div class="p-6">
        <breadcrumb-bima :items="breadcrumbItems" class="mb-6" />
        <div class="bg-white rounded-lg shadow-sm mt-5 p-6">
            <div v-if="modal.error" class="fixed inset-0 z-[999] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/50" @click="modal.error = false"></div>
                <div class="relative bg-white rounded-2xl p-8 text-center max-w-sm w-full">
                    <font-awesome-icon icon="exclamation-circle" class="text-red-500 text-5xl mb-4" />
                    <h3 class="text-lg font-bold mb-2">{{ modal.title }}</h3>
                    <p class="text-gray-600 mb-6">
                        {{ modal.message }}
                    </p>
                    <button @click="modal.error = false" class="w-full py-2 bg-teal-500 text-white rounded-lg">
                        Tutup
                    </button>
                </div>
            </div>

            <building-image-preview :show="modal.preview" :src="selectedImageSrc" @close="modal.preview = false" />

            <div class="flex items-center justify-between mb-8">
                <div v-if="!isFacilityMode" class="flex items-center gap-2 text-gray-500 cursor-pointer"
                    @click="$router.go(-1)">
                    <font-awesome-icon icon="arrow-left" /> Kembali
                </div>
                <h2 class="text-xl font-bold text-gray-800">
                    Edit Data Gedung & Ruangan
                </h2>
            </div>

            <div v-show="!isFacilityMode">
                <building-main-info-form ref="mainInfoForm" @preview-image="
                    (src) => {
                        selectedImageSrc = src;
                        modal.preview = true;
                    }
                " />

                <building-room-list-form ref="roomListForm" @open-facility="handleOpenFacility" />

                <div class="flex justify-center mt-10 pt-6 border-t border-gray-100">
                    <button-app type="primary" color="teal"
                        class="bg-teal-400 hover:bg-teal-500 text-white font-semibold px-12 py-2 rounded-lg shadow-md transition-all duration-200"
                        @click.stop.prevent="handleFinalSave">
                        Simpan Perubahan
                    </button-app>
                </div>
            </div>

            <div v-if="isFacilityMode">
                <building-fasilitas-ruangan-form :room-name="currentRoom.room_name" :facilities="currentRoom.facilities"
                    @back="handleBackFromFacility" />
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
            buildingId: null,
            modal: {
                error: false,
                preview: false,
                title: "Form Belum Lengkap",
                message: "Mohon lengkapi semua data pada form sebelum menyimpan.",
            },
            breadcrumbItems: [
                { text: "Gedung", link: "/app/gedung-list" },
                { text: "Edit Gedung", link: "#" },
            ],
        };
    },
    mounted() {
        this.buildingId = this.$route.params.id;
        if (this.buildingId) {
            this.fetchDetailGedung();
        }
    },
    methods: {
        async fetchDetailGedung() {
            this.$store.commit("SET_LOADING", true);
            try {
                const response = await this.$store.dispatch(DISPATCH.GET_DETAIL_GEDUNG_DATA, this.buildingId);

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

                const rooms = response.rooms || [];
                const populatedRooms = await Promise.all(rooms.map(async (room) => {
                    try {
                        const facResponse = await this.$store.dispatch(DISPATCH.GET_ROOM_FACILITIES, room.id);
                        return {
                            ...room,
                            facilities: facResponse.result || []
                        };
                    } catch (e) {
                        return { ...room, facilities: [] };
                    }
                }));

                if (this.$refs.roomListForm) {
                    this.$refs.roomListForm.rooms = populatedRooms;
                    this.$refs.roomListForm.tableOptions.totalItems = populatedRooms.length;
                }

            } catch (error) {
                console.error("Gagal mengambil detail gedung:", error);
            } finally {
                this.$store.commit("SET_LOADING", false);
            }
        },
        handleBackFromFacility() {
            this.isFacilityMode = false;

            const roomIndex = this.$refs.roomListForm.rooms.findIndex(
                (r) => r.id === this.currentRoom.id
            );

            if (roomIndex !== -1) {
                this.$refs.roomListForm.clearFacilityError(roomIndex);
            }
        },
        handleOpenFacility(index, room) {
            this.currentRoom = room;
            this.isFacilityMode = true;
        },
        async handleFinalSave() {
            if (this.$store.state.isLoading) return;
            const isMainValid = this.$refs.mainInfoForm.validate();
            const isRoomValid = this.$refs.roomListForm.validate();

            if (!isMainValid) {
                this.modal.title = "Informasi Utama Belum Lengkap";
                this.modal.message =
                    "Silakan isi Nama, Kode, dan Lokasi gedung terlebih dahulu.";
                this.modal.error = true;
                return;
            }

            if (!isRoomValid) {
                this.modal.title = "Fasilitas Belum Diisi";
                this.modal.message =
                    "Mohon lengkapi daftar ruangan dan pastikan setiap ruangan sudah memiliki fasilitas (Tombol + merah wajib diisi).";
                this.modal.error = true;
                return;
            }

            if (isMainValid && isRoomValid) {
                try {
                    this.$store.commit("SET_LOADING", true);

                    const mainData = this.$refs.mainInfoForm.getData();
                    const roomData = this.$refs.roomListForm.getData();
                    let uploadedImageId = null;

                    if (mainData.building_image) {
                        const documentResult = await this.$store.dispatch(
                            DISPATCH.UPLOAD_IMAGE,
                            mainData.building_image
                        );
                        uploadedImageId = documentResult.id;
                    }

                    const payload = {
                        id: this.buildingId,
                        building_name: mainData.building_name,
                        building_code: mainData.building_code,
                        building_location: mainData.building_location,
                        building_status: "active",
                        building_image_id: uploadedImageId,
                        rooms: roomData.map((room) => ({
                            id: room.id,
                            room_name: room.room_name,
                            room_code: room.room_code,
                            room_location: mainData.building_location,
                            room_status: room.room_status,
                            room_capacity: room.room_capacity,
                            room_purpose: room.room_purpose,
                            facilities: room.facilities.map((f) => ({
                                id: f.id,
                                facility_id: f.facility_id,
                                quantity: f.quantity,
                            })),
                        })),
                    };

                    await this.$store.dispatch(DISPATCH.UPDATE_GEDUNG_DATA, payload);

                    this.$router.push({ name: "gedung.list" });
                } catch (error) {
                    console.error("Gagal menyimpan:", error);
                    this.modal.title = "Gagal Menyimpan";
                    this.modal.message = error.response?.data?.message || "Terjadi kesalahan pada server saat mencoba menyimpan data.";
                    this.modal.error = true;
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