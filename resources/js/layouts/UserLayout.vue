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
                    label: 'Panel de Usuario',
                    icon: 'pi pi-home',
                    route: '/app'
                },
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
                    label: 'Mi Perfil',
                    icon: 'pi pi-user',
                    route: '/app/profile'
                }
            ]
        }
    ];

    // Saco los datos del usuario que esta conectado de forma segura
    const user = computed(() => authStore().user);

    // Si el usuario es administrador u organizador, le añado el acceso al panel correspondiente
    // Comprobamos tanto el sistema de roles de Spatie como el campo 'rol' de la base de datos
    const isAdmin = computed(() => {
        return user.value?.roles?.some(role => role.name?.toLowerCase().includes('admin')) || 
               user.value?.rol === 'administrador';
    });

    const isOrganizador = computed(() => {
        return user.value?.roles?.some(role => role.name?.toLowerCase().includes('organizador')) || 
               user.value?.rol === 'organizador';
    });

    if (isAdmin.value || isOrganizador.value) {
        list.push({
            label: 'Administracion',
            items: [
                ...(isAdmin.value ? [{
                    label: 'Panel Admin',
                    icon: 'pi pi-cog',
                    route: '/admin'
                }] : []),
                ...(isAdmin.value || isOrganizador.value ? [{
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
