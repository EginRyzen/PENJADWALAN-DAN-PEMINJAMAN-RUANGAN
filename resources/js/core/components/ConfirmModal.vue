<template>
  <modal-app :modelValue="show" @update:modelValue="$emit('close')" max-width="500px">
    <div class="p-8 md:p-10 font-display text-center relative overflow-hidden">
      <!-- Background Decorative Element -->
      <div class="absolute -top-10 -right-10 w-32 h-32 bg-teal-50 rounded-full blur-3xl opacity-50"></div>
      
      <!-- Top Icon Illustration (SVG) -->
      <div class="flex justify-center mb-6 relative">
        <div class="w-32 h-32 flex items-center justify-center animate-float relative">
          <!-- Background Glow -->
          <div 
            class="absolute inset-0 rounded-full blur-2xl opacity-20"
            :style="{ backgroundColor: titleColor }"
          ></div>
          
          <!-- Dynamic SVG -->
          <svg v-if="config.confirmType === 'approve'" class="w-24 h-24 text-[#2DD4BF] drop-shadow-xl" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM10 17L5 12L6.41 10.59L10 14.17L17.59 6.58L19 8L10 17Z" fill="currentColor"/>
          </svg>
          
          <svg v-else-if="config.confirmType === 'reject'" class="w-24 h-24 text-[#EF4444] drop-shadow-xl" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C6.47 2 2 6.47 2 12C2 17.53 6.47 22 12 22C17.53 22 22 17.53 22 12C22 6.47 17.53 2 12 2ZM17 15.59L15.59 17L12 13.41L8.41 17L7 15.59L10.59 12L7 8.41L8.41 7L12 10.59L15.59 7L17 8.41L13.41 12L17 15.59Z" fill="currentColor"/>
          </svg>
          
          <svg v-else class="w-24 h-24 text-[#2DD4BF] drop-shadow-xl" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM13 19H11V17H13V19ZM15.07 11.25L14.17 12.17C13.45 12.9 13 13.5 13 15H11V14.5C11 13.39 11.45 12.39 12.17 11.67L13.41 10.41C13.78 10.05 14 9.55 14 9C14 7.9 13.1 7 12 7C10.9 7 10 7.9 10 9H8C8 6.79 9.79 5 12 5C14.21 5 16 6.79 16 9C16 9.88 15.64 10.68 15.07 11.25Z" fill="currentColor"/>
          </svg>
        </div>
      </div>

      <!-- Title -->
      <h2 
        class="text-2xl font-extrabold mb-3 tracking-tight transition-all duration-300"
        :style="{ color: titleColor }"
      >
        {{ config.textConfirmationTitle }}
      </h2>

      <!-- Body Message -->
      <p class="text-sm font-medium text-slate-500 leading-relaxed mb-8 px-4">
        {{ config.textConfirmationBody }}
      </p>

      <!-- Optional Textarea -->
      <div v-if="config.showKomentarConfirmation" class="text-left space-y-2 mb-8 animate-slide-up">
        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest px-1">
          {{ config.labelKomentarConfirmation }}
          <span v-if="config.komentarConfirmationRequired" class="text-red-500 ml-0.5">*</span>
        </label>
        
        <div class="relative">
          <textarea
            v-model="internalComment"
            :placeholder="config.placeholderKomentarConfirmation"
            :class="[
              'w-full h-32 p-4 rounded-xl border-2 bg-slate-50/50 focus:bg-white transition-all text-sm font-medium outline-none',
              hasError ? 'border-red-400 focus:border-red-500' : 'border-[#2DD4BF]/20 focus:border-[#2DD4BF]'
            ]"
            @input="validateInput"
          ></textarea>
          
          <!-- Character Count -->
          <div v-if="config.showCharCount" class="mt-2 text-right">
             <span class="text-xs font-bold" :class="charsLeft < 0 ? 'text-red-500' : 'text-slate-400'">
                {{ charsLeft }} / {{ config.maxChar }} Karakter Tersisa
             </span>
          </div>
          
          <!-- Error Message -->
          <transition 
            enter-active-class="transition duration-200 ease-out" 
            enter-from-class="transform -translate-y-2 opacity-0"
            leave-active-class="transition duration-150 ease-in"
            leave-to-class="transform -translate-y-2 opacity-0"
          >
            <p v-if="hasError" class="text-[10px] font-extrabold text-red-500 uppercase tracking-wider mt-1.5 px-1">
              {{ config.labelKomentarConfirmationError }}
            </p>
          </transition>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="grid grid-cols-2 gap-4">
        <button 
          @click="$emit('close')"
          class="h-12 rounded-xl border-2 border-red-400 text-red-500 font-extrabold text-sm hover:bg-red-50 transition-all active:scale-95 shadow-sm"
        >
          Tidak
        </button>
        <button 
          @click="handleConfirm"
          :disabled="isSubmitDisabled"
          :class="[
            'h-12 rounded-xl font-extrabold text-sm transition-all focus:ring-4 focus:ring-teal-200 active:scale-95 shadow-lg shadow-teal-500/20 text-white',
            isSubmitDisabled ? 'bg-slate-300 cursor-not-allowed shadow-none' : 'bg-[#2DD4BF] hover:bg-[#26bba8]'
          ]"
        >
          Ya
        </button>
      </div>
    </div>
  </modal-app>
</template>

<script>
import ModalApp from "./Modal.vue";

export default {
  name: "ConfirmModal",
  components: {
    ModalApp
  },
  props: {
    show: {
      type: Boolean,
      default: false
    },
    config: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      internalComment: "",
      hasError: false
    };
  },
  computed: {
    titleColor() {
      const type = this.config.confirmType?.toLowerCase() || "";
      if (type === "reject") return "#EF4444";
      return "#2DD4BF";
    },
    charsLeft() {
      return (this.config.maxChar || 100) - this.internalComment.length;
    },
    isSubmitDisabled() {
      if (!this.config.showKomentarConfirmation) return false;
      if (this.config.komentarConfirmationRequired && !this.internalComment.trim()) return true;
      if (this.config.komentarConfirmationCharacterCheck && this.charsLeft < 0) return true;
      return false;
    }
  },
  watch: {
    show(newVal) {
      if (newVal) {
        this.internalComment = this.config.komentarConfirmation || "";
        this.hasError = false;
      }
    }
  },
  methods: {
    validateInput() {
      if (this.config.komentarConfirmationRequired && this.internalComment.trim()) {
        this.hasError = false;
      }
    },
    handleConfirm() {
      if (this.config.komentarConfirmationRequired && !this.internalComment.trim()) {
        this.hasError = true;
        return;
      }
      this.$emit('confirm', this.internalComment);
    }
  }
};
</script>

<script setup>
// Adding custom animations for a premium feel
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

.font-display {
  font-family: 'Outfit', sans-serif;
}

.animate-float {
  animation: float 4s ease-in-out infinite;
}

@keyframes float {
  0% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
  100% { transform: translateY(0px); }
}

.animate-slide-up {
  animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0px); }
}
</style>
