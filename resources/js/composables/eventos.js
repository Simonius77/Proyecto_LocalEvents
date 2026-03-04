import { ref } from 'vue'
import * as yup from 'yup'
import axios from 'axios'
import { useToast } from './useToast'
import { useValidation } from './useValidation'

export default function useEventos() {
    const eventos = ref([])
    const initialEvento = { id_evento: null, nombre: '', descripcion: '', fecha_inicio: '', fecha_fin: '', aforo: '', precio: '', imagen: null, id_categoria: null }
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

    const eventoSchema = yup.object({
        nombre: yup
            .string()
            .trim()
            .required('El nombre es obligatorio')
            .min(3, 'Debe tener al menos 3 caracteres'),
        descripcion: yup.string().required('Descripción requerida'),
        fecha_inicio: yup.string().required('Fecha de inicio requerida'),
        fecha_fin: yup.string().required('Fecha de fin requerida'),
        id_categoria: yup.number().nullable()
    })

    const withLoading = async (fn) => {
        if (isLoading.value) throw new Error('Operación en curso')
        isLoading.value = true
        try {
            return await fn()
        } finally {
            isLoading.value = false
        }
    }

    const resetEvento = () => {
        evento.value = { ...initialEvento }
        clearErrors()
    }

    const setEvento = (data = {}) => {
        evento.value = {
            id_evento: data.id_evento ?? null,
            nombre: data.nombre ?? '',
            descripcion: data.descripcion ?? '',
            fecha_inicio: data.fecha_inicio ? data.fecha_inicio.substring(0, 16) : '',
            fecha_fin: data.fecha_fin ? data.fecha_fin.substring(0, 16) : '',
            aforo: data.aforo ?? '',
            precio: data.precio ?? '',
            id_categoria: data.id_categoria ?? null,
            imagen: null
        }
        clearErrors()
    }

    const upsertEventoRecord = (record) => {
        if (!record?.id) return
        eventos.value = [
            record,
            ...eventos.value.filter(item => item.id !== record.id)
        ]
    }

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
        eventos.value = response.data?.data ?? response.data.data ?? []
        return response
    }

    const serializeFormData = (data) => {
        const form = new FormData()
        Object.entries(data).forEach(([key, value]) => {
            if (value === undefined || value === null) return
            if (Array.isArray(value)) {
                value.forEach(item => form.append(`${key}[]`, item))
            } else {
                form.append(key, value)
            }
        })
        return form
    }

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

    const updateEvento = async () => {
        const { isValid } = await validate(eventoSchema, evento.value)
        if (!isValid) {
            toast.error('Error de validación', 'Revisa los campos resaltados.')
            throw new Error('Validación')
        }

        try {
            const formData = serializeFormData(evento.value)
            formData.append('_method', 'PUT') // For laravel put patch
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

    const deleteEvento = async (id) => {
        try {
            const response = await withLoading(() => axios.delete(`/api/eventos/${id}`))
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
