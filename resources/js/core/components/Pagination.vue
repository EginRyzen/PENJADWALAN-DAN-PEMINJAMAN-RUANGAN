<template>
  <div class="min-w-max">
    <section
      class="flex flex-col justify-between py-3 text-gray-dark"
      :class="{
        'bg-white': background,
      }"
    >
      <div
        class="flex"
        :class="{
          'justify-between': $slots['footer-leftAction'],
          'justify-end': !$slots['footer-leftAction'],
        }"
      >
        <slot name="footer-leftAction" />
        <div class="flex justify-end">
          <div
            class="flex items-center justify-center mr-2 page-per-rows-caption"
          >
            <span
              >Display {{ firstRowOnPage }} - {{ lastRowOnPage }} to
              {{ total }} entries</span
            >
          </div>
          <div>
            <div class="relative inline-block w-full mr-4">
              <select
                class="page-per-rows-content page-per-rows-text"
                placeholder="Regular input"
                @change="updatePaging($event.target.value)"
              >
                <option
                  v-for="(page, i) in paging"
                  :key="i"
                  :value="page"
                  :selected="perPage === page"
                >
                  {{ page }}
                </option>
              </select>
            </div>
          </div>
          <ul class="flex items-center">
            <li class="pr-1">
              <a href="#" @click.prevent="changePage(prevPage)">
                <div
                  class="flex items-center justify-center hover:bg-primary-dark hover:text-white rounded-md h-8 w-8"
                >
                  <div>
                    <svg
                      class="h-4 w-4"
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        style="color: #94a3b8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7"
                      />
                    </svg>
                  </div>
                </div>
              </a>
            </li>
            <li class="pr-1" v-for="(page, i) in pages" :key="i">
              <a href="#" @click.prevent="changePage(page)">
                <div
                  :class="{
                    'page-number-selected': current === page,
                    'text-white-300': current !== page,
                  }"
                  class="flex hover:bg-primary-dark hover:text-white rounded-md h-8 w-8 items-center justify-center"
                >
                  <span>{{ page }}</span>
                </div>
              </a>
            </li>
            <li class="pr-1">
              <a href="#" @click.prevent="changePage(nextPage)">
                <div
                  class="flex items-center justify-center hover:bg-primary-dark hover:text-white rounded-md h-8 w-8"
                >
                  <div>
                    <svg
                      class="h-4 w-4"
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        style="color: #94a3b8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5l7 7-7 7"
                      />
                    </svg>
                  </div>
                </div>
              </a>
            </li>
          </ul>
        </div>
      </div>
      <div class="flex justify-end">
        <slot name="footer-action" />
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: "PaginationApp",
  props: {
    current: {
      type: Number,
      default: 1,
    },
    total: {
      type: Number,
      default: 0,
    },
    totalRowsOnPage: {
      type: Number,
      default: 0,
    },
    perPage: {
      type: Number,
      default: 9,
    },
    pageRange: {
      type: Number,
      default: 2,
    },
    paging: {
      type: [Array, Function],
      default: () => [10, 25, 50, 100],
    },
    background: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      input: "",
      initiatePage: this.current,
      firstRowOnPage: this.total ? 1 : 0,
      lastRowOnPage: this.perPage,
    };
  },
  methods: {
    hasPrev() {
      return this.current > 1;
    },
    hasNext() {
      return this.current < this.totalPages;
    },
    changePage(page) {
      if (isNaN(page)) return false;
      this.initiatePage = page;
      if (page === 1 && this.current === 1) {
        return false;
      }
      if (
        (page > 0 && page < this.totalPages) ||
        this.current != this.totalPages
      ) {
        this.$emit("page-changed", page);
      }
      this.setFirstAndLastRowOnPage();
    },
    updatePaging(page) {
      this.initiatePage = 1;
      this.$emit("paging-change", parseInt(page));
      this.setFirstAndLastRowOnPage();
    },
    setFirstAndLastRowOnPage() {
      if (this.current > 1) {
        this.firstRowOnPage = 1 + (this.initiatePage - 1) * this.perPage;
      } else {
        this.firstRowOnPage = 1;
      }

      let lastRow = this.perPage * this.current;
      if (lastRow > this.total) this.lastRowOnPage = this.total;
      else this.lastRowOnPage = lastRow;
    },
    createRange(from, to) {
      const range = [];

      from = from > 0 ? from : 1;

      for (let i = from; i <= to; i++) {
        range.push(i);
      }

      return range;
    },
  },
  computed: {
    pages() {
      this.setFirstAndLastRowOnPage();
      const maxLength = Math.min(7, this.totalPages);
      if (this.totalPages === 1) return [1];
      if (this.totalPages <= maxLength)
        return [...this.createRange(1, maxLength)];

      const even = maxLength % 2 === 0 ? 1 : 0;
      const left = Math.floor(maxLength / 2);
      const right = this.totalPages - left + 1 + even;
      if (this.current > left && this.current < right) {
        const first = 1;
        const last = parseInt(this.totalpages);
        const start = this.current - left + 2;
        const end = this.current + left - 2 - even;
        const second = start - 1 === first + 1 ? 2 : "...";
        const beforeLast = end + 1 === last - 1 ? end + 1 : "...";

        return [
          1,
          second,
          ...this.createRange(start, end),
          beforeLast,
          this.totalPages,
        ];
      } else if (this.current === left) {
        const end = this.current + left - 1 - even;
        return [...this.createRange(1, end), "...", this.totalPages];
      } else if (this.current === right) {
        const start = this.current - left + 1;
        return [1, "...", ...this.createRange(start, this.totalPages)];
      } else {
        return [
          ...this.createRange(1, left),
          "...",
          ...this.createRange(right, this.totalPages),
        ];
      }
    },
    rangeStart() {
      let start = this.current - this.pageRange;

      return start > 0 ? start : 1;
    },
    rangeEnd() {
      let end = this.current + this.pageRange;
      return end < this.totalPages ? end : this.totalPages;
    },
    totalPages() {
      return Math.ceil(this.total / this.perPage);
    },
    nextPage() {
      let result = this.current + 1;
      return result > this.totalPages ? this.totalPages : result;
    },
    prevPage() {
      let result = this.current - 1;
      return result < 1 ? 1 : result;
    },
  },
};
</script>

<style>
select::-ms-expand {
  display: run-in;
}
.page-number-selected {
  background-color: #c0f7f2;
  color: #46bebb;
}
.page-per-rows-text {
  width: 32px;
  height: 16px;
  font-family: "Poppins", sans-serif;
  font-style: normal;
  font-weight: 600;
  font-size: 12px;
  line-height: 16px;
  letter-spacing: 0.02em;
  color: #3f4253;
  flex: none;
  order: 0;
  flex-grow: 0;
}
.page-per-rows-content {
  display: flex;
  flex-direction: row;
  align-items: center;
  padding: 9px 8px;
  width: 64px;
  height: 34px;
  background-color: #f5f8fa;
  border-radius: 5px;
  border: none;
  flex: none;
  order: 1;
  flex-grow: 0;
}
.page-per-rows-caption {
  font-family: "Poppins", sans-serif;
  font-style: normal;
  font-weight: 600;
  font-size: 12px;
  line-height: 16px;
  text-align: right;
  letter-spacing: 0.02em;
  color: #7f8299;
  flex: none;
  order: 0;
  flex-grow: 0;
}
</style>
