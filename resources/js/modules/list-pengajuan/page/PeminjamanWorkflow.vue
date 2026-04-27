<template>
  <div class="h-full bg-slate-50 min-h-screen font-display pb-12 md:pb-20">
    <div class="max-w-full mx-auto px-4 md:px-8 pt-4 md:pt-6 pb-2">
      <breadcrumb :items="breadcrumbs" class="hidden md:block"></breadcrumb>
    </div>

    <!-- Header Navigation -->
    <div class="max-w-full mx-auto px-4 md:px-8 pb-10 md:pb-16">
      <!-- Mobile Header Row -->
      <div class="flex items-center justify-between md:hidden mb-8">
        <div
          @click="goBack"
          class="inline-flex items-center gap-2 cursor-pointer text-teal-600 bg-white shadow-sm border border-teal-100 px-3 py-2 rounded-xl font-semibold text-sm"
        >
          <font-awesome-icon icon="arrow-left" />
          Kembali
        </div>
        <h1 class="text-xl font-semibold text-gray-900">Workflow</h1>
        <div class="w-20"></div> <!-- Spacer for centering -->
      </div>

      <!-- Desktop Header Section -->
      <div class="hidden md:flex flex-col mb-10 gap-6">
        <!-- Back Button - Desktop -->
        <div class="flex items-center">
          <div
            @click="goBack"
            class="text-teal-600 font-semibold flex items-center cursor-pointer hover:text-teal-700 transition-colors bg-white px-4 py-2 rounded-xl shadow-sm border border-slate-50"
          >
            <font-awesome-icon icon="arrow-left" class="mr-2" />
            Kembali
          </div>
        </div>
        
        <!-- Left Aligned Title -->
        <div class="text-start">
          <h1 class="text-2xl font-semibold text-center text-gray-700 tracking-tight">
            Workflow Peminjaman
          </h1>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex flex-col items-center justify-center py-20 gap-4 md:items-start md:pl-10">
        <div class="w-12 h-12 border-4 border-teal-500 border-t-transparent rounded-full animate-spin"></div>
        <p class="text-slate-500 font-medium italic">Memuat history...</p>
      </div>

      <!-- Main Content Card -->
      <div v-else class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-slate-100 p-5 md:p-12 w-full max-w-4xl md:mx-0">
        <workflow-timeline v-if="workflowSteps.length > 0" :steps="workflowSteps" />
        <div v-else class="text-center py-10 md:text-start">
          <div class="text-slate-300 mb-4">
            <font-awesome-icon icon="history" size="3x" />
          </div>
          <p class="text-slate-500">Belum ada history untuk pengajuan ini.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Breadcrumb from "@/core/components/Breadcrumb.vue";
import WorkflowTimeline from "../components/WorkflowTimeline.vue";
import DISPATCHES from "@/core/plugins/constants/dispatches";
import { mapState } from "vuex";
import moment from "moment";

export default {
  name: "PeminjamanWorkflow",
  components: {
    Breadcrumb,
    WorkflowTimeline
  },
  data() {
    return {
      loading: false,
      breadcrumbs: [
        { text: "Dashboard", link: "/app/dashboard" },
        { text: "List Peminjaman", link: "/app/list-peminjaman-ruangan" },
        { text: "Workflow Peminjaman", link: "#" },
      ]
    };
  },
  computed: {
    ...mapState("listPengajuan", ["workflow_history"]),
    workflowId() {
      return this.$route.params.id;
    },
    workflowSteps() {
      if (!this.workflow_history) return [];
      
      const result = [];
      
      this.workflow_history.forEach(item => {
        // Mapping label performer
        let performerLabel = "Oleh";
        if (item.aksi === 'CREATED') performerLabel = "Dibuat oleh";
        else if (item.aksi === 'SUBMITTED') performerLabel = "Diajukan oleh";
        else if (['APPROVE', 'APPROVED', 'DISETUJUI', 'COMPLETED'].includes(item.aksi)) performerLabel = "Disetujui oleh";
        else if (['REJECT', 'REJECTED', 'DITOLAK'].includes(item.aksi)) performerLabel = "Ditolak oleh";
        else if (['REVISION', 'KOREKSI'].includes(item.aksi)) performerLabel = "Dikoreksi oleh";

        const timestamp = item.created_at ? moment(item.created_at).format("DD/MM/YYYY HH:mm:ss") : "-";

        // Logic for "Diajukan kepada"
        const getTargetNames = (historyItem) => {
          if (!historyItem.status?.role || historyItem.status.is_final) return [];
          const roleName = historyItem.status.role.name_role;
          const users = historyItem.status.role.users || [];
          if (users.length > 0) {
            return users.map(u => `${u.name} (${roleName})`);
          }
          return [roleName];
        };

        // Case: Approval Action (Split into two entries as requested)
        if (['APPROVE', 'APPROVED', 'DISETUJUI', 'COMPLETED'].includes(item.aksi) && item.aksi !== 'SUBMITTED') {
          // 1. Approved Milestone Entry
          result.push({
            title: 'Approved',
            status: 'success',
            timestamp: timestamp,
            performerLabel: performerLabel,
            performerName: item.user?.name || "System",
            targetNames: [],
            commentLabel: "Catatan",
            comment: item.catatan
          });

          // 2. Next Status Entry
          result.push({
            title: item.status?.nama_status || item.aksi,
            status: 'success',
            timestamp: timestamp,
            performerLabel: performerLabel,
            performerName: item.user?.name || "System",
            targetNames: getTargetNames(item),
            commentLabel: "Catatan",
            comment: null // Comment already shown in Approved step
          });
        } 
        else {
          // Standard Entry (CREATED, SUBMITTED, REJECT, etc)
          result.push({
            title: item.status?.nama_status || item.aksi,
            status: this.getStatusType(item.aksi),
            timestamp: timestamp,
            performerLabel: performerLabel,
            performerName: item.user?.name || "System",
            targetNames: item.aksi === 'SUBMITTED' ? getTargetNames(item) : [],
            commentLabel: "Catatan",
            comment: item.catatan
          });
        }
      });
      
      return result;
    }
  },
  async created() {
    await this.fetchWorkflowHistory();
  },
  methods: {
    async fetchWorkflowHistory() {
      if (!this.workflowId) return;
      this.loading = true;
      try {
        await this.$store.dispatch(DISPATCHES.GET_WORKFLOW_HISTORY, this.workflowId);
      } catch (error) {
        console.error("Failed to fetch workflow history:", error);
      } finally {
        this.loading = false;
      }
    },
    getStatusType(aksi) {
      const a = aksi.toLowerCase();
      if (a.includes('reject') || a.includes('tolak') || a.includes('revision') || a.includes('koreksi')) {
        return 'error';
      }
      return 'success';
    },
    goBack() {
      this.$router.go(-1);
    }
  }
};
</script>


