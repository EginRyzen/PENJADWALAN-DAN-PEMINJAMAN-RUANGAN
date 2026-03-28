<template>
  <div class="flex flex-col relative w-full" ref="selectContainer">
    <label v-if="label" class="text-sm font-semibold text-gray-700 mb-1">
      <span v-if="required && !markRequiredRight && !disabled" class="text-red-500">*</span>
      {{ label }}
      <span v-if="required && markRequiredRight && !disabled" class="text-red-500">*</span>
    </label>
    <div
      class="flex rounded-md border transition-all duration-200 bg-white"
      :class="{ 'ring-1 ring-teal-100 border-teal-500': isFocus && !error, 'border-teal-400': !isFocus && !error, 'border-red-500': error }"
    >
      <input
        v-if="isFocus"
        v-model="searchQuery"
        type="text"
        ref="searchInput"
        class="text-sm h-10 px-3 w-full border-0 rounded-md focus:outline-none focus:ring-0 text-gray-700 bg-transparent"
        placeholder="Ketik untuk mencari..."
        @blur="onBlur"
      />
      <input
        v-else
        readonly
        type="text"
        :value="selectedLabel"
        class="text-sm h-11 px-3 w-full border-0 rounded-md focus:outline-none focus:ring-0 cursor-pointer text-gray-700 bg-transparent truncate"
        :placeholder="placeholder"
        @click="onFocus"
      />
      <div class="flex items-center pr-3 cursor-pointer text-gray-400" @click="toggleFocus">
        <svg fill="none" width="10" height="10" viewBox="0 0 16 8" stroke="currentColor"
          class="transform transition-transform duration-200"
          :class="isFocus ? 'rotate-180 text-teal-500' : 'rotate-0'">
          <path d="M0.5 0.95L1.01625 0.25H15.0088L15.5 0.925L8.46625 7.75H7.4325L0.5 0.95Z" fill="currentColor" />
        </svg>
      </div>
    </div>

    <teleport to="body">
      <ul 
        v-if="isFocus" 
        class="fixed bg-white rounded-lg shadow-2xl border border-gray-100 overflow-y-auto max-h-48 p-1"
        :style="dropdownStyle"
      >
        <li
          v-for="opt in filteredOptions"
          :key="opt[itemValue]"
          @mousedown.prevent="selectOption(opt)"
          class="px-3 py-2 text-sm cursor-pointer flex items-center rounded-md transition-colors mb-0.5 hover:bg-teal-50"
          :class="modelValue === opt[itemValue] ? 'bg-teal-50 text-teal-700 font-medium' : 'text-gray-600'"
        >
          {{ opt[itemText] }}
        </li>
        <li v-if="filteredOptions.length === 0" class="px-4 py-3 text-sm text-gray-400 text-center">
          Data tidak ditemukan
        </li>
      </ul>
    </teleport>
  </div>
</template>

<script>
export default {
  name: "SelectAutoComplete",
  props: {
    modelValue: [String, Number, Object],
    options: { type: Array, default: () => [] },
    itemText: { type: String, default: "name" },
    itemValue: { type: String, default: "id" },
    placeholder: { type: String, default: "Pilih..." },
    disabled: { type: Boolean, default: false },
    label: { type: String, default: "" },
    required: { type: Boolean, default: false },
    markRequiredRight: { type: Boolean, default: false },
    error: { type: Boolean, default: false },
  },
  emits: ['update:modelValue', 'change', 'focus', 'blur', 'search'],
  data() {
    return {
      isFocus: false,
      searchQuery: "",
      dropdownStyle: {
        top: '0px',
        left: '0px',
        width: '0px',
        zIndex: 9999
      }
    };
  },
  watch: {
    searchQuery(val) {
      this.$emit('search', val);
    }
  },
  computed: {
    selectedLabel() {
      const found = this.options.find(opt => opt[this.itemValue] === this.modelValue);
      return found ? found[this.itemText] : "";
    },
    filteredOptions() {
      if (!this.searchQuery) return this.options;
      const query = this.searchQuery.toLowerCase();
      return this.options.filter(opt => 
        opt[this.itemText].toString().toLowerCase().includes(query)
      );
    }
  },
  methods: {
    updateDropdownPosition() {
      if (this.$refs.selectContainer) {
        const rect = this.$refs.selectContainer.getBoundingClientRect();
        this.dropdownStyle = {
          top: `${rect.bottom + window.scrollY + 5}px`, // Muncul di bawah input
          left: `${rect.left + window.scrollX}px`,
          width: `${rect.width}px`,
          zIndex: 9999
        };
      }
    },
    onFocus() {
      if (this.disabled) return;
      this.updateDropdownPosition();
      this.isFocus = true;
      this.searchQuery = "";
      this.$emit('focus');
      this.$nextTick(() => {
        this.$refs.searchInput.focus();
      });
      // Update posisi saat scroll
      window.addEventListener('scroll', this.updateDropdownPosition);
    },
    onBlur() {
      setTimeout(() => {
        this.isFocus = false;
        this.$emit('blur');
        window.removeEventListener('scroll', this.updateDropdownPosition);
      }, 200);
    },
    toggleFocus() {
      this.isFocus ? (this.isFocus = false) : this.onFocus();
    },
    selectOption(option) {
      this.$emit('update:modelValue', option[this.itemValue]);
      this.$emit('change', option);
      this.isFocus = false;
    }
  },
  beforeUnmount() {
    window.removeEventListener('scroll', this.updateDropdownPosition);
  }
};
</script>