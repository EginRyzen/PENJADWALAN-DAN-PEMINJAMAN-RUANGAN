import { createRouter, createWebHistory } from 'vue-router';

// Layouts
import MasterLayout from '@/core/layouts/MasterLayout.vue';
import LoginLayout from '@/core/layouts/LoginLayout.vue';

// Pages
import Login from '@/modules/auth/pages/Login.vue';
import dashboardRoutes from '@/modules/dashboard/router';
import gedungRoutes from '@/modules/gedung/router';
import masterDataRoutes from '@/modules/master-data/router';
import pengajuanRoutes from '@/modules/list-pengajuan/router';

const routes = [
    {
        path: '/',
        component: LoginLayout,
        children: [
            {
                path: '',
                name: 'auth.login',
                component: Login,
                meta: { guestOnly: true }
            }
        ]
    },

    {
        path: '/app',
        component: MasterLayout,
        meta: { requiresAuth: true },
        children: [
            ...dashboardRoutes,
            ...gedungRoutes,
            ...masterDataRoutes,
            ...pengajuanRoutes
        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const isAuthenticated = localStorage.getItem('token');

    if (to.meta.requiresAuth && !isAuthenticated) {
        next({ name: 'auth.login' });
    } else if (to.meta.guestOnly && isAuthenticated) {
        next({ name: 'dashboard' });
    } else {
        next();
    }
});

export default router;