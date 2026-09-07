import { createRouter, createWebHistory } from 'vue-router';
import DashboardView from '../views/DashboardView.vue';
import ProgramsView from '../views/ProgramsView.vue';
import ProgramDetailView from '../views/ProgramDetailView.vue';
import SettingsView from '../views/SettingsView.vue';

const routes = [
    {
        path: '/',
        redirect: '/dashboard',
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('../views/LoginView.vue'),
        meta: { title: 'Masuk - SCM TaxVault', layout: 'blank' }
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('../views/LoginView.vue'),
        meta: { title: 'Daftar Akun - SCM TaxVault', layout: 'blank' }
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: DashboardView,
        meta: { title: 'Overview - SCM TaxVault' }
    },
    {
        path: '/programs',
        name: 'programs',
        component: ProgramsView,
        meta: { title: 'Arsip Program - SCM TaxVault' }
    },
    {
        path: '/programs/:id',
        name: 'program-detail',
        component: ProgramDetailView,
        meta: { title: 'Detail Program - SCM TaxVault' }
    },
    {
        path: '/users',
        name: 'users',
        component: () => import('../views/UsersView.vue'),
        meta: { title: 'Manajemen User - SCM TaxVault' }
    },
    {
        path: '/settings',
        name: 'settings',
        component: SettingsView,
        meta: { title: 'Pengaturan - SCM TaxVault' }
    },
    {
        path: '/:pathMatch(.*)*',
        redirect: '/dashboard',
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior() {
        return { top: 0 };
    }
});

router.beforeEach((to, from, next) => {
    if (to.meta.title) {
        document.title = to.meta.title;
    }
    next();
});

export default router;
