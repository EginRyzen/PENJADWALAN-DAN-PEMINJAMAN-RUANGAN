<template>
  <div :id="$attrs.id" class="flex flex-col">
    <label
      class="flex items-center text-sm text-black-primary"
      :class="{ 'text-error': error }"
    >
      <span class="text-error" v-if="required">*</span>{{ label }}
    </label>
    <div class="relative text-sm w-full">
      <button
        class="flex items-center justify-between pl-2 pr-6 py-2 w-full text-lg border"
        :class="[
          {
            'rounded-md': customClass === '',
            'disabled border-none text-gray-darkest cursor-not-allowed':
              disabled,
            'text-gray-lightest': disabled,
            'bg-white border-teal-400 focus:shadow-primary-lg focus:border-teal-400':
              !disabled && !noBorder,
            'border-error': error,
            'hover:shadow-primary-sm': !noBorder && !disabled,
            'text-base': size !== 'large',
            'h-11': size === 'large',
          },
          customClass,
        ]"
        @click="toggleOptions"
        type="button"
      >
        <div
          class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
          v-if="iconLeft"
        >
          <span :class="iconLeft"></span>
        </div>
        <div
          class="text-sm text-white-300"
          :class="{
            'pl-6': iconLeft,
            'cursor-not-allowed': disabled,
            'text-black-primary':
              (value === '' || value === undefined) && !disabled,
            'text-gray': value !== '',
          }"
        >
          <slot name="selected" :slot-props="selected">
            <span :class="{ 'text-black-primary': !disabled }">{{
              selected || placeholder
            }}</span>
          </slot>
        </div>
        <svg
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          class="h-4 w-4 transform transition-transform duration-200 ease-in-out"
          :class="!readOnly && isOptionsExpanded ? 'rotate-180' : 'rotate-0'"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M19 9l-7 7-7-7"
          />
        </svg>
      </button>
      <transition
        enter-active-class="transform transition duration-500 ease-custom"
        enter-class="-translate-y-1/2 scale-y-0 opacity-0"
        enter-to-class="translate-y-0 scale-y-100 opacity-100"
        leave-active-class="transform transition duration-300 ease-custom"
        leave-class="translate-y-0 scale-y-100 opacity-100"
        leave-to-class="-translate-y-1/2 scale-y-0 opacity-0"
      >
        <ul
          v-show="!readOnly && isOptionsExpanded"
          class="absolute left-0 right-0 mb-4 rounded-md overflow-y-auto bg-white z-50 max-h-48"
          :class="{
            'shadow-primary-sm':
              !disabled && (color === undefined || color === 'primary'),
            'shadow-secondary-sm': !disabled && color === 'secondary',
            'shadow-tertiary-sm': !disabled && color === 'tertiary',
          }"
        >
          <li
            v-for="(v, i) in options"
            :key="i"
            class="px-3 py-2 transition-colors duration-100 text-gray text-regular cursor-pointer"
            :class="{
              'bg-primary-lightest text-primary-darkest':
                typeof v === 'object' ? value === v[itemKey] : value === v,
              'cursor-not-allowed disabled': v.disabled ? v.disabled : false,
              'hover:bg-primary hover:text-white': v.disabled ? false : true,
            }"
            :value="typeof v === 'object' ? v[itemKey] : v"
            :label="label"
            @click="setOption(v)"
          >
            <slot name="option" :slot-props="v">{{
              typeof v === "object" ? v[itemText] : v
            }}</slot>
          </li>
          <li
            v-if="!options.length"
            class="px-3 py-2 transition-colors duration-100 text-gray text-regular cursor-pointer text-center"
          >
            No Data Available
          </li>
        </ul>
      </transition>
      <div class="text-xs text-gray mt-1" v-if="hint && !dense">
        {{ hint }}
      </div>
      <div class="text-xs text-error mt-1">
        <slot name="error-message"></slot>
      </div>
    </div>
  </div>
</template>

<script>
/*
  <select-gista
    v-validate="'required'"
    data-vv-as="Jenis"
    :options="arrJenis"
    v-model="form.jenis"
    label="Jenis*"
    :error="errors.has('jenis')"
  >
    <error-message
      class="relative"
      :class="{ 'text-error': errors.has('jenis') }"
      >{{ errors.first("jenis") }}</error-message
    >
  </select-gista>
*/

