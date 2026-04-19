<template>
  <div class="p-4 md:p-6">
    <!-- Breadcrumb -->
    <breadcrumb-bima :items="breadcrumbItems" class="mb-6" />

    <!-- Page Title -->
    <div class="flex items-center gap-3 mb-6">
      <div class="p-2.5 bg-teal-500 rounded-xl shadow-lg shadow-teal-200">
        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
        </svg>
      </div>
      <div>
        <h1 class="text-xl font-black text-gray-800">Manajemen Menu</h1>
        <p class="text-xs text-gray-500 mt-0.5">Kelola struktur menu aplikasi, urutan, dan visibilitas perangkat</p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left: Form -->
      <div class="lg:col-span-1 space-y-5">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sticky top-6">
          <div class="flex items-center gap-2 mb-5">
            <div class="p-1.5 bg-teal-50 rounded-lg">
              <svg class="w-4 h-4 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path v-if="isEdit" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
            </div>
            <h3 class="text-sm font-bold text-gray-800">{{ isEdit ? 'Edit Menu' : 'Tambah Menu' }}</h3>
          </div>

          <div class="space-y-4">
            <!-- Parent Menu -->
            <select-auto-complete
              v-model="form.parent_id"
              label="Induk Menu"
              placeholder="-- Sebagai Menu Utama --"
              :options="[{ id: null, menu_name: '-- Sebagai Menu Utama --' }, ...allMenusFlat.filter(m => m.id !== form.id)]"
              item-value="id"
              item-text="menu_name"
            />

            <!-- Code & Name -->
            <div class="grid grid-cols-2 gap-3">
              <app-input
                v-model="form.menu_code"
                label="Kode Menu"
                placeholder="cth: M001"
                :required="true"
              />
              <app-input
                v-model="form.menu_name"
                label="Nama Menu"
                placeholder="cth: Dashboard"
                :required="true"
              />
            </div>

            <!-- ID Alias & Sequence -->
            <div class="grid grid-cols-2 gap-3">
              <app-input
                v-model="form.menu_id_alias"
                label="ID Alias (Routing)"
                placeholder="cth: dashboard"
              />
              <app-input
                v-model.number="form.sequence"
                type="number"
                label="Urutan"
              />
            </div>

            <!-- Platform -->
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-2">Tersedia di Platform</label>
              <div class="flex gap-4">
                <checkbox-app v-model="form.is_desktop">
                  Desktop
                </checkbox-app>
                <checkbox-app v-model="form.is_mobile">
                  Mobile
                </checkbox-app>
              </div>
            </div>

            <!-- Description -->
            <textarea-app
              v-model="form.menu_desc"
              label="Deskripsi"
              :rows="2"
            />

            <p v-if="formError" class="text-xs text-red-500 flex items-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/>
              </svg>
              {{ formError }}
            </p>

            <div class="flex gap-2 mt-2">
              <button
                v-if="isEdit"
                @click="resetForm"
                class="flex-1 h-11 flex items-center justify-center rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 transition"
              >
                Batal
              </button>
              <button
                @click="handleSave"
                :disabled="isSaving"
                class="flex-1 h-11 flex items-center justify-center rounded-xl bg-teal-500 text-white font-bold hover:bg-teal-600 shadow-lg shadow-teal-100 transition disabled:opacity-50"
              >
                <template v-if="isSaving">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Menyimpan...
                </template>
                <span v-else>{{ isEdit ? 'Simpan Perubahan' : 'Tambah Menu' }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Right: Table Section -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden flex flex-col h-[calc(100vh-180px)]">
          <div class="px-6 py-5 border-b border-gray-100 bg-white flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
              <h3 class="text-base font-black text-gray-800">Daftar Struktur Menu</h3>
              <p class="text-[11px] text-gray-400 font-medium tracking-wide">Kelola navigasi dan urutan menu aplikasi</p>
            </div>
            <div class="relative w-full md:w-64">
              <input
                v-model="search"
                type="text"
                placeholder="Cari menu..."
                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all outline-none font-medium"
              />
              <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>
          </div>

          <div class="overflow-y-auto flex-1 custom-scrollbar">
            <table class="w-full border-collapse table-fixed">
              <thead class="sticky top-0 z-20 bg-gray-50/95 backdrop-blur-sm border-b border-gray-100">
                <tr>
                  <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Menu & Kode</th>
                  <th class="px-4 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] w-32">Platform</th>
                  <th class="px-4 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] w-24">Urutan</th>
                  <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] w-32">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                <template v-for="menu in filteredMenus" :key="menu.id">
                  <!-- Level 1: Parent -->
                  <tr class="bg-white hover:bg-teal-50/30 transition-colors group border-l-4 border-l-teal-500">
                    <td class="px-6 py-5">
                      <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center shadow-sm">
                          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                        </div>
                        <div>
                          <div class="text-[13px] font-black text-gray-800 uppercase tracking-tight">{{ menu.menu_name }}</div>
                          <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ menu.menu_code }} <span v-if="menu.menu_id_alias" class="normal-case font-medium ml-1 opacity-60">• {{ menu.menu_id_alias }}</span></div>
                        </div>
                      </div>
                    </td>
                    <td class="px-4 py-5">
                      <div class="flex items-center justify-center gap-2">
                        <div v-if="menu.is_desktop" class="w-7 h-7 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center border border-blue-100/50" title="Desktop">
                          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div v-if="menu.is_mobile" class="w-7 h-7 rounded-lg bg-purple-50 text-purple-500 flex items-center justify-center border border-purple-100/50" title="Mobile">
                          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                      </div>
                    </td>
                    <td class="px-4 py-5 text-center">
                      <span class="text-sm font-black text-gray-700">{{ menu.sequence }}</span>
                    </td>
                    <td class="px-6 py-5 text-right">
                      <div class="flex items-center justify-end gap-2">
                        <button @click="editMenu(menu)" class="w-8 h-8 rounded-lg bg-white border border-gray-100 text-teal-600 hover:bg-teal-50 transition-colors flex items-center justify-center shadow-sm">
                          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        <button @click="confirmDelete(menu)" class="w-8 h-8 rounded-lg bg-white border border-gray-100 text-red-500 hover:bg-red-50 transition-colors flex items-center justify-center shadow-sm">
                          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0a1 1 0 00-1-1h-4a1 1 0 00-1 1m6 0H7"/></svg>
                        </button>
                      </div>
                    </td>
                  </tr>

                  <template v-for="child in menu.children" :key="child.id">
                    <!-- Level 2: Child -->
                    <tr class="bg-gray-50/10 hover:bg-gray-50 transition-colors group">
                      <td class="px-6 py-4 pl-14">
                        <div class="flex items-center gap-3">
                          <div class="w-2 h-0.5 bg-gray-300 rounded-full"></div>
                          <div>
                            <div class="text-[13px] font-bold text-gray-700 leading-none mb-1">{{ child.menu_name }}</div>
                            <div class="text-[9px] text-gray-400 font-bold uppercase">{{ child.menu_code }}</div>
                          </div>
                        </div>
                      </td>
                      <td class="px-4 py-4">
                        <div class="flex items-center justify-center gap-2 opacity-50">
                          <div v-if="child.is_desktop" class="w-6 h-6 rounded-md bg-white border border-gray-100 text-blue-400 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                          </div>
                          <div v-if="child.is_mobile" class="w-6 h-6 rounded-md bg-white border border-gray-100 text-purple-400 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                          </div>
                        </div>
                      </td>
                      <td class="px-4 py-4 text-center">
                        <span class="text-xs font-bold text-gray-400">{{ child.sequence }}</span>
                      </td>
                      <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                          <button @click="editMenu(child)" class="w-7 h-7 rounded-lg bg-white border border-gray-100 text-teal-600 hover:bg-teal-500 hover:text-white transition-all flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                          </button>
                          <button @click="confirmDelete(child)" class="w-7 h-7 rounded-lg bg-white border border-gray-100 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0a1 1 0 00-1-1h-4a1 1 0 00-1 1m6 0H7"/></svg>
                          </button>
                        </div>
                      </td>
                    </tr>

                    <!-- Level 3: Grandchild -->
                    <tr v-for="grandchild in child.children" :key="grandchild.id" class="bg-gray-100/10 hover:bg-gray-100/30 transition-colors group italic">
                      <td class="px-6 py-3 pl-20">
                        <div class="flex items-center gap-3">
                          <svg class="w-3 h-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                          <div>
                            <div class="text-[13px] font-medium text-gray-600 leading-none mb-1">{{ grandchild.menu_name }}</div>
                            <div class="text-[8px] text-gray-400 font-black tracking-tighter uppercase">{{ grandchild.menu_code }}</div>
                          </div>
                        </div>
                      </td>
                      <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-2 opacity-30 scale-90">
                          <div v-if="grandchild.is_desktop" class="w-6 h-6 rounded bg-white border border-gray-100 text-blue-400 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                          </div>
                          <div v-if="grandchild.is_mobile" class="w-6 h-6 rounded bg-white border border-gray-200 text-purple-400 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                          </div>
                        </div>
                      </td>
                      <td class="px-4 py-3 text-center">
                        <span class="text-[10px] font-bold text-gray-300">{{ grandchild.sequence }}</span>
                      </td>
                      <td class="px-6 py-3 text-right">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                          <button @click="editMenu(grandchild)" class="w-6 h-6 rounded bg-white border border-gray-100 text-teal-600 hover:bg-teal-500 hover:text-white transition-all flex items-center justify-center">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                          </button>
                          <button @click="confirmDelete(grandchild)" class="w-6 h-6 rounded bg-white border border-gray-100 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0a1 1 0 00-1-1h-4a1 1 0 00-1 1m6 0H7"/></svg>
                          </button>
                        </div>
                      </td>
                    </tr>
                  </template>
                </template>
              </tbody>
            </table>

            <!-- Empty State -->
            <div v-if="filteredMenus.length === 0" class="flex flex-col items-center justify-center py-20 px-6 text-center">
              <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <h4 class="text-gray-400 font-bold">Menu tidak ditemukan</h4>
              <p class="text-[11px] text-gray-300">Coba gunakan kata kunci pencarian yang berbeda</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirm Modal -->
    <modal-pop-up-confirm
      v-model="showConfirm"
      title="Hapus Menu?"
      :description="`Apakah Anda yakin ingin menghapus menu '${deletingItem?.menu_name}'? Ini juga akan menghapus sub-menu di dalamnya.`"
      @confirm="handleHapus"
    />

    <!-- Success Modal -->
    <modal-pop-up-success
      v-model="showSuccess"
      :title="successData.title"
      :description="successData.description"
    />
  </div>
