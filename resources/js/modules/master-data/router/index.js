import ListMataKuliah from '@/modules/master-data/page/ListMataKuliah.vue';
import ListMahasiswa from '@/modules/master-data/page/ListMahasiswa.vue';
import ListProgramStudi from '@/modules/master-data/page/ListProgramStudi.vue';
import ListDosen from '@/modules/master-data/page/ListDosen.vue';
import ListKelasMataKuliah from '@/modules/master-data/page/ListKelasMataKuliah.vue';
import ListUser from '@/modules/master-data/page/ListUser.vue';

const masterDataRoutes = [
    {
        path: 'mata-kuliah',
        name: 'master-data.mata-kuliah',
        component: ListMataKuliah,
        meta: { requiresAuth: true }
    },
    {
        path: 'mahasiswa-list',
        name: 'master-data.mahasiswa-list',
        component: ListMahasiswa,
        meta: { requiresAuth: true }
    },
    {
        path: 'program-studi',
        name: 'master-data.program-studi',
        component: ListProgramStudi,
        meta: { requiresAuth: true }
    },
    {
        path: 'dosen-list',
        name: 'master-data.dosen-list',
        component: ListDosen,
        meta: { requiresAuth: true }
    },
    {
        path: 'kelas-mata-kuliah',
        name: 'master-data.kelas-mata-kuliah',
        component: ListKelasMataKuliah,
        meta: { requiresAuth: true }
    },
    {
        path: 'user-list',
        name: 'master-data.user-list',
        component: ListUser,
        meta: { requiresAuth: true }
    }
];

export default masterDataRoutes;
