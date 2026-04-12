<template>
  <div class="h-full">
    <breadcrumb :items="breadcrumbs"></breadcrumb>
    
    <!-- Desktop View -->
    <div v-if="!isMobile" class="mt-10">
      <list-peminjaman-dekstop />
    </div>

    <!-- Mobile View placeholder -->
    <div v-else class="mt-10 p-4 bg-white rounded-lg shadow">
      <list-peminjaman-mobile />
    </div>
  </div>
</template>

<script>
import Breadcrumb from "@/core/components/Breadcrumb.vue";
import ButtonApp from "@/core/components/Button.vue";
import ListPeminjamanDekstop from "../components/ListPeminjamanDekstop.vue";
import ListPeminjamanMobile from "../components/ListPeminjamanMobile.vue";

export default {
  name: "PeminjamanRuanganList",
  components: {
    Breadcrumb,
    ButtonApp,
    ListPeminjamanDekstop,
    ListPeminjamanMobile,
  },
  data() {
    return {
      isMobile: false,
      breadcrumbs: [
        { text: "Gedung", link: "#" },
        { text: "List Peminjaman Ruangan", link: "/app/list-peminjaman-ruangan" },
      ],
    };
  },
  mounted() {
    this.checkResolution();
    window.addEventListener("resize", this.checkResolution);
  },
  beforeUnmount() {
    window.removeEventListener("resize", this.checkResolution);
  },
  methods: {
    checkResolution() {
      this.isMobile = window.innerWidth < 768;
    },
  },
};
</script>
