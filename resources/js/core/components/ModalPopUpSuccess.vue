<template>
  <modal-app
    :model-value="modelValue"
    size="small"
    :click-outside="false"
    @update:modelValue="$emit('update:modelValue', $event)"
  >
    <div class="p-6 text-center">
      <!-- Static Success Icon -->
      <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-teal-100 mb-4">
        <svg class="h-10 w-10 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
      </div>

      <!-- Dynamic Title -->
      <h3 class="text-xl font-bold text-gray-900 mb-2">{{ title }}</h3>
      
      <!-- Dynamic Description -->
      <p class="text-sm text-gray-500 mb-6 px-4">
        {{ description }}
      </p>

      <!-- Action Button -->
      <div class="flex justify-center">
        <button
          @click="onClose"
          class="px-8 py-2.5 rounded-xl bg-teal-400 hover:bg-teal-500 text-white text-sm font-semibold shadow-md transition-all duration-200 min-w-[120px]"
        >
          {{ buttonText }}
        </button>
      </div>
    </div>
  </modal-app>
</template>

<script>
import ModalApp from "./Modal.vue";

export default {
  name: "ModalPopUpSuccess",
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
      default: "Berhasil!",
    },
    description: {
      type: String,
      default: "Data telah berhasil diproses.",
    },
    buttonText: {
      type: String,
      default: "Oke",
    },
  },
  emits: ["update:modelValue", "close-action"],
  methods: {
    onClose() {
      this.$emit("close-action");
      this.$emit("update:modelValue", false);
    },
  },
};
</script>
