<template>
  <ModalBima v-model="showModel" max-width="550px">
    <div class="p-6 text-normal text-black font-semibold">
      <span>Filter Rentang Waktu</span>
    </div>
    <hr />
    <div class="p-6">
      <div class="flex justify-center items-center flex-col gap-6">
        <div class="flex justify-center items-center gap-6 w-full">
          <div class="flex flex-col w-full">
            <label class="block text-sm text-gray md:text-left mb-1 md:mb-0 pr-4">
              Tanggal Mulai
            </label>
            <div class="relative">
              <div class="h-11 p-2.5 border text-gray rounded-lg placeholder-gray-light focus:outline-none focus:border-primary w-full pr-10 border-primary">
                {{ formatDate(datePicker?.start) }}
              </div>
              <div class="absolute icon-calendar inset-y-0 flex items-center right-0 pr-4 font-semibold" />
            </div>
          </div>
          <div class="flex flex-col w-full">
            <label class="block text-sm text-gray md:text-left mb-1 md:mb-0 pr-4">
              Tanggal Selesai
            </label>
            <div class="relative">
              <div class="h-11 p-2.5 border text-gray rounded-lg placeholder-gray-light focus:outline-none focus:border-primary w-full pr-10 border-primary">
                {{ formatDate(datePicker?.end) }}
              </div>
              <div class="absolute icon-calendar inset-y-0 flex items-center right-0 pr-4 font-semibold" />
            </div>
          </div>
        </div>
        <DatePicker
          v-model.range="datePicker"
          class="custom-calendar-picker"
          :columns="2"
          color="teal"
          locale="id"
          :first-day-of-week="2"
          trim-weeks
        />
      </div>
    </div>
    <hr />
    <div class="p-6">
      <div class="flex justify-end items-center gap-3">
        <button
          class="py-3 px-4 border border-primary rounded-md text-primary"
          @click="close"
        >
          <span class="w-20 inline-block">Batal</span>
        </button>
        <button
          class="py-3 px-8 rounded-md text-white btn-submit-modal"
          @click="submit"
        >
          Terapkan
        </button>
      </div>
    </div>
  </ModalBima>
</template>

<script>
import ModalBima from "@/core/components/Modal.vue";
import { DatePicker } from "v-calendar";
import "v-calendar/dist/style.css";
import moment from "moment";

export default {
  name: "ModalDatePicker",
  components: { DatePicker, ModalBima },
  props: {
    show: {
      type: Boolean,
      default: false,
    },
    date: {
      type: Object,
      default: () => ({
        start: new Date(moment().startOf("month").format()),
        end: new Date(moment().endOf("month").format()),
      }),
    },
  },
  emits: ["update:show", "close", "submit"],
  computed: {
    showModel: {
      get() {
        return this.show;
      },
      set(val) {
        this.$emit("update:show", val);
      }
    }
  },
  data() {
    return {
      datePicker: {
        start: new Date(moment().startOf("month").format()),
        end: new Date(moment().endOf("month").format()),
      },
    };
  },
  watch: {
    date: {
      handler(newVal) {
        if (newVal) {
          this.datePicker = { 
            start: newVal.start ? new Date(newVal.start) : new Date(),
            end: newVal.end ? new Date(newVal.end) : new Date()
          };
        }
      },
      deep: true,
      immediate: true
    }
  },
  methods: {
    formatDate(date) {
      if (!date) return "";
      return moment(date).format("DD/MM/YYYY");
    },
    close() {
      this.showModel = false;
      this.$emit("close");
      this.datePicker = { ...this.date };
    },
    submit() {
      if (!this.datePicker) return;
      this.$emit("submit", this.datePicker);
      this.showModel = false;
    },
  },
};
</script>

<style scoped>
.btn-submit-modal {
  background: linear-gradient(135deg, #797ef6 0%, #3dc6d1 45.31%, #30d5c9 100%);
}
</style>
