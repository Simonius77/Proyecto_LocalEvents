<template>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Bienvenida -->
            <Card class="col-span-full shadow-sm border-none bg-gradient-to-r from-blue-600 to-indigo-700 text-white overflow-hidden relative">
                <template #content>
                    <div class="flex items-center gap-6 p-4 relative z-10">
                        <Avatar :image="auth.user.avatar" :label="auth.user.nombre ? auth.user.nombre[0] : 'U'" size="xlarge" shape="circle" class="border-2 border-white/30 bg-white/10" />
                        <div>
                            <h1 class="text-3xl font-bold mb-1">¡Hola, {{ auth.user.nombre }}!</h1>
                            <p class="text-blue-100 opacity-90">Bienvenido a tu Panel de Organizador. Aquí podrás gestionar tus eventos y reservas.</p>
                        </div>
                    </div>
                </template>
            </Card>

            <!-- Estadísticas rápidas -->
            <Card class="shadow-sm border border-gray-100 dark:border-gray-800 hover:shadow-md transition-shadow">
                <template #title>
                    <div class="flex items-center gap-3 text-lg font-semibold text-gray-700 dark:text-gray-200">
                        <i class="pi pi-calendar text-blue-500 bg-blue-50 dark:bg-blue-900/20 p-2 rounded-lg text-sm"></i>
                        Mis Eventos
                    </div>
                </template>
                <template #content>
                    <div class="text-4xl font-bold text-gray-800 dark:text-gray-100 mb-2">0</div>
                    <p class="text-sm text-gray-500">Eventos publicados este mes</p>
                </template>
            </Card>

            <Card class="shadow-sm border border-gray-100 dark:border-gray-800 hover:shadow-md transition-shadow">
                <template #title>
                    <div class="flex items-center gap-3 text-lg font-semibold text-gray-700 dark:text-gray-200">
                        <i class="pi pi-users text-green-500 bg-green-50 dark:bg-green-900/20 p-2 rounded-lg text-sm"></i>
                        Asistentes
                    </div>
                </template>
                <template #content>
                    <div class="text-4xl font-bold text-gray-800 dark:text-gray-100 mb-2">0</div>
                    <p class="text-sm text-gray-500">Personas inscritas en total</p>
                </template>
            </Card>

            <Card class="shadow-sm border border-gray-100 dark:border-gray-800 hover:shadow-md transition-shadow">
                <template #title>
                    <div class="flex items-center gap-3 text-lg font-semibold text-gray-700 dark:text-gray-200">
                        <i class="pi pi-chart-line text-purple-500 bg-purple-50 dark:bg-purple-900/20 p-2 rounded-lg text-sm"></i>
                        Ingresos
                    </div>
                </template>
                <template #content>
                    <div class="text-4xl font-bold text-gray-800 dark:text-gray-100 mb-2">0 €</div>
                    <p class="text-sm text-gray-500">Generados por tus eventos</p>
                </template>
            </Card>
        </div>

        <!-- Acciones rápidas -->
        <div class="mt-8">
            <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-100">Acciones Rápidas</h2>
            <div class="flex flex-wrap gap-4">
                <Button label="Crear Nuevo Evento" icon="pi pi-plus" class="p-button-lg rounded-xl shadow-sm hover:shadow-md transition-all px-6" />
                <Button label="Ver Mis Reservas" icon="pi pi-list" severity="secondary" outlined class="p-button-lg rounded-xl px-6" />
                <Button label="Editar Perfil" icon="pi pi-user-edit" severity="info" text class="p-button-lg rounded-xl px-6" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { authStore } from "@/store/auth";
const auth = authStore();
</script>

<style scoped>
:deep(.p-card) {
    border-radius: 1.25rem !important;
}
:deep(.p-card-content) {
    padding: 1.25rem !important;
}
</style>
