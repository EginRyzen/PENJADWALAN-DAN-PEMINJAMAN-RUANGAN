import ListPeminjamanRuangan from '../page/PeminjamanRuanganList.vue';

const routes = [
    {
        path: 'list-peminjaman-ruangan',
        name: 'peminjaman.list',
        component: ListPeminjamanRuangan,
        meta: { title: 'Daftar Peminjaman Ruangan' }
    },
    {
        path: 'buat-peminjaman-ruangan',
        name: 'peminjaman.create',
        component: () => import('../page/PeminjamanRuanganCreate.vue'),
        meta: { title: 'Buat Peminjaman Ruangan' }
    },
    {
        path: 'detail-peminjaman-ruangan/:id',
        name: 'peminjaman.detail',
        component: () => import('../page/PeminjamanRuanganDetail.vue'),
        meta: { title: 'Detail Peminjaman Ruangan' }
    },
    {
        path: 'workflow-peminjaman-ruangan/:id',
        name: 'peminjaman.workflow',
        component: () => import('../page/PeminjamanWorkflow.vue'),
        meta: { title: 'Workflow Peminjaman Ruangan' }
    },
];

export default routes;
