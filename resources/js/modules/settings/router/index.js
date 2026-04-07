import KelasAndMahasiswa from '@/modules/settings/page/KelasAndMahasiswa.vue';
import Ujian from '@/modules/settings/page/Ujian.vue';

const settingsRoutes = [
    {
        path: 'pengaturan-kelas-mahasiswa',
        name: 'settings.kelas-mahasiswa',
        component: KelasAndMahasiswa,
        meta: { requiresAuth: true }
    },
    {
        path: 'pengaturan-ujian-ruangan',
        name: 'settings.ujian',
        component: Ujian,
        meta: { requiresAuth: true }
    }
];

export default settingsRoutes;
