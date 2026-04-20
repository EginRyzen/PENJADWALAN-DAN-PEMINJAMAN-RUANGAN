<template>
  <div class="p-4 md:p-6">
    <!-- Breadcrumb -->
    <breadcrumb-bima :items="breadcrumbItems" class="mb-6" />

    <!-- Page Title -->
    <div class="flex items-center gap-3 mb-6">
      <div class="p-2.5 bg-teal-500 rounded-xl shadow-lg shadow-teal-200">
        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04M12 21.48c-3.12-1.312-5.787-3.321-7.536-5.816M12 21.48c3.12-1.312 5.787-3.321 7.536-5.816M12 21.48V10.74M12 2.944v4.544"/>
        </svg>
      </div>
      <div>
        <h1 class="text-xl font-black text-gray-800">Hak Akses Menu</h1>
        <p class="text-xs text-gray-500 mt-0.5">Atur menu yang dapat diakses oleh setiap role user</p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
      <!-- Left: Role List -->
      <div class="lg:col-span-1 space-y-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
          <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Pilih Role
          </h3>
          <div class="space-y-1">
            <button
              v-for="role in roles"
              :key="role.id"
              @click="selectRole(role)"
              class="w-full text-left px-3 py-2.5 rounded-lg text-sm transition-all duration-200 flex items-center justify-between group"
              :class="selectedRole?.id === role.id ? 'bg-teal-500 text-white font-bold shadow-md shadow-teal-100' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-600'"
            >
              <span>{{ role.name_role }}</span>
              <svg v-if="selectedRole?.id === role.id" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </button>
          </div>
        </div>

        <div v-if="selectedRole" class="bg-teal-50 rounded-xl p-4 border border-teal-100">
          <div class="flex items-center gap-2 text-teal-700 font-bold text-sm mb-1">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Informasi
          </div>
          <p class="text-[11px] text-teal-600 leading-relaxed">
            Mencentang menu akan memberikan hak akses kepada role <strong>{{ selectedRole.name_role }}</strong>. Jangan lupa klik <strong class="text-teal-700">Simpan Perubahan</strong>.
          </p>
        </div>
      </div>

      <!-- Right: Menu Tree Checklist -->
      <div class="lg:col-span-3">
        <div v-if="!selectedRole" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 flex flex-col items-center justify-center text-center">
          <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
            </svg>
          </div>
          <h3 class="text-gray-500 font-medium">Silakan pilih role terlebih dahulu di sebelah kiri untuk mengatur hak akses menu.</h3>
        </div>

        <div v-else class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden flex flex-col h-[calc(100vh-180px)]">
          <div class="px-6 py-5 border-b border-gray-100 bg-white flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
              <h3 class="text-base font-black text-gray-800">Daftar Struktur Menu</h3>
              <p class="text-[11px] text-gray-400 font-medium tracking-wide">Pilih menu yang akan diberikan akses untuk role ini</p>
            </div>
            <div class="flex items-center gap-2">
              <button @click="selectAll" class="px-3 py-1.5 rounded-lg bg-teal-50 text-teal-600 text-xs font-bold hover:bg-teal-100 transition-colors">Pilih Semua</button>
              <button @click="deselectAll" class="px-3 py-1.5 rounded-lg bg-gray-50 text-gray-500 text-xs font-bold hover:bg-gray-100 transition-colors">Hapus Semua</button>
            </div>
          </div>

          <div class="p-6 overflow-y-auto flex-1 custom-scrollbar bg-gray-50/20">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
              <div v-for="menu in menuList" :key="menu.id" class="space-y-3">
                <!-- Parent Menu (Level 1) -->
                <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 bg-white hover:border-teal-200 transition-all group shadow-sm">
                  <checkbox-app
                    :model-value="assignedMenuIds.includes(menu.id)"
                    @change="toggleMenu(menu.id)"
                  >
                    <span class="text-[13px] font-black text-gray-800 uppercase tracking-tight">{{ menu.menu_name }}</span>
                    <span class="ml-2 text-[10px] px-1.5 py-0.5 rounded bg-gray-50 text-gray-400 font-bold uppercase tracking-tight">{{ menu.menu_code }}</span>
                  </checkbox-app>
                </div>

                <!-- Children (Level 2) -->
                <div v-if="menu.children && menu.children.length > 0" class="pl-8 space-y-4">
                  <div v-for="child in menu.children" :key="child.id" class="space-y-2.5">
                    <div class="flex items-center gap-2 py-1 group">
                      <checkbox-app
                        :model-value="assignedMenuIds.includes(child.id)"
                        :disabled="!assignedMenuIds.includes(menu.id)"
                        @change="toggleMenu(child.id)"
                      >
                        <span class="text-[13px] font-bold transition-colors" :class="assignedMenuIds.includes(menu.id) ? 'text-gray-700 group-hover:text-teal-600' : 'text-gray-300 italic'">
                          {{ child.menu_name }}
                        </span>
                      </checkbox-app>
                    </div>

                    <!-- Grandchildren (Level 3) -->
                    <div v-if="child.children && child.children.length > 0" class="pl-6 space-y-2 border-l-2 border-gray-100 ml-1.5">
                      <div v-for="grandchild in child.children" :key="grandchild.id" class="flex items-center gap-2 py-0.5 group italic">
                        <checkbox-app
                          :model-value="assignedMenuIds.includes(grandchild.id)"
                          :disabled="!assignedMenuIds.includes(child.id)"
                          @change="toggleMenu(grandchild.id)"
                        >
                          <span class="text-xs transition-colors" :class="assignedMenuIds.includes(child.id) ? 'text-gray-500 group-hover:text-teal-500' : 'text-gray-300'">
                            {{ grandchild.menu_name }}
                          </span>
                        </checkbox-app>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="px-6 py-5 bg-white border-t border-gray-100 flex items-center justify-between shadow-[0_-4px_20px_-5px_rgba(0,0,0,0.05)]">
            <div class="text-[11px] text-gray-400 font-medium italic hidden md:block">
              * Perubahan akan langsung berdampak pada user dengan role ini.
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
              <button
                @click="handleCancel"
                :disabled="isSaving"
                class="flex-1 md:flex-none px-6 h-10 rounded-xl border border-gray-200 text-gray-500 font-bold text-sm hover:bg-gray-50 transition-colors disabled:opacity-50"
              >
                Batal
              </button>
              <button
                @click="handleSave"
                :disabled="isSaving"
                class="flex-1 md:min-w-[200px] h-10 rounded-xl bg-teal-500 text-white font-bold text-sm hover:bg-teal-600 shadow-lg shadow-teal-100 transition-all flex items-center justify-center disabled:opacity-50"
              >
                <template v-if="isSaving">
                  <svg class="animate-spin h-4 w-4 text-white mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Menyimpan...
                </template>
                <span v-else>Simpan Perubahan</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Success Modal -->
    <modal-pop-up-success
      v-model="showSuccess"
      title="Berhasil Disimpan"
      description="Hak akses menu untuk role ini telah berhasil diperbarui."
    />
  </div>
