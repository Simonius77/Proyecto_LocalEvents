import { ref } from 'vue'
import axios from 'axios'
import { useToast } from './useToast'

// Yo guardo las funciones para que el frontend pueda manejar las reservas
export default function useReservas() {
    const reservas = ref([])
    const isLoading = ref(false)
    const toast = useToast()

    // Yo pido al servidor la lista de reservas que existen
    const getReservas = async () => {
        isLoading.value = true
        try {
            const response = await axios.get('/api/reservas')
            reservas.value = response.data.data
        } catch (error) {
            console.error(error)
            toast.error('Error', 'No se pudieron cargar las reservas')
        } finally {
            isLoading.value = false
        }
    }

    // Yo mando la peticion para crear una reserva nueva de un evento
    const crearReserva = async (id_evento, cantidad = 1) => {
        try {
            const response = await axios.post('/api/reservas', { id_evento, cantidad })
            toast.success('Exito', 'Reserva realizada correctamente')
            return response.data
        } catch (error) {
            toast.error('Error', error.response?.data?.message || 'No se pudo realizar la reserva')
            throw error
        }
    }

    // Yo le digo al servidor que el usuario quiere pagar su reserva
    const pagarReserva = async (id) => {
        try {
            await axios.post(`/api/reservas/${id}/pagar`)
            toast.success('Pago realizado', 'Tu reserva ha sido pagada con exito')
            await getReservas()
        } catch (error) {
            toast.error('Error', 'No se pudo procesar el pago')
        }
    }

    // Yo aviso que el usuario ha pedido cancelar el evento
    const solicitarCancelacion = async (id) => {
        try {
            await axios.post(`/api/reservas/${id}/solicitar-cancelacion`)
            toast.info('Solicitud enviada', 'La cancelacion ha sido solicitada correctamente')
            await getReservas()
        } catch (error) {
            toast.error('Error', 'No se pudo enviar la solicitud')
        }
    }

    // Yo pido que se borre definitivamente el registro de la reserva
    const eliminarReserva = async (id) => {
        try {
            await axios.delete(`/api/reservas/${id}`)
            toast.success('Eliminada', 'La reserva ha sido eliminada')
            await getReservas()
        } catch (error) {
            toast.error('Error', 'No se pudo eliminar la reserva')
        }
    }

    return {
        reservas,
        isLoading,
        getReservas,
        crearReserva,
        pagarReserva,
        solicitarCancelacion,
        eliminarReserva
    }
}
