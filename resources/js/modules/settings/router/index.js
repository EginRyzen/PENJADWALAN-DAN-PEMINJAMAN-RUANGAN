import SettingKelas from '@/modules/settings/page/SettingKelas.vue';
import Ujian from '@/modules/settings/page/Ujian.vue';

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
    }
];

export default settingsRoutes;
