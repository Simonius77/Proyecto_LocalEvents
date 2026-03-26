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

    // Si yo veo que el usuario es administrador, le añado el acceso al panel central
    if (user?.roles?.some(role => role.name.includes('admin'))) {
        list.push({
            label: 'Administracion',
            items: [
                {
                    label: 'Panel Control',
                    icon: 'pi pi-cog',
                    route: '/admin'
                }
            ]
        });
    }

    return list;
});
</script>
