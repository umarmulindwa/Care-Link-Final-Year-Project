import { createRouter, createWebHistory } from 'vue-router'

const routes = [
    {
        path: '/',
        name: 'dashboard',
        meta: { 
                title: 'Dashboard', 
                authRequired: true, 
            },
        component: () => import('../views/home.vue'),
    },
    {
        path: '/auth/login-1',
        meta: { authRequired: true, title: 'Login' },
        component: () => import('../views/sample-pages/login-sample.vue')
    },
    {
        path: '/login',
        name: 'login',
        meta: { title: 'Login' },
        component: () => import('../views/account/login.vue')
    },
    {
        path: '/auth/register',
        name: 'register',
        meta: { title: 'Register' },
        component: () => import('../views/account/register.vue')
    },
    {
        path: '/auth/login-2',
        meta: { authRequired: true, title: 'Login' },
        component: () => import('../views/sample-pages/login-2.vue')
    },
    {
        path: '/auth/register-1',
        meta: { title: 'Register' },
        component: () => import('../views/account/register.vue')
    },
    {
        path: '/forget-password',
        meta: { title: 'Forget Password' },
        component: () => import('../views/account/forgot-password.vue')
    },
    {
        path: '/reset-password/:token',
        meta: { title: 'Reset Password' },
        component: () => import('../views/account/reset-password.vue')
    },
    {
        path: '/auth/register-2',
        meta: { authRequired: true, title: 'Register' },
        component: () => import('../views/sample-pages/register-2.vue')
    },
    {
        path: '/auth/recoverpw',
        meta: { authRequired: true, title: 'Reset Password' },
        component: () => import('../views/sample-pages/recoverpw-sample.vue')
    },
    {
        path: '/auth/recoverpwd-2',
        meta: { authRequired: true, title: 'Reset Password' },
        component: () => import('../views/sample-pages/recoverpwd-2.vue')
    },
    {
        path: '/auth/lock-screen',
        meta: { authRequired: true, title: 'Lock Screen' },
        component: () => import('../views/sample-pages/lockscreen.vue')
    },
    {
        path: '/auth/lock-screen-2',
        meta: { authRequired: true, title: 'Lock Screen' },
        component: () => import('../views/sample-pages/lockscreen-2.vue')
    },
    {
        path: '/auth/confirm-mail',
        meta: { authRequired: true, title: 'Confirm Mail' },
        component: () => import('../views/sample-pages/confirm-mail.vue')
    },
    {
        path: '/auth/confirm-mail-2',
        meta: { authRequired: true, title: 'Confirm Mail' },
        component: () => import('../views/sample-pages/confirm-mail-2.vue')
    },
    {
        path: '/auth/email-verification',
        meta: { authRequired: true, title: 'Email Verification' },
        component: () => import('../views/sample-pages/email-verification.vue')
    },
    {
        path: '/auth/email-verification-2',
        meta: { authRequired: true, title: 'Email Verification' },
        component: () => import('../views/sample-pages/email-verification-2.vue')
    },
    {
        path: '/auth/two-step-verification',
        meta: { authRequired: true, title: 'Two Step Verification' },
        component: () => import('../views/sample-pages/two-step-verification.vue')
    },
    {
        path: '/auth/two-step-verification-2',
        meta: { authRequired: true, title: 'Two Step Verification' },
        component: () => import('../views/sample-pages/two-step-verification-2.vue')
    },
    {
        path: '/pages/starter',
        meta: { authRequired: true, title: 'Starter Page' },
        component: () => import('../views/utility/starter.vue')
    },
    {
        path: '/pages/maintenance',
        meta: { authRequired: true, title: 'Maintenance' },
        component: () => import('../views/utility/maintenance.vue')
    },
    {
        path: '/pages/coming-soon',
        meta: { authRequired: true, title: 'Comming Soon' },
        component: () => import('../views/utility/coming-soon.vue')
    },
    {
        path: '/pages/404',
        meta: { authRequired: true, title: '404' },
        component: () => import('../views/utility/404.vue')
    },
    {
        path: '/pages/500',
        meta: { authRequired: true, title: '500' },
        component: () => import('../views/utility/500.vue')
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

// Before each route evaluates...
router.beforeEach(async (routeTo, routeFrom, next) => {
    // set title name
    if (routeTo.meta.title != undefined) {
        document.title = routeTo.meta.title + " | Skote Laravel 10 + Vue 3 Admin & Dashboard";
    }

    const authRequired = routeTo.matched.some((route) => route.meta.authRequired);
    if (!authRequired) return next();

    if (localStorage.getItem('user')) {
        next();
    } else {
        next({ name: 'login', query: { redirectFrom: routeTo.fullPath } });
    }

});

export default router;