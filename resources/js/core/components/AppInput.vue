<template>
  <div class="flex flex-col">
    <label
      class="text-sm font-semibold text-gray-700 mb-1"
      :for="$attrs.id"
      v-show="label && label.length > 0"
    >
      <span
        v-if="required && !markRequiredRight && !disabled"
        class="text-error"
        >*</span
      >
      {{ label }}
      <span v-if="required && markRequiredRight && !disabled" class="text-error"
        >*</span
      >
    </label>

    <div
      class="rounded-md flex gap-1 px-2 relative transition-all duration-200"
      :class="{
        'border border-red-500 bg-red-50': error,
        'border border-teal-400 bg-white hover:border-teal-500':
          !error && !disabled && !muted,

        'hover:border-teal-500 focus-within:border-teal-500':
          error && !disabled,

        'border-0 h-11 bg-white-100 text-white-300': disabled,
        'border-secondary shadow-secondary-sm bg-white': isFocus && !error,
        'mb-1': error || $slots['hint'],
        'border border-white-200 bg-white-100': muted && !disabled,
      }"
    >
      <div v-if="$slots['icon-left']" class="flex items-center pl-1">
        <slot name="icon-left"></slot>
      </div>

      <input
        v-show="isFocus"
        ref="inputRef"
        :type="type"
        :value="modelValue"
        @input="handleInput"
        @blur="handleBlur"
        @focus="handleFocus"
        class="w-full h-11 py-3 text-gray-600 focus:outline-none text-sm bg-transparent pl-2"
        :class="{ 'text-red-600': error }"
        :placeholder="placeholder"
        :disabled="disabled"
      />

      <input
        v-show="!isFocus"
        readonly
        :type="type"
        :value="computedValue"
        class="w-full h-11 py-3 text-gray-500 focus:outline-none text-sm bg-transparent cursor-pointer pl-2"
        :class="{ 'text-red-600': error }"
        :placeholder="placeholder"
        @click="handleClick"
        :disabled="disabled || disabledTyping"
      />

      <div v-if="$slots['icon-right']" class="flex items-center pr-1">
        <slot name="icon-right"></slot>
      </div>
    </div>

    <div v-if="$slots['hint']" class="text-xs text-gray-400 mt-1">
      <slot name="hint"></slot>
    </div>

    <div class="text-xs text-red-500 mt-1 font-medium" v-if="error">
      <slot name="error-message"></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: "AppInput",
  props: {
    modelValue: {
      // Vue 3 menggunakan modelValue untuk v-model
      type: [String, Number],
      required: true,
      default: "",
    },
    hyperlink: {
      type: Boolean,
      default: false,
    },
    markRequiredRight: {
      type: Boolean,
      default: false,
      required: false,
    },
    label: {
      type: String,
      default: "",
    },
    error: Boolean,
    separator: Boolean,
    allowDecimal: {
      type: Boolean,
      required: false,
      default: false,
    },
    placeholder: {
      type: String,
      default: "",
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    disabledTyping: {
      type: Boolean,
      default: false,
    },
    type: {
      type: String,
      default: "text",
    },
    required: {
      type: Boolean,
      default: false,
    },
    name: {
      type: String,
      default: "input",
    },
    maxChar: {
      type: Number,
      default: 255,
    },
    maxNum: {
      type: Number,
      default: 0,
    },
    muted: {
      type: Boolean,
      default: false,
    },
    prefix: {
      type: String,
      default: "",
    },
    textRight: {
      type: Boolean,
      default: false,
    },
  },
  emits: [
    "update:modelValue",
    "input",
    "blur",
    "focus",
    "keypress",
    "keydown",
    "paste",
    "handleHyperlink",
    "clear-error",
    "click",
  ],
  data() {
    return {
      isFocus: false,
    };
  },
  watch: {
    modelValue(newVal) {
      if (newVal && this.error) {
        this.$emit("clear-error");
      }
    },
  },
  methods: {
    handleInput(e) {
      if (this.isNumberTypeAndValid(e) && this.isExceedingMaxNum(e)) {
        e.preventDefault();
        return false;
      }

      if (this.isExceedingMaxChar(e) && this.maxChar) {
        e.preventDefault();
        return false;
      }
      // Emit update:modelValue untuk v-model Vue 3
      this.$emit("update:modelValue", e.target.value);
      this.$emit("input", e.target.value);
    },
    handleBlur(e) {
      this.isFocus = false;
      this.$emit("blur", e.target.value);
    },
    handleFocus(e) {
      this.isFocus = true;
      this.$emit("focus", e.target.value);
    },
    handleKeyPress(e) {
      const charCode = typeof e.which === "undefined" ? e.keyCode : e.which;
      const char = String.fromCharCode(charCode);
      if (this.type === "number") {
        if (this.allowDecimal) {
          if (!/^([0-9,]+)$/.test(char)) {
            e.preventDefault();
            return false;
          }
        } else if (!/^(\d+)$/.test(char)) {
          e.preventDefault();
          return false;
        }
      }
      this.$emit("keypress", e.target.value);
    },
    handleClick(e) {
      if (this.disabled) return;
      this.$emit("click", e);
      this.isFocus = true;
      // Menggunakan nextTick untuk memastikan ref sudah ada
      this.$nextTick(() => {
        if (this.$refs.inputRef) {
          this.$refs.inputRef.focus();
        }
      });
    },
    handleOnPaste(e) {
      let pastedData = e.clipboardData || window.clipboardData;
      if (this.type === "number") {
        this.handleOnPasteNumber(e, pastedData);
      } else {
        this.handleOnPasteString(e, pastedData);
      }
    },
    handleOnPasteString(e, pastedData) {
      const text = (this.modelValue || "") + pastedData.getData("text");
      if (text.length > this.maxChar) {
        e.preventDefault();
        const trimmedText = text.substring(0, this.maxChar);
        // Kita manipulasi value manual jika paste melebihi limit
        this.$emit("update:modelValue", trimmedText);
        this.$emit("input", trimmedText);
        this.$emit("paste", trimmedText);
      } else {
        this.$emit("paste", pastedData);
      }
    },
    handleOnPasteNumber(e, pastedData) {
      const text = pastedData.getData("text");
      let u = text.replace(".", "");
      if (u && u.length > this.maxChar) {
        u = u.substring(0, this.maxChar);
      }
      const v = parseInt(u);
      if (
        text.includes(".") ||
        (!this.allowDecimal && text.includes(",")) ||
        !/^([0-9,]+)$/.test(v) ||
        isNaN(v)
      ) {
        e.preventDefault();
        return false;
      } else {
        e.preventDefault();
        this.$emit("update:modelValue", u);
        this.$emit("input", u);
        this.$emit("paste", u);
      }
    },
    handleKeyDown(e) {
      const charCode = String(
        typeof e.which === "undefined" ? e.keyCode : e.which
      );
      const isAllowedKey = this.isAllowedKeyCode(charCode);

      if (this.isNumberTypeAndValid(e) && this.isExceedingMaxNum(e)) {
        if (!isAllowedKey) {
          e.preventDefault();
          return false;
        }
      }

      if (this.isExceedingMaxChar(e) && !isAllowedKey) {
        e.preventDefault();
        return false;
      }

      this.$emit("keydown", e.target.value);
    },
    isAllowedKeyCode(charCode) {
      const allowedKeyCodes = ["46", "8", "13", "37", "38", "39", "40"];
      return allowedKeyCodes.includes(charCode);
    },
    isNumberTypeAndValid(e) {
      return this.type === "number" && /^([0-9,]+)$/.test(e.key);
    },
    isExceedingMaxNum(e) {
      return this.maxNum > 0 && +(e.target.value + e.key) > this.maxNum;
    },
    isExceedingMaxChar(e) {
      return e.target.value.length > this.maxChar;
    },
    handleKeyUp() {
      const maxStrSize = "" + this.maxNum;
      // Gunakan this.modelValue
      const v = parseInt(("" + this.modelValue).replace(".", ""));
      if (this.type === "number") {
        if (v > this.maxNum && this.maxNum > 0) {
          this.$emit(
            "update:modelValue",
            ("" + v).substring(0, maxStrSize.length - 1)
          );
        }
      }
    },
    formatDecimalID(val) {
      let res = null;
      if (typeof val === "string") {
        res = val.replace(".", "");
        return res.replace(",", ".");
      } else {
        res = val;
        return res;
      }
    },
  },
  computed: {
    computedValue() {
      if (this.separator) {
        // Gunakan this.modelValue
        const v = this.formatDecimalID(this.modelValue);
        // Cek jika v valid number
        if (!v && v !== 0) return this.modelValue;

        try {
          let formated = new Intl.NumberFormat("in-ID", {
            maximumFractionDigits: 2,
          }).format(v);
          if (this.prefix) {
            formated = this.prefix + formated;
          }
          return formated;
        } catch (e) {
          return this.modelValue;
        }
      } else {
        return this.modelValue;
      }
    },
  },
};
</script>

<style scoped>
.label-input {
  color: #0f172a;
}

input:disabled {
  color: #1b253b;
  opacity: 1 !important;
}

input {
  opacity: 1 !important;
}

.hyperlink-text {
  /* -webkit-text-fill-color: #0b64fe; */
  opacity: 1;
  /* color: #0b64fe !important; */
  font-style: normal;
  font-weight: 500;
  text-decoration: underline;
  cursor: pointer;
}

::placeholder {
  font-size: 14px;
  color: #94a3b8;
}

[type="number"]:focus,
[type="text"]:focus,
input:focus-visible {
  border: 0 none transparent;
  outline: none;
}

.overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  color: rgba(255, 255, 255, 0);
  background: rgba(255, 255, 255, 0);
  cursor: pointer;
  pointer-events: all;
  z-index: 10;
}
</style>