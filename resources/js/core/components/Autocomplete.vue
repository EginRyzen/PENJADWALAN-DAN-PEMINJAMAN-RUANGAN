<template>
  <div class="flex flex-col relative" id="autocompleteWrapper" :title="selectedValueText">
    <label class="text-sm font-semibold text-gray-700 mb-1">
      <span v-if="label && required && !markRequiredRight && !disabled" class="text-red-500">*</span>
      {{ label }}
      <span v-if="label && required && markRequiredRight && !disabled" class="text-red-500">*</span>
    </label>

    <div
      class="flex rounded-md border-2 transition-all duration-200 bg-white"
      :class="{
        'border-red-500': error,
        'border-teal-500 ring-1 ring-teal-100': isFocus && !error,
        'border-gray-200': !isFocus && !error,
        'bg-gray-50 opacity-75': disabled,
      }"
    >
      <input
        v-show="!isFocus"
        readonly
        type="text"
        :value="selectedValueText"
        @click="onDisplayTextClick"
        :id="id"
        :placeholder="placeholder"
        class="text-sm h-10 px-3 z-0 w-full border-0 rounded-md focus:outline-none focus:ring-0 cursor-pointer text-gray-700 bg-transparent truncate"
        :disabled="disabled"
      />
      
      <input
        v-show="isFocus"
        v-model="search"
        type="text"
        ref="autocompleteRef"
        @blur="onAutocompleteBlur"
        class="text-sm h-10 px-3 z-0 w-full border-0 rounded-md focus:outline-none focus:ring-0 text-gray-700 bg-transparent"
        @input="onInput"
        placeholder="Ketik untuk mencari..."
      />
      <div class="flex items-center pr-3 cursor-pointer text-gray-400" @click="onClickArrow">
        <svg fill="none" width="12" height="12" viewBox="0 0 16 8" stroke="currentColor"
          class="transform transition-transform duration-200"
          :class="isFocus ? 'rotate-180 text-teal-500' : 'rotate-0'">
          <path d="M0.5 0.95L1.01625 0.25H15.0088L15.5 0.925L8.46625 7.75H7.4325L0.5 0.95Z" fill="currentColor" />
        </svg>
      </div>
    </div>

    <ul v-show="isFocus" class="absolute bg-white w-full top-[72px] rounded-lg shadow-xl border border-gray-100 z-[100] overflow-y-auto max-h-60 p-1">
      <li
        v-if="multiple && showSelectAll && options.length > 0"
        @mousedown.prevent="toggleSelectAll"
        class="px-3 py-2 text-sm cursor-pointer hover:bg-teal-50 rounded-md flex items-center mb-1 border-b border-gray-50"
      >
        <div class="custom-checkbox mr-3" :class="{ 'checked': isAllSelected }">
           <div v-if="isAllSelected" class="checkmark"></div>
        </div>
        <span class="font-bold text-teal-700">ALL</span>
      </li>

      <li
        v-for="(option, i) in computedOptions"
        :key="`option-${i}`"
        @mousedown.prevent="setValue(option)"
        class="px-3 py-2 text-sm cursor-pointer flex items-center rounded-md transition-colors mb-0.5"
        :class="option.selected ? 'bg-teal-50 text-teal-700 font-medium' : 'hover:bg-gray-50 text-gray-600'"
      >
        <div class="custom-checkbox mr-3" :class="{ 'checked': option.selected }">
           <div v-if="option.selected" class="checkmark"></div>
        </div>
        <span class="flex-1">{{ optionType === 'string' ? option : option[itemText] }}</span>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: "Autocomplete",
  props: {
    // Di Vue 3, gunakan modelValue
    modelValue: { type: Array, required: true, default: () => [] },
    label: { type: String, default: "" },
    placeholder: { type: String, default: "Pilih..." },
    options: { type: Array, default: () => [] },
    itemText: { type: String, default: "name" },
    itemValue: { type: String, default: "id" },
    multiple: { type: Boolean, default: false },
    showSelectAll: { type: Boolean, default: false },
    required: { type: Boolean, default: false },
    markRequiredRight: { type: Boolean, default: false },
    error: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    loading: { type: Boolean, default: false },
    id: { type: String, default: "" }
  },
  emits: ['update:modelValue', 'search'], // Deklarasikan emits
  data() {
    return {
      isFocus: false,
      search: "",
    };
  },
  computed: {
    optionType() {
      return this.options.length > 0 ? typeof this.options[0] : "string";
    },
    isAllSelected() {
      if (this.options.length === 0 || !this.modelValue) return false;
      return this.options.every(opt => {
        const optVal = this.optionType === 'string' ? opt : opt[this.itemValue];
        return this.modelValue.some(v => (this.optionType === 'string' ? v : v[this.itemValue]) === optVal);
      });
    },
    computedOptions() {
      const searchTerm = this.search.toLowerCase();
      return this.options
        .filter(opt => {
          const text = (this.optionType === 'string' ? opt : opt[this.itemText]).toString().toLowerCase();
          return text.includes(searchTerm);
        })
        .map(opt => {
          const optVal = this.optionType === 'string' ? opt : opt[this.itemValue];
          const isSelected = this.modelValue.some(v => (this.optionType === 'string' ? v : v[this.itemValue]) === optVal);
          return { ...opt, selected: isSelected };
        });
    },
    selectedValueText() {
      if (!this.modelValue || this.modelValue.length === 0) return "";
      return this.modelValue.map(v => (this.optionType === 'string' ? v : v[this.itemText])).join(", ");
    }
  },
  methods: {
    onDisplayTextClick() {
      if (this.disabled) return;
      this.isFocus = true;
      this.$nextTick(() => this.$refs.autocompleteRef.focus());
    },
    onAutocompleteBlur() {
      setTimeout(() => {
        this.isFocus = false;
        this.search = "";
      }, 200);
    },
    onClickArrow() {
      this.isFocus ? (this.isFocus = false) : this.onDisplayTextClick();
    },
    setValue(option) {
      let newValue = [...this.modelValue];
      const optVal = this.optionType === 'string' ? option : option[this.itemValue];

      if (this.multiple) {
        const index = newValue.findIndex(v => (this.optionType === 'string' ? v : v[this.itemValue]) === optVal);
        index > -1 ? newValue.splice(index, 1) : newValue.push(option);
        this.$nextTick(() => this.$refs.autocompleteRef.focus());
      } else {
        newValue = [option];
        this.isFocus = false;
      }
      this.$emit("update:modelValue", newValue);
    },
    toggleSelectAll() {
      const newValue = this.isAllSelected ? [] : [...this.options];
      this.$emit("update:modelValue", newValue);
      this.$nextTick(() => this.$refs.autocompleteRef.focus());
    },
    onInput(e) {
      this.$emit("search", e.target.value);
    }
  }
};
</script>

<style scoped>
.custom-checkbox {
  width: 1.1rem;
  height: 1.1rem;
  border: 2px solid #d1d5db;
  border-radius: 0.25rem;
  background-color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
  flex-shrink: 0;
}

.custom-checkbox.checked {
  background-color: #14b8a6 !important;
  border-color: #14b8a6 !important;
}

.checkmark {
  width: 0.65rem;
  height: 0.65rem;
  clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
  background-color: white;
}

.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>