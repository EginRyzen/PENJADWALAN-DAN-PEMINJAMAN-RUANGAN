<template>
  <!-- Mobile Bottom Navigation Bar (md:hidden) -->
  <nav class="fixed bottom-0 left-0 right-0 z-50 md:hidden bg-white border-t border-gray-100 shadow-[0_-4px_20px_rgba(0,0,0,0.06)]">
    <div class="flex items-end justify-around px-2 pt-2 pb-safe">
      <!-- Dashboard -->
      <router-link
        to="/app/dashboard"
        class="bottom-nav-item"
        :class="isActive('/app/dashboard') ? 'active' : ''"
      >
        <div class="bottom-nav-icon-wrap" :class="isActive('/app/dashboard') ? 'active' : ''">
          <font-awesome-icon icon="home" class="bottom-nav-icon" />
        </div>
        <span class="bottom-nav-label">Dashboard</span>
      </router-link>

      <!-- Dynamic Menus (Level 1) -->
      <template v-for="menu in filteredMenus" :key="menu.id">
        <button
          @click="toggleDrawer(menu)"
          class="bottom-nav-item"
          :class="isActiveGroup(menu) ? 'active' : ''"
        >
          <div class="bottom-nav-icon-wrap" :class="isActiveGroup(menu) ? 'active' : ''">
            <font-awesome-icon :icon="getMenuIcon(menu.menu_code)" class="bottom-nav-icon" />
          </div>
          <span class="bottom-nav-label">{{ menu.menu_name }}</span>
        </button>
      </template>

      <!-- Profile -->
      <button
        @click="toggleDrawer('profile')"
        class="bottom-nav-item"
        :class="activeDrawer === 'profile' ? 'active' : ''"
      >
        <div class="bottom-nav-icon-wrap" :class="activeDrawer === 'profile' ? 'active' : ''">
          <font-awesome-icon icon="user-circle" class="bottom-nav-icon" />
        </div>
        <span class="bottom-nav-label">Profil</span>
      </button>
    </div>

    <!-- Sub-menu Backdrop -->
    <transition name="fade">
      <div
        v-if="activeDrawer"
        class="fixed inset-0 bg-black/40 z-40 bottom-16"
        @click="closeDrawer"
      ></div>
    </transition>

    <!-- Sub-menu Drawer (slides up from bottom nav) -->
    <transition name="slide-up">
      <div
        v-if="activeDrawer"
        class="fixed bottom-16 left-0 right-0 z-50 bg-white rounded-t-3xl shadow-2xl border-t border-gray-100 px-5 pt-5 pb-6"
      >
        <!-- Drawer Handle -->
        <div class="flex justify-center mb-4">
          <div class="w-10 h-1 bg-gray-200 rounded-full"></div>
        </div>

        <!-- Drawer Title -->
        <p class="text-xs font-extrabold text-gray-400 uppercase tracking-widest mb-4 px-1">
          {{ drawerTitle }}
        </p>

        <!-- Dynamic Menu Drawer -->
        <template v-if="typeof activeDrawer === 'object'">
          <div class="grid grid-cols-1 gap-1">
            <template v-for="(child, childIndex) in activeDrawer.children" :key="child.id">
              <!-- If child has children (Level 3) -->
              <div v-if="child.children && child.children.length > 0" class="mb-2">
                <p class="text-[10px] font-bold text-gray-400 px-4 py-2 uppercase tracking-tight">{{ child.menu_name }}</p>
                <router-link
                  v-for="grandchild in child.children"
                  :key="grandchild.id"
                  :to="grandchild.menu_id_alias"
                  class="drawer-item ml-4"
                  @click="closeDrawer"
                >
                  <div class="drawer-icon-wrap bg-teal-50">
                    <font-awesome-icon icon="chevron-right" class="text-teal-500 text-[10px]" />
                  </div>
                  <span>{{ grandchild.menu_name }}</span>
                </router-link>
              </div>

              <!-- Regular Level 2 link -->
              <router-link
                v-else
                :to="child.menu_id_alias"
                class="drawer-item"
                @click="closeDrawer"
              >
                <div class="drawer-icon-wrap" :class="getDrawerIconBg(childIndex)">
                  <font-awesome-icon :icon="getMenuIcon(child.menu_code)" :class="getDrawerIconColor(childIndex)" />
                </div>
                <span>{{ child.menu_name }}</span>
              </router-link>
            </template>
          </div>
        </template>



        <!-- Profile Menu -->
        <template v-if="activeDrawer === 'profile'">
          <div class="flex items-center gap-4 p-4 bg-teal-50 rounded-2xl mb-4">
            <img
              class="w-12 h-12 rounded-full border-2 border-white shadow"
              src="https://avatars.githubusercontent.com/u/57622665?s=460&u=8f581f4c4acd4c18c33a87b3e6476112325e8b38&v=4"
              alt="User"
            />
            <div>
              <p class="font-bold text-gray-800 text-sm">{{ userName }}</p>
              <p class="text-xs text-gray-500">{{ userRole }}</p>
            </div>
          </div>
          <button @click="logoutUser" class="drawer-item text-red-500 w-full">
            <div class="drawer-icon-wrap bg-red-50">
              <font-awesome-icon icon="sign-out-alt" class="text-red-500" />
            </div>
            <span class="font-bold">Logout</span>
          </button>
        </template>
      </div>
    </transition>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useStore } from "vuex";
