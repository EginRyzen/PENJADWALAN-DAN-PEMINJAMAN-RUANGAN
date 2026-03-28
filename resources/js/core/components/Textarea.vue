<template>
  <div class="flex flex-col">
    <label class="text-sm label-input mb-1" :for="id" v-if="label">
      <span
        v-if="required && !markRequiredRight && !disabled"
        class="text-error"
        >*</span
      >
      {{ label
      }}<span
        v-if="required && markRequiredRight && !disabled"
        class="text-error"
        >*</span
      >
    </label>
    <div
      class="rounded-md relative"
      :class="{
        'border border-teal-400 bg-white transition-all hover:border-teal-500': !disabled && !muted && !isFocused && !showError,
        'border-0 bg-gray-100 text-gray-400': disabled,
        'border border-teal-500 ring-1 ring-teal-100': isFocused && !showError,
        'mb-1': showError || $slots['hint'],
        'border border-red-500 bg-red-50 hover:border-red-600': showError,
        'border border-gray-200 bg-gray-50': muted && !disabled && !showError,
      }"
    >
      <textarea
        ref="textarea"
        :id="id"
        :disabled="disabled || disabledTyping"
        :value="modelValue !== undefined ? modelValue : value"
        :placeholder="placeholder"
        @input="handleInput"
        @blur="handleBlur"
        @focus="handleFocus"
        class="rounded-md w-full py-3 px-4 focus:ring-0 text-sm text-black-200 focus:border-0 focus:shadow-none focus:outline-none"
        :class="{
          'bg-white': !muted && !disabled,
          'bg-white-100': muted || disabled,
          'resize-none': !resizeable || autoHeight,
        }"
        :maxlength="maxChar"
        :rows="rows"
      ></textarea>
    </div>
    <div v-if="$slots['hint']" class="text-xs text-black-100 mb-1">
      <slot name="hint"></slot>
    </div>
    <div class="text-xs text-error">
      <slot name="error-message"></slot>
    </div>
    <div>
      <slot name="message"></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: "TextareaApp",
  props: {
    id: String,
    markRequiredRight: { type: Boolean, default: false },
    label: String,
    error: Boolean,
    errorMessage: {
      type: String,
      default: "Alasan Pembelian Barang wajib diisi.",
    },
    value: { type: String, default: "" },
    modelValue: { type: [String, Number] },
    placeholder: { type: String, default: "" },
    disabled: { type: Boolean, default: false },
    disabledTyping: { type: Boolean, default: false },
    maxChar: { type: Number, default: 255 },
    rows: { type: Number, default: 2 },
    required: { type: Boolean, default: false },
    resizeable: { type: Boolean, default: true },
    muted: { type: Boolean, default: false },
    autoHeight: { type: Boolean, default: false },
  },
  data() {
    return {
      isFocused: false,
      localError: false,
    };
  },
  computed: {
    showError() {
      return this.error || this.localError;
    },
  },
  mounted() {
    if (this.autoHeight) {
      this.adjustHeight();
    }
  },
  methods: {
    handleInput(e) {
      this.$emit("input", e.target.value);
      this.$emit("update:modelValue", e.target.value);
      if (this.required && e.target.value.trim() === "") {
        this.localError = true;
      } else {
        this.localError = false;
      }
      if (this.autoHeight) {
        this.adjustHeight();
      }
    },
    handleBlur(e) {
      this.isFocused = false;
      this.$emit("blur", e.target.value);
      if (this.required && e.target.value.trim() === "") {
        this.localError = true;
      }
    },
    handleFocus(e) {
      this.isFocused = true;
      this.$emit("focus", e.target.value);
    },
    adjustHeight() {
      const el = this.$refs.textarea;
      if (el) {
        el.style.height = "auto"; // reset dulu
        el.style.height = el.scrollHeight + "px";
      }
    },
  },
};
</script>

<style scoped>
.label-input {
  color: black;
}
::placeholder {
  font-size: 14px;
  color: #94a3b8;
}
::-webkit-resizer {
  display: none;
}
textarea {
  border: none;
  outline: none;
  opacity: 1 !important; /* required on iOS */
}
textarea:disabled {
  color: #1b253b;
  opacity: 1 !important; /* required on iOS */
}
</style>
