<template>
  <div ref="pageContainer" class="min-h-screen bg-gray-50/50 pb-20 md:pb-10 p-4 md:p-6">
    <!-- Breadcrumb -->
    <breadcrumb-bima :items="breadcrumbItems" class="mb-6 hidden md:block" />

    <!-- Mobile Header (Sticky) -->
    <div class="md:hidden bg-teal-500 text-white fixed top-0 left-0 right-0 z-40 px-4 py-4 shadow-lg flex items-center justify-between">
       <div class="flex items-center gap-3">
         <button @click="$router.back()" class="p-1">
           <font-awesome-icon icon="arrow-left" class="text-lg" />
         </button>
         <h1 class="text-lg font-bold">Notifikasi</h1>
       </div>
       <button 
          v-if="unreadCount > 0"
          @click="markAllRead"
          class="text-xs font-bold bg-white/20 px-3 py-1.5 rounded-full backdrop-blur-sm"
        >
          Baca Semua
        </button>
    </div>

    <!-- Desktop Title Section -->
    <div class="hidden md:flex items-center justify-between mb-8">
      <div class="flex items-center gap-3">
        <div class="p-2.5 bg-teal-500 rounded-xl shadow-lg shadow-teal-200 text-white">
          <font-awesome-icon icon="bell" class="w-6 h-6" />
        </div>
        <div>
          <h1 class="text-xl font-black text-gray-800">Pusat Notifikasi</h1>
          <p class="text-xs text-gray-500 mt-0.5">Pantau seluruh pembaruan aktivitas peminjaman Anda</p>
        </div>
      </div>
      <button 
        v-if="unreadCount > 0"
        @click="markAllRead"
        class="flex items-center gap-2 px-4 py-2 bg-white border border-teal-100 text-teal-600 rounded-xl text-sm font-bold hover:bg-teal-50 transition-colors shadow-sm"
      >
        <font-awesome-icon icon="check-double" class="text-xs" />
        Tandai Semua Dibaca
      </button>
    </div>

    <div class="max-w-5xl mx-auto mt-14 md:mt-0">
      <!-- Filter Tabs -->
      <div class="flex px-4 md:px-0 mb-4 mt-4 md:mt-0 gap-2">
        <button 
          v-for="tab in tabs" 
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'px-5 py-2 rounded-full text-sm font-bold transition-all duration-300',
            activeTab === tab.id 
              ? 'bg-teal-500 text-white shadow-md shadow-teal-100' 
              : 'bg-white text-gray-500 hover:bg-gray-100'
          ]"
        >
          {{ tab.label }}
          <span v-if="tab.id === 'unread' && unreadCount > 0" class="ml-1 opacity-80">
            ({{ unreadCount }})
          </span>
        </button>
      </div>

      <!-- Notification List -->
      <div v-if="loading && page === 1" class="space-y-3 px-4 md:px-0">
        <div v-for="i in 5" :key="i" class="bg-white p-4 rounded-2xl animate-pulse flex gap-4">
          <div class="w-12 h-12 bg-gray-200 rounded-xl"></div>
          <div class="flex-1 space-y-2">
            <div class="h-4 bg-gray-200 rounded w-1/3"></div>
            <div class="h-3 bg-gray-100 rounded w-full"></div>
            <div class="h-3 bg-gray-100 rounded w-1/2"></div>
          </div>
        </div>
      </div>

      <div v-else-if="filteredNotifications.length > 0" class="space-y-3 px-4 md:px-0 pb-10">
        <div 
          v-for="notif in filteredNotifications" 
          :key="notif.id"
          @click="handleNotificationClick(notif)"
          :class="[
            'group relative bg-white p-4 rounded-2xl border border-transparent transition-all duration-300 hover:shadow-xl hover:shadow-gray-200/50 cursor-pointer flex gap-4 overflow-hidden',
            !notif.read_at ? 'ring-1 ring-teal-500/10 shadow-lg shadow-teal-500/5 bg-white' : 'opacity-80'
          ]"
        >
          <!-- Unread Indicator -->
          <div v-if="!notif.read_at" class="absolute top-0 right-0 w-2 h-2 bg-teal-500 rounded-bl-lg"></div>

          <!-- Icon Wrapper -->
          <div :class="[
            'w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 transition-transform group-hover:scale-110 duration-300',
            getIconBg(notif.data.type || 'info')
          ]">
            <font-awesome-icon 
              :icon="getIcon(notif.data.type || 'info')" 
              :class="getIconColor(notif.data.type || 'info')"
              class="text-lg"
            />
          </div>

          <!-- Content -->
          <div class="flex-1 min-w-0">
            <div class="flex justify-between items-start mb-1">
              <h3 class="font-bold text-gray-800 truncate pr-2">{{ notif.data.title }}</h3>
              <span class="text-[10px] font-bold text-gray-400 whitespace-nowrap bg-gray-50 px-2 py-0.5 rounded-full uppercase">
                {{ formatTime(notif.created_at) }}
              </span>
            </div>
            <p class="text-sm text-gray-500 leading-relaxed line-clamp-2 mb-2">
              {{ notif.data.message }}
            </p>
            
            <div class="flex items-center gap-4">
               <span v-if="notif.data.action_label" class="text-xs font-bold text-teal-600 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                 {{ notif.data.action_label }}
                 <font-awesome-icon icon="arrow-right" class="text-[10px]" />
               </span>
            </div>
          </div>
        </div>

        <!-- Desktop Pagination -->
        <div class="hidden md:flex justify-end pt-6">
          <pagination-app
            :current="pagination.current_page || 1"
            :total="pagination.total || 0"
            :per-page="pagination.per_page || 10"
            :total-rows-on-page="notifications.length"
            @page-changed="handlePageChange"
            @paging-change="handlePagingChange"
          />
        </div>

        <!-- Mobile Loading Indicator (Bottom) -->
        <div v-if="loading && page > 1" class="md:hidden flex justify-center py-4">
          <font-awesome-icon icon="spinner" spin class="text-teal-500" />
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="flex flex-col items-center justify-center py-20 px-4 text-center">
        <div class="w-48 h-48 bg-teal-50 rounded-full flex items-center justify-center mb-6">
           <font-awesome-icon icon="bell-slash" class="text-6xl text-teal-200" />
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">Belum ada notifikasi</h2>
        <p class="text-gray-500 max-w-xs mx-auto">
          Notifikasi mengenai aktivitas Anda akan muncul di sini agar Anda tetap terinformasi.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';