import DISPATCHES from "@/core/plugins/constants/dispatches";

const route = useRoute();
const router = useRouter();
const store = useStore();

const activeDrawer = ref(null);

onMounted(() => {
  if (store.state.auth.appMenuList.length === 0) {
    store.dispatch(DISPATCHES.GET_APP_MENU);
  }
});

const filteredMenus = computed(() => {
  // Only show Level 1 menus that are marked for mobile
  return store.state.auth.appMenuList || [];
});

const drawerTitle = computed(() => {
  if (typeof activeDrawer.value === 'object') {
    return activeDrawer.value.menu_name;
  }
  if (activeDrawer.value === 'profile') return "Akun Saya";
  return "";
});

const isActive = (path) => route.path === path;

const isActiveGroup = (menu) => {
  if (!menu.children) return isActive(menu.menu_id_alias);
  const checkChildren = (list) => {
    return list.some(item => {
      if (isActive(item.menu_id_alias)) return true;
      if (item.children) return checkChildren(item.children);
      return false;
    });
  };
  return checkChildren(menu.children);
};

const toggleDrawer = (val) => {
  activeDrawer.value = activeDrawer.value === val ? null : val;
};

const closeDrawer = () => {
  activeDrawer.value = null;
};

const getMenuIcon = (code) => {
  const icons = {
    'GEDUNG': 'building',
    'MASTER': 'database',
    'SETTING': 'cog',
    'PINJAM': 'clipboard-list',
    'KEMBALI': 'undo',
    'JADWAL': 'calendar-alt',
  };
  return icons[code] || 'th-large';
};

const getDrawerIconBg = (index) => {
  const colors = ['bg-blue-50', 'bg-purple-50', 'bg-orange-50', 'bg-pink-50', 'bg-teal-50'];
  return colors[index % colors.length];
};

const getDrawerIconColor = (index) => {
  const colors = ['text-blue-500', 'text-purple-500', 'text-orange-500', 'text-pink-500', 'text-teal-500'];
  return colors[index % colors.length];
};

const userName = computed(() => {
  try {
    const user = JSON.parse(localStorage.getItem("user") || "{}");
    return user.name || "Pengguna";
  } catch {
    return "Pengguna";
  }
});

const userRole = computed(() => {
  try {
    const roles = JSON.parse(localStorage.getItem("user_roles") || "[]");
    return Array.isArray(roles) && roles.length > 0 ? roles[0] : "User";
  } catch {
    return "User";
  }
});

const logoutUser = async () => {
  closeDrawer();
  try {
    await window.axios.post("/api/logout");
  } catch (error) {
    console.error("Gagal logout di backend:", error);
  } finally {
    localStorage.removeItem("token");
    localStorage.removeItem("user_roles");
    delete window.axios.defaults.headers.common["Authorization"];
    router.push("/");
  }
};
</script>

<style scoped>
/* Bottom Nav Item */
.bottom-nav-item {
  @apply flex flex-col items-center gap-1 pb-1 flex-1 text-gray-400 transition-colors duration-200;
}
.bottom-nav-item.active {
  @apply text-teal-500;
}

/* Icon bubble */
.bottom-nav-icon-wrap {
  @apply w-10 h-7 flex items-center justify-center rounded-full transition-all duration-200;
}
.bottom-nav-icon-wrap.active {
  @apply bg-teal-50;
}

.bottom-nav-icon {
  @apply text-[18px];
}

/* Label */
.bottom-nav-label {
  @apply text-[10px] font-semibold leading-none;
}

/* Drawer item */
.drawer-item {
  @apply flex items-center gap-4 px-1 py-3 rounded-2xl text-gray-700 font-semibold text-sm
         transition-colors hover:bg-gray-50 active:bg-gray-100;
}

.drawer-icon-wrap {
  @apply w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 text-sm;
}

/* Transition: backdrop */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Transition: drawer */
.slide-up-enter-active,
.slide-up-leave-active {
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.2s ease;
}
.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(100%);
  opacity: 0;
}

/* Safe area padding for iPhone notch */
.pb-safe {
  padding-bottom: max(8px, env(safe-area-inset-bottom));
}
</style>
