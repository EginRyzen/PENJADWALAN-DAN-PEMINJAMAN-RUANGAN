import SettingKelas from '@/modules/settings/page/SettingKelas.vue';
import Ujian from '@/modules/settings/page/Ujian.vue';
import HariLibur from '@/modules/settings/page/HariLibur.vue';
import Periode from '@/modules/settings/page/Periode.vue';

const settingsRoutes = [
    {
        path: 'pengaturan-kelas',
        name: 'settings.kelas',
        component: SettingKelas,
        meta: { requiresAuth: true }
    },
    {
        path: 'pengaturan-ujian-ruangan',
        name: 'settings.ujian',
        component: Ujian,
        meta: { requiresAuth: true }
    },
    {
        path: 'pengaturan-hari-libur',
        name: 'settings.hari-libur',
        component: HariLibur,
        meta: { requiresAuth: true }
    },
    {
        path: 'pengaturan-periode',
        name: 'settings.periode',
        component: Periode,
        meta: { requiresAuth: true }
    },
];

export default settingsRoutes;

