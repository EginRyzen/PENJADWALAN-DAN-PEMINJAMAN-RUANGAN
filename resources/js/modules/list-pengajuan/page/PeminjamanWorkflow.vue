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
          title: "BM Create CM",
          status: "success",
          timestamp: "18/09/2024 20:25:30",
          performerLabel: "Dibuat oleh",
          performerName: "mb_bm_mb - BMnya MB"
        },
        {
          title: "Waiting For Approval 1",
          status: "pending",
          timestamp: "18/09/2024 20:25:30",
          performerLabel: "Diajukan oleh",
          performerName: "mb_bm_mb - BMnya MB"
        },
        {
          title: "Correction",
          status: "correction",
          timestamp: "18/09/2024 20:43:58",
          performerLabel: "Dikoreksi oleh",
          performerName: "opr_kcu_0009 - OPR KCU",
          commentLabel: "Alasan",
          comment: "Coba cek di gudang siapa tau ada"
        },
        {
          title: "Waiting For Approval 1",
          status: "pending",
          timestamp: "18/09/2024 21:26:33",
          performerLabel: "Diajukan oleh",
          performerName: "mb_bm_mb - BMnya MB"
        },
        {
          title: "Approved",
          status: "success",
          timestamp: "18/09/2024 21:27:58",
          performerLabel: "Disetujui oleh",
          performerName: "opr_kcu_0009 - OPR KCU",
          commentLabel: "Komentar",
          comment: "Baik, lanjutkan"
        },
        {
          title: "Waiting For Approval 2",
          status: "pending",
          timestamp: "18/09/2024 21:27:58",
          performerLabel: "Diajukan oleh",
          performerName: "opr_kcu_0009 - OPR KCU"
        },
        {
          title: "Correction",
          status: "correction",
          timestamp: "19/09/2024 10:15:20",
          performerLabel: "Dikoreksi oleh",
          performerName: "mgr_pms_001 - Manager PMS",
          commentLabel: "Alasan",
          comment: "Dokumen bukti peminjaman belum lengkap."
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

