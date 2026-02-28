<template>
  <nav class="relative app-teal z-50">
    <div class="mx-auto px-2 sm:px-6 lg:px-8">
      <div class="relative flex h-16 items-center justify-between">
        <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
          <div class="hidden md:block">
            <div class="flex space-x-4">
              <template v-for="(menu, index) in menus" :key="index">
                <!-- Dropdown Menu -->
                <div v-if="menu.children" class="relative group" @mouseenter="openDropdown(index)"
                  @mouseleave="closeDropdown(index)">
                  <button @click="toggleDropdown(index)"
                    class="flex items-center gap-1 rounded-md px-3 py-2 text-md font-medium text-gray-200 hover:text-white transition">
                    {{ menu.label }}
                    <svg class="w-4 h-4 transition-transform duration-200"
                      :class="{ 'rotate-180': activeDropdown === index }" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>

                  <transition enter-active-class="transition ease-out duration-100"
                    enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-75"
                    leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                    <div v-if="activeDropdown === index"
                      class="absolute left-0 mt-0 w-48 origin-top-left rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                      <router-link v-for="(child, childIndex) in menu.children" :key="childIndex" :to="child.path"
                        class="block px-4 py-2 text-md font-medium text-gray-500 hover:bg-indigo-50">
                        {{ child.label }}
                      </router-link>
                    </div>
                  </transition>
                </div>

                <!-- Regular Link -->
                <router-link v-else :to="menu.path"
                  class="rounded-md px-3 py-2 text-md font-medium text-gray-200 hover:text-white"
                  :class="{ 'text-white': menu.path === '/app/dashboard' }">
                  {{ menu.label }}
                </router-link>
              </template>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref } from "vue";

const activeDropdown = ref(null);

const menus = ref([
  {
    label: "Dashboard",
    path: "/app/dashboard",
  },
  {
    label: "Gedung",
    children: [
      { label: "Profil Gedung", path: "/app/gedung-list" },
      { label: "List Pengajuan", path: "/app/pengajuan-list" },
      // { label: "List Pengajuan", path: "/list-pengajuan" },
      // { label: "Perizinan", path: "/perizinan" },
    ],
  },
  {
    label: "Settings",
    path: "/app/settings",
  },
  // {
  //   label: "Calendar",
  //   path: "/calendar",
  // }
]);

const openDropdown = (index) => {
  activeDropdown.value = index;
};

const closeDropdown = (index) => {
  if (activeDropdown.value === index) {
    activeDropdown.value = null;
  }
};

const toggleDropdown = (index) => {
  if (activeDropdown.value === index) {
    activeDropdown.value = null;
  } else {
    activeDropdown.value = index;
  }
};
</script>