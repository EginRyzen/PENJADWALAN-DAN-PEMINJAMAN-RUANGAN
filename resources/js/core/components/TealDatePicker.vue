<template>
  <div class="relative" ref="wrapper">
    <label v-if="label" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>

    <!-- Trigger Input -->
    <button
      type="button"
      @click="togglePicker"
      class="w-full h-11 flex items-center gap-2 px-3 border rounded-lg transition-all duration-200 text-sm text-left"
      :class="isOpen
        ? 'border-teal-500 ring-1 ring-teal-200 bg-white'
        : modelValue
          ? 'border-teal-400 bg-white text-gray-700 hover:border-teal-500'
          : 'border-gray-200 bg-white text-gray-400 hover:border-teal-400'"
    >
      <!-- Calendar icon -->
      <svg class="w-4 h-4 flex-shrink-0" :class="modelValue ? 'text-teal-500' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
      </svg>

      <!-- Value / Placeholder -->
      <span class="flex-1 font-medium" :class="modelValue ? 'text-gray-700' : 'text-gray-400'">
        {{ modelValue ? formatDisplayDate(modelValue) : placeholder }}
      </span>

      <!-- Clear button -->
      <span
        v-if="modelValue"
        @click.stop="clearDate"
        class="w-4 h-4 flex items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300 text-gray-500 transition ml-auto"
      >
        <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </span>

      <!-- Chevron -->
      <svg v-else class="w-4 h-4 text-gray-400 ml-auto flex-shrink-0 transition-transform" :class="{'rotate-180': isOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </button>

    <!-- Warning: holiday -->
    <div v-if="modelValue && isHoliday(modelValue)" class="absolute -bottom-5 left-0 text-xs text-amber-600 font-medium flex items-center gap-1">
      <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
      </svg>
      {{ getHolidayReason(modelValue) }}
    </div>

    <!-- Calendar Dropdown Popup -->
    <Transition name="picker-drop">
      <div
        v-if="isOpen"
        class="fixed z-50 bg-white rounded-2xl shadow-2xl shadow-gray-200/80 border border-gray-100 overflow-hidden"
        style="min-width:290px; width:300px;"
        :style="{ top: dropTop + 'px', left: dropLeft + 'px' }"
      >
        <!-- Calendar header -->
        <div class="bg-gradient-to-r from-teal-500 to-teal-600 px-4 py-3 flex items-center justify-between">
          <button @click="prevMonth" class="w-7 h-7 flex items-center justify-center rounded-lg bg-white/20 hover:bg-white/30 text-white transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>
          <span class="text-sm font-bold text-white">{{ monthYearLabel }}</span>
          <button @click="nextMonth" class="w-7 h-7 flex items-center justify-center rounded-lg bg-white/20 hover:bg-white/30 text-white transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
        </div>

        <!-- Day headers -->
        <div class="grid grid-cols-7 px-3 pt-2 pb-1">
          <div v-for="d in dayHeaders" :key="d" class="h-7 flex items-center justify-center text-xs font-bold text-gray-400">{{ d }}</div>
        </div>

        <!-- Date Grid -->
        <div class="grid grid-cols-7 px-3 pb-3 gap-y-0.5">
          <!-- Leading blanks -->
          <div v-for="i in leadingBlanks" :key="'b'+i"></div>

          <!-- Days -->
          <button
            v-for="day in daysInMonth"
            :key="day"
            type="button"
            @click="selectDay(day)"
            :disabled="isDisabledDay(day)"
            class="relative h-8 w-full flex items-center justify-center text-xs rounded-lg font-medium transition-all duration-150"
            :class="getDayClass(day)"
            :title="getDayTooltip(day)"
          >
            {{ day }}
            <!-- Holiday dot indicator -->
            <span
              v-if="getHolidayType(day)"
              class="absolute bottom-0.5 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full"
              :class="getHolidayType(day) === 'nasional' ? 'bg-red-400' : 'bg-orange-400'"
            ></span>
          </button>
        </div>

        <!-- Legend -->
        <div class="border-t border-gray-100 px-4 py-2.5 flex items-center gap-4 flex-wrap">
          <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-red-400"></span>
            <span class="text-xs text-gray-500">Libur Nasional</span>
          </div>
          <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-orange-400"></span>
            <span class="text-xs text-gray-500">Libur Kampus</span>
          </div>
          <button @click="selectToday" class="ml-auto text-xs text-teal-600 font-semibold hover:text-teal-700 transition">Hari Ini</button>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script>
