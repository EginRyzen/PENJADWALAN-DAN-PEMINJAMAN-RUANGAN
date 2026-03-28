<template>
  <div>
    <div class="grid grid-cols-2 gap-2 w-64">
      <VueScrollPicker
        :options="hourList"
        v-model="hourValue"
        @update:modelValue="(e) => onInput('hour', e)"
        ref="hourRef"
        :id="`hourPicker-${id}`"
      ></VueScrollPicker>
      <VueScrollPicker
        :options="minuteList"
        v-model="minuteValue"
        @update:modelValue="(e) => onInput('minute', e)"
        ref="minuteRef"
        :id="`minutePicker-${id}`"
      ></VueScrollPicker>
    </div>
    <p v-show="showError" class="text-xs text-error text-center">
      {{ error }}
    </p>
  </div>
</template>

<script>
import { VueScrollPicker } from "vue-scroll-picker";
import "vue-scroll-picker/style.css";
import { generateUUID } from "../plugins/constants/utils";

export default {
  name: "TimePickerScroll",
  components: {
    VueScrollPicker,
  },
  props: {
    // 09:00
    modelValue: {
      type: String,
      default: ""
    },
    disabledHours: {
      type: Array,
      default: () => [],
    },
    disabledMinutes: {
      type: Array,
      default: () => [],
    },
    error: {
      type: String,
      default: "",
    },
    id: {
      type: String,
      default: generateUUID(),
    },
  },
  emits: ["update:modelValue"],
  data() {
    return {
      hourValue: null,
      minuteValue: null,
      showError: false,
      direction: "bottom", // top / bottom
    };
  },
  watch: {
    modelValue: {
      immediate: true,
      handler(val) {
        if (val && val.includes(":")) {
          const [h, m] = val.split(":");
          this.hourValue = h;
          this.minuteValue = m;
        }
      }
    }
  },
  computed: {
    hourList() {
      let arr = [];
      let endHour = 24;
      for (let i = 0; i < endHour; i++) {
        arr.push(String(i).padStart(2, "0"));
      }
      return arr.map((hour) => {
        return {
          value: hour,
          name: hour,
          disabled: this.disabledMinutes.includes("59")
            ? this.disabledHours.length - 1 >= hour
            : this.disabledHours.length - 1 > hour,
        };
      });
    },
    minuteList() {
      let arr = [];
      let endMinutes = 60;
      for (let i = 0; i < endMinutes; i++) {
        arr.push(String(i).padStart(2, "0"));
      }
      return arr.map((minute) => ({
        value: minute,
        name: minute,
        disabled:
          this.disabledMinutes.length - 1 < 59 &&
          this.hourValue <= this.disabledHours.length - 1
            ? this.disabledMinutes.length - 1 >= minute
            : false,
      }));
    },
    disabledTime() {
      if (!this.disabledHours.length || !this.disabledMinutes.length)
        return null;
      const hour = this.disabledHours.length - 1;
      const minute = this.disabledMinutes.length - 1;
      let disabledTime = new Date();
      disabledTime.setHours(hour, minute, 0, 0);
      return disabledTime;
    },
    computedValue() {
      const hour = parseInt(this.hourValue);
      const minute = parseInt(this.minuteValue);
      if (!this.hourValue || !this.minuteValue || isNaN(hour) || isNaN(minute))
        return null;
      let selectedTime = new Date();
      selectedTime.setHours(hour, minute, 0, 0);
      return selectedTime;
    },
  },
  async mounted() {
    this.listenerToSetDirection(`hourPicker-${this.id}`);
    this.listenerToSetDirection(`minutePicker-${this.id}`);
    if (this.disabledHours.length) {
      const availableHour = this.searchNearestAvailableOption(
        this.hourValue,
        this.hourList
      );
      this.setHourMinute(availableHour, "hour");
    }
    if (this.disabledMinutes.length) {
      const availableMinute = this.searchNearestAvailableOption(
        this.minuteValue,
        this.minuteList
      );
      this.setHourMinute(availableMinute, "minute");
    }
  },
  methods: {
    listenerToSetDirection(elementId) {
      // inspect the DOM for better understanding
      const el = document.getElementById(elementId);
      if (!el) return;
      const pickerLayers = el.children.item(1).children;
      for (const pickerLayer of pickerLayers) {
        if (
          pickerLayer.className === "top" ||
          pickerLayer.className === "bottom"
        )
          pickerLayer.addEventListener("click", this.setDirection);
      }
    },
    setDirection(e) {
      this.direction = e.target.className; // top / bottom
    },
    setHourMinute(availableResult, key) {
      if (availableResult && key === "hour") {
        this.hourValue = availableResult.value;
      } else if (availableResult && key === "minute") {
        this.minuteValue = availableResult.value;
      }
    },
    async onInput(key, payload) {
      let keyObj = this[`${key}List`].find((item) => item.value === payload);
      if (keyObj && keyObj.disabled) {
        this.showError = true;
        this.$emit("update:modelValue", "");
      } else {
        this.showError =
          this.computedValue?.getTime() <= this.disabledTime?.getTime();

        if (this.showError) this.$emit("update:modelValue", "");
        else this.$emit("update:modelValue", `${this.hourValue || '00'}:${this.minuteValue || '00'}`);
      }
    },
    searchNearestAvailableOption(currentOption, options) {
      let result = null;
      let minimumIndex = null;
      let maximumIndex = null;
      let stepToMinimum = null;
      let stepToMaximum = null;
      let currentOptionIndex = options.findIndex(
        (option) => option.value === currentOption
      );

      for (let minIndex = currentOptionIndex - 1; minIndex > -1; minIndex--) {
        if (!options[minIndex].disabled) {
          minimumIndex = minIndex;
          break;
        }
      }

      for (
        let maxIndex = currentOptionIndex + 1;
        maxIndex < options.length;
        maxIndex++
      ) {
        if (!options[maxIndex].disabled) {
          maximumIndex = maxIndex;
          break;
        }
      }

      if (minimumIndex === null && maximumIndex === null) {
        return null;
      }

      if (minimumIndex === null || stepToMinimum > stepToMaximum) {
        result = options[maximumIndex];
      } else if (maximumIndex === null || stepToMinimum < stepToMaximum) {
        result = options[minimumIndex];
      } else {
        result =
          this.direction === "bottom"
            ? options[maximumIndex]
            : options[minimumIndex];
      }

      return result;
    },
  },
};
</script>

<style scoped>
>>> .vue-scroll-picker-item {
  height: 48px;
  line-height: 48px;
  font-size: 20px;
}
>>> .vue-scroll-picker-item.-selected {
  height: 48px;
  line-height: 58px;
  font-weight: bold;
  font-size: 36px;
  color: #46bebb;
  background-color: #f0fdfa;
  border-radius: 8px;
}
>>> .vue-scroll-picker-layer .top {
  border-bottom: none;
}
>>> .vue-scroll-picker-layer .bottom {
  border-top: none;
}
</style>
