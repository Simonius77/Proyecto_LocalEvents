<template>
    <MainLayout :menuItems="items" />
</template>

<script setup>
import { computed } from 'vue';
import MainLayout from './MainLayout.vue';
import { authStore } from '../store/auth';

// Defino los elementos del menu lateral
const items = computed(() => {
    // Empiezo con la lista basica de opciones
    const list = [
        {
            label: 'Contenido',
            items: [
                {
                    label: 'Posts',
                    icon: 'pi pi-th-large',
                    route: '/app/posts'
                },
                {
                    label: 'Mis Reservas',
                    icon: 'pi pi-calendar',
                    route: '/app/reservas'
                },
                // Enlace directo al carrito del usuario
                {
                    label: 'Mi Carrito',
                    icon: 'pi pi-shopping-cart',
                    route: '/app/carrito'
                },
                // Enlace directo al registro de compras pasadas
                {
                    label: 'Histórico de Compras',
                    icon: 'pi pi-history',
                    route: '/app/historico'
                },
            ]
        },
        {
            label: 'Cuenta',
            items: [
                {
                    label: 'Perfil',
                    icon: 'pi pi-user',
                    route: '/app/profile'
                },
                {
                    label: 'Eventos',
                    icon: 'pi pi-calendar',
                    route: '/app/eventos'
                }
            ]
        }
    ];

    // Saco los datos del usuario que esta conectado
    const user = authStore().user;

    // Si el usuario es administrador u organizador, le añado el acceso al panel correspondiente
    if (user?.roles?.some(role => role.name.toLowerCase().includes('admin') || role.name.toLowerCase().includes('organizador'))) {
        const isAdmin = user.roles.some(role => role.name.toLowerCase().includes('admin'));
        const isOrganizador = user.roles.some(role => role.name.toLowerCase().includes('organizador'));

        list.push({
            label: 'Administracion',
            items: [
                ...(isAdmin ? [{
                    label: 'Panel Admin',
                    icon: 'pi pi-cog',
                    route: '/admin'
                }] : []),
                ...(isOrganizador ? [{
                    label: 'Panel Organizador',
                    icon: 'pi pi-calendar',
                    route: '/organizador'
                }] : [])
            ]
        });
    }

    return list;
});
</script>
