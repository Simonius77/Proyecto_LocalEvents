<template>
    <MainLayout :menuItems="items" />
</template>

<script setup>
import { computed } from 'vue';
import MainLayout from './MainLayout.vue';
import { authStore } from '../store/auth';

// Define the sidebar menu items
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
                },
                {
                    label: 'Eventos',
                    icon: 'pi pi-calendar',
                    route: '/app/eventos'
                }
            ]
        }
    ];

    // Get logged in user data
    const user = authStore().user;

    // If the user is an administrator, add the Administration folder to the menu
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
