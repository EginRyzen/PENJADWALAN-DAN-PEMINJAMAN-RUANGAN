<template>
  <div
    ref="selectV2"
    class="selectv2 flex flex-col relative"
    :style="{ maxWidth: maxWidth }"
  >
    <label v-if="label" :for="id" class="label text-sm">
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
    <slot name="toggle-button">
      <button
        @click="toggle"
        class="select"
        type="button"
        :class="{
          'select--error': error,
          'select--disabled': disabled,
          'select--primary': color === 'primary',
        }"
        :disabled="disabled"
      >
        <span
          class="select__value--all"
          v-if="value?.length === options.length && options.left > 1"
        >
          All asdasad
        </span>
        <div v-else-if="chip">
          <div v-if="value?.length">
            <span
              v-for="val in value"
              :key="val"
              class="chip"
              :class="{ 'hyperlink-text': chipHyperlink }"
              @click="handleClickChip(val)"
            >
              {{ getItemText(val) }}
              <button
                v-if="!disabled"
                class="chip-delete"
                @click="removeChip(val)"
              >
                ×
              </button>
            </span>
          </div>
          <div v-else class="chip-placeholder">
            {{ placeholder }}
          </div>
        </div>
        <span
          v-else
          class="select__value"
          :class="maxWidth ? 'truncate' : ''"
          :style="{
            color:
              disabled &&
              ((Array.isArray(value) && value?.length > 0) ||
                (!Array.isArray(value) && value))
                ? '#1b253b'
                : !disabled &&
                  ((Array.isArray(value) && value?.length > 0) ||
                    (!Array.isArray(value) && value))
                ? '#1b253b'
                : '#94a3b8',
          }"
        >
          {{ customValue || valueString || placeholder }}
        </span>
        <span v-if="!disabled">
          <!-- Icon Dropdown -->
          <svg
            fill="black"
            viewBox="0 0 24 24"
            stroke="currentColor"
            class="h-4 w-4 transform transition-transform duration-200 ease-in-out"
            :class="isOpen ? 'rotate-180' : 'rotate-0'"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1"
              d="M19 9l-7 7-7-7 l14 0"
            />
          </svg>
        </span>
      </button>
    </slot>
    <div class="text-xs text-error mt-2">
      <slot name="error-message"></slot>
    </div>
    <ol
      class="options"
      :class="{
        hidden: !isOpen,
        flex: isOpen,
        [optionsClass]: true,
      }"
      :style="`top: ${topPosition}px;`"
    >
      <li
        v-if="!hideOptionAll && multiple && options.length"
        class="options__item"
        :class="{ 'options__item--selected': all }"
        @click="selectAll"
      >
        All
        <input
          class="item__checkbox"
          type="checkbox"
          name="all"
          id="all"
          v-model="all"
        />
      </li>
      <li
        class="options__item"
        :class="{
          'options__item--selected': isSelected(option),
          'options__item--disabled': isDisabled(option),
        }"
        v-for="option in options"
        :key="option[itemValue]"
        @click="(e) => setSelected(option, e)"
      >
        <slot name="option" :option="option">
          <p class="flex-1">
            {{ itemText ? option[itemText] : option }}
          </p>
        </slot>
        <input
          v-if="multiple"
          class="item__checkbox"
          type="checkbox"
          :name="option[itemText]"
          :id="`checkbox_${option[itemText]}`"
          :disabled="isDisabled(option)"
          :checked="isSelected(option)"
          @change="() => setSelected(option)"
        />
      </li>
      <li v-if="!options.length" class="options__item options__item--nodata">
        <p>No Data Available</p>
      </li>
    </ol>
    <bottom-sheet-bima
      v-model="isBottomSheetOpen"
      :title="labelBottomsheet ?? label"
      @close="toggle"
    >
      <ol
        :style="{ minHeight: bottomSheetMinHeight }"
        class="bottom-sheet-options"
      >
        <li
          v-if="!hideOptionAll && multiple"
          class="options__item"
          :class="{ 'options__item--selected': bottomSheetAll }"
          @click="selectAll"
        >
          All
        </li>

        <li
          class="options__item"
          :class="{
            'options__item--selected': isSelected(option),
            'options__item--disabled': isDisabled(option),
          }"
          v-for="option in options"
          :key="option[itemValue]"
          @click="(e) => setSelected(option, e)"
        >
          <slot name="option" :option="option"></slot>
          <label :for="itemText ? option[itemText] : option" class="flex-1">
            {{ itemText ? option[itemText] : option }}
          </label>

          <input
            v-if="selectionInputType === 'radio'"
            class="item__radio"
            type="radio"
            :name="label"
            :id="itemText ? option[itemText] : option"
            :checked="isSelected(option)"
            @change="() => setSelected(option)"
          />
          <input
            v-else
            class="item__checkbox"
            type="checkbox"
            :name="label"
            :id="itemText ? option[itemText] : option"
            :checked="isSelected(option)"
            :disabled="isDisabled(option)"
            @change="() => setSelected(option)"
          />
        </li>
      </ol>
      <div class="action" v-if="!onMobileAutoApply">
        <button
          class="action__button"
          :class="{
            '--disabled': isBottomSheetActionDisabled,
          }"
          :disabled="isBottomSheetActionDisabled"
          @click="apply"
        >
          {{ actionButtonLabel }}
        </button>
      </div>
    </bottom-sheet-bima>
  </div>
