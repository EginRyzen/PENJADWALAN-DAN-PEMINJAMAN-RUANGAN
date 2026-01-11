import Dashboard from '../pages/Dashboard.vue';
// import WorkOrderCreate from '../pages/WorkOrderCreate.vue';

const routes = [
    {
        path: 'dashboard',
        name: 'dashboard',
        component: Dashboard,
        meta: { title: 'Dashboard' }
    },
    // {
    //     path: '/work-order/create',
    //     name: 'work-order.create',
    //     component: WorkOrderCreate,
    //     meta: { title: 'Buat Work Order' }
    // }
];

export default routes;