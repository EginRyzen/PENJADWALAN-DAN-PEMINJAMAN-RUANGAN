<template>
  <div class="bg-white shadow-xl rounded-2xl p-6 border border-gray-100">
    <div class="flex items-center gap-2 mb-4">
      <div class="w-1 h-6 bg-teal-500 rounded-full"></div>
      <p class="text-sm font-bold text-gray-800 uppercase tracking-wider">
        Status Aktivasi Gedung
      </p>
    </div>

    <div class="flex flex-col gap-2 mb-6 ml-3">
      <Checkbox
        v-model="activeStatus"
        idName="status-active"
        true-value="active"
      >
        Gedung Aktif
      </Checkbox>

      <Checkbox
        v-model="activeStatus"
        idName="status-inactive"
        true-value="inactive"
      >
        Gedung Non-Aktif
      </Checkbox>
    </div>

    <hr class="mb-6 border-gray-100" />

    <div class="flex items-center gap-2 mb-4">
      <div class="w-1 h-6 bg-teal-500 rounded-full"></div>
      <p class="text-sm font-bold text-gray-800 uppercase tracking-wider">
        Gedung
      </p>
    </div>

    <div class="ml-3">
      <Autocomplete
        label="Cari Gedung"
        :options="buildingOptions"
        item-value="id"
        item-text="name"
        placeholder="Masukkan nama gedung..."
        v-model="selectedBuilding"
        class="mt-1"
        multiple
        show-select-all
      />
    </div>
  </div>
</template>

<script>
import Autocomplete from "@/core/components/Autocomplete.vue";
import Checkbox from "@/core/components/Checkbox.vue";

export default {
  name: "FilterGedung",
  components: {
    Autocomplete,
    Checkbox,
  },
  data() {
    return {
      activeStatus: ["active"],
      selectedBuilding: [],
      buildingOptions: [
        { id: 1, name: "Gedung Menara BCA" },
        { id: 2, name: "Gedung Wisma Asia" },
        { id: 4, name: "Gedung KCU Bandung" },
        { id: 5, name: "Gedung KCU Jakarta" },
        { id: 6, name: "Gedung KCU Sumatera" },
        { id: 7, name: "Gedung KCU Suraya" },
        { id: 8, name: "Gedung KCU Malang" },
        { id: 9, name: "Gedung KCU Depok" },
        { id: 10, name: "Gedung KCU Tanggerang" },
        { id: 11, name: "Gedung KCU Bogor" },
        { id: 12, name: "Gedung KCU Yogjakarta" },
      ],
    };
  },
};
</script>