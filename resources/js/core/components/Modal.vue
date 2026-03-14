<template>
  <div
    v-if="modelValue"
    class="fixed inset-0 flex items-center justify-center p-4"
    style="z-index: 9999; background-color: rgba(0,0,0,0.5);"
    @click.self="handleClickOutside"
  >
    <!-- Modal Card -->
    <div
      class="relative bg-white rounded-2xl shadow-xl overflow-y-auto"
      style="max-height: 90vh;"
      :class="{
        'w-3/4': size === 'large',
        'w-2/4': size === 'medium',
        'w-2/6': size === 'small',
        'w-1/6': size === 'micro',
      }"
      :style="`max-width: ${maxWidth};`"
      @click.stop
    >
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: "ModalApp",
  props: {
    modelValue: {
      type: Boolean,
      default: false,
    },
    maxWidth: {
      type: String,
      default: "600px",
    },
    minHeight: {
      type: String,
      default: "",
    },
    size: {
      type: String,
      default: "medium",
    },
    clickOutside: {
      type: Boolean,
      default: true,
    },
  },
  emits: ["update:modelValue", "close"],
  methods: {
    handleClickOutside() {
      if (this.clickOutside) {
        this.$emit("update:modelValue", false);
        this.$emit("close", false);
      }
    },
  },
};
</script>

<style scoped>
@media screen and (max-width: 641px) {
  .bg-white {
    width: 90% !important;
  }
}
</style>