import DISPATCHES from '@/core/plugins/constants/dispatches.js';

export default {
  name: 'TealDatePicker',
  props: {
    modelValue:    { type: String, default: '' },   // 'YYYY-MM-DD'
    label:         { type: String, default: '' },
    placeholder:   { type: String, default: 'Pilih tanggal...' },
    required:      { type: Boolean, default: false },
    minDate:       { type: String, default: '' },   // 'YYYY-MM-DD'
    hariLiburList: { type: Array, default: () => [] }, // Prop list (optional if store integrated)
    disableWeekend:{ type: Boolean, default: false },
  },
  emits: ['update:modelValue', 'change'],
  data() {
    const today = new Date();
    return {
      isOpen: false,
      viewYear:  today.getFullYear(),
      viewMonth: today.getMonth(), // 0-indexed
      dayHeaders: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
      dropTop: 0,
      dropLeft: 0,
    };
  },
  computed: {
    // Merged list from props and settings store
    computedHariLiburList() {
      const storeList = this.$store.state.settings?.hariLiburList || [];
      // Combine and unique by tanggal
      const combined = [...this.hariLiburList, ...storeList];
      const unique = [];
      const map = new Map();
      for (const item of combined) {
        if (!map.has(item.tanggal)) {
          map.set(item.tanggal, true);
          unique.push(item);
        }
      }
      return unique;
    },
    monthYearLabel() {
      return new Date(this.viewYear, this.viewMonth, 1)
        .toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
    },
    daysInMonth() {
      return new Date(this.viewYear, this.viewMonth + 1, 0).getDate();
    },
    leadingBlanks() {
      const day = new Date(this.viewYear, this.viewMonth, 1).getDay();
      return day === 0 ? 6 : day - 1;
    },
    todayStr() {
      const t = new Date();
      return `${t.getFullYear()}-${String(t.getMonth()+1).padStart(2,'0')}-${String(t.getDate()).padStart(2,'0')}`;
    },
  },
  mounted() {
    document.addEventListener('click', this.handleOutsideClick);
    if (this.modelValue) {
      const d = new Date(this.modelValue + 'T00:00:00');
      this.viewYear = d.getFullYear();
      this.viewMonth = d.getMonth();
    }

    // Auto fetch holidays if store is empty
    this.checkAndFetchHolidays();
  },
  beforeUnmount() {
    document.removeEventListener('click', this.handleOutsideClick);
  },
  methods: {
    async checkAndFetchHolidays() {
      if ((this.$store.state.settings?.hariLiburList || []).length === 0) {
        try {
          // Fetch with large size to get all holidays
          await this.$store.dispatch(DISPATCHES.GET_HARI_LIBUR, { size: 500 });
        } catch (error) {
          console.error("TealDatePicker: Gagal mengambil data hari libur otomatis", error);
        }
      }
    },
    togglePicker() {
      this.isOpen = !this.isOpen;
      if (this.isOpen) {
        this.$nextTick(() => {
          const btn = this.$refs.wrapper?.querySelector('button');
          if (btn) {
            const rect = btn.getBoundingClientRect();
            this.dropTop = rect.bottom + 6;
            const dropWidth = 300;
            const vpWidth = window.innerWidth;
            this.dropLeft = rect.left + dropWidth > vpWidth
              ? vpWidth - dropWidth - 12
              : rect.left;
          }
        });
      }
    },
    handleOutsideClick(e) {
      if (this.$refs.wrapper && !this.$refs.wrapper.contains(e.target)) {
        this.isOpen = false;
      }
    },
    prevMonth() {
      if (this.viewMonth === 0) { this.viewMonth = 11; this.viewYear--; }
      else this.viewMonth--;
    },
    nextMonth() {
      if (this.viewMonth === 11) { this.viewMonth = 0; this.viewYear++; }
      else this.viewMonth++;
    },
    dayStr(day) {
      return `${this.viewYear}-${String(this.viewMonth + 1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
    },
    isDisabledDay(day) {
      const ds = this.dayStr(day);
      if (this.minDate && ds < this.minDate) return true;
      if (this.disableWeekend) {
        const dow = new Date(ds + 'T00:00:00').getDay();
        if (dow === 0 || dow === 6) return true;
      }
      return false;
    },
    isHoliday(dateStr) {
      return this.computedHariLiburList.some(h => h.tanggal === dateStr);
    },
    getHolidayReason(dateStr) {
      const h = this.computedHariLiburList.find(h => h.tanggal === dateStr);
      return h ? `${h.keterangan} (${h.tipe === 'nasional' ? 'Libur Nasional' : 'Libur Kampus'})` : '';
    },
    getHolidayType(day) {
      const h = this.computedHariLiburList.find(h => h.tanggal === this.dayStr(day));
      return h ? h.tipe : null;
    },
    isSunday(day) {
      return new Date(this.dayStr(day) + 'T00:00:00').getDay() === 0;
    },
    getDayClass(day) {
      const ds = this.dayStr(day);
      const isSelected = this.modelValue === ds;
      const isToday = this.todayStr === ds;
      const isDisabled = this.isDisabledDay(day);
      const holiday = this.computedHariLiburList.find(h => h.tanggal === ds);
      const isSunday = new Date(ds + 'T00:00:00').getDay() === 0;

      if (isDisabled) {
        return 'text-gray-300 cursor-not-allowed';
      }
      if (isSelected) return 'bg-teal-500 text-white font-bold shadow-sm shadow-teal-200';
      if (isToday) return 'border-2 border-teal-400 text-teal-600 font-bold hover:bg-teal-50';
      
      // Holiday styling (but selectable)
      if (holiday?.tipe === 'nasional') return 'bg-red-50 text-red-600 font-bold hover:bg-red-100';
      if (holiday?.tipe === 'kampus') return 'bg-orange-50 text-orange-600 font-bold hover:bg-orange-100';
      
      if (isSunday) return 'text-red-400 hover:bg-red-50';
      return 'text-gray-700 hover:bg-teal-50 hover:text-teal-700';
    },
    getDayTooltip(day) {
      const ds = this.dayStr(day);
      const h = this.computedHariLiburList.find(h => h.tanggal === ds);
      if (h) return `${h.keterangan}`;
      return '';
    },
    selectDay(day) {
      if (this.isDisabledDay(day)) return;
      const ds = this.dayStr(day);
      this.$emit('update:modelValue', ds);
      this.$emit('change', ds);
      this.isOpen = false;
    },
    selectToday() {
      const today = this.todayStr;
      if (!this.isDisabledDay(new Date().getDate())) {
        this.viewYear = new Date().getFullYear();
        this.viewMonth = new Date().getMonth();
        this.$emit('update:modelValue', today);
        this.$emit('change', today);
        this.isOpen = false;
      }
    },
    clearDate() {
      this.$emit('update:modelValue', '');
      this.$emit('change', '');
    },
    formatDisplayDate(dateStr) {
      if (!dateStr) return '';
      const d = new Date(dateStr + 'T00:00:00');
      return d.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
    },
  },
};
</script>

<style scoped>
.picker-drop-enter-active, .picker-drop-leave-active {
  transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
}
.picker-drop-enter-from, .picker-drop-leave-to {
  opacity: 0;
  transform: translateY(-6px) scale(0.98);
}
</style>
