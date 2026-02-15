<template>
  <div class="h-full">
    <breadcrumb :items="breadcrumbs"></breadcrumb>
    <div class="flex gap-4 mt-10">
      <div class="w-1/4">
        <h6 class="font-bold mb-5">Filter</h6>

        <content-filter
          ref="filterComponent"
          @filter-status="handleStatusFilter"
          @filter-buildings="handleBuildingFilter"
        ></content-filter>

        <button-app
          expanded
          color="teal"
          class="mt-4 bg-teal-400 hover:bg-teal-500 text-white font-semibold px-4 py-2 rounded-lg shadow-md transition-colors duration-200"
          @click="$router.push({ name: 'gedung.create' })"
        >
          <template #icon-left>
            <font-awesome-icon icon="plus" />
          </template>
          Buat Data Gedung
        </button-app>

        <button-app
          expanded
          type="secondary"
          color="teal"
          class="mt-2 border border-teal-400 text-teal-400 hover:bg-teal-50"
        >
          <template #icon-left>
            <font-awesome-icon icon="download" />
          </template>
          Unduh Data Table
        </button-app>
      </div>

      <div class="w-3/4">
        <p class="font-normal text-sm mb-6 text-gray-600">
          Menampilkan 1 - {{ buildings.length }} dari
          {{ pagination.total_elements }} Daftar Gedung
        </p>

        <div
          v-if="selectedBuildingObjects.length > 0"
          class="flex flex-wrap gap-4 mb-6"
        >
          <chip
            v-for="building in selectedBuildingObjects"
            :key="building.id"
            close
            @close="removeSingleFilter(building.id)"
          >
            {{ building.name }}
          </chip>

          <button
            @click="resetAllFilters"
            class="text-teal-400 border-b-2 border-teal-400 text-sm hover:text-teal-600 transition-colors"
          >
            Hapus Filter
          </button>
        </div>

        <div
          class="flex flex-col bg-white p-6 rounded-lg shadow-md text-gray-900"
        >
          <div class="flex justify-between items-center">
            <h6 class="font-semibold flex-1 text-lg">Daftar Gedung</h6>

            <div class="relative flex-1 max-w-80">
              <div class="relative">
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Masukkan Nama Gedung/Code Gedung"
                  class="w-full bg-white border border-teal-400 rounded-md py-2 pl-4 pr-10 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition-all duration-200 text-gray-500"
                />
                <div
                  class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"
                >
                  <font-awesome-icon icon="search" class="text-gray-500" />
                </div>
              </div>
            </div>
          </div>

          <div
            v-if="buildings.length > 0"
            class="grid lg:grid-cols-3 gap-4 mt-6"
          >
            <gedung-list-item
              v-for="(building, index) in buildings"
              :key="index"
              :building="building"
            ></gedung-list-item>
          </div>

          <div
            v-else
            class="flex flex-col items-center justify-center py-20 text-gray-400"
          >
            <font-awesome-icon
              icon="exclamation-circle"
              class="text-5xl mb-4"
            />
            <p class="text-lg font-semibold">Gedung tidak ditemukan</p>
            <p class="text-sm">Coba masukkan kata kunci pencarian yang lain.</p>
          </div>

          <div v-if="buildings.length > 0" class="flex justify-end mt-8">
            <pagination
              :current="pagination.current_page + 1"
              :total="pagination.total_elements"
              :per-page="pagination.total_elements_per_page"
              :totalRowsOnPage="buildings.length"
              @page-changed="handlePageChange"
              @paging-change="handleSizeChange"
            ></pagination>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Breadcrumb from "@/core/components/Breadcrumb.vue";
import Pagination from "@/core/components/Pagination.vue";
import ContentFilter from "@/core/components/ContentFilter.vue";
import GedungListItem from "@/modules/gedung/components/GedungListItem.vue";
import ButtonApp from "@/core/components/Button.vue";
import DISPATCH from "@/core/plugins/constants/dispatches";
import _ from "lodash";

export default {
  name: "GedungList",
  components: {
    Breadcrumb,
    Pagination,
    ContentFilter,
    GedungListItem,
    ButtonApp,
  },
  data() {
    return {
      searchQuery: "",
      selectedBuildingObjects: [],
      params: {
        page: 0,
        size: 9,
        active: "true",
      },
      breadcrumbs: [
        { text: "Gedung" },
        { text: "Profile Gedung", link: "/app/gedung-list" },
      ],
    };
  },
  computed: {
    buildings() {
      return this.$store.state.gedung.buildings;
    },
    pagination() {
      return this.$store.state.gedung.pagination;
    },
  },
  watch: {
    searchQuery: _.debounce(function () {
      this.params.page = 0;
      this.fetchBuildings();
    }, 500),
  },
  methods: {
    async fetchBuildings() {
      try {
        this.$store.commit("SET_LOADING", true);
        const finalParams = { ...this.params, search: this.searchQuery };

        if (finalParams.ids && finalParams.ids.length > 0) {
          finalParams.ids = finalParams.ids.join(",");
        }

        await this.$store.dispatch(DISPATCH.GET_GEDUNG_DATA, finalParams);
        this.$store.commit("SET_LOADING", false);
      } catch (error) {
        console.error("Gagal memuat data gedung:", error);
        this.$store.commit("SET_LOADING", false);
      }
    },
    handleBuildingFilter(buildings) {
      this.selectedBuildingObjects = buildings;

      if (buildings && buildings.length > 0) {
        this.params.ids = buildings.map((b) => b.id);
      } else {
        delete this.params.ids;
      }

      this.params.page = 0;
      this.fetchBuildings();
    },
    removeSingleFilter(id) {
      const updated = this.selectedBuildingObjects.filter((b) => b.id !== id);
      if (this.$refs.filterComponent) {
        this.$refs.filterComponent.updateSelectedFromParent(updated);
      }
    },
    resetAllFilters() {
      this.selectedBuildingObjects = [];
      delete this.params.ids;
      if (this.$refs.filterComponent) {
        this.$refs.filterComponent.resetFilter();
      }
      this.params.page = 0;
      this.fetchBuildings();
    },
    handlePageChange(page) {
      this.params.page = page - 1;
      this.fetchBuildings();
    },
    handleSizeChange(size) {
      this.params.size = size;
      this.params.page = 0;
      this.fetchBuildings();
    },
    handleStatusFilter(statusArray) {
      const hasActive = statusArray.includes("active");
      const hasInactive = statusArray.includes("inactive");

      if (hasActive && hasInactive) {
        delete this.params.active;
      } else if (hasActive) {
        this.params.active = "true";
      } else if (hasInactive) {
        this.params.active = "false";
      } else {
        delete this.params.active;
      }

      this.params.page = 0;
      this.fetchBuildings();
    },
  },
  mounted() {
    this.fetchBuildings();
  },
};
</script>