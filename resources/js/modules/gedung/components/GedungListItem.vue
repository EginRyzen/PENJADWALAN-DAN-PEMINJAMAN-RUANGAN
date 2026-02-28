<template>
  <div
    @click="handleClick"
    class="flex flex-col rounded-lg bg-white border border-gray-100 cursor-pointer hover:shadow-lg transition-shadow duration-300"
  >
    <div class="block overflow-hidden bg-indigo-100 rounded-t-lg">
      <img
        v-if="building.image && building.image.file_path"
        :src="'/storage/' + building.image.file_path"
        :alt="building.building_name"
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
        :class="
          building.building_status === 'active'
            ? 'building-active'
            : 'building-inactive'
        "
      >
        <p class="text-[10px] z-10 font-bold uppercase tracking-wider">
          {{ building.building_status }}
        </p>
      </div>
      <div class="px-3 py-4">
        <h4
          class="font-bold truncate text-gray-800"
          :title="building.building_name"
        >
          {{ building.building_name }}
        </h4>
        <p
          class="text-sm mb-4 truncate text-gray-500"
          :title="building.building_location"
        >
          {{ building.building_location }}
        </p>
      </div>
      <router-link
        class="flex items-end text-indigo-600 font-semibold text-md px-3 mb-4 hover:text-indigo-800"
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
        branch_name: "Nama Gedung",
        regional_name: "Regional",
        activation: true,
        incomplete_image: false,
        incomplete_document: false,
      }),
    },
  },

  methods: {
    handleClick() {
      this.$router.push({
        name: "gedung.detail",
        params: { id: this.building.id },
      });
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