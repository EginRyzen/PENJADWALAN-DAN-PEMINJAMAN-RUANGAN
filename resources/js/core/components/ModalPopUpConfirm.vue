<template>
  <modal-app
    :model-value="modelValue"
    size="small"
    :click-outside="false"
    @update:modelValue="$emit('update:modelValue', $event)"
  >
    <div class="p-6 text-center">
      <!-- Static Warning Icon -->
      <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
        <svg class="h-10 w-10 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 14c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>

      <!-- Dynamic Title -->
      <h3 class="text-xl font-bold text-gray-900 mb-2">{{ title }}</h3>
      
      <!-- Dynamic Description -->
      <p class="text-sm text-gray-500 mb-6 px-4">
        {{ description }}
      </p>

      <!-- Action Buttons -->
      <div class="flex justify-center gap-3">
        <button
          @click="onCancel"
          class="px-6 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-semibold shadow-md transition-all duration-200 min-w-[100px]"
        >
          Batal
        </button>
        <button
          @click="onConfirm"
          class="px-6 py-2.5 rounded-xl bg-teal-400 hover:bg-teal-500 text-white text-sm font-semibold shadow-md transition-all duration-200 min-w-[100px]"
        >
          Ya
        </button>
      </div>
    </div>
  </modal-app>
</template>

<script>
import ModalApp from "./Modal.vue";

export default {
  name: "ModalPopUpConfirm",
  components: {
    ModalApp,
  },
  props: {
    modelValue: {
      type: Boolean,
      default: false,
    },
    title: {
      type: String,
      default: "Apakah Anda yakin?",
    },
    description: {
      type: String,
      default: "Data yang dihapus tidak dapat dikembalikan.",
    },
  },
  emits: ["update:modelValue", "confirm", "cancel"],
  methods: {
    onConfirm() {
      this.$emit("confirm");
      this.close();
    },
    onCancel() {
      this.$emit("cancel");
      this.close();
    },
    close() {
      this.$emit("update:modelValue", false);
    },
  },
};
</script>
