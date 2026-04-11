<template>
  <table-app
    :headers="headers"
    :items="rooms"
    :options="tableOptions"
    :show-pagination="true"
    :use-custom-row="true"
    :searchable="false"
  >
    <template v-slot:customrow="{ rows }">
      <tr
        v-for="(row, index) in rows"
        :key="index"
        class="hover:bg-gray-50 transition-colors"
      >
        <td class="p-4 border-b border-gray-100 font-medium">
          {{ startingIndex + index + 1 }}.
        </td>
        <td class="p-4 border-b border-gray-100">{{ row.room_name }}</td>
        <td class="p-4 border-b border-gray-100">{{ row.room_code }}</td>
        <td class="p-4 border-b border-gray-100 text-center">
          {{ row.room_capacity }}
        </td>
        <td class="p-4 border-b border-gray-100">{{ row.room_purpose }}</td>
        <td class="p-4 border-b border-gray-100 text-center">
          <span
            class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
            :class="row.can_ujian ? 'bg-teal-100 text-teal-700' : 'bg-gray-100 text-gray-500'"
          >
            {{ row.can_ujian ? 'Bisa' : 'Tidak' }}
          </span>
        </td>
        <td class="p-4 border-b border-gray-100 text-center">
          <span
            class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
            :class="row.can_pembelajaran ? 'bg-teal-100 text-teal-700' : 'bg-gray-100 text-gray-500'"
          >
            {{ row.can_pembelajaran ? 'Bisa' : 'Tidak' }}
          </span>
        </td>
        <td class="p-4 border-b border-gray-100 text-center">
          <span
            class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
            :class="
              row.room_status === 'active'
                ? 'bg-teal-100 text-teal-700'
                : 'bg-gray-100 text-gray-500'
            "
          >
            {{ row.room_status }}
          </span>
        </td>
        <td class="p-4 border-b border-gray-100 text-center">
          <button
            type="button"
            @click="$emit('open-facility', startingIndex + index, row)"
            class="w-9 h-9 flex items-center justify-center rounded-lg bg-teal-50 text-teal-500 hover:bg-teal-500 hover:text-white transition-all shadow-sm mx-auto"
          >
            <font-awesome-icon icon="eye" />
          </button>
        </td>
      </tr>
    </template>
  </table-app>
</template>

<script>
import TableApp from "@/core/components/Table.vue";

export default {
  name: "BuildingDetailRoomTable",
  components: { TableApp },
  props: { rooms: Array },
  data() {
    return {
      tableOptions: { page: 1, itemsPerPage: 10, totalItems: 0 },
      headers: [
        { text: "No", value: "no", width: "w-12" },
        { text: "Nama Ruangan", value: "room_name" },
        { text: "Kode", value: "room_code" },
        { text: "Kapasitas", value: "room_capacity", align: "center" },
        { text: "Peruntukan", value: "room_purpose" },
        { text: "Ujian?", value: "can_ujian", align: "center" },
        { text: "Pembelajaran?", value: "can_pembelajaran", align: "center" },
        { text: "Status", value: "room_status", align: "center" },
        { text: "Aksi", value: "aksi", align: "center", width: "w-20" },
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