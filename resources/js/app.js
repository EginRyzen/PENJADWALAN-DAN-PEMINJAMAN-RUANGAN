import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import router from '@/core/router';
import App from '@/core/App.vue';

import store from '@/core/services/Store';
import GlobalLoader from '@/core/components/GlobalLoader.vue';
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

import { faSearch, faBell, faCog, faHome, faPlus, faDownload, faArrowLeft, faExclamationCircle, faExclamationTriangle, faTrashAlt, faEye, faTimes, faCloudUploadAlt, faImage, faEdit, faCheck, faCalendar, faClock, faUpload, faTimesCircle, faCheckCircle, faCalendarAlt, faSpinner } from '@fortawesome/free-solid-svg-icons';
library.add(faSearch, faBell, faCog, faHome, faPlus, faDownload, faArrowLeft, faExclamationCircle, faExclamationTriangle, faTrashAlt, faEye, faTimes, faCloudUploadAlt, faImage, faEdit, faCheck, faCalendar, faClock, faUpload, faTimesCircle, faCheckCircle, faCalendarAlt, faSpinner);

import { setupCalendar } from 'v-calendar';

const app = createApp(App);
app.component('font-awesome-icon', FontAwesomeIcon);
app.component('GlobalLoader', GlobalLoader);

// Router Guards
router.beforeEach((to, from, next) => {
    store.commit('SET_LOADING', true);
    next();
});

router.afterEach(() => {
    setTimeout(() => {
        store.commit('SET_LOADING', false);
    }, 500);
});

// Axios Interceptor
window.axios.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        store.commit('SET_LOADING', true);
        return config;
    },
    (error) => {
        store.commit('SET_LOADING', false);
        return Promise.reject(error);
    }
);

window.axios.interceptors.response.use(
    (response) => {
        store.commit('SET_LOADING', false);
        return response;
    },
    (error) => {
        store.commit('SET_LOADING', false);
        return Promise.reject(error);
    }
);

// Inisialisasi Aplikasi
const initApp = async () => {
    const token = localStorage.getItem('token');

    if (token) {
        window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

        try {
            const response = await window.axios.get('/api/user/profile');
            const userData = response.data.result.user;
            localStorage.setItem('user_roles', JSON.stringify(userData.roles));

            store.commit('SET_USER', userData);
        } catch (error) {
            console.error("Gagal memvalidasi sesi", error);
            localStorage.removeItem('token');
            localStorage.removeItem('user_roles');
            store.commit('SET_LOADING', false);
            router.push('/');
        }
    }

    app.use(store);
    app.use(router);
    app.use(setupCalendar, {});
    app.mount('#app');
};

initApp();