import WorkOrderList from '../pages/WorkOrderList.vue';
// import WorkOrderCreate from '../pages/WorkOrderCreate.vue';

const routes = [
    {
        path: 'work-order',
        name: 'work-order.list',
        component: WorkOrderList,
        meta: { title: 'Daftar Work Order' }
    },
    // {
    //     path: '/work-order/create',
    //     name: 'work-order.create',
    //     component: WorkOrderCreate,
    //     meta: { title: 'Buat Work Order' }
    // }
];

export default routes;