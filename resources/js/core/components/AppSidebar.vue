<template>
  <aside
    class="relative inset-y-0 z-10 flex-shrink-0 bg-white lg:static dark:bg-darker focus:outline-none hidden md:flex flex-col"
  >
    <div
      class="flex flex-col flex-shrink-0 h-full px-2 py-4"
    >
      <div class="flex-shrink-0">
        <a
          href="#"
          class="inline-block text-xl font-bold tracking-wider text-teal-500 uppercase dark:text-light"
        >
          <img
            src="../assets/logo.png"
            alt="Your Company"
            class="mx-auto h-20 w-auto"
          />
        </a>
      </div>

      <div class="flex flex-col items-center justify-center flex-1 space-y-4">
        <button
          class="p-2 shadow-md text-teal-400 transition-colors duration-200 rounded-md bg-indigo-50 hover:text-teal-100 hover:bg-indigo-100 dark:hover:text-light dark:hover:bg-teal-500 dark:bg-dark focus:outline-none focus:bg-indigo-100 dark:focus:bg-teal-600 focus:ring-teal-800"
        >
          <span class="sr-only">Open Home</span>
          <font-awesome-icon icon="home" class="w-8 h-8" />
        </button>
        <button
          @click="goToNotifications"
          class="p-2 shadow-md text-teal-400 transition-colors duration-200 rounded-md bg-indigo-50 hover:text-teal-400 hover:bg-indigo-100 dark:hover:text-light dark:hover:bg-teal-500 dark:bg-dark focus:outline-none focus:bg-indigo-100 dark:focus:bg-teal-600 focus:ring-teal-800"
        >
          <span class="sr-only">Open Notifications</span>
          <font-awesome-icon icon="bell" class="w-8 h-8" />
        </button>
        <button
          @click="goToSearch"
          class="p-2 shadow-md text-teal-400 transition-colors duration-200 rounded-md bg-indigo-50 hover:text-teal-400 hover:bg-indigo-100 dark:hover:text-light dark:hover:bg-teal-500 dark:bg-dark focus:outline-none focus:bg-indigo-100 dark:focus:bg-teal-600 focus:ring-teal-800"
        >
          <span class="sr-only">Open Search</span>
          <font-awesome-icon icon="search" class="w-8 h-8" />
        </button>

        <button
          @click="$emit('open-settings')"
         class="p-2 shadow-md text-teal-400 transition-colors duration-200 rounded-md bg-indigo-50 hover:text-teal-400 hover:bg-indigo-100 dark:hover:text-light dark:hover:bg-teal-500 dark:bg-dark focus:outline-none focus:bg-indigo-100 dark:focus:bg-teal-600 focus:ring-teal-800"
        >
          <span class="sr-only">Open Settings</span>
          <font-awesome-icon icon="cog" class="w-8 h-8" />
        </button>
      </div>

      <div class="relative flex items-center justify-center flex-shrink-0 z-50">
        <div class="" v-click-outside="() => (isUserMenuOpen = false)">
          <button
            @click="isUserMenuOpen = !isUserMenuOpen"
            type="button"
            aria-haspopup="true"
            :aria-expanded="isUserMenuOpen ? 'true' : 'false'"
            class="block transition-opacity duration-200 rounded-full dark:opacity-75 dark:hover:opacity-100 focus:outline-none focus:ring dark:focus:opacity-100"
          >
            <span class="sr-only">User menu</span>
            <img
              class="w-10 h-10 rounded-full"
              src="https://avatars.githubusercontent.com/u/57622665?s=460&u=8f581f4c4acd4c18c33a87b3e6476112325e8b38&v=4"
              alt="User"
            />
          </button>

          <Transition
            enter-active-class="transition-all transform ease-out duration-100"
            enter-from-class="-translate-y-1/2 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition-all transform ease-in duration-100"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="-translate-y-1/2 opacity-0"
          >
            <div
              v-show="isUserMenuOpen"
              class="absolute w-56 z-50 py-1 mb-4 bg-white rounded-md shadow-lg min-w-max left-5 bottom-full ring-1 ring-black ring-opacity-5 dark:bg-dark focus:outline-none"
              role="menu"
            >
              <a
                href="#"
                class="block px-4 py-2 text-sm font-semibold text-gray-700 transition-colors hover:text-white hover:bg-teal-400"
                >Your Profile</a
              >
              <a
                href="#"
                class="block px-4 py-2 text-sm font-semibold text-gray-700 transition-colors hover:text-white hover:bg-teal-400"
                >Settings</a
              >
              <a
                @click.prevent="logout"
                href="#"
                class="block px-4 py-2 text-sm font-semibold text-gray-700 transition-colors hover:text-white hover:bg-teal-400"
                >Logout</a
              >
            </div>
          </Transition>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const isUserMenuOpen = ref(false);

const goToNotifications = () => {
  router.push("/notifications");
};

const goToSearch = () => {
  router.push("/search");
};

const logout = () => {
  console.log("Logging out...");
  router.push("/");
};

const vClickOutside = {
  mounted(el, binding) {
    el.clickOutsideEvent = function (event) {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value(event, el);
      }
    };
    document.body.addEventListener("click", el.clickOutsideEvent);
  },
  unmounted(el) {
    document.body.removeEventListener("click", el.clickOutsideEvent);
  },
};
</script>