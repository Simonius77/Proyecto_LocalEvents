<template>
    <div class="p-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Mis Reservas</h1>
            <Button icon="pi pi-refresh" outlined @click="getReservas" :loading="isLoading" />
        </div>

        <div v-if="isLoading && reservas.length === 0" class="flex justify-center p-8">
            <i class="pi pi-spin pi-spinner text-4xl"></i>
        </div>

        <div v-else-if="reservas.length === 0" class="text-center p-12 bg-surface-50 dark:bg-surface-900 rounded-lg">
            <i class="pi pi-calendar-times text-5xl text-surface-400 mb-4"></i>
            <p class="text-xl text-surface-500">Aún no tienes ninguna reserva.</p>
            <Button label="Explorar Eventos" as="router-link" to="/" class="mt-4" />
        </div>

        <div v-else class="grid grid-cols-1 gap-4">
            <Card v-for="reserva in reservas" :key="reserva.id_reserva" class="overflow-hidden">
                <template #title>
                    <div class="flex justify-between items-start">
                        <span>{{ reserva.evento?.nombre }}</span>
                        <Tag :value="reserva.estado.replace('_', ' ').toUpperCase()" :severity="getStatusSeverity(reserva.estado)" />
                    </div>
                </template>
                <template #content>
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="flex-1">
                            <ul class="list-none p-0 m-0 space-y-2">
                                <li class="flex items-center gap-2">
                                    <i class="pi pi-calendar text-primary"></i>
                                    <span class="font-medium">Fecha:</span>
                                    <span>{{ formatDate(reserva.evento?.fecha_inicio) }}</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="pi pi-ticket text-primary"></i>
                                    <span class="font-medium">Cantidad:</span>
                                    <span>{{ reserva.cantidad }}</span>
                                </li>
                                <li class="flex items-center gap-2 text-lg font-bold">
                                    <i class="pi pi-money-bill text-primary"></i>
                                    <span>Total: {{ reserva.total }}€</span>
                                </li>
                            </ul>
                        </div>
                        <div class="flex flex-col gap-2 justify-center">
                            <Button 
                                v-if="reserva.estado === 'pendiente'" 
                                label="Pagar Ahora" 
                                icon="pi pi-credit-card" 
                                severity="success" 
                                class="w-full"
                                @click="pagarReserva(reserva.id_reserva)"
                            />
                            <Button 
                                v-if="reserva.estado !== 'solicitada_cancelacion' && reserva.estado !== 'cancelado'" 
                                label="Solicitar Cancelación" 
                                icon="pi pi-times-circle" 
                                severity="warning" 
                                outlined
                                class="w-full"
                                @click="solicitarCancelacion(reserva.id_reserva)"
                            />
                            <Button 
                                v-if="reserva.estado === 'solicitada_cancelacion' || reserva.estado === 'pendiente'"
                                label="Eliminar Registro" 
                                icon="pi pi-trash" 
                                severity="danger" 
                                text
                                class="w-full"
                                @click="eliminarReserva(reserva.id_reserva)"
                            />
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

const { reservas, isLoading, getReservas, pagarReserva, solicitarCancelacion, eliminarReserva } = useReservas();

// Yo decido el color del aviso segun como este la reserva
const getStatusSeverity = (status) => {
    switch (status) {
        case 'pagado': return 'success';
        case 'pendiente': return 'info';
        case 'solicitada_cancelacion': return 'warning';
        case 'cancelado': return 'danger';
        default: return 'secondary';
    }
};

// Yo cambio el formato de la fecha para que se lea bien
const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleString();
};

// Cuando se carga la pantalla, yo pido la lista de reservas
onMounted(() => {
    getReservas();
});
</script>
