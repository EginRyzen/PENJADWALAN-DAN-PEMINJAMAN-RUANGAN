<template>
  <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 md:p-10 mb-8 font-display">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
      <!-- Request Number -->
      <div class="space-y-1">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-tighter">No. Pengajuan</p>
        <router-link 
          v-if="form.id"
          :to="{ name: 'peminjaman.workflow', params: { id: form.id } }" 
          class="inline-block group outline-none"
        >
          <h2 class="text-xl md:text-lg font-extrabold text-blue-600 group-hover:text-blue-800 group-hover:underline leading-none transition-all">
            {{ form.no_pengajuan }}
          </h2>
        </router-link>
        <h2 v-else class="text-xl md:text-lg font-extrabold text-slate-400 italic leading-none">
          {{ form.no_pengajuan }}
        </h2>
      </div>
      
      <!-- Status Badge -->
      <div class="space-y-2">
         <p class="text-xs font-bold text-slate-400 uppercase tracking-tighter">Status</p>
         <div 
           class="px-4 py-1.5 rounded-lg text-sm font-bold shadow-sm inline-block"
           :style="getStatusStyle(form.status?.nama_status)"
         >
           {{ form.status?.nama_status || "Pending" }}
         </div>
      </div>

      <!-- Submission Date -->
      <div class="space-y-1">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-tighter">Tanggal Pengajuan</p>
        <p class="text-sm font-bold text-slate-700">{{ formatDate(form.created_at) }}</p>
      </div>

      <!-- Applicant Info -->
      <div class="space-y-1">
        <p class="text-xs font-bold text-slate-400 uppercase tracking-tighter">Diajukan Oleh</p>
        <div class="flex flex-col">
          <span class="text-sm font-bold text-slate-700 leading-tight">{{ form.user?.name || "-" }}</span>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5" v-if="form.user?.role?.name">
            {{ form.user.role.name }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment";

export default {
  name: "DetailHeader",
  props: {
    form: {
      type: Object,
      required: true
    },
    getStatusStyle: {
      type: Function,
      required: true
    }
  },
  methods: {
    formatDate(date) {
      if (!date) return "-";
      return moment(date).format("DD/MM/YYYY");
    }
  }
};
</script>