<template>
  <modal-app v-model="internalShow" size="small">
    <div class="p-6 text-normal text-gray-800 font-semibold border-b border-gray-100 bg-gray-50/50">
      <span class="flex items-center">
        <span class="w-1.5 h-5 bg-teal-500 rounded-full mr-2"></span>
        Filter Rentang Waktu
      </span>
    </div>
    
    <div class="p-6">
      <div class="flex flex-col gap-6">
        <!-- Date Picker Component placed above inputs to prioritize touch targeting -->
        <DatePicker
          v-model.range="datePicker"
          class="custom-calendar-picker mx-auto w-full"
          :columns="1"
          color="teal"
          locale="id"
          :first-day-of-week="2"
          trim-weeks
        />

        <!-- Input previews stacked vertically for mobile -->
        <div class="flex flex-col gap-4 w-full">
          <div class="flex flex-col w-full">
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Tanggal Mulai
            </label>
            <div class="relative">
              <div
                class="h-11 p-2.5 border border-gray-300 text-gray-600 rounded-md bg-white w-full flex items-center shadow-sm"
              >
                {{ datePicker && datePicker.start ? formatDate(datePicker.start) : '-' }}
              </div>
              <div
                class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none"
              >
                <font-awesome-icon icon="calendar" class="text-teal-500" />
              </div>
            </div>
          </div>

          <div class="flex flex-col w-full">
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Tanggal Selesai
            </label>
            <div class="relative">
              <div
                class="h-11 p-2.5 border border-gray-300 text-gray-600 rounded-md bg-white w-full flex items-center shadow-sm"
              >
                {{ datePicker && datePicker.end ? formatDate(datePicker.end) : '-' }}
              </div>
              <div
                class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none"
              >
                <font-awesome-icon icon="calendar" class="text-teal-500" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Actions Area -->
    <div class="p-5 border-t border-gray-100 flex justify-end items-center gap-3 bg-gray-50/50">
      <button
        class="py-2.5 px-6 border border-gray-300 rounded-md text-gray-600 hover:bg-gray-100 transition-all font-semibold"
        @click="close"
      >
        Batal
      </button>
      <button
        class="py-2.5 px-6 rounded-md text-white bg-teal-500 hover:bg-teal-600 font-semibold shadow-md shadow-teal-500/20 transition-all"
        @click="submit"
      >
        Terapkan
      </button>
    </div>
  </modal-app>
</template>

<script>
import ModalApp from "@/core/components/Modal.vue";
import { DatePicker } from "v-calendar";
import "v-calendar/dist/style.css";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import moment from "moment";

export default {
  name: "ModalDatePickerMobile",
  components: { DatePicker, ModalApp, FontAwesomeIcon },
  props: {
    show: {
      type: Boolean,
      default: false,
    },
    date: {
      type: Object,
      default: () => ({
        start: new Date(moment().startOf("months").format()),
        end: new Date(moment().endOf("months").format()),
      }),
    },
  },
  data() {
    return {
      datePicker: {
        start: new Date(moment().startOf("months").format()),
        end: new Date(moment().endOf("months").format()),
      },
    };
  },
  computed: {
    internalShow: {
      get() {
        return this.show;
      },
      set(val) {
        if (!val) {
          this.close();
        }
      }
    }
  },
  watch: {
    date: {
      handler(newVal) {
        if (newVal) {
          this.datePicker = { ...newVal };
        }
      },
      deep: true,
      immediate: true
    }
  },
  methods: {
    formatDate(date) {
      if (!date) return "-";
      return moment(date).format("DD/MM/YYYY");
    },
    close() {
      this.$emit("close");
      if (this.date) {
        this.datePicker = { ...this.date };
      }
    },
    submit() {
      if (!this.datePicker) return;
      this.$emit("submit", this.datePicker);
    },
  },
};
</script>

<style scoped>
.custom-calendar-picker {
  border: none !important;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
}
</style>
