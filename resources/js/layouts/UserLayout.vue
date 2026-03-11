<template>
    <MainLayout :menuItems="items" />
</template>

<script setup>
import { computed } from 'vue';
import MainLayout from './MainLayout.vue';
import { authStore } from '../store/auth';

// Definimos los elementos del menu lateral
const items = computed(() => {
    const list = [
        {
            label: 'Contenido',
            items: [
                {
                    label: 'Posts',
                    icon: 'pi pi-th-large',
                    route: '/app/posts'
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
                }
            ]
        }
    ];

    // Sacamos los datos del usuario logueado
    const user = authStore().user;

    // Si el usuario es administrador, añadimos la carpeta de Administracion al menu
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
