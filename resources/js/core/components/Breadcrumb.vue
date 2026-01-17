<template>
  <div class="breadcrumb flex gap-2 text-md">
    <template v-for="(item, index) in items" :key="`bc-${index}`">
      <router-link
        :to="item.link"
        class="font-semibold"
        :class="{
          'duration-200 hover:text-teal-400': true,
          'text-gray-500': index < items.length - 1,
          'text-teal-400': index === items.length - 1,
        }"
      >
        {{ item.text }}
      </router-link>
      
      <span
        class="text-gray-light"
        v-if="index < items.length - 1"
      >
        >
      </span>
    </template>
  </div>
</template>

<script>
export default {
  name: "Breadcrumb",
  props: {
    items: {
      default: () => [
        {
          link: "#",
          text: "Top Level",
        },
        {
          link: "#",
          text: "Middle Level",
        },
        {
          link: "#",
          text: "Lowest Level",
        },
      ],
      type: Array,
    },
  },
};
</script>

<style scoped>
/* .breadcrumb {
  margin-top: 20px;
} */
</style>
