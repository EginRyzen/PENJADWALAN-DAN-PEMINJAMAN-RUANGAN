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
import DISPATCH from "@/core/plugins/constants/dispatches";

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
      buildingOptions: [],
    };
  },

  methods: {
    async fetchBuildingOptions() {
      try {
        const activeParams = this.activeStatus.join(",");

        const data = await this.$store.dispatch(DISPATCH.GET_BUILDINGS_ONLY, {
          active: activeParams,
        });

        this.buildingOptions = data.map((item) => ({
          id: item.id,
          name: item.building_code,
        }));
      } catch (error) {
        console.error("Gagal memuat filter gedung:", error);
      }
    },
    updateSelectedFromParent(newSelected) {
      this.selectedBuilding = newSelected;
    },
    resetFilter() {
      this.selectedBuilding = [];
    },
    handleStatusChange(value) {
      this.activeStatus = value;
    },
  },
  mounted() {
    this.fetchBuildingOptions();
  },

  watch: {
    activeStatus: {
      deep: true,
      handler(newVal) {
        this.fetchBuildingOptions();
        this.$emit("filter-status", newVal);
      },
    },
    selectedBuilding: {
      deep: true,
      handler(newVal) {
        this.$emit("filter-buildings", newVal);
      },
    },
  },
};
</script>