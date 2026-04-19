import SettingKelas from '@/modules/settings/page/SettingKelas.vue';
import Ujian from '@/modules/settings/page/Ujian.vue';
import HariLibur from '@/modules/settings/page/HariLibur.vue';
import Periode from '@/modules/settings/page/Periode.vue';
import MenuManagement from '@/modules/settings/page/MenuManagement.vue';
import RoleMenuManagement from '@/modules/settings/page/RoleMenuManagement.vue';

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
    {
        path: 'pengaturan-menu',
        name: 'settings.menu',
        component: MenuManagement,
        meta: { requiresAuth: true }
    },
    {
        path: 'pengaturan-role-menu',
        name: 'settings.role-menu',
        component: RoleMenuManagement,
        meta: { requiresAuth: true }
    },
];

export default settingsRoutes;

