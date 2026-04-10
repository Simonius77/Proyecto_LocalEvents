<template>
    <div 
        class="fixed w-full z-50 border-b border-gray-200 dark:border-gray-800 transition-all duration-300"
        :class="isDarkTheme ? 'bg-gray-900' : 'bg-white'">
        <nav class="container mx-auto px-6 py-4 flex items-center justify-between">
            <!-- Logo -->
            <router-link to="/" class="flex items-center gap-2">
                <img src="images\Logo_vector.svg" alt="logo" class="h-25 w-auto"/>
            </router-link>

            <!-- Mobile Menu Button -->
            <button
                v-if="!isDesktop"
                @click="visibleMobileMenu = true"
                class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <i class="pi pi-bars text-2xl"></i>
            </button>

            <!-- Desktop Menu -->
            <div v-if="isDesktop" class="flex items-center gap-6">
                <router-link 
                    v-for="link in navLinks" 
                    :key="link.route" 
                    :to="link.route" 
                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors"
                >
                    {{ link.label }}
                </router-link>
                
                <!-- Actions -->
                <div class="flex items-center gap-3 pl-6 border-l border-gray-200 dark:border-gray-700">
                    <LocaleSwitcher />
                    
                    <button 
                        type="button" 
                        @click="toggleDarkMode"
                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <i :class="isDarkTheme ? 'pi-moon' : 'pi-sun'" class="pi text-lg"></i>
                    </button>

                    <template v-if="!auth.authenticated">
                        <Button @click="() => { console.log('Login clicked'); router.push({ name: 'public.login' }); }" label="Iniciar sesión" text size="small" />
                        <Button @click="() => { console.log('Register clicked'); router.push({ name: 'public.register' }); }" label="Registrarse" severity="primary" size="small" />
                    </template>

                    <div v-else>
                        <button 
                            type="button" 
                            @click="toggle"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <Avatar :image="auth.user.avatar" :label="auth.user.nombre ? auth.user.nombre[0] : 'U'" shape="circle" size="small" />
                            <span class="text-sm font-medium hidden xl:inline">{{ auth.user?.nombre }}</span>
                            <i class="pi pi-chevron-down text-xs"></i>
                        </button>
                        <Menu ref="menu" :model="items" popup />
                    </div>
                </div>
            </div>
        </nav>

        <!-- Mobile Menu -->
        <div v-if="visibleMobileMenu" class="fixed inset-0 z-50 lg:hidden">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/50" @click="visibleMobileMenu = false"></div>
            
            <!-- Panel -->
            <div 
                class="absolute right-0 top-0 h-full w-full sm:w-80 shadow-2xl"
                :class="isDarkTheme ? 'bg-gray-900 text-white' : 'bg-white text-gray-900'"
                @click.stop>
                <!-- Header -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-800">
                    <div class="flex items-center gap-2">
                        <img src="/images/logo.svg" alt="logo" class="h-8"/>
                        <span class="font-bold text-lg">Menu</span>
                    </div>
                    <button 
                        @click="visibleMobileMenu = false"
                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <i class="pi pi-times text-xl"></i>
                    </button>
                </div>

                <!-- Content -->
                <div class="flex flex-col gap-4 p-4 h-[calc(100%-5rem)] overflow-y-auto">
                    <!-- Nav Links -->
                    <div class="flex flex-col gap-1">
                        <router-link 
                            v-for="link in navLinks"
                            :key="link.route"
                            :to="link.route" 
                            @click="visibleMobileMenu = false"
                            class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <i :class="link.icon"></i>
                            <span>{{ link.label }}</span>
                        </router-link>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-800"></div>

                    <!-- Auth -->
                    <div class="flex flex-col gap-3">
                        <template v-if="!auth.authenticated">
                            <Button @click="() => { router.push({ name: 'public.login' }); visibleMobileMenu = false; }" label="Iniciar Sesión" outlined class="w-full" />
                            <Button @click="() => { router.push({ name: 'public.register' }); visibleMobileMenu = false; }" label="Registrarse" class="w-full" />
                        </template>
                        <template v-else>
                            <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                                <div class="font-medium">{{ auth.user.nombre }}</div>
                                <div class="text-xs text-gray-500">{{ auth.user.email }}</div>
                            </div>
                            <Button 
                                v-if="auth.authenticated && auth.user?.roles?.some(r => (r.nombre || r.name || '').toLowerCase().includes('admin'))" 
                                label="Panel Administrador" 
                                icon="pi pi-cog" 
                                outlined 
                                @click="() => { visibleMobileMenu = false; router.push('/admin'); }" 
                                class="mb-2"
                            />
                            <Button 
                                v-if="auth.authenticated && auth.user?.roles?.some(r => (r.nombre || r.name || '').toLowerCase().includes('organizador'))" 
                                label="Panel Organizador" 
                                icon="pi pi-calendar" 
                                outlined 
                                @click="() => { visibleMobileMenu = false; router.push('/organizador'); }" 
                                class="mb-2"
                            />
                            <Button 
                                v-if="auth.authenticated && auth.user?.roles?.every(r => !(r.nombre || r.name || '').toLowerCase().includes('admin') && !(r.nombre || r.name || '').toLowerCase().includes('organizador'))" 
                                label="Panel Usuario" 
                                icon="pi pi-user" 
                                outlined 
                                @click="() => { visibleMobileMenu = false; router.push('/app'); }" 
                                class="mb-2"
                            />
                            <Button label="Cerrar Sesion" icon="pi pi-power-off" severity="danger" text @click="handleLogout" />
                        </template>
                    </div>
                    
                    <!-- Theme Toggle -->
                    <div 
                        class="mt-auto flex items-center justify-between p-3 rounded-lg"
                        :class="isDarkTheme ? 'bg-gray-800' : 'bg-gray-50'">
                        <span class="text-sm font-medium">Tema</span>
                        <button 
                            @click="toggleDarkMode"
                            class="p-2 rounded-lg transition-colors"
                            :class="isDarkTheme ? 'hover:bg-gray-700' : 'hover:bg-gray-200'">
                            <i :class="isDarkTheme ? 'pi-moon' : 'pi-sun'" class="pi"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Spacer -->
    <div class="h-20"></div>