</template>

<script>
import BreadcrumbBima from '@/core/components/Breadcrumb.vue';
import ModalPopUpConfirm from '@/core/components/ModalPopUpConfirm.vue';
import ModalPopUpSuccess from '@/core/components/ModalPopUpSuccess.vue';
import AppInput from '@/core/components/AppInput.vue';
import SelectAutoComplete from '@/core/components/SelectAutoComplete.vue';
import ButtonApp from '@/core/components/Button.vue';
import TextareaApp from '@/core/components/Textarea.vue';
import CheckboxApp from '@/core/components/Checkbox.vue';
import TableApp from '@/core/components/Table.vue';
import DISPATCHES from '@/core/plugins/constants/dispatches.js';

export default {
  name: 'MenuManagement',
  components: { 
    BreadcrumbBima, 
    ModalPopUpConfirm, 
    ModalPopUpSuccess,
    AppInput,
    SelectAutoComplete,
    ButtonApp,
    TextareaApp,
    CheckboxApp,
    TableApp
  },
  data() {
    return {
      search: '',
      isSaving: false,
      isEdit: false,
      form: {
        id: null,
        parent_id: null,
        menu_code: '',
        menu_name: '',
        menu_id_alias: '',
        menu_desc: '',
        sequence: 0,
        is_desktop: true,
        is_mobile: false,
      },
      formError: '',
      showConfirm: false,
      showSuccess: false,
      deletingItem: null,
      successData: { title: '', description: '' },
      breadcrumbItems: [
        { text: 'Settings', link: '#' },
        { text: 'Manajemen Menu', link: '/app/pengaturan-menu' },
      ],
      tableOptions: {
        page: 1,
        itemsPerPage: 10,
        totalItems: 0
      },
    };
  },
  computed: {
    menuTree() {
      return this.$store.state.settings.menuList || [];
    },
    allMenusFlat() {
      const flat = [];
      const traverse = (list) => {
        list.forEach(m => {
          flat.push(m);
          if (m.children && m.children.length > 0) traverse(m.children);
        });
      };
      traverse(this.menuTree);
      return flat;
    },
    filteredMenus() {
      if (!this.search) return this.menuTree;
      const s = this.search.toLowerCase();
      
      const filterRecursive = (list) => {
        return list.filter(item => {
          const match = item.menu_name.toLowerCase().includes(s) || 
                       item.menu_code.toLowerCase().includes(s);
          
          if (match) return true;
          
          if (item.children && item.children.length > 0) {
            const childrenMatch = filterRecursive(item.children);
            if (childrenMatch.length > 0) {
              item.children = childrenMatch; // Note: this mutates if not careful, but for local filtering it's often okay
              return true;
            }
          }
          return false;
        });
      };
      
      // Deep clone to avoid mutating original tree
      const treeCopy = JSON.parse(JSON.stringify(this.menuTree));
      return filterRecursive(treeCopy);
    }
  },
  mounted() {
    this.fetchMenus();
  },
  methods: {
    async fetchMenus() {
      this.$store.commit('SET_LOADING', true);
      try {
        await this.$store.dispatch(DISPATCHES.GET_MENUS, { tree: true });
      } catch (err) {
        console.error(err);
      } finally {
        this.$store.commit('SET_LOADING', false);
      }
    },
    resetForm() {
      this.isEdit = false;
      this.form = {
        id: null,
        parent_id: null,
        menu_code: '',
        menu_name: '',
        menu_id_alias: '',
        menu_desc: '',
        sequence: this.menuTree.length + 1,
        is_desktop: true,
        is_mobile: false,
      };
      this.formError = '';
    },
    editMenu(item) {
      this.isEdit = true;
      // Normalisasi data boolean dari 1/0 ke true/false untuk checkbox
      this.form = { 
        ...item,
        is_desktop: !!item.is_desktop,
        is_mobile: !!item.is_mobile,
        sequence: parseInt(item.sequence)
      };
      this.formError = '';
      window.scrollTo({ top: 0, behavior: 'smooth' });
    },
    async handleSave() {
      if (!this.form.menu_code || !this.form.menu_name) {
        this.formError = 'Kode dan Nama menu wajib diisi.';
        return;
      }

      this.isSaving = true;
      try {
        if (this.isEdit) {
          await this.$store.dispatch(DISPATCHES.UPDATE_MENU, this.form);
          this.successData = { title: 'Berhasil Diperbarui', description: `Menu ${this.form.menu_name} telah diperbarui.` };
        } else {
          await this.$store.dispatch(DISPATCHES.CREATE_MENU, this.form);
          this.successData = { title: 'Berhasil Ditambahkan', description: `Menu ${this.form.menu_name} telah ditambahkan ke sistem.` };
        }
        this.showSuccess = true;
        this.resetForm();
        this.fetchMenus();
      } catch (err) {
        this.formError = err.response?.data?.message || 'Gagal menyimpan menu.';
      } finally {
        this.isSaving = false;
      }
    },
    confirmDelete(item) {
      this.deletingItem = item;
      this.showConfirm = true;
    },
    async handleHapus() {
      if (!this.deletingItem) return;
      this.$store.commit('SET_LOADING', true);
      try {
        await this.$store.dispatch(DISPATCHES.DELETE_MENU, this.deletingItem.id);
        this.successData = { title: 'Berhasil Dihapus', description: 'Menu telah dihapus dari sistem.' };
        this.showSuccess = true;
        this.fetchMenus();
      } catch (err) {
        console.error(err);
      } finally {
        this.$store.commit('SET_LOADING', false);
      }
    }
  }
};
</script>
