<template>
  <button
    class="flex items-center justify-center transition focus:outline-none"
    :class="[cursor, sizeButton, buttonColor, customClass]"
    @click="click($event)"
    :disabled="disabled"
    type="button"
  >
    <div v-if="$slots['icon-left']" class="mr-2 flex items-center">
      <slot name="icon-left"></slot>
    </div>

    <slot></slot>

    <div v-if="$slots['icon-right']" class="ml-2 flex items-center">
      <slot name="icon-right"></slot>
    </div>
  </button>
</template>

<script>
/**
 * Contoh Penggunaan:
 * <Button
 * type="primary"
 * color="primary"
 * @click="saveData"
 * >
 * Simpan Data
 * </Button>
 */

export default {
  name: "ButtonApp",

  props: {
    size: {
      // small, normal, large
      type: String,
      default: "normal",
    },

    type: {
      // primary, secondary, tertiary
      type: String,
      default: "primary",
    },

    color: {
      // menggunakan nama warna yang ada di tailwind.config.js (e.g., 'primary', 'error', 'teal')
      type: String,
      default: "primary",
    },

    isDark: {
      // jika primary & isDark false, teks menjadi abu-abu
      type: Boolean,
      default: true,
    },

    disabled: {
      type: Boolean,
      default: false,
    },

    icon: {
      // jika true, padding x & y akan sama (simetris untuk icon saja)
      type: Boolean,
      default: false,
    },

    rounded: {
      type: Boolean,
      default: false,
    },

    expanded: {
      // jika true -> w-full
      type: Boolean,
      default: false,
    },

    flat: {
      // jika true -> tombol tidak memiliki shadow
      type: Boolean,
      default: false,
    },

    customClass: {
      type: String,
      default: "",
    },
  },

  computed: {
    cursor() {
      return this.disabled ? "cursor-not-allowed opacity-60" : "cursor-pointer";
    },

    sizeButton() {
      const roundedClass = this.rounded ? "rounded-full" : "";
      const expandedClass = this.expanded ? "w-full" : "";

      switch (this.size) {
        case "small":
          return `${
            this.icon
              ? "font-semibold text-base w-10 h-10"
              : "font-medium text-sm py-2 px-4"
          } ${this.rounded ? "rounded-full" : "rounded-md"} ${expandedClass}`;
        case "large":
          return `font-bold ${
            this.icon ? "text-2xl w-16 h-16" : "text-xl py-4 px-8"
          } ${this.rounded ? "rounded-full" : "rounded-xl"} ${expandedClass}`;
        default:
          return `text-base font-semibold ${
            this.icon ? "text-lg w-12 h-12" : "text-base py-3 px-6"
          } ${this.rounded ? "rounded-full" : "rounded-lg"} ${expandedClass}`;
      }
    },

    textColor() {
      switch (this.type) {
        case "primary":
          return this.isDark ? "text-white" : "text-gray-600";
        case "secondary":
        case "tertiary":
          return this.disabled
            ? `text-${this.color}-300`
            : `text-${this.color} hover:text-${this.color}-dark`;
        default:
          return this.isDark ? "text-white" : "text-gray-600";
      }
    },

    buttonColor() {
      const borderSize = this.size === "large" ? "border-2" : "border";

      switch (this.type) {
        case "secondary":
          return this.disabled
            ? `bg-white ${this.textColor} ${borderSize} border-${this.color}-200`
            : `bg-white hover:bg-${this.color}-50 ${this.textColor} ${borderSize} border-${this.color} ${this.shadowButton}`;
        case "tertiary":
          return this.disabled
            ? `bg-gray-50 ${this.textColor}`
            : `bg-transparent hover:bg-${this.color}-50 ${this.textColor} ${this.shadowButton}`;
        default: // Primary
          return this.disabled
            ? `bg-${this.color}-300 ${this.textColor}`
            : `bg-${this.color} hover:bg-${this.color}-dark ${this.textColor} ${this.shadowButton}`;
      }
    },

    shadowButton() {
      if (this.flat) return "";
      return `shadow-sm hover:shadow-md active:shadow-none`;
    },
  },

  methods: {
    click(event) {
      if (!this.disabled) {
        this.$emit("click", event);
      }
    },
  },
};
</script>