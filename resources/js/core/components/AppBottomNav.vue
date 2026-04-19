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

      <!-- Notifikasi -->
      <router-link
        to="/notifications"
        class="bottom-nav-item"
        :class="isActive('/notifications') ? 'active' : ''"
      >
        <div class="bottom-nav-icon-wrap" :class="isActive('/notifications') ? 'active' : ''">
          <font-awesome-icon icon="bell" class="bottom-nav-icon" />
        </div>
        <span class="bottom-nav-label">Notifikasi</span>
      </router-link>

      <!-- Gedung (with sub-menu drawer) -->
      <button
        @click="toggleDrawer('gedung')"
        class="bottom-nav-item"
        :class="isActiveGroup(['/app/gedung-list', '/app/list-peminjaman-ruangan', '/app/pengembalian-list', '/app/penjadwalan']) ? 'active' : ''"
      >
        <div class="bottom-nav-icon-wrap" :class="isActiveGroup(['/app/gedung-list', '/app/list-peminjaman-ruangan', '/app/pengembalian-list', '/app/penjadwalan']) ? 'active' : ''">
          <font-awesome-icon icon="building" class="bottom-nav-icon" />
        </div>
        <span class="bottom-nav-label">Gedung</span>
      </button>



      <!-- Profile / Logout -->
      <button
        @click="toggleDrawer('profile')"
        class="bottom-nav-item"
      >
        <div class="bottom-nav-icon-wrap">
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

        <!-- Gedung Menu -->
        <template v-if="activeDrawer === 'gedung'">

          <router-link to="/app/list-peminjaman-ruangan" class="drawer-item" @click="closeDrawer">
            <div class="drawer-icon-wrap bg-blue-50">
              <font-awesome-icon icon="clipboard-list" class="text-blue-500" />
            </div>
            <span>List Peminjaman</span>
          </router-link>

          <router-link to="/app/penjadwalan" class="drawer-item" @click="closeDrawer">
            <div class="drawer-icon-wrap bg-purple-50">
              <font-awesome-icon icon="calendar-alt" class="text-purple-500" />
            </div>
            <span>Penjadwalan</span>
          </router-link>
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
import { ref, computed } from "vue";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();

const activeDrawer = ref(null);

const drawerTitles = {
  gedung: "Gedung",
  masterdata: "Master Data",
  settings: "Pengaturan",
  profile: "Akun Saya",
};

const drawerTitle = computed(() => drawerTitles[activeDrawer.value] || "");

const isActive = (path) => route.path === path;

const isActiveGroup = (paths) => paths.some((p) => route.path.startsWith(p));

const toggleDrawer = (name) => {
  activeDrawer.value = activeDrawer.value === name ? null : name;
};

const closeDrawer = () => {
  activeDrawer.value = null;
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
