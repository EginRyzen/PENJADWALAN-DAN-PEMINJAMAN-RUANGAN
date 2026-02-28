import GedungList from '../pages/GedungList.vue';
import GedungCreate from '../pages/GedungCreate.vue';
import GedungDetail from '../pages/GedungDetail.vue';

const routes = [
    {
        path: 'gedung-list',
        name: 'gedung.list',
        component: GedungList,
        meta: { title: 'Daftar Gedung' }
    },
    {
        path: 'gedung-create',
        name: 'gedung.create',
        component: GedungCreate,
        meta: { title: 'Tambah Gedung' }
    },
    {
        path: 'gedung-detail/:id',
        name: 'gedung.detail',
        component: GedungDetail,
        meta: { title: 'Detail Gedung' }
    },
];

export default routes;