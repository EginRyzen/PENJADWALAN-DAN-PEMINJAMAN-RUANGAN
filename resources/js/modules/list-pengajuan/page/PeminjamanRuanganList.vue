<template>
  <div class="h-full">
    <!-- Desktop: show breadcrumb -->
    <div class="hidden md:block">
      <breadcrumb :items="breadcrumbs"></breadcrumb>
    </div>
    
    <!-- Desktop View -->
    <div v-if="!isMobile" class="mt-10">
      <list-peminjaman-dekstop />
    </div>

    <!-- Mobile View: full screen, no wrapper padding -->
    <div v-else class="h-full">
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
