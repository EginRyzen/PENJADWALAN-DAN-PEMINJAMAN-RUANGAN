import ListPeminjamanRuangan from '../page/ListPeminjamanRuangan.vue';

const routes = [
    {
        path: 'list-peminjaman-ruangan',
        name: 'pengajuan.list',
        component: ListPeminjamanRuangan,
        meta: { title: 'Daftar Peminjaman Ruangan' }
    },
];

export default routes;
