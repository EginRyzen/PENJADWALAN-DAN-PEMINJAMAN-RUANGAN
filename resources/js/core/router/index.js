import { createRouter, createWebHistory } from 'vue-router';

// Layouts
import MasterLayout from '@/core/layouts/MasterLayout.vue';
import LoginLayout from '@/core/layouts/LoginLayout.vue';

// Pages
import Login from '@/modules/auth/pages/Login.vue';
import workOrderRoutes from '@/modules/work-order/router';
import dashboardRoutes from '@/modules/dashboard/router';

const routes = [
    {
        path: '/',
        component: LoginLayout,
        children: [
            {
                path: '',
                name: 'auth.login',
                component: Login
            }
        ]
    },

    {
        path: '/app',
        component: MasterLayout,
        children: [
                ...workOrderRoutes,
                ...dashboardRoutes
            ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;