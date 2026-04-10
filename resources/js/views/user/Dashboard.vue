<template>
    <div class="p-4 sm:p-6 max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                ¡Hola, {{ auth.user?.nombre || auth.user?.name }}!
            </h1>
            <p class="text-gray-500 dark:text-gray-400">Bienvenido a tu panel de control personal.</p>
        </div>

        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <Card class="border-t-4 border-blue-500 rounded-2xl shadow-sm overflow-hidden transition-all hover:shadow-md">
                <template #content>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center text-blue-500">
                            <i class="pi pi-calendar text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Mis Reservas</p>
                            <p class="text-3xl font-black text-gray-900 dark:text-white">{{ stats.reservas }}</p>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <Button label="Ver todas" text size="small" as="router-link" to="/app/reservas" icon="pi pi-arrow-right" iconPos="right" />
                    </div>
                </template>
            </Card>

            <Card class="border-t-4 border-orange-500 rounded-2xl shadow-sm overflow-hidden transition-all hover:shadow-md">
                <template #content>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-orange-50 dark:bg-orange-900/20 rounded-xl flex items-center justify-center text-orange-500">
                            <i class="pi pi-shopping-cart text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">En el Carrito</p>
                            <p class="text-3xl font-black text-gray-900 dark:text-white">{{ carrito.length }}</p>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <Button label="Ir al carrito" text size="small" as="router-link" to="/app/carrito" severity="warning" icon="pi pi-arrow-right" iconPos="right" />
                    </div>
                </template>
            </Card>

            <Card class="border-t-4 border-green-500 rounded-2xl shadow-sm overflow-hidden transition-all hover:shadow-md">
                <template #content>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center text-green-500">
                            <i class="pi pi-history text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Total Invertido</p>
                            <p class="text-3xl font-black text-gray-900 dark:text-white">{{ stats.totalInvertido }}€</p>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <Button label="Ver historial" text size="small" as="router-link" to="/app/historico" severity="success" icon="pi pi-arrow-right" iconPos="right" />
                    </div>
                </template>
            </Card>
        </div>

        <!-- Call to Action -->
        <Card class="bg-gradient-to-r from-blue-600 to-indigo-700 dark:from-blue-800 dark:to-indigo-900 text-white rounded-3xl shadow-xl overflow-hidden mb-12 border-none">
            <template #content>
                <div class="flex flex-col md:flex-row items-center gap-8 py-4 px-2">
                    <div class="flex-1 text-center md:text-left">
                        <h2 class="text-3xl font-black mb-4">¿Buscas algo nuevo?</h2>
                        <p class="text-blue-100 text-lg mb-8 max-w-xl">
                            Descubre los eventos más emocionantes cerca de ti y reserva tus entradas antes de que se agoten.
                        </p>
                        <Button label="Explorar Próximos Eventos" icon="pi pi-search" class="p-button-lg rounded-2xl px-10 bg-white text-blue-700 hover:bg-blue-50 border-none font-bold" @click="$router.push('/')" />
                    </div>
                    <div class="hidden md:flex shrink-0">
                        <i class="pi pi-ticket text-[120px] opacity-20 rotate-12"></i>
                    </div>
                </div>
            </template>
        </Card>

        <!-- Pending Reservations Section -->
        <div class="mb-12" v-if="pendientes.length > 0">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold flex items-center gap-2">
                    <i class="pi pi-clock text-orange-500"></i>
                    Tus reservas pendientes de pago
                </h3>
                <Button label="Ver todas" text as="router-link" to="/app/reservas" icon="pi pi-chevron-right" iconPos="right" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <Card v-for="reserva in pendientes.slice(0, 4)" :key="reserva.id_reserva" class="border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden group">
                    <template #content>
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-1">
                            <div class="flex-1 w-full sm:w-auto cursor-pointer" @click="showDetalles(reserva.evento)">
                                <h4 class="font-bold text-gray-900 dark:text-white mb-2 line-clamp-1 truncate transition-colors group-hover:text-blue-500">{{ reserva.evento?.nombre }}</h4>
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <div class="flex items-center gap-1">
                                        <i class="pi pi-calendar"></i>
                                        <span>{{ formatEventDate(reserva.evento?.fecha_inicio) }}</span>
                                    </div>
                                    <span class="mx-1">•</span>
                                    <div class="flex items-center gap-2">
                                        <Button icon="pi pi-minus" size="small" rounded text class="h-6 w-6" @click.stop="cambiarCantidad(reserva, -1)" :disabled="reserva.cantidad <= 1" />
                                        <span class="font-bold text-gray-700 dark:text-gray-300">{{ reserva.cantidad }}</span>
                                        <Button icon="pi pi-plus" size="small" rounded text class="h-6 w-6" @click.stop="cambiarCantidad(reserva, 1)" />
                                    </div>
                                    <span class="mx-1">•</span>
                                    <span class="font-bold text-blue-600 dark:text-blue-400">{{ reserva.total }}€</span>
                                </div>
                            </div>
                            <Button 
                                :label="yaEnCarrito(reserva.id_reserva) ? 'En Carrito' : 'Añadir al Carrito'" 
                                :icon="yaEnCarrito(reserva.id_reserva) ? 'pi pi-check' : 'pi pi-shopping-cart'" 
                                :severity="yaEnCarrito(reserva.id_reserva) ? 'secondary' : 'success'" 
                                :disabled="yaEnCarrito(reserva.id_reserva)"
                                class="w-full sm:w-auto rounded-xl px-4 py-2 text-sm font-bold"
                                @click="aniadirACarrito(reserva)"
                            />
                        </div>
                    </template>
                </Card>
            </div>
        </div>

        <!-- Diálogo de detalles del evento -->
        <Dialog v-model:visible="displayDialog" modal :header="selectedEvento?.nombre" :style="{ width: '50rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" :dismissableMask="true" class="event-detail-dialog">
            <template v-if="selectedEvento">
                <img v-if="selectedEvento.imagen" :src="selectedEvento.imagen" :alt="selectedEvento.nombre" class="w-full h-auto max-h-96 object-cover mb-6 rounded-2xl shadow-sm" />
                <div class="flex flex-wrap items-center gap-4 mb-6 text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-800 px-3 py-1.5 rounded-lg border border-gray-100 dark:border-gray-700">
                        <i class="pi pi-calendar text-blue-500"></i>
                        <span class="font-medium">{{ formatEventDate(selectedEvento.fecha_inicio) }}</span>
                    </div>
                    <div v-if="selectedEvento.aforo" class="flex items-center gap-2 bg-gray-50 dark:bg-gray-800 px-3 py-1.5 rounded-lg border border-gray-100 dark:border-gray-700">
                        <i class="pi pi-users text-green-500"></i>
                        <span class="font-medium">Aforo: {{ selectedEvento.aforo }}</span>
                    </div>
                    <div class="flex items-center gap-2 bg-blue-50 dark:bg-blue-900/20 px-3 py-1.5 rounded-lg border border-blue-100 dark:border-blue-800 ml-auto">
                        <span class="text-xl font-black text-blue-600 dark:text-blue-400">
                            {{ selectedEvento.precio > 0 ? selectedEvento.precio + '€' : 'Gratis' }}
                        </span>
                    </div>
                </div>
                <div class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line mb-4 bg-gray-50/50 dark:bg-gray-800/50 p-6 rounded-2xl border border-gray-100 dark:border-gray-800">
                    {{ selectedEvento.descripcion }}
                </div>
            </template>
        </Dialog>

        <!-- Informative section -->
        <div class="flex flex-col md:flex-row gap-8">
            <div class="flex-1">
                <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <i class="pi pi-info-circle text-blue-500"></i>
                    ¿Cómo funciona mi panel?
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
                        <h4 class="font-bold text-lg mb-2">1. Reservas</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Cuando "Reserves" un evento en la home, aparecerá aquí como pendiente de pago.</p>
                    </div>
                    <div class="p-4 bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
                        <h4 class="font-bold text-lg mb-2">2. Pago</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Añade tus reservas al carrito y realiza el pago para confirmar tu asistencia.</p>
                    </div>
                    <div class="p-4 bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
                        <h4 class="font-bold text-lg mb-2">3. Historial</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Una vez pagadas, tus entradas aparecerán en el historial con sus detalles.</p>
                    </div>
                    <div class="p-4 bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
                        <h4 class="font-bold text-lg mb-2">4. Cancelación</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Si cambias de opinión, puedes solicitar una cancelación desde tu panel.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, onMounted, computed } from 'vue';
