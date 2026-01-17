<template>
  <div class="h-full">
    <breadcrumb :items="breadcrumbs"></breadcrumb>
    <div class="mt-10">
      <h1 class="text-2xl font-bold mb-4">List Work Order</h1>

      <div class="overflow-x-auto">
        <table class="table w-full bg-base-100 shadow-xl rounded-box">
          <thead>
            <tr>
              <th>ID</th>
              <th>Job</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>WO-001</td>
              <td>Perbaikan AC</td>
              <td><span class="badge badge-warning">Pending</span></td>
            </tr>
          </tbody>
        </table>
      </div>

      <span class="btn btn-primary mt-4"> Buat Baru </span>
    </div>
  </div>
</template>

<script>
import Breadcrumb from "@/core/components/Breadcrumb.vue";

export default {
  name: "GedungList",
  components: {
    Breadcrumb,
  },
  data() {
    return {
      breadcrumbs: [
        {
          text: "Gedung",
        },
        {
          text: "Profile Gedung",
          link: "/app/gedung-list",
        },
      ],
    };
  },
};
// Anda bisa menambahkan logic dashboard di sini
</script>