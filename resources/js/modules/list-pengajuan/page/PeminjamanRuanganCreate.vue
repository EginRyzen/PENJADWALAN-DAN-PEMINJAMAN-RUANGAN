<template>
  <div class="h-full bg-gray-50">
    <breadcrumb :items="breadcrumbs"></breadcrumb>

    <div class="max-w-full mx-auto mt-6 px-4 md:px-8 pb-16">
      <!-- Header Section -->
      <div class="relative flex flex-col md:flex-row items-center justify-center mb-10 gap-4">
        <!-- Back Button - Absolute on desktop to keep title centered, relative on mobile -->
        <div class="md:absolute md:left-0">
          <div
            color="teal"
            type="tertiary"
            @click="goBack"
            class="text-teal-600 font-semibold flex items-center cursor-pointer"
          >
            <font-awesome-icon icon="arrow-left" class="mr-2" />
            Kembali
          </div>
        </div>
        
        <!-- Centered Title -->
        <div class="text-center">
          <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">
            Buat Peminjaman Ruangan
          </h1>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-8">
        <!-- Main Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="p-6 border-b border-gray-50 bg-gray-50/30">
            <h2 class="font-semibold text-gray-800 flex items-center">
              <span class="w-2 h-6 bg-teal-500 rounded-full mr-3"></span>
              Informasi Utama Peminjaman
            </h2>
          </div>
          
          <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <!-- Tipe Pengajuan -->
              <div class="flex flex-col">
                <select-app
                  label="Tipe Pengajuan"
                  placeholder="Pilih Tipe..."
                  v-model="form.tipe_pengajuan"
                  :options="tipeOptions"
                  item-text="name"
                  item-key="id"
                  required
                  :error="!!errors.tipe_pengajuan"
                />
                <div v-if="errors.tipe_pengajuan" class="text-xs text-red-500 mt-1 font-medium">
                  {{ errors.tipe_pengajuan }}
                </div>
              </div>

              <!-- Waktu Peminjaman -->
              <div class="flex flex-col">
                <label class="text-sm font-semibold text-gray-700 mb-1">
                  <span class="text-red-500">*</span> Waktu Peminjaman
                </label>
                <button
                  @click="modalDatePicker = true"
                  class="h-11 rounded-md border bg-white px-4 flex items-center justify-between text-gray-600 transition-all shadow-sm"
                  :class="errors.waktu_peminjaman ? 'border-red-500 bg-red-50 hover:border-red-600' : 'border-teal-400 hover:border-teal-500'"
                >
                  <div class="flex items-center gap-3">
                    <span v-if="form.tanggal_start && form.tanggal_end" class="text-sm font-medium">
                      {{ formatDate(form.tanggal_start) }} - {{ formatDate(form.tanggal_end) }}
                    </span>
                    <span v-else class="text-sm text-gray-400">Pilih Rentang Waktu</span>
                  </div>
                  <font-awesome-icon icon="calendar" class="text-teal-500" />
                </button>
                <div v-if="errors.waktu_peminjaman" class="text-xs text-red-500 mt-1 font-medium">
                  {{ errors.waktu_peminjaman }}
                </div>
              </div>

              <!-- Jam Mulai -->
              <div class="flex flex-col cursor-pointer" @click="openTimeModal('start')">
                <app-input
                  label="Jam Mulai"
                  placeholder="Pilih Jam Mulai"
                  v-model="form.jam_mulai"
                  required
                  readonly
                  disabled-typing
                  class="pointer-events-none"
                  :error="!!errors.jam_mulai"
                >
                  <template #icon-right>
                    <font-awesome-icon icon="clock" class="text-teal-500" />
                  </template>
                  <template #error-message>
                    {{ errors.jam_mulai }}
                  </template>
                </app-input>
              </div>

              <!-- Jam Selesai -->
              <div class="flex flex-col cursor-pointer" @click="openTimeModal('end')">
                <app-input
                  label="Jam Selesai"
                  placeholder="Pilih Jam Selesai"
                  v-model="form.jam_selesai"
                  required
                  readonly
                  disabled-typing
                  class="pointer-events-none"
                  :error="!!errors.jam_selesai"
                >
                  <template #icon-right>
                    <font-awesome-icon icon="clock" class="text-teal-500" />
                  </template>
                  <template #error-message>
                    {{ errors.jam_selesai }}
                  </template>
                </app-input>
              </div>

              <!-- Keterangan -->
              <div>
                <textarea-app
                  v-model="form.keterangan"
                  label="Keterangan Peminjaman"
                  placeholder="Contoh: Digunakan untuk kegiatan Seminar Nasional atau Workshop..."
                  required
                  :error="!!errors.keterangan"
                  :maxChar="250"
                >
                  <template #error-message>
                    {{ errors.keterangan }}
                  </template>
                  <template #message>
                    <div class="text-xs text-gray-500 mt-1 text-right font-medium">
                      {{ 250 - (form.keterangan ? form.keterangan.length : 0) }} Karakter Tersisa
                    </div>
                  </template>
                </textarea-app>
              </div>

              <!-- Lampiran -->
              <div class="mt-1">
                <label for="upload" class="cursor-pointer">
                  <p class="text-sm font-semibold text-gray-700 mb-1">
                    Lampiran
                  </p>
                  <div
                    class="flex justify-between items-center bg-gray-50 mt-1 rounded-md w-full p-5 transition-all"
                    :class="{
                      'border border-dashed border-gray-300 hover:border-teal-400': !form.uploadError,
                      'border border-red-500 border-dashed bg-red-50': form.uploadError,
                    }"
                    @click="simulateClick"
                  >
                    <div
                      v-if="!form.file_name"
                      class="flex items-center gap-4 grow"
                    >
                      <font-awesome-icon icon="cloud-upload-alt" class="text-3xl text-teal-500" />
                      <div class="flex flex-col items-start">
                        <p class="font-bold text-sm mb-0 text-gray-700">Unggah File PDF</p>
                        <p class="text-gray-400 text-xs">
                          Silakan pilih file PDF yang akan diunggah (Opsional)
                        </p>
                      </div>
                    </div>
                    <div
                      v-else
                      class="flex items-center gap-4 grow overflow-hidden"
                    >
                      <font-awesome-icon icon="file-pdf" class="text-3xl text-red-500 flex-shrink-0" />
                      <div class="flex flex-col items-start overflow-hidden w-full">
                        <p class="font-bold text-sm mb-0 truncate w-full pr-2 text-gray-700">
                          {{ form.file_name }}
                        </p>
                        <p class="text-xs w-full pr-2 text-gray-500">
                          <span v-show="form.created_at">{{ form.created_at }}</span>
                          <span v-show="form.created_by_name"> - Diunggah Oleh {{ form.created_by_name }}</span>
                        </p>
                      </div>
                    </div>
                    <div class="flex flex-shrink-0">
                      <div
                        @click.stop="viewUploadedPdf"
                        v-if="form.file_name"
                        class="flex items-center justify-center w-10 h-10 mr-2 bg-teal-500 hover:bg-teal-600 transition-all text-white rounded-md cursor-pointer shadow-sm"
                        title="Lihat Dokumen"
                      >
                        <font-awesome-icon icon="eye" />
                      </div>
                      <div
                        @click.stop="removeUploadedPdf"
                        v-if="form.file_name"
                        class="flex items-center justify-center w-10 h-10 bg-red-500 hover:bg-red-600 transition-all text-white rounded-md cursor-pointer shadow-sm"
                        title="Hapus Dokumen"
                      >
                        <font-awesome-icon icon="trash-alt" />
                      </div>
                      <div
                        v-if="!form.file_name"
                        class="flex items-center justify-center w-10 h-10 bg-teal-500 hover:bg-teal-600 transition-all text-white rounded-md cursor-pointer shadow-sm"
                      >
                        <font-awesome-icon icon="upload" />
                      </div>
                    </div>
                  </div>

                  <input
                    class="hidden"
                    ref="uploadLampiranRef"
                    type="file"
                    accept="application/pdf"
                    @change="onUploadChange"
                  />
                </label>

                <p v-if="form.uploadError" class="text-xs text-red-500 mt-1 font-medium">
                  File gagal terunggah, Format file harus PDF
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Rooms Selection Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="p-6 border-b border-gray-50 bg-gray-50/30 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-semibold text-gray-800 flex items-center">
              <span class="w-2 h-6 bg-teal-500 rounded-full mr-3 flex-shrink-0"></span>
              Pemilihan Gedung & Ruangan
            </h2>
            <button-app
              color="teal"
              size="sm"
              class="bg-teal-500 hover:bg-teal-600 text-white font-semibold text-xs px-4 py-2 w-full md:w-auto flex justify-center items-center"
              @click="addRoomRow"
            >
              <font-awesome-icon icon="plus" class="mr-2" />
              Tambah Ruangan
            </button-app>
          </div>

          <div class="p-6">
            <div v-if="form.items.length === 0" class="py-16 text-center border-2 border-dashed border-gray-100 rounded-2xl bg-gray-50/50">
               <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-gray-100">
                  <font-awesome-icon icon="plus" class="text-teal-500 text-xl" />
               </div>
               <p class="text-gray-500 font-medium">Belum ada ruangan yang dipilih.</p>
               <p class="text-gray-400 text-sm mt-1">Silakan klik tombol "Tambah Ruangan" untuk mulai memilih.</p>
            </div>
            
            <div v-else class="space-y-6">
              <div 
                v-for="(item, index) in form.items" 
                :key="index"
                class="relative group p-8 rounded-2xl border border-gray-100 bg-gray-50/30 transition-all hover:bg-white hover:shadow-xl hover:shadow-teal-900/5"
              >

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                  <!-- Building Selection (Searchable) -->
                  <div>
                    <select-auto-complete
                      label="Pilih Gedung"
                      placeholder="Cari Gedung..."
                      v-model="item.building_id"
                      :options="getAvailableBuildings(index)"
                      item-text="name"
                      item-value="id"
                      required
                      :error="!!(errors.items[index] && errors.items[index].building_id)"
                      @update:modelValue="(val) => onBuildingChange(val, index)"
                    />
                    <div v-if="errors.items[index] && errors.items[index].building_id" class="text-xs text-red-500 mt-1 font-medium">
                      {{ errors.items[index].building_id }}
                    </div>
                  </div>

                  <!-- Room Selection (Multiple via Autocomplete) -->
                  <div class="flex items-start gap-3 w-full">
                    <div class="flex-1">
                      <autocomplete
                        label="Pilih Ruangan"
                        placeholder="Pilih Ruangan..."
                        v-model="item.selected_rooms"
                        :options="item.rooms_list || []"
                        item-text="name"
                        item-value="id"
                        multiple
                        show-select-all
                        :disabled="!item.building_id || item.loadingRooms"
                        required
                        :error="!!(errors.items[index] && errors.items[index].selected_rooms)"
                      />
                      <div v-if="errors.items[index] && errors.items[index].selected_rooms" class="text-xs text-red-500 mt-1 font-medium">
                        {{ errors.items[index].selected_rooms }}
                      </div>
                    </div>
                    
                    <!-- Trash Button aligned with input -->
                    <button 
                      v-if="form.items.length > 1"
                      @click="removeRoomRow(index)"
                      class="mt-7 w-10 h-10 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all border border-red-100 shadow-sm shrink-0"
                      title="Hapus Ruangan"
                    >
                      <font-awesome-icon icon="trash-alt" class="text-sm" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex flex-col md:flex-row justify-center items-center gap-4 md:gap-6 mt-12 pb-10">
          <button-app
            color="red"
            class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 w-full md:w-44 shadow-lg shadow-red-500/10 order-2 md:order-1"
            @click="goBack"
          >
            Batal
          </button-app>
          <button-app
            color="teal"
            class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 w-full md:w-44 shadow-lg shadow-teal-500/10 order-1 md:order-2"
            @click="submitForm"
          >
            Kirim
          </button-app>
        </div>
      </div>
    </div>

    <!-- Date Picker Modal -->
    <ModalDatePicker
      v-if="modalDatePicker && !isMobile"
      :show="modalDatePicker"
      :date="datePicker"
      @close="modalDatePicker = false"
      @submit="submitDatePicker"
    />

    <!-- Date Picker Modal (Mobile) -->
    <ModalDatePickerMobile
      v-if="modalDatePicker && isMobile"
      :show="modalDatePicker"
      :date="datePicker"
      @close="modalDatePicker = false"
      @submit="submitDatePicker"
    />

    <!-- Time Picker Start Modal -->
    <modal-app size="small" v-model="modalTimeStart">
      <div class="flex flex-col justify-between gap-6 p-8 h-[450px]">
        <div class="flex flex-col gap-4">
          <p class="text-center font-bold text-lg text-gray-800">Pilih Jam Mulai</p>
          <div class="w-full mt-4 flex flex-col justify-center items-center gap-4">
            <div class="w-full flex justify-center text-gray-500 font-medium text-sm uppercase tracking-wider">
              <span class="w-32 text-center">Jam</span>
              <span class="w-32 text-center">Menit</span>
            </div>
            <TimePickerScroll
              id="time-picker-start"
              v-model="tempTimeStart"
            />
          </div>
        </div>
        <div class="w-full flex justify-end gap-3 border-t pt-6">
          <button-app
            color="teal"
            type="secondary"
            class="px-6 py-2"
            @click="modalTimeStart = false"
          >
            Tutup
          </button-app>
          <button-app
            color="teal"
            class="px-8 py-2 bg-teal-500 hover:bg-teal-600 text-white"
            @click="saveTime('start')"
          >
            Pilih
          </button-app>
        </div>
      </div>
    </modal-app>

    <!-- Time Picker End Modal -->
    <modal-app size="small" v-model="modalTimeEnd">
      <div class="flex flex-col justify-between gap-6 p-8 h-[450px]">
        <div class="flex flex-col gap-4">
          <p class="text-center font-bold text-lg text-gray-800">Pilih Jam Selesai</p>
          <div class="w-full mt-4 flex flex-col justify-center items-center gap-4">
            <div class="w-full flex justify-center text-gray-500 font-medium text-sm uppercase tracking-wider">
              <span class="w-32 text-center">Jam</span>
              <span class="w-32 text-center">Menit</span>
            </div>
            <TimePickerScroll
              id="time-picker-end"
              v-model="tempTimeEnd"
            />
          </div>
        </div>
        <div class="w-full flex justify-end gap-3 border-t pt-6">
          <button-app
            color="teal"
            type="secondary"
            class="px-6 py-2"
            @click="modalTimeEnd = false"
          >
            Tutup
          </button-app>
          <button-app
            color="teal"
            class="px-8 py-2 bg-teal-500 hover:bg-teal-600 text-white"
            @click="saveTime('end')"
          >
            Pilih
          </button-app>
        </div>
      </div>
    </modal-app>
  </div>