import { authStore } from "@/store/auth";
import useReservas from '@/composables/reservas';
import useCarrito from '@/composables/carrito';
import { useToast } from '@/composables/useToast';

const auth = authStore();
const toast = useToast();
const { reservas, getReservas, actualizarCantidad } = useReservas();
const { carrito, agregarAlCarrito } = useCarrito();

const displayDialog = ref(false);
const selectedEvento = ref(null);

// Muestro los detalles del evento en un dialogo
const showDetalles = (evento) => {
    selectedEvento.value = evento;
    displayDialog.value = true;
};

// Cambio la cantidad de entradas de la reserva
const cambiarCantidad = async (reserva, delta) => {
    const nuevaCantidad = reserva.cantidad + delta;
    if (nuevaCantidad < 1) return;
    
    try {
        await actualizarCantidad(reserva.id_reserva, nuevaCantidad);
        toast.info('Actualizado', 'Cantidad actualizada correctamente');
    } catch (error) {
        // Manejado por el composable
    }
};

const stats = reactive({
    reservas: 0,
    totalInvertido: 0
});

// Filtro las reservas pendientes para mostrarlas en la lista rapida
const pendientes = computed(() => {
    return reservas.value.filter(r => r.estado === 'pendiente' && r.total > 0);
});

// Añado una reserva suelta al carrito y muestro un mensaje de exito
const aniadirACarrito = (reserva) => {
    agregarAlCarrito(reserva);
    toast.success('Añadido', 'Entrada añadida a tu carrito');
};

// Compruebo si este evento en cuestion ya se encuentra metido en el carrito actual
const yaEnCarrito = (id) => {
    return carrito.value.some(item => item.id_reserva === id);
};

// Metodo corto para aplicar un formato de fecha bonito y elegante a los eventos
const formatEventDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
};

// Calculo las estadisticas basadas en los datos recibidos del servidor
const calculateStats = () => {
    stats.reservas = reservas.value.length;
    stats.totalInvertido = reservas.value
        .filter(r => ['pagado', 'confirmado'].includes(r.estado))
        .reduce((acc, curr) => acc + parseFloat(curr.total || 0), 0)
        .toFixed(2);
};

onMounted(async () => {
    await getReservas();
    calculateStats();
});
</script>

<style scoped>
:deep(.p-card-content) {
    padding: 1.5rem !important;
}
</style>
