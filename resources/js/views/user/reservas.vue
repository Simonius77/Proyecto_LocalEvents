<template>
    <div class="p-4 sm:p-6 max-w-5xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">Mis Reservas</h1>
                <p class="text-gray-500 dark:text-gray-400">Gestiona tus entradas y solicitudes de cancelación</p>
            </div>
            <Button 
                icon="pi pi-refresh" 
                label="Actualizar"
                outlined 
                @click="getReservas" 
                :loading="isLoading" 
                class="rounded-xl"
            />
        </div>

        <!-- Loading State -->
        <div v-if="isLoading && reservas.length === 0" class="flex flex-col items-center justify-center p-20">
            <i class="pi pi-spin pi-spinner text-5xl text-blue-500 mb-4"></i>
            <p class="text-gray-500 font-medium">Cargando tus reservas...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="reservas.length === 0" class="text-center p-16 bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="w-20 h-20 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="pi pi-calendar-times text-4xl text-blue-500"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2 text-gray-900 dark:text-white">Aún no tienes ninguna reserva</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-sm mx-auto">
                Explora los próximos eventos locales y reserva tu lugar ahora mismo.
            </p>
            <Button label="Explorar Eventos" as="router-link" to="/" icon="pi pi-search" class="rounded-xl px-8" />
        </div>

        <!-- List View -->
        <div v-else class="grid grid-cols-1 gap-6">
            <Card v-for="reserva in reservas" :key="reserva.id_reserva" class="overflow-hidden border border-gray-100 dark:border-gray-800 rounded-3xl shadow-sm hover:shadow-md transition-shadow">
                <template #content>
                    <div class="flex flex-col md:flex-row gap-8 p-1">
                        <!-- Left: Event Basic Info -->
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-4">
                                <Tag :value="formatStatus(reserva.estado)" :severity="getStatusSeverity(reserva.estado)" class="px-3 py-1 rounded-lg uppercase text-xs font-bold" />
                                <span class="text-xs text-gray-400 font-mono">ID: #{{ reserva.id_reserva }}</span>
                            </div>
                            
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 line-clamp-2">
                                {{ reserva.evento?.nombre }}
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-6">
                                <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                                    <div class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center">
                                        <i class="pi pi-calendar text-blue-500"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase font-bold text-gray-400">Fecha y Hora</p>
                                        <p class="text-sm font-medium">{{ formatDate(reserva.evento?.fecha_inicio) }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                                    <div class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center">
                                        <i class="pi pi-ticket text-green-500"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase font-bold text-gray-400">Entradas</p>
                                        <p class="text-sm font-medium">{{ reserva.cantidad }} {{ reserva.cantidad > 1 ? 'Personas' : 'Persona' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Pricing and Actions -->
                        <div class="md:w-64 flex flex-col justify-between border-t md:border-t-0 md:border-l border-gray-100 dark:border-gray-800 pt-6 md:pt-0 md:pl-8">
                            <div class="mb-6">
                                <p class="text-xs uppercase font-bold text-gray-400 mb-1">Total abonado</p>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-3xl font-black text-gray-900 dark:text-white">
                                        {{ reserva.total == 0 ? 'Gratis' : reserva.total + '€' }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex flex-col gap-2">
                                <Button 
                                    v-if="reserva.estado === 'pendiente' && reserva.total > 0" 
                                    label="Realizar Pago" 
                                    icon="pi pi-credit-card" 
                                    severity="success" 
                                    class="w-full rounded-xl py-3 font-bold"
                                    @click="pagarReserva(reserva.id_reserva)"
                                />
                                
                                <Button 
                                    v-if="['pendiente', 'pagado', 'confirmado'].includes(reserva.estado)" 
                                    label="Pedir Cancelación" 
                                    icon="pi pi-times-circle" 
                                    severity="secondary" 
                                    outlined
                                    class="w-full rounded-xl border-gray-200 dark:border-gray-700 hover:bg-red-50 dark:hover:bg-red-900/10 hover:text-red-500 dark:hover:text-red-400 hover:border-red-200 dark:hover:border-red-800 transition-all font-medium"
                                    @click="solicitarCancelacion(reserva.id_reserva)"
                                />

                                <Button 
                                    v-if="['solicitada_cancelacion', 'cancelado'].includes(reserva.estado)"
                                    label="Eliminar del Historial" 
                                    icon="pi pi-trash" 
                                    severity="danger" 
                                    text
                                    class="w-full rounded-xl hover:bg-red-50 dark:hover:bg-red-900/10 transition-all"
                                    @click="confirmarBorrado(reserva.id_reserva)"
                                />
                            </div>
                        </div>
                    </div>
                </template>
            </Card>
        </div>
    </div>
</template>

<script setup>
// Yo traigo las herramientas para manejar las reservas
import { onMounted } from 'vue';
import useReservas from '@/composables/reservas';
import { useConfirm } from "primevue/useconfirm";

const confirm = useConfirm();
const { reservas, isLoading, getReservas, pagarReserva, solicitarCancelacion, eliminarReserva } = useReservas();

// Yo decido el color del aviso segun como este la reserva
const getStatusSeverity = (status) => {
    switch (status) {
        case 'pagado':
        case 'confirmado': return 'success';
        case 'pendiente': return 'info';
        case 'solicitada_cancelacion': return 'warning';
        case 'cancelado': return 'danger';
        default: return 'secondary';
    }
};

const formatStatus = (status) => {
    return status.replace(/_/g, ' ');
};

// Yo cambio el formato de la fecha para que se lea bien
const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const confirmarBorrado = (id) => {
    confirm.require({
        message: '¿Estás seguro de que quieres eliminar este registro de tu historial?',
        header: 'Confirmar Eliminación',
        icon: 'pi pi-exclamation-triangle',
        acceptLabel: 'Sí, eliminar',
        rejectLabel: 'No, mantener',
        acceptClass: 'p-button-danger rounded-xl',
        rejectClass: 'p-button-secondary p-button-text rounded-xl',
        accept: () => {
            eliminarReserva(id);
        }
    });
};

// Cuando se carga la pantalla, yo pido la lista de reservas
onMounted(() => {
    getReservas();
});
</script>

<style scoped>
:deep(.p-card-content) {
    padding: 1.5rem !important;
}
</style>
