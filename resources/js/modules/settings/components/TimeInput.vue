<template>
  <div class="relative w-full">
    <!-- Display Input -->
    <div
      @click="togglePicker"
      :class="[
        'flex items-center justify-between px-4 py-2 bg-white border-2 rounded-xl cursor-pointer transition-all duration-300',
        modalVisible ? 'border-teal-400 ring-4 ring-teal-50' : 'border-gray-100 hover:border-teal-200',
        disabled ? 'bg-gray-50 text-gray-400 border-gray-100 cursor-not-allowed' : 'text-gray-700'
      ]"
    >
      <span class="text-sm font-medium">{{ displayTime }}</span>
      <svg class="w-4 h-4 text-gray-300 transition-colors" :class="{'text-teal-400': modalVisible}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
    </div>

    <!-- Time Picker Modal (Matching PeminjamanRuanganCreate.vue) -->
    <modal-app size="small" v-model="modalVisible">
      <div class="flex flex-col justify-between gap-6 p-8 h-[450px] bg-white rounded-2xl">
        <div class="flex flex-col gap-4">
          <p class="text-center font-bold text-lg text-gray-800">Pilih Waktu</p>
          <div class="w-full mt-4 flex flex-col justify-center items-center gap-4">
            <div class="w-full flex justify-center text-gray-500 font-medium text-sm uppercase tracking-wider">
              <span class="w-32 text-center">Jam</span>
              <span class="w-32 text-center">Menit</span>
            </div>
            <TimePickerScroll
              id="settings-time-picker"
              v-model="tempTime"
            />
          </div>
        </div>
        <div class="w-full flex justify-end gap-3 border-t pt-6 border-gray-100">
          <button-app
            color="teal"
            type="secondary"
            class="px-6 py-2 border-teal-400 text-teal-600 font-bold"
            @click="modalVisible = false"
          >
            Tutup
          </button-app>
          <button-app
            color="teal"
            class="px-8 py-2 bg-teal-500 hover:bg-teal-600 text-white font-bold shadow-md shadow-teal-500/20"
            @click="handleSelect"
          >
            Pilih
          </button-app>
        </div>
      </div>
    </modal-app>
  </div>
</template>

<script>
import ModalApp from "@/core/components/Modal.vue";
import ButtonApp from "@/core/components/Button.vue";
import TimePickerScroll from "@/core/components/TimePickerScroll.vue";

export default {
  name: "TimeInputSettings",
  components: {
    ModalApp,
    ButtonApp,
    TimePickerScroll,
  },
  props: {
    modelValue: {
      type: String,
      default: "08:00",
    },
    disabled: {
      type: Boolean,
      default: false,
    },
  },
  emits: ["update:modelValue"],
  data() {
    return {
      modalVisible: false,
      tempTime: "08:00",
    };
  },
  computed: {
    displayTime() {
      if (!this.modelValue) return "--:--";
      return this.modelValue;
    }
  },
  watch: {
    modelValue: {
      immediate: true,
      handler(val) {
        if (!this.modalVisible && val) {
          this.tempTime = val;
        }
      }
    }
  },
  methods: {
    togglePicker() {
      if (this.disabled) return;
      this.tempTime = this.modelValue || "08:00";
      this.modalVisible = true;
    },
    handleSelect() {
      this.$emit("update:modelValue", this.tempTime);
      this.modalVisible = false;
    }
  }
};
</script>

<style scoped>
/* No CSS needed as we rely perfectly on the global TimePickerScroll component styling */
</style>
