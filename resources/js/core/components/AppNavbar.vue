<template>
  <nav class="relative app-teal z-50">
    <div class="mx-auto px-2 sm:px-6 lg:px-8">
      <div class="relative flex h-16 items-center justify-between">
        <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
          <div class="hidden md:block">
            <div class="flex space-x-4">
              <template v-for="(menu, index) in menus" :key="index">
                <!-- Dropdown Menu -->
                <div v-if="menu.children" class="relative group" @mouseenter="handleMouseEnter(index)"
                  @mouseleave="handleMouseLeave()">
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
                      class="absolute left-0 mt-1 w-56 origin-top-left rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50">

                      <template v-for="(child, childIndex) in menu.children" :key="childIndex">
                        <!-- Submenu / Secondary Dropdown -->
                        <div v-if="child.children" class="relative" @mouseenter="handleSubMouseEnter(childIndex)"
                          @mouseleave="handleSubMouseLeave()">
                          <button @click.prevent="toggleSubDropdown(childIndex)"
                            class="w-full flex items-center justify-between px-4 py-2 text-md font-medium text-gray-500 hover:bg-indigo-50">
                            {{ child.label }}
                            <svg class="w-4 h-4 transition-transform duration-200 -rotate-90" fill="none"
                              viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                            </svg>
                          </button>

                          <transition enter-active-class="transition ease-out duration-100"
                            enter-from-class="transform opacity-0 scale-95"
                            enter-to-class="transform opacity-100 scale-100"
                            leave-active-class="transition ease-in duration-75"
                            leave-from-class="transform opacity-100 scale-100"
                            leave-to-class="transform opacity-0 scale-95">
                            <div v-if="activeSubDropdown === childIndex"
                              class="absolute left-full top-0 ml-1 w-56 rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50 border border-gray-100">
                              <router-link v-for="(grandchild, gcIndex) in child.children" :key="gcIndex"
                                :to="grandchild.path"
                                class="block px-4 py-2 text-md font-medium text-gray-500 hover:bg-indigo-50">
                                {{ grandchild.label }}
                              </router-link>
                            </div>
                          </transition>
                        </div>

                        <!-- Regular Child Link -->
                        <router-link v-else :to="child.path"
                          class="block px-4 py-2 text-md font-medium text-gray-500 hover:bg-indigo-50">
                          {{ child.label }}
                        </router-link>
                      </template>

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
import { ref, onMounted, onUnmounted } from "vue";

const activeDropdown = ref(null);
const activeSubDropdown = ref(null);
const isPinned = ref(false);
const isSubPinned = ref(false);

const closeAllDropdowns = (e) => {
  const clickedInsideDropdown = e.target.closest('.group');
  const clickedLink = e.target.closest('a');

  if (!clickedInsideDropdown || clickedLink) {
    activeDropdown.value = null;
    activeSubDropdown.value = null;
    isPinned.value = false;
    isSubPinned.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', closeAllDropdowns);
});

onUnmounted(() => {
  document.removeEventListener('click', closeAllDropdowns);
});

const menus = ref([
  {
    label: "Dashboard",
    path: "/app/dashboard",
  },
  {
    label: "Gedung",
    children: [
      { label: "Profil Gedung", path: "/app/gedung-list" },
      {
        label: "Peminjaman Gedung",
        children: [
          { label: "List Peminjaman", path: "/app/list-peminjaman-ruangan" },
          { label: "List Pengembalian", path: "/app/pengembalian-list" },
        ]
      },
      { label: "Peminjaman Fasilitas", path: "/app/peminjaman-fasilitas" }
    ],
  },
  {
    label: "Master Data",
    children: [
      {
        label: "General",
        children: [
          { label: "Mata Kuliah", path: "/app/mata-kuliah" },
          { label: "Program Studi", path: "/app/program-studi" },
        ]
      },
      { label: "Mahasiswa", path: "/app/mahasiswa-list" },
      { label: "Dosen", path: "/app/dosen-list" },
    ],
  },
  {
    label: "Settings",
    path: "/app/settings",
  },
]);

const handleMouseEnter = (index) => {
  if (isPinned.value) {
    if (activeDropdown.value !== index) {
      activeDropdown.value = index;
      isSubPinned.value = false;
      activeSubDropdown.value = null;
    }
  } else {
    activeDropdown.value = index;
  }
};

const handleMouseLeave = () => {
  if (!isPinned.value) {
    activeDropdown.value = null;
    activeSubDropdown.value = null;
  }
};

const toggleDropdown = (index) => {
  if (activeDropdown.value === index && isPinned.value) {
    isPinned.value = false;
    activeDropdown.value = null;
    isSubPinned.value = false;
    activeSubDropdown.value = null;
  } else {
    activeDropdown.value = index;
    isPinned.value = true;
    isSubPinned.value = false;
    activeSubDropdown.value = null;
  }
};

const handleSubMouseEnter = (childIndex) => {
  activeSubDropdown.value = childIndex;
};

const handleSubMouseLeave = () => {
  if (!isSubPinned.value) {
    activeSubDropdown.value = null;
  }
};

const toggleSubDropdown = (childIndex) => {
  if (activeSubDropdown.value === childIndex && isSubPinned.value) {
    isSubPinned.value = false;
    activeSubDropdown.value = null;
  } else {
    activeSubDropdown.value = childIndex;
    isSubPinned.value = true;
    isPinned.value = true;
  }
};
</script>