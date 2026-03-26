// Traigo las herramientas para manejar el carrito de forma reactiva y con el localstorage
import { ref, watch, computed } from 'vue';

// Busco el carrito guardado en el navegador o bien creo un array vacio
const carrito = ref(JSON.parse(localStorage.getItem('mi_carrito_eventos')) || []);

// Guardo el carrito en el navegador cada vez que se produce algun cambio dentro
watch(carrito, (nuevoValor) => {
    localStorage.setItem('mi_carrito_eventos', JSON.stringify(nuevoValor));
}, { deep: true });

export default function useCarrito() {
    
    // Meto la reserva en el carrito despues de comprobar que no este ya metida
    const agregarAlCarrito = (reserva) => {
        const existe = carrito.value.find(item => item.id_reserva === reserva.id_reserva);
        if (!existe) {
            carrito.value.push(reserva);
        }
    };

    // Busco el id de la reserva y la quito completamente del carrito
    const eliminarDelCarrito = (id_reserva) => {
        carrito.value = carrito.value.filter(item => item.id_reserva !== id_reserva);
    };

    // Vacio el carrito por completo eliminando todos sus elementos
    const limpiarCarrito = () => {
        carrito.value = [];
    };

    // Cuento todo el dinero que vale el carrito en total acumulando los precios
    const totalCarrito = computed(() => {
        return carrito.value.reduce((total, item) => total + Number(item.total || 0), 0);
    });

    return {
        carrito,
        agregarAlCarrito,
        eliminarDelCarrito,
        limpiarCarrito,
        totalCarrito
    };
}
