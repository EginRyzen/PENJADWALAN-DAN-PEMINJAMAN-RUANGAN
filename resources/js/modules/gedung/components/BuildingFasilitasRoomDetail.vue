<template>
  <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-md">
    <div class="flex items-center justify-between mb-6 border-b border-gray-50 pb-4">
      <div class="flex items-center gap-3">
        <div class="w-1.5 h-8 bg-teal-500 rounded-full"></div>
        <div>
          <p class="text-xs text-gray-500 uppercase font-bold tracking-widest">Detail Fasilitas</p>
          <h3 class="text-xl font-extrabold text-gray-800">{{ roomName }}</h3>
        </div>
      </div>

      <div @click="$emit('back')"
        class="flex items-center gap-2 text-gray-500 hover:text-teal-600 cursor-pointer transition-colors font-semibold text-sm">
        <font-awesome-icon icon="arrow-left" />
        <span>Kembali ke Daftar Ruangan</span>
      </div>
    </div>

    <table-app :headers="headers" :items="facilities" :options="tableOptions" :show-pagination="true"
      :use-custom-row="true" :searchable="false">
      <template v-slot:customrow="{ rows }">
        <tr v-for="(row, index) in rows" :key="index" class="hover:bg-gray-50 transition-colors">
          <td class="p-4 border-b border-gray-100 font-medium text-gray-400 w-12">
            {{ startingIndex + index + 1 }}.
          </td>
          <td class="p-4 border-b border-gray-100">
            {{ row.facility?.facility_name || '-' }}
          </td>
          <td class="p-4 border-b border-gray-100 font-bold text-teal-600">
            {{ row.quantity }}
          </td>
        </tr>
      </template>
    </table-app>
  </div>
</template>

<script>
import TableApp from "@/core/components/Table.vue";

export default {
  name: "BuildingFasilitasRoomDetail",
  components: { TableApp },
  props: {
    roomName: String,
    facilities: Array
  },
  data() {
    return {
      tableOptions: { page: 1, itemsPerPage: 10, totalItems: 0 },
      headers: [
        { text: "No", value: "no", width: "w-12" },
        { text: "Nama Fasilitas", value: "facility_name" },
        { text: "Jumlah (Qty)", value: "quantity", width: "w-40" },
      ],
    };
  },
  computed: {
    startingIndex() {
      return (this.tableOptions.page - 1) * this.tableOptions.itemsPerPage;
    },
  },
};
</script>