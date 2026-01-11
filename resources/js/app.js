import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import router from '@/core/router';
import App from '@/core/App.vue';

import store from '@/core/services/Store';
import GlobalLoader from '@/core/components/GlobalLoader.vue';
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

import { faSearch, faBell, faCog, faHome } from '@fortawesome/free-solid-svg-icons';
library.add(faSearch, faBell, faCog, faHome);

const app = createApp(App);
app.component('font-awesome-icon', FontAwesomeIcon);
app.component('GlobalLoader', GlobalLoader);
router.beforeEach((to, from, next) => {
    store.commit('SET_LOADING', true);
    next();
});

router.afterEach(() => {
    setTimeout(() => {
        store.commit('SET_LOADING', false);
    }, 500);
});

window.axios.interceptors.request.use(
    (config) => {
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


app.use(store);
app.use(router);
app.mount('#app');