import GedungList from '../pages/GedungList.vue';
// import WorkOrderCreate from '../pages/WorkOrderCreate.vue';

const routes = [
    {
        path: 'gedung-list',
        name: 'gedung.list',
        component: GedungList,
        meta: { title: 'Daftar Gedung' }
    },
    // {
    //     path: '/work-order/create',
    //     name: 'work-order.create',
    //     component: WorkOrderCreate,
    //     meta: { title: 'Buat Work Order' }
    // }
];

export default routes;