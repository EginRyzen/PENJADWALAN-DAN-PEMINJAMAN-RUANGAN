import PenjadwalanOtomatis from '@/modules/penjadwalan/page/PenjadwalanOtomatis.vue';

const penjadwalanRoutes = [
    {
        path: 'penjadwalan',
        name: 'penjadwalan.otomatis',
        component: PenjadwalanOtomatis,
        meta: { requiresAuth: true }
    },
];

export default penjadwalanRoutes;