</template>

<script>
import BottomSheetBima from "./BottomSheet.vue";
import handleErrorMixin from "@/core/helper/mixins/handleErrorMixin";

const MOBILE_BREAKPOINT = 640;
export default {
  components: { BottomSheetBima },
  name: "SelectV2App",
  mixins: [handleErrorMixin],
  props: {
    onMobileAutoApply: {
      type: Boolean,
      default: false,
      required: false,
    },
    stickDesktopMode: {
      type: Boolean,
      default: false,
      required: false,
    },
    hideOptionAll: {
      type: Boolean,
      default: false,
      required: false,
    },
    labelBottomsheet: {
      type: String,
      default: null,
      required: false,
    },
    markRequiredRight: {
      type: Boolean,
      default: false,
      required: false,
    },
    value: [String, Array, Number, Boolean, Object],
    id: String,
    label: String,
    required: Boolean,
    placeholder: String,
    multiple: Boolean,
    error: Boolean,
    disabled: Boolean,
    options: {
      type: Array,
      default: () => [],
    },
    itemValue: {
      type: String,
      default: "value",
    },
    itemText: {
      type: String,
      default: "text",
    },
    disabledOptions: {
      type: Array,
      default: () => [],
    },
    maxWidth: {
      type: String,
      default: "",
    },
    bottomSheetMinHeight: {
      type: String,
      default: "",
    },
    actionButtonLabel: {
      type: String,
      default: "Terapkan",
    },
    selectionInputType: {
      type: String,
      default: "checkbox",
    },
    customValue: {
      type: String,
      default: "",
    },
    color: {
      type: String,
      default: "",
    },
    hasConfirmButton: {
      type: Boolean,
      default: false,
    },
    optionsClass: {
      type: String,
      default: "",
    },
    chip: {
      type: Boolean,
      default: false,
    },
    chipHyperlink: {
      type: Boolean,
      default: false,
      required: false,
    },
  },
  data: function () {
    return {
      isMobile: false,
      selectedIndex: null,
      isOpen: false,
      isBottomSheetOpen: false,
      all: false,
      topPosition: 72,
      bottomSheetValues: undefined,
      bottomSheetOptions: [],
      isBottomSheetActionDisabled: false,
      bottomSheetAll: false,
      tempValue: "",
    };
  },
  watch: {
    value: {
      handler: function () {
        this.all = this.value.length === this.options.length && this.multiple;
        if (this.onMobileAutoApply) {
          this.bottomSheetValues = [...this.value];
        }
      },
      deep: true,
    },
  },
  computed: {
    valueString() {
      if (!this.value) return "";
      if (this.multiple) {
        return this.multipleValueString();
      }
      if (!Array.isArray(this.value) && typeof this.value === "object") {
        return this.value[this.itemText];
      }
      if (this.itemText) {
        if (Array.isArray(this.value)) {
          const selected = this.options.find(
            (item) => item[this.itemValue] === this.value[0]
          );
          if (selected) return selected[this.itemText];
          return this.value[0];
        } else if (typeof this.value === "string") {
          const selected = this.options.find(
            (item) => item[this.itemValue] === this.value
          );
          if (selected) return selected[this.itemText];
        }
      }
      return this.value;
    },
  },
  methods: {
    getItemText(itemValue) {
      return (
        this.options.find((val) => val[this.itemValue] == itemValue)[
          this.itemText
        ] ?? ""
      );
    },
    multipleValueString() {
      const selected = [];
      this.value.forEach((item) => {
        const target = this.options.find(
          (option) => option[this.itemValue] === item
        );
        if (target) selected.push(target);
      });
      return selected
        .map((item) => item[this.itemText])
        .toString()
        .replaceAll(",", ", ");
    },
    checkViewport() {
      if (
        document.getElementById("bima_theme").offsetWidth < MOBILE_BREAKPOINT &&
        !this.stickDesktopMode
      ) {
        this.isMobile = true;
      }
    },
    isSelected(option) {
      if (!this.value) return false;
      const values = this.isBottomSheetOpen
        ? this.bottomSheetValues
        : this.value;
      if (this.multiple) {
        return (
          values &&
          values.findIndex((item) => item === option[this.itemValue]) !== -1
        );
      }
      return (
        values === option[this.itemValue] ||
        values[0] === option[this.itemValue] ||
        values[this.itemValue] === option[this.itemValue]
      );
    },
    isDisabled(option) {
      return (
        this.disabledOptions.findIndex(
          (item) => item[this.itemValue] === option[this.itemValue]
        ) !== -1
      );
    },
    setSelected(option, e) {
      if (e) {
        if (e.target && e.target.tagName === "INPUT") return;
        e.preventDefault();
      }
      if (this.isDisabled(option)) return false;
      if (this.multiple) this.selectMultiple(option);
      else this.selectSingle(option);
    },
    selectSingle(option) {
      if (this.isBottomSheetOpen)
        this.bottomSheetValues = option[this.itemValue];
      this.$emit("input", option[this.itemValue]);
      if (!this.hasConfirmButton || !this.isMobile) this.toggle();
    },
    selectMultiple(option) {
      const values = this.isBottomSheetOpen
        ? this.bottomSheetValues
        : this.value;
      let newValues = JSON.parse(JSON.stringify(values));
      const isExistIndex = values.findIndex(
        (item) => item === option[this.itemValue]
      );
      if (isExistIndex > -1) {
        newValues.splice(isExistIndex, 1);
      } else {
        newValues.push(option[this.itemValue]);
      }
      this.all = this.value.length === this.options.length;
      if (this.isBottomSheetOpen) {
        this.bottomSheetValues = newValues;
        this.bottomSheetAll =
          this.bottomSheetValues.length === this.options.length;
        if (this.onMobileAutoApply) this.$emit("input", newValues);
      } else this.$emit("input", newValues);
    },
    toggle() {
      this.checkViewport();
      if (this.isMobile) {
        this.isBottomSheetOpen = !this.isBottomSheetOpen;
        this.bottomSheetValues = JSON.parse(JSON.stringify(this.value));
        this.bottomSheetAll =
          this.bottomSheetValues.length === this.options.length;
        if (this.onMobileAutoApply) {
          this.tempValue = JSON.parse(JSON.stringify(this.value));
        }
        if (this.isBottomSheetOpen) this.tempValue = this.bottomSheetValues;
        else this.$emit("input", this.tempValue);
      } else this.isOpen = !this.isOpen;
    },
    onClickOutside(e) {
      if (!this.$el.contains(e.target)) {
        this.isOpen = false;
      }
    },
    selectAll() {
      if (this.isBottomSheetOpen) {
        this.onBottomSheetAll();
      } else {
        this.onSelectAll();
      }
    },
    filterDisabled(option) {
      return (
        this.disabledOptions.findIndex(
          (disabled) => disabled[this.itemValue] === option[this.itemValue]
        ) === -1
      );
    },
    onSelectAll() {
      this.all = !this.all;
      if (this.all) {
        this.$emit(
          "input",
          this.options
            .filter(this.filterDisabled)
            .map((item) => item[this.itemValue])
        );
      } else {
        this.$emit("input", []);
      }
    },
    onBottomSheetAll() {
      this.bottomSheetAll = !this.bottomSheetAll;
      if (this.bottomSheetAll) {
        this.bottomSheetValues = this.options
          .filter(this.filterDisabled)
          .map((item) => item[this.itemValue]);
      } else {
        this.bottomSheetValues = [];
      }
    },
    apply() {
      if (this.hasConfirmButton) {
        if (this.value === "" || this.value === undefined) {
          this.handleErrorFieldDialog();
          return;
        }
      } else {
        this.$emit("input", this.bottomSheetValues);
        this.$emit("refresh", this.bottomSheetValues);
      }
      this.isBottomSheetOpen = false;
      this.bottomSheetOptions = [];
      this.bottomSheetValues = undefined;
    },
    handleClickChip(value) {
      if (this.chipHyperlink) {
        this.$emit("handleHyperlink", value);
      }
    },
    removeChip(value) {
      this.$emit(
        "input",
        this.value.filter((val) => val != value)
      );
    },
  },
  created() {
    document.addEventListener("click", this.onClickOutside);
  },
  mounted() {
    this.topPosition = this.$refs.selectV2?.clientHeight || 72;
    this.checkViewport();
  },
};
</script>