export default {
  props: {
    size: {
      // prefer not to use this
      type: String,
      default: "large",
    },
    disabled: Boolean,
    noBorder: {
      // prefer not to use this
      type: Boolean,
      default: false,
    },
    error: {
      // error flag
      type: Boolean,
      default: false,
    },
    dense: {
      // if true then theres no space for hint
      type: Boolean,
      default: false,
    },
    readOnly: {
      type: Boolean,
      default: false,
    },
    label: String,
    iconLeft: String,
    hint: String,
    placeholder: {
      type: String,
      default: "Select",
    },
    options: Array, // option items
    value: {
      // for v-model
      type: [Object, String],
    },
    customClass: {
      type: String,
      default: () => "",
    },
    multiple: {
      type: Boolean,
      default: false,
    },
    color: {
      type: String,
      default: "primary",
    },
    itemKey: {
      type: String,
      default: "value",
    },
    itemText: {
      type: String,
      default: "text",
    },
    modelValue: [Object, String, Number],
    required: Boolean,
  },
  name: "SelectApp",
  data() {
    return {
      isOptionsExpanded: false,
      selected:
        typeof this.modelValue === "object"
          ? this.modelValue[this.itemText]
          : this.modelValue,
      internalValue: [],
    };
  },
  mounted() {
    if (typeof this.value === "object") {
      this.selected = this.value[this.itemText];
      this.internalValue = this.value[this.itemKey];
    } else {
      this.selected = this.value;
      this.internalValue = this.value;
    }
  },
  created() {
    document.addEventListener("click", this.onClickOutside);
  },
  methods: {
    setOption(v) {
      if (v.disabled) return false;
      this.isOptionsExpanded = false;
      this.internalValue = typeof v === "object" ? v[this.itemKey] : v;
      this.selected = typeof v === "object" ? v[this.itemText] : v;
      this.$emit("update:modelValue", this.internalValue);
      this.$emit("input", this.internalValue);
    },
    toggleOptions() {
      if (this.disabled) {
        this.isOptionsExpanded = false;
      } else {
        this.isOptionsExpanded = !this.isOptionsExpanded;
      }
    },
    onClickOutside(e) {
      if (!this.$el.contains(e.target)) {
        this.isOptionsExpanded = false;
      }
    },
    updateSelectedLabel(val) {
      if (val === null || val === undefined || val === "") {
        this.selected = "";
        return;
      }

      if (typeof val === "object") {
        this.selected = val[this.itemText];
        this.internalValue = val[this.itemKey];
      } else {
        const option = this.options.find(
          (opt) => (typeof opt === "object" ? opt[this.itemKey] : opt) === val
        );

        if (option) {
          this.selected =
            typeof option === "object" ? option[this.itemText] : option;
        } else {
          this.selected = val;
        }
        this.internalValue = val;
      }
    },
    setOption(v) {
      if (v.disabled) return false;
      this.isOptionsExpanded = false;

      const val = typeof v === "object" ? v[this.itemKey] : v;
      const text = typeof v === "object" ? v[this.itemText] : v;

      this.internalValue = val;
      this.selected = text;

      this.$emit("update:modelValue", val);
      this.$emit("input", val);
    },
  },
  watch: {
    value(val) {
      this.updateSelectedLabel(val);
    },
    modelValue: {
      handler(val) {
        if (val !== null && val !== undefined) {
          const found = this.options.find(
            (opt) => (typeof opt === "object" ? opt[this.itemKey] : opt) === val
          );
          if (found) {
            this.selected =
              typeof found === "object" ? found[this.itemText] : found;
          } else {
            this.selected = val;
          }
        }
      },
      immediate: true,
    },
    options: {
      handler() {
        this.updateSelectedLabel(this.modelValue || this.value);
      },
      deep: true,
    },
  },
  beforeDestroy() {
    document.removeEventListener("click", this.onClickOutside);
  },
};
</script>

<style>
.ease-custom {
  transition-timing-function: cubic-bezier(0.61, -0.53, 0.43, 1.43);
}
.disabled {
  background-color: #f8fafc;
  color: #64748b;
}
</style>
