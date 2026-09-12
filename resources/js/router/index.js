import { createRouter, createWebHistory } from 'vue-router';
import DashboardView from '../views/DashboardView.vue';
import ProgramsView from '../views/ProgramsView.vue';
import ProgramDetailView from '../views/ProgramDetailView.vue';
import SettingsView from '../views/SettingsView.vue';
import LoginView from '../views/LoginView.vue';
import { useTaxStore } from '../store/taxStore';
import { resolveAuthRedirect } from '../store/authSession';

const routes = [
    {
        path: '/',
        redirect: '/dashboard',
    },
    {
        path: '/login',
        name: 'login',
        component: LoginView,
        meta: { title: 'Masuk - SCM TaxVault', layout: 'blank', public: true }
    },
    {
        path: '/register',
        name: 'register',
        component: LoginView,
        meta: { title: 'Daftar Akun - SCM TaxVault', layout: 'blank', public: true }
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

    const store = useTaxStore();
    const redirect = resolveAuthRedirect(
        { name: to.name, isPublic: to.meta.public === true },
        { loggedIn: store.isLoggedIn.value, isAdmin: store.isAdmin.value }
    );

    if (redirect) {
        return next(redirect);
    }

    next();
});

export default router;