<style scoped>
.label {
  color: rgb(var(--black-100));
  font-size: 14px;
  margin-bottom: 4px;
}

.select {
  font-style: normal;
  font-weight: 500;
  font-size: 14px;
  color: #0f172a;
  border: 1px solid rgb(var(--white-200));
  border-radius: 8px;
  min-height: 44px;
  min-width: 90px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 16px;
}

.select.select--primary {
  border: 1px solid var(--teal-600);
}

.select.select--error {
  border-color: rgb(var(--red-500));
}

.select.select--disabled {
  background: rgb(var(--white-100));
  border-color: rgb(var(--white-100));
  cursor: not-allowed;
}

.select__value,
.select__value--all {
  text-align: left;
  line-height: 1;
}

.options {
  position: absolute;
  background: #ffffff;
  flex-direction: column;
  align-items: flex-start;
  padding: 8px 0;
  box-shadow: var(--shadow-primary-sm);
  border-radius: 8px;
  max-height: 226px;
  overflow: auto;
  min-width: 240px;
  z-index: 1;
}

.options__item {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
  width: 100%;
  font-size: 14px;
  min-height: 44px;
  cursor: pointer;
  border-bottom: 1px solid rgb(var(--white-200));
}

.options__item.options__item--nodata {
  cursor: not-allowed;
}

