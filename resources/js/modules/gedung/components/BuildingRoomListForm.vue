<template>
  <div class="mb-8">
    <div class="flex items-center gap-2 mb-6">
      <div class="w-1.5 h-6 bg-teal-500 rounded-full"></div>
      <h3 class="text-lg font-bold text-gray-800">Daftar Ruangan</h3>
    </div>

    <table-app v-model:options="tableOptions" :headers="headers" :items="rooms" :show-pagination="true"
      :use-custom-row="true" :searchable="false">
      <template v-slot:customrow="{ rows }">
        <tr v-for="(row, index) in rows" :key="row.id" class="hover:bg-gray-50 transition-colors">
          <td class="p-4 border-b border-gray-100 text-start font-medium align-top">
            {{ startingIndex + index + 1 }}.
          </td>
          <td class="p-2 border-b border-gray-100 align-top">
            <app-input v-model="row.room_name" placeholder="Nama Ruangan"
              :error="!!errors[startingIndex + index]?.room_name"
              @clear-error="errors[startingIndex + index].room_name = null">
              <template #error-message>{{
                errors[startingIndex + index]?.room_name
              }}</template>
            </app-input>
          </td>
          <td class="p-2 border-b border-gray-100 align-top">
            <app-input v-model="row.room_code" placeholder="R-01" :error="!!errors[startingIndex + index]?.room_code"
              @clear-error="errors[startingIndex + index].room_code = null">
              <template #error-message>{{
                errors[startingIndex + index]?.room_code
              }}</template>
            </app-input>
          </td>
          <td class="p-2 border-b border-gray-100 align-top">
            <app-input v-model.number="row.room_capacity" type="number" placeholder="0"
              :error="!!errors[startingIndex + index]?.room_capacity"
              @clear-error="errors[startingIndex + index].room_capacity = null" @keydown="onlyNumber">
              <template #error-message>{{
                errors[startingIndex + index]?.room_capacity
              }}</template>
            </app-input>
          </td>

          <td class="p-2 border-b border-gray-100 align-top">
            <div class="relative">
              <select v-model="row.room_purpose"
                class="w-full bg-white border border-teal-400 text-gray-700 py-3 px-3 pr-8 rounded-md appearance-none text-sm h-11 transition-all focus:border-teal-500 outline-none">
                <option v-for="purpose in room_purpose_options" :key="purpose" :value="purpose">
                  {{ purpose }}
                </option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                  <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                </svg>
              </div>
            </div>
          </td>
          <td class="p-2 border-b border-gray-100 align-top">
            <select v-model="row.can_ujian"
              class="w-full bg-white border border-teal-400 text-gray-700 py-3 px-3 rounded-md appearance-none text-sm h-11 transition-all focus:border-teal-500 outline-none">
              <option :value="true">Bisa</option>
              <option :value="false">Tidak</option>
            </select>
          </td>
          <td class="p-2 border-b border-gray-100 align-top">
            <select v-model="row.can_pembelajaran"
              class="w-full bg-white border border-teal-400 text-gray-700 py-3 px-3 rounded-md appearance-none text-sm h-11 transition-all focus:border-teal-500 outline-none">
              <option :value="true">Bisa</option>
              <option :value="false">Tidak</option>
            </select>
          </td>
          <td class="p-2 border-b border-gray-100 align-top">
            <select v-model="row.room_status"
              class="w-full bg-white border border-teal-400 text-gray-700 py-3 px-3 rounded-md appearance-none text-sm h-11">
              <option value="active">Aktif</option>
              <option value="inactive">Tidak Aktif</option>
            </select>
          </td>
          <td class="p-2 border-b border-gray-100 text-center flex justify-center gap-2 align-top pt-3">
            <button type="button" @click="$emit('open-facility', startingIndex + index, row)"
              :disabled="!isRowComplete(row)"
              class="w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200" :class="[
                !isRowComplete(row)
                  ? 'bg-gray-100 text-gray-400'
                  : errors[startingIndex + index]?.facilities_empty
                    ? 'bg-red-500 text-white shadow-lg'
                    : 'bg-teal-50 text-teal-500',
              ]">
              <font-awesome-icon icon="plus" />
            </button>
            <button type="button" @click="removeRow(row.id)" :disabled="rooms.length === 1"
              class="w-8 h-8 flex items-center justify-center rounded-lg" :class="[
                rooms.length > 1
                  ? 'bg-red-50 text-red-500'
                  : 'bg-gray-100 text-gray-400',
              ]">
              <font-awesome-icon icon="trash-alt" />
            </button>
          </td>
        </tr>
      </template>
      <template v-slot:lastrow>
        <tr>
          <td colspan="9" class="p-4 border-t border-gray-100">
            <button type="button" @click="addRow" class="text-teal-500 font-bold">
              + Tambah Baris Ruangan
            </button>
          </td>
        </tr>
      </template>
    </table-app>
  </div>
