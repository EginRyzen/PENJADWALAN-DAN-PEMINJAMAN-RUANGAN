<template>
  <div class="mb-12">
    <div class="flex items-center gap-2 mb-6">
      <div class="w-1.5 h-6 bg-teal-500 rounded-full"></div>
      <h3 class="text-lg font-bold text-gray-800">Informasi Utama Gedung</h3>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
      <div class="lg:col-span-1">
        <label class="text-sm font-medium mb-2 block text-gray-700">Foto Gedung (Opsional)</label>
        <div
          class="relative border-2 border-dashed rounded-xl overflow-hidden group transition-all duration-300 h-52 flex flex-col items-center justify-center bg-gray-50"
          :class="[
            errors.building_image
              ? 'border-red-500 bg-red-50'
              : 'border-gray-300 hover:border-teal-400',
            imagePreview ? 'border-solid shadow-sm' : 'cursor-pointer',
          ]" @click="!imagePreview && triggerSelectFile()">
          <input type="file" ref="fileInput" class="hidden" accept="image/*" @change="onFileChange" />
          <template v-if="imagePreview">
            <img :src="imagePreview" class="w-full h-full object-cover" />
            <div
              class="absolute bottom-3 right-3 flex gap-2 translate-y-2 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
              <button type="button" @click.stop="$emit('preview-image', imagePreview)"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white text-teal-600 shadow-xl hover:bg-teal-500 hover:text-white transition-all">
                <font-awesome-icon icon="eye" />
              </button>
              <button type="button" @click.stop="handleRemove"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white text-red-600 shadow-xl hover:bg-red-500 hover:text-white transition-all">
                <font-awesome-icon icon="trash-alt" />
              </button>
            </div>
          </template>
          <template v-else>
            <div
              class="flex flex-col items-center justify-center text-gray-400 group-hover:text-teal-500 transition-colors">
              <font-awesome-icon icon="cloud-upload-alt" class="text-4xl mb-2" />
              <p class="text-xs font-bold uppercase tracking-wider text-center px-4">
                Klik Unggah Foto
              </p>
            </div>
          </template>
        </div>
      </div>
      <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
        <app-input label="Kode Gedung" v-model="form.building_code" placeholder="GD-001" :error="!!errors.building_code"
          @clear-error="errors.building_code = null" required>
          <template #error-message>{{ errors.building_code }}</template>
        </app-input>
        <app-input label="Nama Gedung" v-model="form.building_name" placeholder="Nama Gedung"
          :error="!!errors.building_name" @clear-error="errors.building_name = null" required>
          <template #error-message>{{ errors.building_name }}</template>
        </app-input>
        <app-input label="Lokasi Gedung" v-model="form.building_location" placeholder="Alamat Gedung"
          class="md:col-span-2" :error="!!errors.building_location" @clear-error="errors.building_location = null"
          required>
          <template #error-message>{{ errors.building_location }}</template>
        </app-input>
      </div>
    </div>
  </div>
</template>

<script>
import AppInput from "@/core/components/AppInput.vue";

export default {
  components: { AppInput },
  data() {
    return {
      imagePreview: null,
      form: {
        building_name: "",
        building_code: "",
        building_location: "",
        building_image: null,
      },
      errors: {
        building_name: null,
        building_code: null,
        building_location: null,
      },
    };
  },
  methods: {
    triggerSelectFile() {
      this.$refs.fileInput.click();
    },
    onFileChange(e) {
      const file = e.target.files[0];
      if (!file) return;
      this.form.building_image = file;
      const reader = new FileReader();
      reader.onload = (e) => {
        this.imagePreview = e.target.result;
      };
      reader.readAsDataURL(file);
    },
    handleRemove() {
      this.form.building_image = null;
      this.imagePreview = null;
      this.$refs.fileInput.value = "";
    },
    validate() {
      let isValid = true;
      if (!this.form.building_name) {
        this.errors.building_name = "Wajib diisi";
        isValid = false;
      }
      if (!this.form.building_code) {
        this.errors.building_code = "Wajib diisi";
        isValid = false;
      }
      if (!this.form.building_location) {
        this.errors.building_location = "Wajib diisi";
        isValid = false;
      }
      return isValid;
    },
    getData() {
      return this.form;
    },
  },
};
</script>