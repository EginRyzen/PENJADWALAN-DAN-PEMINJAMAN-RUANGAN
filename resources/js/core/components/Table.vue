<template>
  <div
    :class="{
      'overflow-x-auto': scrollX,
    }"
  >
    <input
      type="text"
      class="form-input w-1/2 hover:shadow-primary-sm focus:ring-0 focus:shadow-primary focus:border-primary shadow-primary border-primary rounded-lg mb-4"
      placeholder="Search..."
      v-model="search"
      @input="handleSearch"
      v-if="searchable"
    />
    <div
      :class="{
        'overflow-x-auto w-full': scrollX,
      }"
      :style="customHeight"
    >
      <table
        class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-700"
        aria-describedby="bima-table"
      >
        <caption v-if="caption">
          {{
            caption
          }}
        </caption>
        <thead class="bg-white text-gray-darkest">
          <tr>
            <th
              v-for="(head, i) in headers"
              :key="i"
              class="px-4 py-3 border-b-2 border-primary bg-gray-100 text-sm font-semibold text-gray-700 tracking-wider"
              :class="{
                'text-left': head.align === 'start',
                'text-center': head.align === 'center',
                'text-right': head.align === 'end',
                'cursor-pointer': head.sortable,
              }"
              scope="col"
            >
              <div
                class="relative flex justify-items-center items-center"
                @click="head.sortable && sortBy($event, head.value)"
              >
                <div class="text-error" v-if="head.required">*</div>
                {{ head.text }}
                <div
                  v-if="
                    head.sortable && findSortDirection(head.value) === false
                  "
                  class="sort-default"
                ></div>
                <div
                  :class="{
                    'sort-desc': findSortDirection(head.value) === 'desc',
                    'sort-asc': findSortDirection(head.value) === 'asc',
                  }"
                ></div>
              </div>
              <button
                class="rounded-full h-5 w-5 inline-flex justify-center items-center bg-primary-lightest ml-1 text-xs text-primary-dark"
                @click="removeSorting(head.value)"
                v-if="findSort(head.value) && sortOrder.length > 1"
              >
                {{ findSortIndex(head.value) + 1 }}
              </button>
            </th>
          </tr>
        </thead>
        <tbody class="text-gray-dark">
          <tr v-if="showNotFound && tableData.length < 1 && !$slots.nodata">
            <td
              :colspan="headers.length"
              class="bg-primary-lightest bg-opacity-25 text-center p-4"
            >
              {{ notFoundLabel }}
            </td>
          </tr>
          <template v-if="!useCustomRow">
            <tr v-for="(data, i) in tableData" :key="i" class="bg-white">
              <td
                class="p-4 border-b"
                v-for="head in headers"
                :key="head.value"
                :class="[
                  {
                    'text-left': head.align === 'start',
                    'text-center': head.align === 'center',
                    'text-right': head.align === 'end',
                  },
                  head.width,
                  'border-primary',
                ]"
              >
                <div :class="['flex', head.width, wordWrap(head.width)]">
                  <slot
                    :name="head.value"
                    :slot-props="{
                      data,
                      index: getCurrentIndex(i),
                    }"
                  >
                    {{ data[head.value] }}
                  </slot>
                </div>
              </td>
            </tr>
          </template>
          <slot
            v-if="useCustomRow"
            name="customrow"
            :rows="tableData"
            :headers="headers"
          ></slot>
          <slot name="lastrow"></slot>
        </tbody>
      </table>
      <slot v-if="tableData.length < 1" name="nodata"></slot>
    </div>
    <div class="form-group text-gray-dark space-x-1" v-if="showPagination">
      <Pagination
        :current="currentPage"
        :total="totalRecords"
        :per-page="pageCount"
        :total-rows-on-page="totalRowsOnPage"
        @page-changed="handlePageChange($event)"
        @paging-change="handlePagingChange($event)"
      >
        <template v-slot:footer-action>
          <slot name="footer-action"></slot>
        </template>
      </Pagination>
    </div>
  </div>
</template>

<script>
import _ from "lodash";
import Pagination from "@/core/components/Pagination.vue";

