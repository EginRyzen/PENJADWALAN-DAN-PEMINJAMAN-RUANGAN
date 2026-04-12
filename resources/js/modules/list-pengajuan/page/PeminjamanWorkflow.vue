<template>
  <div class="h-full bg-slate-50 min-h-screen font-display pb-20">
    <div class="max-w-full mx-auto px-4 md:px-8 pt-6 pb-2">
      <breadcrumb :items="breadcrumbs" class="hidden md:block"></breadcrumb>
    </div>

    <!-- Header Navigation -->
    <div class="max-w-full mx-auto px-4 md:px-8 pb-16">
      <!-- Mobile Back Button -->
      <div class="block md:hidden -mt-10 mb-6">
        <div
          @click="goBack"
          class="inline-flex items-center cursor-pointer text-teal-600 font-bold bg-white/50 backdrop-blur-sm px-4 py-2 rounded-full shadow-sm border border-teal-50"
        >
          <font-awesome-icon icon="arrow-left" class="mr-2" />
          Kembali
        </div>
      </div>

      <!-- Header Section -->
      <div class="relative flex flex-col md:flex-row items-center justify-center mb-10 gap-4">
        <!-- Back Button - Desktop Only -->
        <div class="hidden md:block md:absolute md:left-0">
          <div
            @click="goBack"
            class="text-teal-600 font-semibold flex items-center cursor-pointer hover:text-teal-700 transition-colors"
          >
            <font-awesome-icon icon="arrow-left" class="mr-2" />
            Kembali
          </div>
        </div>
        
        <!-- Centered Title -->
        <div class="text-center">
          <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">
            Workflow
          </h1>
        </div>
      </div>

      <!-- Main Content Card -->
      <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-4 md:p-12 w-full">
        <workflow-timeline :steps="workflowSteps" />
      </div>
    </div>
  </div>
</template>

<script>
import Breadcrumb from "@/core/components/Breadcrumb.vue";
import WorkflowTimeline from "../components/WorkflowTimeline.vue";

export default {
  name: "PeminjamanWorkflow",
  components: {
    Breadcrumb,
    WorkflowTimeline
  },
  data() {
    return {
      breadcrumbs: [
        { text: "Gedung", link: "#" },
        { text: "List Peminjaman Ruangan", link: "/app/list-peminjaman-ruangan" },
        { text: "Workflow Peminjaman", link: "#" },
      ],
      workflowSteps: [
        {
          title: "DRAFT_PENGAJUAN",
          status: "success",
          timestamp: "18/09/2024 20:25:30",
          performerLabel: "Dibuat oleh",
          performerName: "mb_bm_mb - BMnya MB"
        },
        {
          title: "VERIFIKASI_TU",
          status: "pending",
          timestamp: "18/09/2024 20:25:30",
          performerLabel: "Diajukan oleh",
          performerName: "mb_bm_mb - BMnya MB",
          targetName: "tu_peminjaman - TU Peminjaman"
        },
        {
          title: "DISETUJUI",
          status: "success",
          timestamp: "18/09/2024 21:27:58",
          performerLabel: "Disetujui oleh",
          performerName: "tu_peminjaman - TU Peminjaman",
          commentLabel: "Komentar",
          comment: "Baik, ruangan tersedia dan bisa digunakan."
        }
      ]
    };
  },
  methods: {
    goBack() {
      this.$router.go(-1);
    }
  }
};
</script>