import BreadcrumbBima from '@/core/components/Breadcrumb.vue';
import PaginationApp from '@/core/components/Pagination.vue';
import DISPATCHES from '@/core/plugins/constants/dispatches';
import moment from 'moment';
import 'moment/dist/locale/id';

moment.locale('id');

const store = useStore();
const router = useRouter();

// Reference to page container to find parent scroll
const pageContainer = ref(null);
const scrollEl = ref(null);

const loading = ref(false);
const page = ref(1);
const activeTab = ref('all');

const breadcrumbItems = [
  { text: 'Dashboard', link: '/app/dashboard' },
  { text: 'Notifikasi', link: '/notifications' }
];

const tabs = [
  { id: 'all', label: 'Semua' },
  { id: 'unread', label: 'Belum Dibaca' }
];

const notifications = computed(() => store.state.dashboard.notifications);
const unreadCount = computed(() => store.state.dashboard.unreadCount);
const pagination = computed(() => store.state.dashboard.notificationPagination);

const filteredNotifications = computed(() => {
  if (activeTab.value === 'unread') {
    return notifications.value.filter(n => !n.read_at);
  }
  return notifications.value;
});

const hasMore = computed(() => pagination.value.current_page < pagination.value.last_page);

const fetchNotifications = async (isLoadMore = false) => {
  if (loading.value) return;
  
  loading.value = true;
  // Set global loading only for initial load or desktop pagination
  if (!isLoadMore) store.commit('SET_LOADING', true);

  try {
    await store.dispatch(DISPATCHES.GET_NOTIFICATIONS, { 
      page: page.value,
      per_page: 10,
      isAppend: isLoadMore
    });
  } finally {
    loading.value = false;
    store.commit('SET_LOADING', false);
  }
};

