<template>
  <div
    class="fixed z-50 top-0 right-0 bottom-0 left-0 modal flex items-center"
    v-show="value"
    @click.self="handleClickOutside"
  >
    <div
      class="rounded-md bg-white mx-auto flex flex-col items-stretch modal-width max-h-screen overflow-y-auto"
      :class="{
        'w-3/4': size === 'large',
        'w-2/4': size === 'medium',
        'w-2/6': size === 'small',
        'w-1/6': size === 'micro',
      }"
      :style="`max-width: ${maxWidth}; min-height: ${minHeight}`"
    >
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: "ModalApp",
  props: {
    value: Boolean,
    maxWidth: {
      type: String,
      default: "1140px",
    },
    minHeight: String,
    size: {
      type: String,
      default: "large",
    },
    clickOutside: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    handleClickOutside() {
      this.$emit("close", false);
    },
  },
};
</script>
<style scoped>
@media screen and (max-width: 641px) {
  .modal-width {
    width: 90% !important;
  }
}

.z-50 {
  z-index: 50;
}
</style>
