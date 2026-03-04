import { ref } from 'vue'
import * as yup from 'yup'
import axios from 'axios'
import { useToast } from './useToast'
import { useValidation } from './useValidation'

export default function useEventos() {
  const eventos = ref([])
  const eventosList = ref([])
  const initialEvento = {
    id_evento: null,
    nombre: '',
    descripcion: '',
    latitud: null,
    longitud: null,
    precio: null,
    aforo: null,
    limite_edad: null,
    fecha_inicio: '',
    fecha_fin: '',
    id_categoria: null,
    id_organizador: null
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

  const eventoSchema = yup.object({
    nombre: yup
      .string()
      .trim()
      .required('El nombre es obligatorio')
      .min(3, 'Debe tener al menos 3 caracteres'),
    descripcion: yup
      .string()
      .trim()
      .required('La descripción es obligatoria')
      .min(5, 'Debe tener al menos 5 caracteres'),
    latitud: yup
      .number()
      .nullable()
      .typeError('La latitud debe ser un número'),
    longitud: yup
      .number()
      .nullable()
      .typeError('La longitud debe ser un número'),
    precio: yup
      .number()
      .nullable()
      .min(0, 'El precio no puede ser negativo'),
    aforo: yup
      .number()
      .nullable()
      .min(1, 'El aforo mínimo es 1'),
    limite_edad: yup
      .number()
      .nullable()
      .min(0, 'La edad límite no puede ser negativa'),
    fecha_inicio: yup
      .string()
      .required('La fecha de inicio es obligatoria'),
    fecha_fin: yup
      .string()
      .required('La fecha de fin es obligatoria'),
    id_categoria: yup
      .number()
      .nullable()
      .required('Debe seleccionar una categoría'),
    id_organizador: yup
      .number()
      .nullable()
      .required('Debe seleccionar un organizador')
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
      latitud: data.latitud ?? null,
      longitud: data.longitud ?? null,
      precio: data.precio ?? null,
      aforo: data.aforo ?? null,
      limite_edad: data.limite_edad ?? null,
      fecha_inicio: data.fecha_inicio ?? '',
      fecha_fin: data.fecha_fin ?? '',
      id_categoria: data.id_categoria ?? null,
      id_organizador: data.id_organizador ?? null
    }
    clearErrors()
  }

  const upsertEventoRecord = (eventoRecord) => {
    if (!eventoRecord?.id_evento) return
    eventos.value = [
      eventoRecord,
      ...eventos.value.filter(item => item.id_evento !== eventoRecord.id_evento)
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
    eventos.value = response.data?.data ?? response.data ?? []
    return response
  }

  const getEventosList = async () => {
    try {
      const response = await axios.get('/api/eventos-list')
      eventosList.value = response.data?.data ?? response.data ?? []
      return response
    } catch (error) {
      handleRequestError(error, {
        fallbackMessage: 'No se pudo obtener la lista de eventos',
        onGenericError: (message) => toast.error('Error', message)
      })
    }
  }

  const createEvento = async () => {
    const { isValid } = await validate(eventoSchema, evento.value)
    if (!isValid) {
      toast.error('Error de validación', 'Revisa los campos resaltados.')
      throw new Error('Validación')
    }

    try {
      const response = await withLoading(() =>
        axios.post('/api/eventos', evento.value)
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
      const response = await withLoading(() =>
        axios.put(`/api/eventos/${evento.value.id_evento}`, evento.value)
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
      eventos.value = eventos.value.filter(item => item.id_evento !== id)
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
    eventosList,
    isLoading,
    errors,
    hasError,
    getError,
    resetEvento,
    setEvento,
    upsertEventoRecord,
    getEventos,
    getEventosList,
    createEvento,
    updateEvento,
    deleteEvento
  }
}