</template>

<script>
import Breadcrumb from "@/core/components/Breadcrumb.vue";
import ButtonApp from "@/core/components/Button.vue";
import AppInput from "@/core/components/AppInput.vue";
import TextareaApp from "@/core/components/Textarea.vue";
import SelectApp from "@/core/components/Select.vue";
import SelectAutoComplete from "@/core/components/SelectAutoComplete.vue";
import Autocomplete from "@/core/components/Autocomplete.vue";
import ModalDatePicker from "@/core/components/ModalDatePicker.vue";
import ModalDatePickerMobile from "@/core/components/ModalDatePickerMobile.vue";
import TimePickerScroll from "@/core/components/TimePickerScroll.vue";
import ModalApp from "@/core/components/Modal.vue";
import DISPATCH from "@/core/plugins/constants/dispatches";
import moment from "moment";

export default {
  name: "PeminjamanRuanganCreate",
  components: {
    Breadcrumb,
    ButtonApp,
    AppInput,
    TextareaApp,
    SelectApp,
    SelectAutoComplete,
    Autocomplete,
    ModalDatePicker,
    ModalDatePickerMobile,
    TimePickerScroll,
    ModalApp,
  },
  data() {
    return {
      breadcrumbs: [
        { text: "Gedung" },
        { text: "List Peminjaman Ruangan", link: "/app/list-peminjaman-ruangan" },
        { text: "Buat Pengajuan Peminjaman" },
      ],
      tipeOptions: [
        { id: "PEMBELAJARAN", name: "Pembelajaran" },
        { id: "EVENT", name: "Event" },
      ],
      buildingOptions: [],
      modalDatePicker: false,
      isMobile: window.innerWidth < 768,
      datePicker: {
        start: new Date(),
        end: new Date(moment().add(1, 'days').format()),
      },
      modalTimeStart: false,
      modalTimeEnd: false,
      tempTimeStart: "08:00",
      tempTimeEnd: "09:00",
      form: {
        tipe_pengajuan: null,
        tanggal_start: "",
        tanggal_end: "",
        jam_mulai: "",
        jam_selesai: "",
        keterangan: "",
        file_name: "",
        file_url: "",
        file_raw: null,
        created_at: "",
        created_by_name: "",
        uploadError: false,
        items: [
          {
            building_id: null,
            selected_rooms: [],
            rooms_list: [],
            loadingRooms: false,
          },
        ],
      },
      errors: {
        tipe_pengajuan: "",
        waktu_peminjaman: "",
        keterangan: "",
        jam_mulai: "",
        jam_selesai: "",
        items: [],
      },
    };
  },
  mounted() {
    this.fetchBuildingOptions();
    window.addEventListener("resize", this.onResize);
  },
  beforeDestroy() {
    window.removeEventListener("resize", this.onResize);
  },
  methods: {
    onResize() {
      this.isMobile = window.innerWidth < 768;
    },
    async fetchBuildingOptions() {
      try {
        const data = await this.$store.dispatch(`gedung/${DISPATCH.GET_BUILDINGS_ONLY.split('/')[1]}`, {
          active: "active",
        });

        this.buildingOptions = data.map((item) => ({
          id: item.id,
          name: item.building_code + " - " + item.building_name,
        }));
      } catch (error) {
        console.error("Gagal memuat filter gedung:", error);
      }
    },
    getAvailableBuildings(index) {
      // Get IDs selected in other rows
      const selectedOtherBuildingIds = this.form.items
        .filter((_, i) => i !== index)
        .map(item => item.building_id)
        .filter(id => id !== null);
      
      // Filter main options to exclude those IDs
      return this.buildingOptions.filter(opt => !selectedOtherBuildingIds.includes(opt.id));
    },
    async onBuildingChange(buildingId, index) {
      if (!buildingId) {
        this.form.items[index].rooms_list = [];
        this.form.items[index].selected_rooms = [];
        return;
      }

      try {
        this.form.items[index].loadingRooms = true;
        this.form.items[index].selected_rooms = [];
        
        // Fetch rooms for this specific building using GET_DETAIL_GEDUNG_DATA
        const data = await this.$store.dispatch(`gedung/${DISPATCH.GET_DETAIL_GEDUNG_DATA.split('/')[1]}`, buildingId);
        
        if (data && data.rooms) {
           this.form.items[index].rooms_list = data.rooms.map(room => ({
              id: room.id,
              name: room.room_name
           }));
        } else {
           this.form.items[index].rooms_list = [];
        }
      } catch (error) {
        console.error("Gagal memuat data ruangan:", error);
      } finally {
        this.form.items[index].loadingRooms = false;
      }
    },
    addRoomRow() {
      this.form.items.push({
        building_id: null,
        selected_rooms: [],
        rooms_list: [],
        loadingRooms: false,
      });
    },
    removeRoomRow(index) {
      this.form.items.splice(index, 1);
    },
    formatDate(date) {
      if (!date) return "";
      return moment(date).format("DD/MM/YYYY");
    },
    submitDatePicker(date) {
      if (date && date.start && date.end) {
        this.form.tanggal_start = moment(date.start).format("YYYY-MM-DD");
        this.form.tanggal_end = moment(date.end).format("YYYY-MM-DD");
        this.datePicker = { ...date };
        this.modalDatePicker = false;
      }
    },
    goBack() {
      this.$router.push({ name: 'peminjaman.list' });
    },
    openTimeModal(type) {
      if (type === 'start') {
        this.tempTimeStart = this.form.jam_mulai || "08:00";
        this.modalTimeStart = true;
      } else {
        this.tempTimeEnd = this.form.jam_selesai || "09:00";
        this.modalTimeEnd = true;
      }
    },
    saveTime(type) {
      if (type === 'start') {
        this.form.jam_mulai = this.tempTimeStart;
        this.modalTimeStart = false;
      } else {
        this.form.jam_selesai = this.tempTimeEnd;
        this.modalTimeEnd = false;
      }
    },
    simulateClick() {
      if (this.$refs.uploadLampiranRef) {
        this.$refs.uploadLampiranRef.click();
      }
    },
    onUploadChange(e) {
      const file = e.target.files[0];
      if (!file) return;

      if (file.type !== "application/pdf") {
        this.form.uploadError = true;
        this.form.file_name = "";
        this.form.file_raw = null;
        this.form.file_url = "";
        e.target.value = null; // reset input
        return;
      }

      this.form.uploadError = false;
      this.form.file_name = file.name;
      this.form.file_raw = file;
      this.form.file_url = URL.createObjectURL(file);
      this.form.created_at = moment().format("DD/MM/YYYY HH:mm");
      this.form.created_by_name = "Anda"; // Fallback text
    },
    viewUploadedPdf() {
      if (this.form.file_url) {
        window.open(this.form.file_url, "_blank");
      }
    },
    removeUploadedPdf() {
      this.form.file_name = "";
      this.form.file_raw = null;
      this.form.file_url = "";
      this.form.uploadError = false;
      if (this.$refs.uploadLampiranRef) {
        this.$refs.uploadLampiranRef.value = null;
      }
    },
    validateForm() {
      let isValid = true;
      this.errors = {
        tipe_pengajuan: "",
        waktu_peminjaman: "",
        keterangan: "",
        jam_mulai: "",
        jam_selesai: "",
        items: [],
      };

      if (!this.form.tipe_pengajuan) {
        this.errors.tipe_pengajuan = "Tipe pengajuan wajib dipilih";
        isValid = false;
      }

      if (!this.form.tanggal_start || !this.form.tanggal_end) {
        this.errors.waktu_peminjaman = "Waktu peminjaman wajib diisi";
        isValid = false;
      }

      if (!this.form.keterangan || this.form.keterangan.trim() === "") {
        this.errors.keterangan = "Keterangan wajib diisi";
        isValid = false;
      }

      if (!this.form.jam_mulai) {
        this.errors.jam_mulai = "Jam mulai wajib diisi";
        isValid = false;
      }

      if (!this.form.jam_selesai) {
        this.errors.jam_selesai = "Jam selesai wajib diisi";
        isValid = false;
      }

      this.form.items.forEach((item, index) => {
        let itemErrors = {};
        if (!item.building_id) {
          itemErrors.building_id = "Gedung wajib dipilih";
          isValid = false;
        }
        if (!item.selected_rooms || item.selected_rooms.length === 0) {
          itemErrors.selected_rooms = "Ruangan wajib dipilih";
          isValid = false;
        }
        this.errors.items[index] = itemErrors;
      });

      return isValid;
    },
    submitForm() {
      if (!this.validateForm()) {
        return;
      }

      // Logic for submission will go here
      const payload = {
         ...this.form,
         // Flatten selection if backend expects a list of room IDs
         all_room_ids: this.form.items.flatMap(item => item.selected_rooms.map(r => r.id))
      };
      
      console.log("Submitting Form Payload:", payload);
      alert("Pengajuan berhasil disimpan (Simulasi)");
      this.goBack();
    }
  },
};
</script>

<style scoped>
/* Ensure the grid alignment is clean */
.grid-cols-1.md\:grid-cols-2 {
  align-items: start;
}
</style>