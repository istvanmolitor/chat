import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth.js';

const routes = [
    {
        path: '/',
        name: 'home',
        component: () => import('../views/HomeView.vue'),
        meta: { requiresAuth: true, requiresVerified: true },
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('../views/RegisterView.vue'),
        meta: { guest: true },
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('../views/LoginView.vue'),
        meta: { guest: true },
    },
    {
        path: '/verify-email',
        name: 'verify-email',
        component: () => import('../views/VerifyEmailView.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/email/verify/:id/:hash',
        name: 'email-verify-callback',
        component: () => import('../views/EmailVerifyCallbackView.vue'),
    },
    {
        path: '/:pathMatch(.*)*',
        redirect: '/',
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to) => {
    const auth = useAuthStore();

    // Try to load user if not yet loaded
    if (auth.user === null) {
        await auth.fetchUser();
    }

    // Redirect guests away from protected routes
    if (to.meta.requiresAuth && !auth.isLoggedIn) {
        return { name: 'login' };
    }

    // Redirect unverified users to verify-email page
    if (to.meta.requiresVerified && auth.isLoggedIn && !auth.isVerified) {
        return { name: 'verify-email' };
    }

    // Redirect verified+logged-in users away from guest-only pages
    if (to.meta.guest && auth.isLoggedIn) {
        if (!auth.isVerified) {
            return { name: 'verify-email' };
        }
        return { name: 'home' };
    }
});

export default router;

