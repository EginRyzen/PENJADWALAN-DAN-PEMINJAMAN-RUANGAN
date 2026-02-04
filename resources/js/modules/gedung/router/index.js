import GedungList from '../pages/GedungList.vue';
import GedungCreate from '../pages/GedungCreate.vue';

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
    }
];

export default routes;