const loadMore = () => {
  if (loading.value || !hasMore.value) return;
  page.value++;
  fetchNotifications(true);
};

const handlePageChange = (newPage) => {
  page.value = newPage;
  fetchNotifications(false);
};

const handlePagingChange = (newSize) => {
  page.value = 1;
  // If the store supports custom per_page, we can pass it here. 
  // For now we use the default or fixed 10.
  fetchNotifications(false);
};

const handleScroll = (e) => {
  // Infinite scroll only for mobile
  if (window.innerWidth >= 768) return;

  const el = e.target;
  const scrollHeight = el.scrollHeight;
  const scrollTop = el.scrollTop;
  const clientHeight = el.clientHeight;

  if (scrollTop + clientHeight >= scrollHeight - 100) {
    loadMore();
  }
};

const markAllRead = async () => {
  store.commit('SET_LOADING', true);
  try {
    await store.dispatch(DISPATCHES.MARK_ALL_NOTIFICATIONS_READ);
  } finally {
    store.commit('SET_LOADING', false);
  }
};

const handleNotificationClick = async (notif) => {
  store.commit('SET_LOADING', true);
  try {
    if (!notif.read_at) {
      await store.dispatch(DISPATCHES.MARK_NOTIFICATION_READ, notif.id);
    }
    
    if (notif.data.link) {
      router.push(notif.data.link);
    }
  } finally {
    store.commit('SET_LOADING', false);
  }
};

const formatTime = (date) => {
  const m = moment(date);
  if (m.isSame(moment(), 'day')) return m.format('HH:mm');
  if (m.isSame(moment().subtract(1, 'days'), 'day')) return 'Kemarin';
  return m.format('DD/MM');
};

const getIcon = (type) => {
  const icons = {
    info: 'info-circle',
    success: 'check-circle',
    warning: 'exclamation-circle',
    danger: 'times-circle',
    pengajuan: 'clipboard-list',
    pengembalian: 'undo'
  };
  return icons[type] || 'bell';
};

const getIconBg = (type) => {
  const bgs = {
    info: 'bg-blue-50',
    success: 'bg-green-50',
    warning: 'bg-yellow-50',
    danger: 'bg-red-50',
    pengajuan: 'bg-teal-50',
    pengembalian: 'bg-orange-50'
  };
  return bgs[type] || 'bg-gray-50';
};

const getIconColor = (type) => {
  const colors = {
    info: 'text-blue-500',
    success: 'text-green-500',
    warning: 'text-yellow-600',
    danger: 'text-red-500',
    pengajuan: 'text-teal-500',
    pengembalian: 'text-orange-500'
  };
  return colors[type] || 'text-gray-400';
};

onMounted(() => {
  fetchNotifications();
  store.dispatch(DISPATCHES.GET_UNREAD_NOTIFICATION_COUNT);
  
  // Find the scrollable container from MasterLayout
  if (pageContainer.value) {
    const parentScroll = pageContainer.value.closest('.overflow-y-auto');
    if (parentScroll) {
      scrollEl.value = parentScroll;
      parentScroll.addEventListener('scroll', handleScroll);
    } else {
      window.addEventListener('scroll', handleScroll);
    }
  }
});

onUnmounted(() => {
  if (scrollEl.value) {
    scrollEl.value.removeEventListener('scroll', handleScroll);
  } else {
    window.removeEventListener('scroll', handleScroll);
  }
});
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}
</style>
