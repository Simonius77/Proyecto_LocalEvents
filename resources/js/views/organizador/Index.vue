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

            <!-- Marcador especial rojo/naranja de Cancelaciones -->
            <Card v-if="cancelaciones.length > 0" class="col-span-full md:col-span-1 shadow-sm border-2 border-orange-200 dark:border-orange-800 bg-orange-50 dark:bg-orange-900/20 hover:shadow-md transition-shadow">
                <template #title>
                    <div class="flex items-center gap-3 text-lg font-semibold text-orange-700 dark:text-orange-400">
                        <i class="pi pi-bell text-orange-500 bg-orange-100 dark:bg-orange-900/40 p-2 rounded-lg text-sm animate-pulse"></i>
                        Cancelaciones
                    </div>
                </template>
                <template #content>
                    <div class="text-4xl font-bold text-orange-800 dark:text-orange-300 mb-2">{{ cancelaciones.length }}</div>
                    <p class="text-sm text-orange-600 dark:text-orange-500">Reservas por aprobar cancelacion</p>
                </template>
            </Card>
        </div>

        <!-- Acciones rápidas -->
        <div class="mt-8">
            <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-100">Acciones Rapidas</h2>
            <div class="flex flex-wrap gap-4">
                <Button label="Crear Nuevo Evento" icon="pi pi-plus" class="p-button-lg rounded-xl shadow-sm hover:shadow-md transition-all px-6" />
                <Button label="Ver Mis Reservas" icon="pi pi-list" severity="secondary" outlined class="p-button-lg rounded-xl px-6" />
                <Button label="Editar Perfil" icon="pi pi-user-edit" severity="info" text class="p-button-lg rounded-xl px-6" />
            </div>
        </div>

        <!-- Tabla visual de cancelaciones pendientes -->
        <div v-if="cancelaciones.length > 0" class="mt-8">
            <h2 class="text-xl font-bold mb-4 text-orange-800 dark:text-orange-400 flex items-center gap-2">
                <i class="pi pi-exclamation-circle"></i> Aprobar Solicitudes de Cancelacion
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <Card v-for="canc in cancelaciones" :key="canc.id_reserva" class="border border-orange-200 shadow-sm dark:bg-gray-800">
                    <template #content>
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <p class="font-bold text-gray-800 dark:text-white">{{ canc.evento?.nombre }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Usuario: {{ canc.usuario?.name || canc.usuario?.nombre || 'Usuario' }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Entradas: {{ canc.cantidad }} (Aforo se reajustara automatica)</p>
                            </div>
                            <Button label="Aprobar Cancelacion" icon="pi pi-check" severity="danger" @click="aprobarCancelacion(canc.id_reserva)" />
                        </div>
                    </template>
                </Card>
            </div>
            <!-- Necesario para que funcione el popup de aviso de PrimeVue -->
            <ConfirmDialog></ConfirmDialog>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { authStore } from "@/store/auth";
import useCancelaciones from '@/composables/cancelaciones';
import { useConfirm } from "primevue/useconfirm";

// Cargo datos de usuario loggeado, logica de cancelaciones y el servicio de ventana emergente
const auth = authStore();
const confirm = useConfirm();
const { cancelaciones, getCancelacionesPendientes, confirmarCancelacion } = useCancelaciones();

// Cuando entra pide los datos de inmediato
onMounted(() => {
    getCancelacionesPendientes();
});

// Metodo que lanza la alerta antes de que el organizador borre la reserva definitivamente
const aprobarCancelacion = (id) => {
    confirm.require({
        message: '¿Estas seguro de aprobar esta cancelacion? El aforo disponible de tu evento se recalculara automaticamente liberando esa entrada.',
        header: 'Confirmar Cancelacion',
        icon: 'pi pi-exclamation-triangle',
        acceptLabel: 'Si, cancelar reserva',
        rejectLabel: 'Cerrar sin hacer nada',
        acceptClass: 'p-button-danger rounded-xl',
        rejectClass: 'p-button-secondary p-button-text rounded-xl',
        accept: () => {
            confirmarCancelacion(id);
        }
    });
};
</script>

<style scoped>
:deep(.p-card) {
    border-radius: 1.25rem !important;
}
:deep(.p-card-content) {
    padding: 1.25rem !important;
}
</style>
