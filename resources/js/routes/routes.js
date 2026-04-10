import { authStore } from "../store/auth";

const AuthenticatedLayout = () => import('../layouts/AdminLayout.vue');
const AuthenticatedUserLayout = () => import('../layouts/UserLayout.vue');
const GuestLayout = () => import('../layouts/GuestLayout.vue');
const Home = () => import('../views/public/home/index.vue');
const Login = () => import('../views/auth/login/Login.vue');

async function requireLogin(to, from, next) {
    const auth = authStore();
    const isLogin = !!auth.authenticated && !!(auth.user?.name || auth.user?.nombre);

    if (isLogin) {
        next()
    } else {
        if (auth.authenticated) auth.logout();
        next('/login')
    }
}

const hasAdmin = (roles = []) =>
    roles.some((role) => (role?.nombre || role?.name || '').toLowerCase().includes('admin'));

async function guest(to, from, next) {
    const auth = authStore()
    // Check if truly logged in (has both flag and user data)
    let isLogin = !!auth.authenticated && !!(auth.user?.name || auth.user?.nombre);

    if (isLogin) {
        next('/')
    } else {
        // If they are marked as authenticated but have no name, fix the stale state
        if (auth.authenticated) {
            auth.logout();
        }
        next()
    }
}

async function requireAdmin(to, from, next) {
    const auth = authStore();
    let isLogin = !!auth.authenticated && !!(auth.user?.name || auth.user?.nombre);
    let user = auth.user;

    if (isLogin) {
        if (hasAdmin(user.roles)) {
            next()
        } else {
            next('/app')
        }
    } else {
        if (auth.authenticated) auth.logout();
        next('/login')
    }
}

async function requireOrganizador(to, from, next) {
    const auth = authStore();
    let isLogin = !!auth.authenticated && !!(auth.user?.name || auth.user?.nombre);
    let user = auth.user;

    if (isLogin) {
        if (user.roles?.some(r => (r.nombre || r.name || '').toLowerCase().includes('organizador') || (r.nombre || r.name || '').toLowerCase().includes('admin')) || 
            user.rol === 'administrador' || user.rol === 'organizador') {
            next()
        } else {
            next('/app')
        }
    } else {
        if (auth.authenticated) auth.logout();
        next('/login')
    }
}

