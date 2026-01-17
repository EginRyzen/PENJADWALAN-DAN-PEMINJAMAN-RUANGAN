<template>
  <div
    class="flex flex-col relative"
    id="autocompleteWrapper"
    :title="selectedValueText"
  >
    <label class="text-sm text-black-primary mb-1"
      ><span
        v-if="label && required && !markRequiredRight && !disabled"
        class="text-error"
        >*</span
      >
      {{ label
      }}<span
        v-if="label && required && markRequiredRight && !disabled"
        class="text-error"
        >*</span
      ></label
    >
    <div
      class="flex rounded-md border"
      :class="{
        'border-error': error,
        'border-primary': !error,
        'border-white-100': disabled,
      }"
    >
      <input
        v-show="!isFocus"
        readonly
        type="text"
        :value="parseMiddleText ? formattedSelectedText : selectedValueText"
        @click="onDisplayTextClick"
        :id="id"
        :placeholder="placeholder"
        class="text-sm h-11 z-0 w-full border-0 rounded-md py-3 focus:border-0 focus:ring-0 focus:shadow-none"
        :class="{
          'bg-white-100 text-black-50': disabled,
        }"
        :disabled="disabled"
        :name="name"
        ref="myInput"
      />
      <input
        v-show="isFocus"
        v-model="search"
        type="text"
        :name="name"
        ref="autocompleteRef"
        @blur="onAutocompleteBlur"
        id="autocompleteEl"
        :disabled="disabled"
        class="text-sm h-11 focus:border-0 focus:ring-0 focus:shadow-none focus:outline-0 z-0 w-full border-0 rounded-md"
        @input="onInput"
      />
      <div
        class="text-sm text-black flex items-center justify-end"
        v-if="this.$slots['icon-right']"
      >
        <slot name="icon-right"></slot>
      </div>
      <div
        class="flex items-center rounded-md w-fit h-11 py-3 pr-4 border-0 focus:ring-0 text-sm text-black focus:border-0 focus:shadow-none focus:outline-0"
        :class="disabled ? 'bg-white-100' : ''"
        @click="onClickArrow"
      >
        <svg
          fill="none"
          width="16"
          height="8"
          viewBox="0 0 16 8"
          stroke="currentColor"
          class="h-3 w-3 transform transition-transform duration-200 ease-in-out"
          :class="!disabled && isOptionsExpanded ? 'rotate-180' : 'rotate-0'"
        >
          <path
            d="M0.5 0.95L1.01625 0.25H15.0088L15.5 0.925L8.46625 7.75H7.4325L0.5 0.95Z"
            fill="currentColor"
          />
        </svg>
      </div>
    </div>
    <div v-if="itemDescription" class="text-xs text-white-300 mt-1">
      <slot name="item-description">{{ selectedDescription }}</slot>
    </div>
    <div class="text-xs text-error mt-1">
      <slot name="error-message"></slot>
    </div>
    <ul
      class="absolute bg-white w-full top-16 rounded-b-md shadow-primary-md border border-primary-lightest z-30 overflow-y-scroll max-h-48"
      :class="{
        invisible: !isFocus,
        visible: isFocus,
      }"
    >
      <li
        v-if="multiple && showSelectAll && computedOptions.length > 0"
        @click="toggleSelectAll"
        class="px-3 py-2 text-sm cursor-pointer hover:bg-primary-light flex items-center"
      >
        <input
          type="checkbox"
          class="w-4 h-4 rounded-sm mr-2 bg-white border border-primary"
          :checked="isAllSelected"
        />
        All
      </li>
      <li
        v-for="(option, i) in computedOptions"
        :key="`option-${i}`"
        @click="setValue(option)"
        :class="getClass(option)"
      >
        <input
          v-if="multiple"
          class="w-4 h-4 rounded-sm mr-2 bg-white border border-primary"
          type="checkbox"
          :checked="option.selected"
        />
        {{
          optionType === "string"
            ? option
            : dropdownText
            ? option[dropdownText]
            : option[itemText]
        }}
      </li>
      <li
        v-if="computedOptions.length === 0 && !loading"
        class="text-center px-3 py-2 text-sm"
      >
        <slot name="empty-result"><span>No Data Available</span></slot>
      </li>
      <li class="text-center px-3 py-2 text-sm" v-if="loading">
        <slot name="loading-text">Loading...</slot>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: "NewAutocompleteApp",
  props: {
    value: {
      type: Array,
      required: true,
      default: () => [],
    },
    markRequiredRight: {
      type: Boolean,
      default: false,
      required: false,
    },
    label: {
      default: "",
    },
    placeholder: {
      default: "",
    },
    id: {
      default: "",
    },
    description: {
      default: "",
    },
    message: {
      default: "",
    },
    type: {
      default: "text",
    },
    options: {
      type: Array,
      default: () => [],
    },
    required: {
      default: false,
    },
    itemDescription: {
      required: false,
    },
    dropdownText: {
      required: false,
    },
    itemSearch: {
      required: false,
    },
    itemText: {
      default: "text",
    },
    itemValue: {
      default: "value",
    },
    multiple: {
      default: false,
    },
    name: {
      default: "input",
    },
    error: {
      type: Boolean,
      default: false,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    loading: {
      type: Boolean,
      default: false,
    },
    excludeSelected: {
      type: Boolean,
      default: false,
    },
    clearSearchAfterClick: {
      type: Boolean,
      default: true,
    },
    searchMultipleFields: {
      type: Array,
      default: () => [],
    },
    parseMiddleText: {
      type: Boolean,
      default: false,
    },
    showSelectAll: {
      type: Boolean,
      default: false,
    },
  },
  data: function () {
    return {
      isFocus: false,
      search: "",
      selected: [],
      isOptionsExpanded: false,
      arrowDown: false,
    };
  },
  methods: {
    getClass(option) {
      if (Object.prototype.hasOwnProperty.call(option, "is_available")) {
        return {
          "bg-primary-lightest text-primary-darkest px-3 py-2 text-sm cursor-pointer hover:bg-primary-light hover:text-primary-dark flex no-wrap break-words":
            option.selected && option.is_available,
          "bg-white-200 text-white-300 px-3 py-2 text-sm cursor-not-allowed pointer-events-none hover:bg-primary-light hover:text-primary-dark flex no-wrap break-words":
            (!option.selected || option.selected) && !option.is_available,
          "px-3 py-2 text-sm cursor-pointer hover:bg-primary-light hover:text-primary-dark flex no-wrap break-words":
            !option.selected && option.is_available,
        };
      } else {
        return {
          "bg-primary-lightest text-primary-darkest px-3 py-2 text-sm cursor-pointer hover:bg-primary-light hover:text-primary-dark flex no-wrap break-words":
            option.selected,
          "px-3 py-2 text-sm cursor-pointer hover:bg-primary-light hover:text-primary-dark flex no-wrap break-words":
            !option.selected,
        };
      }
    },
    toggleSelectAll() {
      if (this.isAllSelected) {
        this.selected = [];
      } else {
        this.selected = [...this.computedOptions];
      }
      this.$emit("input", this.selected);
      this.$emit("change", this.selected);
    },
    onInput(e) {
      this.$emit("search", e.target.value);
    },
    onClickArrow() {
      if (!this.disabled) {
        if (this.arrowDown) {
          this.onAutocompleteBlur();
          this.isFocus = false;
        } else {
          this.$refs.myInput.click();
        }
        this.arrowDown = !this.arrowDown;
      }
    },
    setValue(value) {
      if (this.multiple) {
        const _isExist = this.selected.findIndex(
          (v) => v[this.itemValue] === value[this.itemValue]
        );
        if (_isExist > -1) {
          this.selected.splice(_isExist, 1);
        } else {
          this.selected.push(value);
        }
        this.$refs.autocompleteRef.focus();
      } else {
        this.selected = [value];
        this.isFocus = false;
        this.isOptionsExpanded = false;
        if (!this.clearSearchAfterClick) {
          this.handleNotClearAfterSelectDropdown(value);
        }
      }
      this.$emit("input", this.selected);
      this.$emit("change", this.selected);
    },
    onDisplayTextClick() {
      this.isFocus = true;
      this.isOptionsExpanded = true;
      if (this.clearSearchAfterClick) {
        this.search = "";
      }
      this.$nextTick(() => {
        this.$refs.autocompleteRef.focus();
      });
      this.$emit("click");
    },
    onAutocompleteBlur() {
      this.$emit("blur");
      this.isOptionsExpanded = false;
    },
    onCheckboxChange(payload) {
      this.$emit("change", payload);
    },
    onRadioChange(payload) {
      this.$emit("change", payload);
    },
    onOptionClick(payload) {
      if (this.multiple) this.onCheckboxChange(payload);
      else this.onRadioChange(payload);
    },
    handleClickOutside(event) {
      if (!this.$el.contains(event.target)) {
        if (!this.clearSearchAfterClick && this.isFocus) {
          if (this.search) {
            this.handleNotClearAfterClickWhenClickOutside();
          }
          if (!this.search) {
            this.search = "";
            this.selected = [];
            this.$emit("input", this.selected);
            this.$emit("change", this.selected);
          }
        }
        this.isFocus = false;
        this.isOptionsExpanded = false;
      } else {
        this.$refs.autocompleteRef.focus();
      }
    },
    handleNotClearAfterSelectDropdown(value) {
      if (typeof value == "string") {
        this.search = value;
      } else if (Array.isArray(value)) {
        if (value.length) {
          if (typeof value[0] == "string") {
            this.search = value[0];
          } else if (typeof value[0] == "object") {
            this.search = value[0][this.itemValue];
          }
        }
      } else if (typeof value == "object") {
        this.search = value[this.itemValue];
      }
    },
    handleNotClearAfterClickWhenClickOutside() {
      if (typeof this.selected == "string") {
        this.search = this.selected;
      } else if (Array.isArray(this.selected)) {
        if (this.selected.length) {
          if (typeof this.selected[0] == "string") {
            this.search = this.selected[0];
          } else if (typeof this.selected[0] == "object") {
            this.search = this.selected[0][this.itemValue];
          }
        }
      } else if (typeof this.selected == "object") {
        this.search = this.selected[this.itemValue];
      }
    },
  },
  computed: {
    isAllSelected() {
      return (
        this.computedOptions.length > 0 &&
        this.selected.length === this.computedOptions.length
      );
    },
    computedOptions() {
      const type = typeof this.options[0];
      if (type === "object") {
        let filteredOptions = this.options
          .filter((opt) => {
            const searchTerm = this.search.toLowerCase();

            if (this.searchMultipleFields.length > 0) {
              return this.searchMultipleFields.some((field) => {
                const fieldValue = opt[field];
                return (
                  fieldValue &&
                  fieldValue.toString().toLowerCase().includes(searchTerm)
                );
              });
            }

            const searchField = this.itemSearch
              ? this.itemSearch
              : this.itemText;
            const fieldValue = opt[searchField];
            return (
              fieldValue &&
              fieldValue.toString().toLowerCase().includes(searchTerm)
            );
          })
          .map((opt) => ({
            ...opt,
            selected:
              this.value.find(
                (v) => v[this.itemValue] === opt[this.itemValue]
              ) !== undefined,
          }));
        if (this.excludeSelected && this.value.length > 0) {
          filteredOptions = filteredOptions.filter(
            (opt) => opt[this.itemValue] !== this.value[0][this.itemValue]
          );
        }
        return filteredOptions;
      } else {
        return this.options.filter(
          (opt) => opt.toLowerCase().indexOf(this.search.toLowerCase()) > -1
        );
      }
    },
    selectedValueText() {
      if (this.optionType === "object" || typeof this.value[0] === "object") {
        return this.value
          .map((v) => v[this.itemText])
          .toString()
          .split(",")
          .join(", ");
      }
      return this.value[0];
    },
    formattedSelectedText() {
      const getFirst = (text) => {
        if (typeof text === "string" && text.includes(" - ")) {
          const parts = text.split(" - ");
          return parts[0];
        }
        return text;
      };

      if (
        this.optionType === "object" ||
        (Array.isArray(this.value) && typeof this.value[0] === "object")
      ) {
        return this.value.map((v) => getFirst(v[this.itemText])).join(", ");
      }

      if (this.value && this.value.length > 0) {
        return getFirst(this.value[0]);
      }
      return "";
    },
    selectedDescription() {
      if (this.optionType === "object" || typeof this.value[0] === "object") {
        return this.value
          .map((v) => v[this.itemDescription])
          .toString()
          .split(",")
          .join(", ");
      }
      return this.value[0];
    },
    optionType() {
      if (this.options.length > 0) {
        return typeof this.options[0];
      }
      return "string";
    },
  },
  mounted() {
    document.addEventListener("click", this.handleClickOutside);
    Object.assign(this.selected, this.value);
  },
  destroyed() {
    document.removeEventListener("click", this.handleClickOutside);
  },
  watch: {
    value(val) {
      this.selected = val;
    },
  },
};
</script>
<style scoped>
input::-webkit-input-placeholder {
  color: #0f172a;
}

input:-moz-placeholder {
  /* Firefox 18- */
  color: #0f172a;
}

input::-moz-placeholder {
  /* Firefox 19+ */
  color: #0f172a;
}

input:-ms-input-placeholder {
  color: #0f172a;
}

input::placeholder {
  color: #0f172a;
}
</style>