.options__item:hover {
  background: rgb(var(--primary));
  color: #ffffff;
}

.options__item.options__item--selected {
  color: rgb(var(--primary));
  cursor: pointer;
}

.options__item:hover.options__item--selected {
  color: #ffffff;
}

.options__item:hover .item__checkbox:checked {
  border-color: #ffffff;
}

.options__item.options__item--disabled {
  color: rgb(var(--white-300));
  background: rgb(var(--white-200));
  cursor: not-allowed;
}

.item__radio {
  color: #46bebb;
}

.item__checkbox {
  border-radius: 4px;
  border-color: rgb(var(--primary));
}

.item__checkbox:checked {
  background-color: rgb(var(--primary));
}

.item__checkbox:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  background-color: rgb(var(--white-400));
  filter: grayscale(1);
}

.bottom-sheet-options {
  margin-bottom: 76px;
  padding: 0 24px;
}

.action {
  background: white;
  box-shadow: 8px 0 8px 0 rgba(0, 0, 0, 0.1);
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
}

.action__button {
  background: var(--teal-600);
  color: white;
  border-radius: 6px;
  height: 44px;
  font-size: 14px;
  font-weight: 600;
  line-height: 18px;
  letter-spacing: 0;
  text-align: center;
}

.chip {
  display: inline-flex;
  align-items: center;
  background-color: #e0f7fa;
  color: #00796b;
  padding: 4px 8px;
  margin: 4px 6px 4px 0;
  border-radius: 16px;
  font-size: 14px;
  line-height: 20px;
  white-space: nowrap;
}

.chip-delete {
  background: none;
  border: none;
  color: #00796b;
  margin-left: 6px;
  font-size: 16px;
  line-height: 1;
  cursor: pointer;
}

.chip-delete:hover {
  color: #d32f2f;
}

@media (min-width: 768px) {
  .options__item {
    border-bottom: none;
  }
}

.hyperlink-text:hover {
  -webkit-text-fill-color: #0b64fe;
  opacity: 1; /* required on iOS */
  color: #0b64fe !important;
  font-style: normal;
  font-weight: 500;
  text-decoration: underline;
  cursor: pointer;
}
</style>