</template>

<script setup>
// Importa las herramientas y recursos que necesito para que la barra de navegacion funcione
import { useLayout } from "@/composables/layout.js";
import useAuth from "@/composables/auth";
import { authStore } from "../store/auth";
import LocaleSwitcher from "../components/LocaleSwitcher.vue";
import { ref, computed, onBeforeMount, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";

// Prepara las variables basicas para mover al usuario y saber su estado
const router = useRouter();
const auth = authStore();

// Crea variables reactivas para controlar los menus y la pantalla
const menu = ref();
const visibleMobileMenu = ref(false);
const isScrolled = ref(false);
const isDesktop = ref(window.innerWidth >= 992);

// Extrae funciones utiles de los archivos externos
const { processing, logout } = useAuth();
const { toggleDarkMode, isDarkTheme, setDefaultMode } = useLayout();

// Controla los enlaces que se ven arriba en el menu dinamico
const navLinks = computed(() => {
    const links = [
        { label: 'Inicio', route: '/', icon: 'pi pi-home' }
    ];
    
    // Si detecta que el usuario tiene un rol, le pone su panel correspondiente
    if (auth.authenticated && auth.user?.roles) {
        if (auth.user.roles.some(r => (r.nombre || r.name || '').toLowerCase().includes('admin'))) {
            links.push({ label: 'Panel Administrador', route: '/admin', icon: 'pi pi-cog' });
        } else if (auth.user.roles.some(r => (r.nombre || r.name || '').toLowerCase().includes('organizador'))) {
            links.push({ label: 'Panel Organizador', route: '/organizador', icon: 'pi pi-calendar' });
        } else {
            // Todos los usuarios autenticados que no son admin u organizador ven el panel de usuario
            links.push({ label: 'Panel Usuario', route: '/app', icon: 'pi pi-user' });
        }
    }
    return links;
});

// Construye el menu desplegable del perfil del usuario con todas sus opciones
const items = computed(() => [
    {
        items: [
            { label: 'Perfil', icon: 'pi pi-user', command: () => router.push('/app/profile') },
            { 
                label: 'Panel Administrador', 
                icon: 'pi pi-cog', 
                command: () => router.push('/admin'),
                visible: auth.user?.roles?.some(r => (r.nombre || r.name || '').toLowerCase().includes('admin')) || false
            },
            { 
                label: 'Panel Organizador', 
                icon: 'pi pi-calendar', 
                command: () => router.push('/organizador'),
                visible: auth.user?.roles?.some(r => (r.nombre || r.name || '').toLowerCase().includes('organizador')) || false
            },
            { 
                label: 'Panel Usuario', 
                icon: 'pi pi-user', 
                command: () => router.push('/app'),
                visible: auth.user?.roles?.every(r => !(r.nombre || r.name || '').toLowerCase().includes('admin') && !(r.nombre || r.name || '').toLowerCase().includes('organizador')) || false
            },
            { separator: true },
            {
                label: 'Cerrar sesión',
                icon: 'pi pi-power-off',
                class: 'text-red-500',
                command: () => {
                    handleLogout()
                }
            }
        ]
    }
]);

// Metodo para abrir y cerrar el menu desplegable de la foto de perfil
const toggle = (event) => {
    menu.value.toggle(event);
};

// Envia al usuario directo a su panel de control y cierra el menu de la vista movil
const navigateToDashboard = () => {
    visibleMobileMenu.value = false;
    router.push('/app');
}

// Cierra la sesion del usuario de forma segura usando el auth y cierra el menu movil
const handleLogout = () => {
    visibleMobileMenu.value = false;
    logout();
}

// Esta variable detecta si la persona ha bajado un poquito la pagina
const handleScroll = () => {
    isScrolled.value = window.scrollY > 20;
}

// Comprueba la anchura real de la pantalla para saber si estamos en pc o en movil
const handleResize = () => {
    isDesktop.value = window.innerWidth >= 992;
}

// Cuando se carga todo arranco los escuchadores de los eventos del navegador
onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    window.addEventListener('resize', handleResize);
});

// Limpia la memoria borrando los escuchadores si navegamos a otra pantalla
onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    window.removeEventListener('resize', handleResize);
});

// Antes de montar la imagen aplico forzosamente el color generico inicial
onBeforeMount(() => {
    setDefaultMode()
})
</script>
