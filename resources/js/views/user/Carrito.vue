<template>
    <div class="p-4 sm:p-6 max-w-5xl mx-auto">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-1">Tu Carrito</h1>
                <p class="text-gray-500 dark:text-gray-400">Revisa y paga tus reservas pendientes</p>
            </div>
            <Button label="Continuar Explorando" as="router-link" to="/" outlined icon="pi pi-arrow-left" class="rounded-xl" />
        </div>

        <div v-if="carrito.length === 0" class="text-center p-16 bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm">
            <div class="w-20 h-20 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="pi pi-shopping-cart text-4xl text-blue-500"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2 text-gray-900 dark:text-white">Tu carrito está vacío</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-sm mx-auto">No tienes ninguna reserva pendiente de pago en tu carrito actualmente.</p>
            <Button label="Ir a mis reservas" as="router-link" to="/app/reservas" icon="pi pi-list" class="rounded-xl px-8" />
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 flex flex-col gap-4">
                <Card v-for="item in carrito" :key="item.id_reserva" class="overflow-hidden border border-gray-100 dark:border-gray-800 rounded-3xl shadow-sm hover:shadow-md transition-shadow">
                    <template #content>
                        <div class="flex items-center justify-between p-2">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">{{ item.evento?.nombre }}</h3>
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <span class="bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-md font-medium text-gray-700 dark:text-gray-300">
                                        {{ item.evento?.precio }}€ cada una
                                    </span>
                                    <span>x</span>
                                    <span class="font-bold text-gray-900 dark:text-white">{{ item.cantidad }} {{ item.cantidad > 1 ? 'entradas' : 'entrada' }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="text-right">
                                    <p class="text-[10px] uppercase font-bold text-gray-400">Subtotal</p>
                                    <span class="text-2xl font-black text-gray-900 dark:text-white">{{ item.total }}€</span>
                                </div>
                                <Button icon="pi pi-trash" severity="danger" text rounded aria-label="Eliminar" @click="eliminarDelCarrito(item.id_reserva)" class="hover:bg-red-50 dark:hover:bg-red-900/10" />
                            </div>
                        </div>
                    </template>
                </Card>
            </div>

            <div class="md:col-span-1">
                <Card class="border border-gray-100 dark:border-gray-800 rounded-3xl shadow-sm sticky top-6 bg-gray-50/30 dark:bg-gray-800/20">
                    <template #content>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Resumen del Pedido</h3>
                        
                         <div class="flex flex-col gap-3 mb-6">
                            <div class="flex justify-between items-center text-sm font-medium">
                                <span class="text-gray-500">Base Imponible</span>
                                <span class="text-gray-900 dark:text-white">{{ baseImponible }}€</span>
                            </div>
                            <div class="flex justify-between items-center text-sm font-medium">
                                <span class="text-gray-500">Impuestos (IVA 21%)</span>
                                <span class="text-gray-900 dark:text-white">{{ impuestos }}€</span>
                            </div>
                            <div class="flex justify-between items-center text-sm font-medium">
                                <span class="text-gray-500">Descuentos</span>
                                <span class="text-green-500">-0,00€</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center border-t border-gray-100 dark:border-gray-800 pt-5 mb-8">
                            <span class="text-lg font-bold text-gray-900 dark:text-white">Total Final</span>
                            <span class="text-3xl font-black text-blue-600 dark:text-blue-400">{{ totalCarrito }}€</span>
                        </div>

                        <Button 
                            label="Pagar Ahora" 
                            icon="pi pi-credit-card" 
                            severity="success" 
                            class="w-full rounded-xl py-4 font-bold text-lg shadow-lg shadow-green-500/20 transition-all hover:scale-[1.02]"
                            :loading="isProcesando"
                            @click="procesarPago"
                        />
                    </template>
                </Card>
            </div>
        </div>
    </div>
</template>

<script setup>
// Controlo la logica principal de pagos del carrito
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import useCarrito from '@/composables/carrito';
import { useToast } from '@/composables/useToast';

const router = useRouter();
const toast = useToast();

// Obtengo los metodos y valores del carrito que acabo de importar
const { 
    carrito, 
    totalCarrito, 
    baseImponible, 
    impuestos, 
    eliminarDelCarrito, 
    limpiarCarrito 
} = useCarrito();

// Variable para controlar si el pago esta en proceso y bloquear el boton
const isProcesando = ref(false);

const procesarPago = async () => {
    isProcesando.value = true;
    try {
        // Ejecuto el pago reserva por reserva iterando todo el carrito
        for (const item of carrito.value) {
            await axios.post(`/api/reservas/${item.id_reserva}/pagar`);
        }
        
        // Si no hay fallos aviso del exito, limpio el carrito y envio al usuario al historico
        toast.success('Compra completada', 'Tus entradas han sido emitidas');
        limpiarCarrito();
        router.push('/app/historico');
        
    } catch (error) {
        // En caso de que algo salga mal comunico el error para evitar disgustos
        toast.error('Error', 'Hubo un error procesando el pago de tu carrito');
        console.error(error);
    } finally {
        isProcesando.value = false;
    }
};
</script>

<style scoped>
:deep(.p-card-content) {
    padding: 1.5rem !important;
}
</style>