</template>

<script>
import BreadcrumbBima from '@/core/components/Breadcrumb.vue';
import ButtonApp from '@/core/components/Button.vue';
import CheckboxApp from '@/core/components/Checkbox.vue';
import ModalPopUpSuccess from '@/core/components/ModalPopUpSuccess.vue';
import DISPATCHES from '@/core/plugins/constants/dispatches.js';

export default {
  name: 'RoleMenuManagement',
  components: { 
    BreadcrumbBima, 
    ButtonApp,
    CheckboxApp,
    ModalPopUpSuccess 
  },
  data() {
    return {
      selectedRole: null,
      assignedMenuIds: [],
      originalAssignedMenuIds: [],
      isSaving: false,
      showSuccess: false,
      breadcrumbItems: [
        { text: 'Settings', link: '#' },
        { text: 'Hak Akses Menu', link: '/app/pengaturan-role-menu' },
      ],
    };
  },
  computed: {
    roles() {
      // In a real app, this might come from a dedicated role store,
      // but here we get it from GET_ROLE_MENUS without params
      return this.$store.state.settings.roleMenuList || [];
    },
    menuList() {
      // All menus in tree structure
      return this.$store.state.settings.menuList || [];
    },
    allMenuIds() {
      const ids = [];
      const traverse = (list) => {
        list.forEach(m => {
          ids.push(m.id);
          if (m.children) traverse(m.children);
        });
      };
      traverse(this.menuList);
      return ids;
    }
  },
  mounted() {
    this.fetchInitialData();
  },
  methods: {
    async fetchInitialData() {
      this.$store.commit('SET_LOADING', true);
      try {
        await Promise.all([
          this.$store.dispatch(DISPATCHES.GET_ROLE_MENUS), // Get roles
          this.$store.dispatch(DISPATCHES.GET_MENUS, { tree: true }) // Get menus
        ]);
      } catch (err) {
        console.error(err);
      } finally {
        this.$store.commit('SET_LOADING', false);
      }
    },
    async selectRole(role) {
      if (!role) return;
      this.selectedRole = role;
      this.$store.commit('SET_LOADING', true);
      try {
        const res = await this.$store.dispatch(DISPATCHES.GET_ROLE_MENUS, { role_id: role.id });
        // Tambahkan pengecekan null/undefined sebelum filter
        if (res && res.menus) {
          const ids = res.menus.filter(m => m.is_assigned).map(m => m.id);
          this.assignedMenuIds = [...ids];
          this.originalAssignedMenuIds = [...ids];
        } else {
          this.assignedMenuIds = [];
          this.originalAssignedMenuIds = [];
        }
      } catch (err) {
        console.error('Error fetching role menus:', err);
        this.assignedMenuIds = [];
      } finally {
        this.$store.commit('SET_LOADING', false);
      }
    },
    isAssigned(id) {
      return this.assignedMenuIds.includes(id);
    },
    toggleMenu(id) {
      const index = this.assignedMenuIds.indexOf(id);
      if (index === -1) {
        this.assignedMenuIds.push(id);
      } else {
        this.assignedMenuIds.splice(index, 1);
      }
    },
    selectAll() {
      this.assignedMenuIds = [...this.allMenuIds];
    },
    deselectAll() {
      this.assignedMenuIds = [];
    },
    handleCancel() {
      this.selectedRole = null;
      this.assignedMenuIds = [];
      this.originalAssignedMenuIds = [];
      // Pastikan daftar role tetap ada dengan fetch ulang jika perlu (atau biarkan state store)
      // Jika role hilang, panggil fetchInitialData lagi
      this.fetchInitialData();
    },
    async handleSave() {
      if (!this.selectedRole) return;
      this.isSaving = true;
      try {
        await this.$store.dispatch(DISPATCHES.UPDATE_ROLE_MENUS, {
          role_id: this.selectedRole.id,
          menu_ids: this.assignedMenuIds
        });
        this.showSuccess = true;
      } catch (err) {
        console.error(err);
      } finally {
        this.isSaving = false;
      }
    }
  }
};
</script>