</template>

<script>
import TableApp from "@/core/components/Table.vue";
import AppInput from "@/core/components/AppInput.vue";

export default {
  components: { TableApp, AppInput },
  data() {
    return {
      rooms: [
        {
          id: Date.now(),
          room_name: "",
          room_code: "",
          room_capacity: 0,
          room_purpose: "RUANG KELAS",
          can_ujian: false,
          can_pembelajaran: false,
          room_status: "inactive",
          facilities: [],
        },
      ],
      room_purpose_options: [
        "RUANG KELAS",
        "LABORATORIUM",
        "AULA",
        "RUANG RAPAT",
        "GUDANG",
        "LAB KOMPUTER",
      ],
      errors: [],
      tableOptions: { page: 1, itemsPerPage: 10, totalItems: 1 },
      headers: [
        { text: "No", value: "no", align: "start", width: "w-10" },
        { text: "Nama Ruangan", value: "room_name", align: "start" },
        { text: "Kode", value: "room_code", align: "start", width: "w-33" },
        {
          text: "Kapasitas",
          value: "room_capacity",
          align: "start",
          width: "w-24",
        },
        { text: "Peruntukan", value: "room_purpose", align: "start" },
        { text: "Ujian?", value: "can_ujian", align: "center", width: "w-24" },
        { text: "Pembelajaran?", value: "can_pembelajaran", align: "center", width: "w-24" },
        {
          text: "Status",
          value: "room_status",
          align: "center",
          width: "w-37",
        },
        { text: "Aksi", value: "aksi", align: "center", width: "w-20" },
      ],
    };
  },
  computed: {
    startingIndex() {
      return (this.tableOptions.page - 1) * this.tableOptions.itemsPerPage;
    },
  },
  methods: {
    clearFacilityError(index) {
      if (this.errors[index]) {
        const room = this.rooms[index];
        const hasFacilities = room.facilities && room.facilities.length > 0;
        const allFacilitiesValid = room.facilities.every(
          (f) => f.facility_id && f.quantity
        );

        if (hasFacilities && allFacilitiesValid) {
          this.errors[index].facilities_empty = false;
        }
      }
    },
    onlyNumber(event) {
      const forbiddenKeys = ["e", "E", "+", "-", ".", ","];

      if (forbiddenKeys.includes(event.key)) {
        event.preventDefault();
      }
    },
    isRowComplete(row) {
      return row.room_name && row.room_code && row.room_purpose;
    },
    addRow() {
      this.rooms.push({
        id: Date.now() + Math.random(),
        room_name: "",
        room_code: "",
        room_capacity: 0,
        room_purpose: "RUANG KELAS",
        can_ujian: false,
        can_pembelajaran: false,
        room_status: "inactive",
        facilities: [],
      });
      this.tableOptions.totalItems = this.rooms.length;

      this.$nextTick(() => {
        const lastPage = Math.ceil(
          this.rooms.length / this.tableOptions.itemsPerPage
        );
        this.tableOptions.page = lastPage;
      });
    },
    removeRow(id) {
      if (this.rooms.length > 1) {
        const idx = this.rooms.findIndex((r) => r.id === id);
        this.rooms.splice(idx, 1);
        this.tableOptions.totalItems = this.rooms.length;
      }
    },
    validate() {
      this.errors = [];
      let isValid = true;
      let existingCodes = new Set();
      let existingNames = new Set();

      this.rooms.forEach((room, index) => {
        let err = {};
        if (!room.room_name) err.room_name = "Wajib";
        if (!room.room_code) err.room_code = "Wajib";
        if (!room.room_capacity && room.room_capacity !== 0) err.room_capacity = "Wajib";
        if (!room.room_purpose) err.room_purpose = "Wajib";

        if (room.room_name && existingNames.has(room.room_name.toLowerCase())) {
          err.room_name = "Nama sudah ada";
        }
        if (room.room_code && existingCodes.has(room.room_code.toLowerCase())) {
          err.room_code = "Kode sudah ada";
        }

        if (room.room_name) existingNames.add(room.room_name.toLowerCase());
        if (room.room_code) existingCodes.add(room.room_code.toLowerCase());

        const hasNoFacilities =
          !room.facilities || room.facilities.length === 0;
        const hasInvalidFacilities =
          room.facilities &&
          room.facilities.some((f) => !f.facility_id || !f.quantity);

        if (
          this.isRowComplete(room) &&
          (hasNoFacilities || hasInvalidFacilities)
        ) {
          err.facilities_empty = true;
          isValid = false;
        }
        this.errors[index] = err;
        if (Object.keys(err).length > 0) isValid = false;
      });
      return isValid;
    },
    getData() {
      return this.rooms;
    },
  },
};
</script>