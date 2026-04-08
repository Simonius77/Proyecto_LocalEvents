import { ref } from 'vue'
import * as yup from 'yup'
import axios from 'axios'
import { useToast } from './useToast'
import { useValidation } from './useValidation'

/**
 * Este composable se encarga de toda la logica de los eventos en el frontend.
 * Maneja el estado, las validaciones y las llamadas a la API.
 */
export default function useEventos() {
    const eventos = ref([])
    const initialEvento = { 
        id_evento: null, 
        nombre: '', 
        descripcion: '', 
        localizacion: '', 
        latitud: '', 
        longitud: '', 
        fecha_inicio: '', 
        fecha_fin: '', 
        aforo: '', 
        precio: '', 
        imagen: null, 
        id_categoria: '' 
    }
    const evento = ref({ ...initialEvento })
    const isLoading = ref(false)
    const toast = useToast()

    const {
        errors,
        validate,
        handleRequestError,
        clearErrors,
        hasError,
        getError
    } = useValidation()

    // Reglas para validar que los datos del evento sean correctos antes de enviarlos
    const eventoSchema = yup.object({
        nombre: yup
            .string()
            .trim()
            .required('El nombre es obligatorio')
            .min(3, 'Debe tener al menos 3 caracteres'),
        descripcion: yup.string().required('Descripción requerida'),
        localizacion: yup.string().required('Localización requerida'),
        fecha_inicio: yup.string().required('Fecha de inicio requerida'),
        fecha_fin: yup.string().required('Fecha de fin requerida'),
        id_categoria: yup.number().nullable()
    })

    /**
     * Funcion para ejecutar acciones mientras mostramos que estamos cargando
     */
    const withLoading = async (fn) => {
        if (isLoading.value) throw new Error('Operación en curso')
        isLoading.value = true
        try {
            return await fn()
        } finally {
            isLoading.value = false
        }
    }

    /**
     * Limpio los datos del evento actual para dejarlo como nuevo
     */
    const resetEvento = () => {
        evento.value = { ...initialEvento }
        clearErrors()
    }

    /**
     * Relleno el evento con datos que ya tenemos (por ejemplo al editar)
     */
    const setEvento = (data = {}) => {
        // Formateo las fechas para que el input de tipo datetime-local las entienda bien
        const formatDate = (dateStr) => {
            if (typeof dateStr === 'string' && dateStr.length >= 16) {
                return dateStr.substring(0, 16).replace(' ', 'T')
            }
            return ''
        }

        evento.value = {
            id_evento: data.id_evento ?? null,
            nombre: data.nombre ?? '',
            descripcion: data.descripcion ?? '',
            localizacion: data.localizacion ?? '',
            latitud: data.latitud ?? null,
            longitud: data.longitud ?? null,
            fecha_inicio: formatDate(data.fecha_inicio),
            fecha_fin: formatDate(data.fecha_fin),
            aforo: data.aforo ?? '',
            precio: data.precio ?? '',
            id_categoria: data.id_categoria ?? '',
            imagen: null
        }
        clearErrors()
    }

    /**
     * Añado o actualizo un evento en la lista que tenemos en memoria
     */
    const upsertEventoRecord = (record) => {
        if (!record?.id_evento) return
        eventos.value = [
            record,
            ...eventos.value.filter(item => item.id_evento !== record.id_evento)
        ]
    }

    /**
     * Pido al servidor la lista de eventos con los filtros que queramos
     */
    const getEventos = async (params = {}) => {
        const defaultParams = {
            page: 1,
            search_id: '',
            search_title: '',
            search_global: '',
            order_column: 'created_at',
            order_direction: 'desc'
        }

        const query = new URLSearchParams({ ...defaultParams, ...params }).toString()
        const response = await axios.get(`/api/eventos?${query}`)
        // Me aseguro de guardar solo los datos de la lista
        eventos.value = response.data?.data ?? []
        return response
    }

    /**
     * Transformo los datos en un formato que permita mandar fotos al servidor
     */
    const serializeFormData = (data) => {
        const form = new FormData()
        Object.entries(data).forEach(([key, value]) => {
            if (value === undefined) return
            const valueToSend = value === null ? '' : value
            
            if (Array.isArray(valueToSend)) {
                valueToSend.forEach(item => form.append(`${key}[]`, item))
            } else {
                form.append(key, valueToSend)
            }
        })
        return form
    }

    /**
     * Mando los datos al servidor para crear un evento nuevo
     */
    const createEvento = async () => {
        const { isValid } = await validate(eventoSchema, evento.value)
        if (!isValid) {
            toast.error('Error de validación', 'Revisa los campos resaltados.')
            throw new Error('Validación')
        }

        try {
            const response = await withLoading(() =>
                axios.post('/api/eventos', serializeFormData(evento.value), {
                    headers: { "content-type": "multipart/form-data" }
                })
            )
            const data = response.data?.data ?? response.data
            toast.crud.created('Evento')
            return data
        } catch (error) {
            handleRequestError(error, {
                fallbackMessage: 'No se pudo crear el evento',
                onValidationError: () =>
                    toast.error('Error de validación', 'Revisa los campos resaltados.'),
                onGenericError: (message) => toast.error('Error', message)
            })
        }
    }

    /**
     * Mando los datos para cambiar las cosas de un evento que ya existe
     */
    const updateEvento = async () => {
        const { isValid } = await validate(eventoSchema, evento.value)
        if (!isValid) {
            toast.error('Error de validación', 'Revisa los campos resaltados.')
            throw new Error('Validación')
        }

        try {
            const formData = serializeFormData(evento.value)
            formData.append('_method', 'PUT') // Uso PUT para que Laravel sepa que es una actualizacion
            const response = await withLoading(() =>
                axios.post(`/api/eventos/${evento.value.id_evento}`, formData, {
                    headers: { "content-type": "multipart/form-data" }
                })
            )
            const data = response.data?.data ?? response.data
            toast.crud.updated('Evento')
            return data
        } catch (error) {
            handleRequestError(error, {
                fallbackMessage: 'No se pudo actualizar el evento',
                onValidationError: () =>
                    toast.error('Error de validación', 'Revisa los campos resaltados.'),
                onGenericError: (message) => toast.error('Error', message)
            })
        }
    }

    /**
     * Le digo al servidor que borre un evento por su ID
     */
    const deleteEvento = async (id) => {
        try {
            const response = await withLoading(() => axios.delete(`/api/eventos/${id}`))
            // Lo quito de la lista local para que desaparezca de la pantalla al instante
            eventos.value = eventos.value.filter(item => item.id !== id && item.id_evento !== id)
            toast.crud.deleted('Evento')
            return response
        } catch (error) {
            handleRequestError(error, {
                fallbackMessage: 'No se pudo eliminar el evento',
                onGenericError: (message) => toast.error('Error', message)
            })
        }
    }

    return {
        eventos,
        evento,
        isLoading,
        errors,
        hasError,
        getError,
        resetEvento,
        setEvento,
        upsertEventoRecord,
        getEventos,
        createEvento,
        updateEvento,
        deleteEvento
    }
}
