<template>
  <nav class="relative app-teal z-50">
    <div class="mx-auto px-2 sm:px-6 lg:px-8">
      <div class="relative flex h-16 items-center justify-between">
        <div
          class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start"
        >
          <div class="hidden md:block">
            <div class="flex space-x-4">
              <router-link
                to="/app/dashboard"
                class="rounded-md px-3 py-2 text-md font-medium text-white"
                aria-current="page"
              >
                Dashboard
              </router-link>

              <div
                class="relative group"
                @mouseenter="isGedungOpen = true"
                @mouseleave="isGedungOpen = false"
              >
                <button
                  @click="isGedungOpen = !isGedungOpen"
                  class="flex items-center gap-1 rounded-md px-3 py-2 text-md font-medium text-gray-200 hover:text-white transition"
                >
                  Gedung
                  <svg
                    class="w-4 h-4 transition-transform duration-200"
                    :class="{ 'rotate-180': isGedungOpen }"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M19 9l-7 7-7-7"
                    />
                  </svg>
                </button>

                <transition
                  enter-active-class="transition ease-out duration-100"
                  enter-from-class="transform opacity-0 scale-95"
                  enter-to-class="transform opacity-100 scale-100"
                  leave-active-class="transition ease-in duration-75"
                  leave-from-class="transform opacity-100 scale-100"
                  leave-to-class="transform opacity-0 scale-95"
                >
                  <div
                    v-if="isGedungOpen"
                    class="absolute left-0 mt-0 w-48 origin-top-left rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                  >
                    <router-link
                      to="/app/gedung-list"
                      class="block px-4 py-2 text-md font-medium text-gray-500 hover:bg-indigo-50"
                    >
                      Profil Gedung
                    </router-link>
                    
                    <router-link
                      to="/aktivasi-gedung"
                      class="block px-4 py-2 text-md font-medium text-gray-500 hover:bg-indigo-50"
                    >
                      Aktivasi Gedung
                    </router-link>
                    
                    <router-link
                      to="/list-pengajuan"
                      class="block px-4 py-2 text-md font-medium text-gray-500 hover:bg-indigo-50"
                    >
                      List Pengajuan
                    </router-link>
                    
                    <router-link
                      to="/perizinan"
                      class="block px-4 py-2 text-md font-medium text-gray-500 hover:bg-indigo-50"
                    >
                      Perizinan
                    </router-link>
                  </div>
                </transition>
              </div>

              <router-link
                to="/projects"
                class="rounded-md px-3 py-2 text-md font-medium text-gray-200 hover:text-white"
              >
                Projects
              </router-link>
              
              <router-link
                to="/calendar"
                class="rounded-md px-3 py-2 text-md font-medium text-gray-200 hover:text-white"
              >
                Calendar
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref } from "vue";

const isGedungOpen = ref(false);
</script>