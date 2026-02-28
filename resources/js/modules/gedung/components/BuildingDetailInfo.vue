<template>
  <div class="mb-12">
    <div class="flex items-center gap-2 mb-6">
      <div class="w-1.5 h-6 bg-teal-500 rounded-full"></div>
      <h3 class="text-lg font-bold text-gray-800">Informasi Utama Gedung</h3>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
      <div class="lg:col-span-1">
        <label class="text-sm font-medium mb-2 block text-gray-700">Foto Gedung</label>
        <div class="relative rounded-xl overflow-hidden h-52 bg-gray-100 border border-gray-200 shadow-sm group">
          <img v-if="imagePreview" :src="imagePreview"
            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
          <div v-else class="flex flex-col items-center justify-center h-full text-gray-400">
            <font-awesome-icon icon="image" class="text-4xl mb-2" />
            <p class="text-xs font-bold uppercase tracking-wider">Tanpa Foto</p>
          </div>

          <div v-if="imagePreview"
            class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
            <button type="button" @click="$emit('preview-image', imagePreview)"
              class="bg-white/90 p-3 rounded-full text-teal-600 hover:bg-white transition-colors shadow-lg">
              <font-awesome-icon icon="eye" />
            </button>
          </div>
        </div>
      </div>

      <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
        <AppInput label="Kode Gedung" v-model="form.building_code" :disabled="true" />

        <AppInput label="Nama Gedung" v-model="form.building_name" :disabled="true" />

        <AppInput label="Lokasi Gedung" v-model="form.building_location" class="md:col-span-2" :disabled="true" />
      </div>
    </div>
  </div>
</template>

<script>
import AppInput from "@/core/components/AppInput.vue";

export default {
  name: "BuildingDetailInfo",
  components: {
    AppInput
  },
  data() {
    return {
      form: {
        building_name: "",
        building_code: "",
        building_location: ""
      },
      imagePreview: null
    };
  }
};
</script>