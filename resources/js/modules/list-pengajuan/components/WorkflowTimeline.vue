<template>
  <div class="workflow-container">
    <div class="relative">
      <!-- Vertical Line -->
      <div 
        class="absolute left-[19px] top-6 bottom-6 w-1.5 bg-teal-400/30 rounded-full"
        style="z-index: 0;"
      ></div>

      <!-- Steps -->
      <div class="space-y-10 relative" style="z-index: 1;">
        <div 
          v-for="(step, index) in steps" 
          :key="index" 
          class="relative flex items-start gap-6"
        >
          <!-- Node Icon -->
          <div class="shrink-0 mt-0.5">
            <div 
              class="w-11 h-11 rounded-full flex items-center justify-center shadow-lg"
              :class="getNodeClass(step.status)"
            >
              <font-awesome-icon 
                :icon="getIcon(step.status)" 
                class="text-2xl text-white" 
              />
            </div>
          </div>

          <!-- Content -->
          <div class="flex-1">
            <h3 class="text-base md:text-lg font-bold text-slate-800 mb-3 md:mb-2 leading-tight">
              {{ step.title }}
            </h3>

            <div class="flex flex-col gap-3 md:gap-1 text-[12px] md:text-[13px] font-medium text-slate-500">
              <!-- Performer Row -->
              <div class="flex flex-col md:flex-row md:items-start gap-1 md:gap-2">
                <span class="md:min-w-[140px] tabular-nums text-slate-400 flex items-center gap-1.5">
                  <font-awesome-icon icon="clock" class="md:hidden text-[10px]" />
                  {{ step.timestamp }}
                </span>
                <div class="flex items-start gap-2">
                  <span class="min-w-[90px] md:min-w-[100px] text-slate-400 md:text-slate-500">{{ step.performerLabel }}</span>
                  <span class="text-slate-700 flex-1">
                    <span class="hidden md:inline mr-2">:</span>
                    <span class="md:hidden">: </span>{{ step.performerName }}
                  </span>
                </div>
              </div>

              <!-- Comment Row -->
              <div v-if="step.comment" class="flex flex-col md:flex-row md:items-start gap-1 md:gap-2">
                <span class="hidden md:block md:min-w-[140px]"></span>
                <div class="flex items-start gap-2">
                  <span class="min-w-[90px] md:min-w-[100px] text-slate-400 md:text-slate-500">{{ step.commentLabel }}</span>
                  <span class="text-slate-700 flex-1 italic md:not-italic">
                    <span class="hidden md:inline mr-2">:</span>
                    <span class="md:hidden">: </span>{{ step.comment }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "WorkflowTimeline",
  props: {
    steps: {
      type: Array,
      required: true
    }
  },
  methods: {
    getNodeClass(status) {
      status = status.toLowerCase();
      if (status.includes('reject') || status.includes('koreksi') || status.includes('correction')) {
        return 'bg-[#EF4444] shadow-red-500/20';
      }
      return 'bg-[#2DD4BF] shadow-teal-500/20';
    },
    getIcon(status) {
      status = status.toLowerCase();
      if (status.includes('reject') || status.includes('koreksi') || status.includes('correction')) {
        return 'times-circle';
      }
      return 'check-circle';
    }
  }
};
</script>
