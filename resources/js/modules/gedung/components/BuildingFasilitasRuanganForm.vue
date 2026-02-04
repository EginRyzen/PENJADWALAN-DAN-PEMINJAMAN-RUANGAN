<template>
  <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-md transition-all duration-300">
    <div class="flex items-center justify-between mb-6 border-b border-gray-50 pb-4">
      <div class="flex items-center gap-3">
        <div class="w-1.5 h-8 bg-teal-500 rounded-full shadow-sm"></div>
        <div>
          <p class="text-xs text-gray-500 uppercase font-bold tracking-widest">
            Manajemen Fasilitas
          </p>
          <h3 class="text-xl font-extrabold text-gray-800">{{ roomName }}</h3>
        </div>
      </div>
      
      <div 
        @click="handleBack" 
        class="flex items-center gap-2 text-gray-500 hover:text-teal-600 cursor-pointer transition-colors font-semibold text-sm select-none"
      >
        <font-awesome-icon icon="arrow-left" />
        <span>Kembali ke Daftar Ruangan</span>
      </div>
    </div>

    <div v-if="showError" class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg shadow-sm flex items-center justify-between animate-pulse">
      <div class="flex items-center gap-3">
        <font-awesome-icon icon="exclamation-triangle" class="text-lg" />
        <span class="text-sm font-medium">Data gagal divalidasi! Pastikan Nama Fasilitas dan Jumlah di setiap baris sudah terisi.</span>
      </div>
      <button @click="showError = false" class="text-red-400 hover:text-red-600">
        <font-awesome-icon icon="times" />
      </button>
    </div>

    <table-app
      v-model:options="tableOptions"
      :headers="headers"
      :items="facilities"
      :show-pagination="true"
      :searchable="false"
      :use-custom-row="true"
      :server-side="false"
      class="table-overflow-visible"
    >
      <template v-slot:customrow="{ rows }">
        <tr
          v-for="(row, index) in rows"
          :key="index"
          class="hover:bg-gray-50/50 transition-colors"
          :class="{ 'bg-red-50/50': showError && (!row.facility_id || !row.quantity) }"
          :style="row.is_active ? 'position: relative; z-index: 100;' : 'position: relative; z-index: 1;'"
        >
          <td class="p-4 border-b border-gray-100 text-start font-medium text-gray-400 w-12">
            {{ startingIndex + index + 1 }}.
          </td>

          <td class="p-2 border-b border-gray-100 overflow-visible">
            <select-auto-complete
              v-model="row.facility_id"
              :options="facilityOptions"
              placeholder="Cari Fasilitas..."
              @focus="row.is_active = true"
              @blur="row.is_active = false"
            />
          </td>

          <td class="p-2 border-b border-gray-100">
            <app-input
              v-model.number="row.quantity"
              type="number"
              placeholder="0"
              custom-class="bg-white"
            />
          </td>

          <td class="p-2 border-b border-gray-100 text-center">
            <button
              type="button"
              @click="removeRow(startingIndex + index)"
              class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-sm mx-auto"
            >
              <font-awesome-icon icon="trash-alt" />
            </button>
          </td>
        </tr>
      </template>

      <template v-slot:lastrow>
        <tr class="bg-gray-50/30 border-t border-gray-100">
          <td colspan="4" class="p-4">
            <button
              type="button"
              @click="addRow"
              class="flex items-center gap-2 text-teal-600 hover:text-teal-700 font-bold transition-all px-2 py-1 rounded-md hover:bg-teal-50 outline-none"
            >
              <font-awesome-icon icon="plus" class="text-xs" />
              Tambah Baris Fasilitas
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
import SelectAutoComplete from "@/core/components/SelectAutoComplete.vue";
import DISPATCH from "@/core/plugins/constants/dispatches";

export default {
  name: "BuildingFasilitasRuanganForm",
  components: { TableApp, AppInput, SelectAutoComplete },
  props: {
    roomName: String,
    facilities: { type: Array, default: () => [] },
  },
  data() {
    return {
      showError: false,
      tableOptions: {
        page: 1,
        itemsPerPage: 10,
        totalItems: 0
      },
      headers: [
        { text: "No", value: "no", align: "start", width: "w-12" },
        { text: "Nama Fasilitas", value: "facility_id", align: "start" },
        { text: "Jumlah (Qty)", value: "quantity", align: "start", width: "w-40" },
        { text: "Aksi", value: "aksi", align: "center", width: "w-24" },
      ],
      facilityOptions: [],
    };
  },
  computed: {
    startingIndex() {
      return (this.tableOptions.page - 1) * this.tableOptions.itemsPerPage;
    },
  },
  methods: {
    handleBack() {
      const isInvalid = this.facilities.some(item => !item.facility_id || !item.quantity);
      if (isInvalid) {
        this.showError = true;
        setTimeout(() => { this.showError = false; }, 5000);
      } else {
        this.showError = false;
        this.$emit('back');
      }
    },
    addRow() {
      this.facilities.push({
        facility_id: null,
        quantity: 1,
        is_active: false,
      });
      this.$nextTick(() => {
        this.tableOptions.page = Math.ceil(this.facilities.length / this.tableOptions.itemsPerPage);
      });
    },
    removeRow(index) {
      if (index > -1) {
        this.facilities.splice(index, 1);
        const maxPage = Math.ceil(this.facilities.length / this.tableOptions.itemsPerPage);
        if (this.tableOptions.page > maxPage) {
          this.tableOptions.page = maxPage || 1;
        }
      }
    },
    async fetchFacilityOptions() {
      try {
        const data = await this.$store.dispatch(DISPATCH.GET_GEDUNG_FACILITIES);
        
        this.facilityOptions = data.map(item => ({
          id: item.id,
          name: item.facility_name 
        }));
      } catch (error) {
        console.error("Gagal memuat data fasilitas gedung:", error);
      }
    },
  },
  mounted(){
    this.fetchFacilityOptions();
  },
  watch: {
    facilities: {
      handler(newVal) {
        this.tableOptions.totalItems = newVal.length;
      },
      deep: true,
      immediate: true,
    },
  },
};
</script>

<style scoped>
:deep(th) {
  white-space: nowrap !important;
}
.table-overflow-visible :deep(.overflow-x-auto) {
  overflow: visible !important;
}
.table-overflow-visible :deep(table) {
  border-collapse: separate !important;
  table-layout: auto !important;
}
</style>