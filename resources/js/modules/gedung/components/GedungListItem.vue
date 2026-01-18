<template>
  <div
    class="flex flex-col rounded-lg bg-white border border-gray-100 cursor-pointer hover:shadow-lg transition-shadow duration-300"
    @click="
      $router.push({
        name: 'DetailProfileGedung',
        query: {
          branch_code: building.branch_code,
          suffix: building.suffix,
        },
      })
    "
  >
    <div class="block overflow-hidden bg-indigo-100 rounded-t-lg">
      <img
        v-if="building.url_image"
        :src="building.url_image"
        :alt="`${building.branch_name} photo`"
        class="building-image object-cover"
        style="height: 150px"
      />
      <img
        v-else
        src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=500&auto=format&fit=crop"
        alt="Image default"
        class="building-image object-cover"
        style="height: 150px"
      />
    </div>
    <div class="flex flex-col">
      <div
        class="building-activation"
        :class="{
          'building-active': building.activation,
          'building-inactive': !building.activation,
        }"
      >
        <p class="text-[10px] z-10 font-bold uppercase tracking-wider">
          {{ building.activation ? "Active" : "Inactive " }}
        </p>
      </div>
      <div class="px-3 py-4">
        <h4
          id="branch_name"
          class="font-bold truncate text-gray-800"
          :title="building.branch_name"
        >
          {{ building.branch_name }}
        </h4>
        <p
          id="regional_name"
          class="text-sm mb-4 truncate text-gray-500"
          :title="building.regional_name"
        >
          {{ building.regional_name }}
        </p>
        
        <div class="space-y-1">
          <div
            v-if="building.incomplete_image"
            class="incomplete-data"
            :title="building.incomplete_image_message"
          >
            <font-awesome-icon icon="exclamation-triangle" class="w-4 h-4 mr-2" />
            <p class="truncate">{{ building.incomplete_image_message }}</p>
          </div>
          
          <div
            v-if="building.incomplete_document"
            class="incomplete-data"
            :title="building.incomplete_document_message"
          >
            <font-awesome-icon icon="exclamation-triangle" class="w-4 h-4 mr-2" />
            <p class="truncate">{{ building.incomplete_document_message }}</p>
          </div>
        </div>
      </div>
      <router-link
        class="flex items-end text-indigo-600 font-semibold text-xs px-3 mb-4 hover:text-indigo-800"
        id="detail_link"
      >
        Lihat selengkapnya
      </router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: "GedungListItem",
  props: {
    building: {
      type: Object,
      required: true,
      default: () => ({
        branch_name: 'Nama Gedung',
        regional_name: 'Regional',
        activation: true,
        incomplete_image: false,
        incomplete_document: false
      })
    },
  },
};
</script>

<style scoped>
.incomplete-data {
  font-family: "Poppins", sans-serif;
  font-weight: 500;
  font-size: 11px;
  color: #f48c06;
  display: flex;
  align-items: center;
  height: 20px;
}
.building-activation {
  height: 25px;
  padding: 0 12px;
  color: #ffffff;
  display: flex;
  align-items: center;
}
.building-image {
  width: 100%;
  transition: transform 0.3s ease;
}
.building-image:hover {
  transform: scale(1.05);
}
#branch_name {
  height: 25px;
}
#regional_name {
  height: 18px;
}
.building-active {
  background: linear-gradient(90deg, #32aaa7 0%, #46bebb 100%);
}
.building-inactive {
  background: linear-gradient(90deg, #324259 0%, #4b5563 100%);
}
</style>