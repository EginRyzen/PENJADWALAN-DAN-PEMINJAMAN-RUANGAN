import Dashboard from '../pages/Dashboard.vue';
// import WorkOrderCreate from '../pages/WorkOrderCreate.vue';

const routes = [
    {
        path: 'dashboard',
        name: 'dashboard',
        component: Dashboard,
        meta: { title: 'Dashboard' }
    },
    {
        path: '/notifications',
        name: 'notifications',
        component: () => import('../pages/NotificationList.vue'),
        meta: { title: 'Notifikasi' }
    },
];

export default routes;