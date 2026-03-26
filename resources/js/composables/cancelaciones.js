// Este codigo se encarga de manejar las peticiones de cancelaciones
// entre los paneles de control de organizador y administrador con el servidor web
import { ref } from 'vue';
import axios from 'axios';
import { useToast } from './useToast.js';

export default function useCancelaciones() {
    const cancelaciones = ref([]);
    const isLoading = ref(false);
    const toast = useToast();

    // Pide al servidor la lista completa de reservas que los clientes han pedido anular
    const getCancelacionesPendientes = async () => {
        isLoading.value = true;
        try {
            const response = await axios.get('/api/reservas-cancelaciones');
            cancelaciones.value = response.data.data;
        } catch (error) {
            console.error(error);
            toast.error('Error', 'Hubo un error al cargar las solicitudes de cancelacion');
        } finally {
            isLoading.value = false;
        }
    };

    // Ordena al servidor que apruebe la cancelacion y cambie el estado de la reserva
    const confirmarCancelacion = async (id) => {
        try {
            await axios.post(`/api/reservas/${id}/confirmar-cancelacion`);
            toast.success('Completado', 'La entrada ha sido cancelada sin problemas');
            // Recargo la lista completa para que deje de aparecer enseguida
            await getCancelacionesPendientes();
        } catch (error) {
            console.error(error);
            toast.error('Fallo', 'Parece que no se pudo finalizar la cancelacion solicitada');
        }
    };

    // Pongo aqui a disposicion del resto de mi programa las variables y las funciones correspondientes
    return {
        cancelaciones,
        isLoading,
        getCancelacionesPendientes,
        confirmarCancelacion
    };
}