// Guardo aqui todas las rutas de la aplicacion
export default [
    {
        path: '/organizador',
        component: AuthenticatedLayout,
        beforeEnter: requireOrganizador,
        meta: { breadCrumb: 'Organizador' },
        children: [
            {
                name: 'organizador.index',
                path: '',
                component: () => import('../views/organizador/Index.vue'),
                meta: {
                    breadCrumb: 'Panel Organizador',
                    hideBreadcrumb: true
                }
            }
        ]
    },
    {
        // Esta es la ruta para la pagina de inicio que ve todo el mundo
        path: '/',
        component: GuestLayout,
        children: [
            {
                path: '',
                name: 'public.home',
                component: Home,
            },

            {
                // Mando aqui a la gente para que entre con su cuenta
                path: 'login',
                name: 'public.login',
                component: Login,
                beforeEnter: guest,
            },
            {
                name: 'public.register',
                path: 'register',
                component: () => import('../views/auth/register/index.vue'),
                beforeEnter: guest,
            },
            {
                name: 'public.aviso-legal',
                path: 'legal/aviso-legal',
                component: () => import('../views/public/legal/AvisoLegal.vue'),
            },
            {
                name: 'public.privacidad',
                path: 'legal/privacidad',
                component: () => import('../views/public/legal/Privacidad.vue'),
            },
            {
                name: 'public.cookies',
                path: 'legal/cookies',
                component: () => import('../views/public/legal/Cookies.vue'),
            },
            {
                path: 'forgot-password',
                name: 'auth.forgot-password',
                component: () => import('../views/auth/passwords/Email.vue'),
                beforeEnter: guest,
            },
            {
                path: 'reset-password/:token',
                name: 'auth.reset-password',
                component: () => import('../views/auth/passwords/Reset.vue'),
                beforeEnter: guest,
            },
        ]
    },

    {
        path: '/app',
        component: AuthenticatedUserLayout,
        name: 'app',
        beforeEnter: requireLogin,
        meta: { breadCrumb: '.' },
        children: [
            {
                name: 'app.dashboard',
                path: '',
                component: () => import('../views/user/Dashboard.vue'),
                meta: {
                    breadCrumb: 'Dashboard',
                    hideBreadcrumb: true
                },
            },
            {
                name: 'app.profile',
                path: 'profile',
                component: () => import('../views/user/profile.vue'),
                meta: {
                    breadCrumb: 'Perfil',
                },
            },
            {
                name: 'app.reservas',
                path: 'reservas',
                component: () => import('../views/user/reservas.vue'),
                meta: {
                    breadCrumb: 'Mis Reservas',
                },
            },
            // Ruta para gestionar el carrito de la compra con las entradas pendientes
            {
                name: 'app.carrito',
                path: 'carrito',
                component: () => import('../views/user/Carrito.vue'),
                meta: {
                    breadCrumb: 'Carrito',
                },
            },
            // Ruta para consultar los tickets y facturas ya abonados
            {
                name: 'app.historico',
                path: 'historico',
                component: () => import('../views/user/Historico.vue'),
                meta: {
                    breadCrumb: 'Histórico de Compras',
                },
            },

        ]
    },


    {
        path: '/admin',
        component: AuthenticatedLayout,
        beforeEnter: requireAdmin,
        meta: { breadCrumb: 'Dashboard' },
        children: [
            {
                name: 'admin.index',
                path: '',
                component: () => import('../views/admin/index.vue'),
                meta: {
                    breadCrumb: 'Admin',
                    hideBreadcrumb: true
                }
            },
            {
                name: 'profile.index',
                path: 'profile',
                component: () => import('../views/admin/profile/index.vue'),
                meta: { breadCrumb: 'Profile' }
            },

            {
                name: 'categories',
                path: 'categories',
                meta: { breadCrumb: 'Categories' },
                children: [
                    {
                        name: 'categories.index',
                        path: '',
                        component: () => import('../views/admin/categories/Index.vue'),
                        meta: {
                            breadCrumb: 'View category',
                            hideBreadcrumb: true
                        }
                    },
                ]
            },

            {
                name: 'eventos',
                path: 'eventos',
                meta: { breadCrumb: 'Eventos' },
                children: [
                    {
                        name: 'eventos.index',
                        path: '',
                        component: () => import('../views/admin/eventos/Index.vue'),
                        meta: {
                            breadCrumb: 'View eventos',
                            hideBreadcrumb: true
                        }
                    },
                ]
            },

            {
                name: 'permissions',
                path: 'permissions',
                meta: { breadCrumb: 'Permisos' },
                children: [
                    {
                        name: 'permissions.index',
                        path: '',
                        component: () => import('../views/admin/permissions/Index.vue'),
                        meta: {
                            breadCrumb: 'Permissions',
                            hideBreadcrumb: true
                        }
                    },
                ]
            },
            {
                name: 'users',
                path: 'users',
                meta: { breadCrumb: 'Usuarios' },
                children: [
                    {
                        name: 'users.index',
                        path: '',
                        component: () => import('../views/admin/users/Index.vue'),
                        meta: {
                            breadCrumb: 'Usuarios',
                            hideBreadcrumb: true // Ocultar breadcrumb del layout porque la Card tiene su propio header
                        }
                    },
                    {
                        name: 'users.create',
                        path: 'create',
                        component: () => import('../views/admin/users/Create.vue'),
                        meta: {
                            breadCrumb: 'Crear Usuario',
                            linked: false
                        }
                    },
                    {
                        name: 'users.edit',
                        path: 'edit/:id',
                        component: () => import('../views/admin/users/Edit.vue'),
                        meta: {
                            breadCrumb: 'Editar Usuario',
                            linked: false
                        }
                    }
                ]
            },

            {
                name: 'roles',
                path: 'roles',
                meta: { breadCrumb: 'Roles' },
                children: [
                    {
                        name: 'roles.index',
                        path: '',
                        component: () => import('../views/admin/roles/Index.vue'),
                        meta: {
                            breadCrumb: 'Roles',
                            hideBreadcrumb: true
                        }
                    },
                    {
                        name: 'admin.roles.edit',
                        path: 'edit/:id',
                        component: () => import('../views/admin/roles/Edit.vue'),
                        meta: {
                            breadCrumb: 'Editar Rol',
                            linked: false
                        }
                    }
                ]
            },
        ]
    },
    {
        path: "/:pathMatch(.*)*",
        name: 'NotFound',
        component: () => import("../views/errors/404.vue"),
    },
];
