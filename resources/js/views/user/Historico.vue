<template>
    <div class="p-4 sm:p-6 max-w-5xl mx-auto">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">Histórico de Compras</h1>
                <p class="text-gray-500 dark:text-gray-400">Consulta los tickets y eventos que has abonado</p>
            </div>
            <Button icon="pi pi-refresh" label="Actualizar" outlined @click="getReservas" :loading="isLoading" class="rounded-xl" />
        </div>

        <div v-if="isLoading && historial.length === 0" class="flex flex-col items-center justify-center p-20">
            <i class="pi pi-spin pi-spinner text-5xl text-blue-500 mb-4"></i>
            <p class="text-gray-500 font-medium">Buscando tu histórico...</p>
        </div>

        <div v-else-if="historial.length === 0" class="text-center p-16 bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="w-20 h-20 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="pi pi-history text-4xl text-blue-500"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2 text-gray-900 dark:text-white">Aún no hay compras en el historial</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-sm mx-auto">Tus eventos comprados se mostrarán aquí una vez que finalices tus pagos.</p>
            <Button label="Ver Eventos" as="router-link" to="/" icon="pi pi-search" class="rounded-xl px-8" />
        </div>

        <div v-else class="grid grid-cols-1 gap-4">
            <Card v-for="reserva in historial" :key="reserva.id_reserva" class="border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                <template #content>
                    <div class="flex flex-col sm:flex-row justify-between items-center p-1">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center text-green-500 shrink-0">
                                <i class="pi pi-check-circle text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white line-clamp-1">{{ reserva.evento?.nombre }}</h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ reserva.cantidad }} {{ reserva.cantidad > 1 ? 'entradas' : 'entrada' }} • 
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Evento el {{ formatEventDate(reserva.evento?.fecha_inicio) }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 sm:mt-0 text-left sm:text-right w-full sm:w-auto">
                            <p class="text-2xl font-black text-gray-900 dark:text-white">{{ reserva.total }}€</p>
                            <p class="text-xs text-gray-400 font-bold tracking-wider mt-1 uppercase">Comprado el {{ formatPurchaseDate(reserva.updated_at) }}</p>
                        </div>
                    </div>
                </template>
            </Card>
        </div>
    </div>
</template>

<script setup>
// En este script recupero y genero la lista del historial de pagos
import { computed, onMounted } from 'vue';
import useReservas from '@/composables/reservas';

// Importo lo necesario para pedir la lista de reservas completas
const { reservas, isLoading, getReservas } = useReservas();

// Filtro las reservas para crear una lista solo con las que esten pagadas o confirmadas
const historial = computed(() => {
    return reservas.value.filter(r => ['pagado', 'confirmado'].includes(r.estado));
});

// Cuando la pantalla cargue pido los datos de las reservas al servidor de inmediato
onMounted(() => {
    getReservas();
});

// Metodo corto para aplicar un formato de fecha bonito y elegante a los eventos
const formatEventDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
};

// Otra utilidad para que la fecha y hora se lea facil y directa
const formatPurchaseDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<style scoped>
:deep(.p-card-content) {
    padding: 1rem !important;
}
</style>