export default {
  name: "TableApp",
  components: {
    Pagination,
  },
  props: {
    customHeight: { type: String, default: "" },
    items: { type: [Array, Function], default: () => [] },
    headers: { type: Array, required: true },
    sortOrder: { type: Array },
    options: { type: Object },
    notFoundLabel: { type: String, default: "Data not found" },
    showNotFound: { type: Boolean, default: true },
    showPagination: { type: Boolean, default: true },
    searchable: { type: Boolean, default: true },
    searchQuery: { type: String, default: null },
    rowClass: { type: Function, default: () => "" },
    serverSide: { type: Boolean, default: false },
    scrollX: { type: Boolean, default: true },
    useCustomRow: { type: Boolean, default: false },
    caption: { type: String, default: "" },
  },
  data() {
    return {
      records: [],
      search: "",
      pageIndex: 0,
      pageCount: this.options.itemsPerPage || 10,
      currentPage: 1,
      totalPage: 0,
      totalRecords: 0,
      totalRowsOnPage: 0,
    };
  },
  mounted() {
    this.mapDataToRows();
  },
  computed: {
    sortData() {
      const startTime = new Date().getTime();
      const obj = this;
      if (this.usesLocalData) {
        obj.records = obj.items;
        if (obj.sortOrder.length) {
          obj.records = _.orderBy(
            obj.records,
            obj.sortOrder.map((q) => q.field),
            obj.sortOrder.map((q) => q.direction)
          );
        }
      }
      const endTime = new Date().getTime();
      const executionTime = endTime - startTime;
      console.log(`Execution time function sort data: ${executionTime} ms`);
      return obj.records;
    },
    filterData() {
      const obj = this;
      const data = obj.sortData
        .filter((_data) => {
          const rows = obj.headers
            .filter((head) => head.sortable)
            .map((q) => q.value);
          let flag = false;
          if (rows.length > 0) {
            rows.forEach((rowsKey) => {
              if (_data[rowsKey] != undefined) {
                if (
                  _data[rowsKey]
                    .toString()
                    .toLowerCase()
                    .indexOf(obj.search.toString().trim().toLowerCase()) != -1
                ) {
                  flag = true;
                }
              }
            });
            return flag;
          }
          return _data;
        })
        .map((_data) => {
          if (obj.search === "") {
            return _data;
          }
          return JSON.parse(JSON.stringify(_data));
        });
      obj.totalRecords = data.length;
      obj.countPage(data.length);
      return data;
    },
    tableData() {
      const obj = this;
      if (this.usesLocalData) {
        let start = obj.pageIndex * obj.pageCount;
        let end = (obj.pageIndex + 1) * obj.pageCount;

        return obj.filterData.slice(start, end);
      }

      return obj.records;
    },
    usesLocalData() {
      return !this.serverSide;
    },
  },
  watch: {
    searchQuery: _.debounce(function () {
      const obj = this;
      if (!obj.searchable && obj.searchQuery !== null) {
        obj.search = obj.searchQuery;
        obj.mapDataToRows();
      }
    }, 500),
    items() {
      const startTime = new Date().getTime();
      this.mapDataToRows();
      const endTime = new Date().getTime();
      const executionTime = endTime - startTime;
      console.log(`Execution time loading data to table: ${executionTime} ms`);
    },
    options(data) {
      this.pageIndex = data.page - 1;
      this.currentPage = data.page;
      this.pageCount = data.itemsPerPage;
    },
    pageCount() {
      this.fetchServerData();
    },
  },
  methods: {
    mapDataToRows() {
      const result = this.usesLocalData
        ? this.tableData
        : this.fetchServerData();

      this.totalRowsOnPage = result ? result.length : 0;

      return result;
    },
    fetchServerData() {
      const startTime = new Date().getTime();
      const obj = this;
      obj.totalRecords = obj.options.totalItems;
      obj.records = obj.items;

      obj.$emit("update:options", {
        page: obj.currentPage || 1,
        itemsPerPage: obj.pageCount,
        totalItems: obj.totalRecords,
      });
      const endTime = new Date().getTime();
      const executionTime = endTime - startTime;
      console.log(`Execution time fetch server data: ${executionTime} ms`);
    },
    onLeftAction() {
      this.$emit("left-action");
    },
    countPage(total = 0) {
      const obj = this;
      if (total < 1) {
        obj.totalPage = 0;
        return obj.totalPage;
      }
      obj.totalPage =
        total < obj.pageCount ? 1 : Math.floor(total / obj.pageCount);
      return obj.totalPage;
    },
    sortBy(event, key) {
      const obj = this;
      const index = obj.sortOrder.findIndex((e) => e.field === key);
      if (index === -1) {
        if (!event.shiftKey) {
          obj.sortOrder = [];
        }

        obj.sortOrder.push({ field: key, direction: "asc" });
      } else {
        if (obj.sortOrder[index].direction === "desc") {
          obj.sortOrder.splice(index, 1);
        } else {
          obj.sortOrder[index].direction = "desc";
        }
      }
      obj.$emit("update:sort-order", obj.sortOrder);
      this.detectUpdate();

      if (obj.usesLocalData) {
        return obj.sortData;
      } else {
        return obj.mapDataToRows();
      }
    },
    removeSorting(value) {
      const obj = this;
      obj.sortOrder = obj.sortOrder.filter((q) => q.field !== value);

      obj.$emit("update:sort-order", obj.sortOrder);
      this.detectUpdate();
    },
    handleSearch: _.debounce(function () {
      this.pageIndex = 0;
      this.currentPage = 1;

      this.mapDataToRows();
      this.detectUpdate();
    }, 500),
    handlePageChange(event) {
      this.currentPage = event;
      this.pageIndex = event - 1;
      this.$emit("pageChange", this.currentPage);
      if (this.usesLocalData) {
        this.$emit("update:options", {
          page: event || 1,
          itemsPerPage: this.options.itemsPerPage,
        });
      }

      this.mapDataToRows();
      this.detectUpdate();
    },
    handlePagingChange(event) {
      this.pageIndex = 0;
      this.currentPage = 1;
      this.pageCount = parseInt(event);
      this.options.itemsPerPage = event;

      this.mapDataToRows();
      this.detectUpdate();
    },
    findSort(field) {
      return this.sortOrder.find((sort) => sort.field === field);
    },
    findSortDirection(field) {
      return this.findSort(field) ? this.findSort(field).direction : false;
    },
    findSortIndex(field) {
      return this.sortOrder.findIndex((sort) => sort.field === field);
    },
    getCurrentIndex(index) {
      return this.pageIndex * this.pageCount + index;
    },
    detectUpdate() {
      this.$emit("refresh");
    },

    wordWrap(width) {
      if (width) {
        return "break-all wrap-word";
      } else {
        return "";
      }
    },
  },
};
</script>

<style scoped>
.wrap-word {
  word-wrap: break-word;
}
</style>
