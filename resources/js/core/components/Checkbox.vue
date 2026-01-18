<template>
  <label class="flex items-center cursor-pointer group select-none">
    <div class="relative">
      <input
        type="checkbox"
        :id="idName"
        class="sr-only"
        :checked="isChecked"
        :disabled="disabled"
        @change="handleChange"
      />
      
      <div 
        class="custom-checkbox transition-all duration-200"
        :class="{ 
          'checked': isChecked,
          'opacity-50 cursor-not-allowed': disabled,
          'border-teal-500': isChecked,
          'border-gray-300 group-hover:border-teal-400': !isChecked 
        }"
      >
        <div v-if="isChecked" class="checkmark"></div>
      </div>
    </div>

    <span 
      v-if="$slots.default" 
      class="ml-3 text-sm font-medium text-gray-700 transition-colors"
      :class="{ 'text-teal-600': isChecked, 'text-gray-400': disabled }"
    >
      <slot></slot>
    </span>
  </label>
</template>

<script>
export default {
  name: "Checkbox",
  props: {
    modelValue: {
      type: [Array, Boolean],
      default: false
    },
    trueValue: {
      default: true
    },
    falseValue: {
      default: false
    },
    idName: {
      type: String,
      default: ""
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  emits: ['update:modelValue', 'change'],
  computed: {
    isChecked() {
      if (Array.isArray(this.modelValue)) {
        return this.modelValue.includes(this.trueValue);
      }
      return this.modelValue === this.trueValue;
    }
  },
  methods: {
    handleChange(event) {
      if (this.disabled) return;

      const checked = event.target.checked;
      let newValue;

      if (Array.isArray(this.modelValue)) {
        newValue = [...this.modelValue];
        if (checked) {
          newValue.push(this.trueValue);
        } else {
          const index = newValue.indexOf(this.trueValue);
          if (index > -1) newValue.splice(index, 1);
        }
      } else {
        newValue = checked ? this.trueValue : this.falseValue;
      }

      this.$emit('update:modelValue', newValue);
      this.$emit('change', newValue);
    }
  }
};
</script>

<style scoped>
.custom-checkbox {
  width: 1.25rem;
  height: 1.25rem;
  /* Hapus atau ubah baris border di bawah ini */
  border-width: 2px;
  border-style: solid;
  border-color: #d1d5db; /* Default warna gray-300 */
  border-radius: 0.375rem;
  background-color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
}

.custom-checkbox.checked {
  background-color: #14b8a6; /* teal-500 */
  border-color: #14b8a6; /* Pastikan border juga berubah warna saat checked */
}

.checkmark {
  width: 0.7rem;
  height: 0.7rem;
  clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
  background-color: white;
}
</style>