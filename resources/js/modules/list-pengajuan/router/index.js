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
];

export default routes;
