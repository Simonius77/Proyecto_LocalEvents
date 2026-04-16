<template>
  <div class="evento-detail-page">
    <div v-if="isLoading" class="text-center py-10">
      <p>Cargando evento...</p>
    </div>

    <div v-else-if="evento" class="space-y-6">
      <!-- Back Button -->
      <router-link to="/" class="inline-block mb-4">
        <Button icon="pi pi-arrow-left" label="Volver" text severity="secondary" />
      </router-link>

      <!-- Event Header -->
      <Card class="evento-header-card">
        <template #content>
          <div class="flex flex-col md:flex-row gap-8">
            <div class="flex-1 space-y-4">
              <h1 class="text-4xl font-bold text-surface-900 dark:text-surface-0">{{ evento.nombre }}</h1>
              <p class="text-lg text-surface-600 dark:text-surface-400">{{ evento.descripcion }}</p>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
                <div class="flex items-start gap-3">
                  <i class="pi pi-calendar text-xl text-primary-500"></i>
                  <div>
                    <p class="text-sm text-surface-600 dark:text-surface-400">Fecha de inicio</p>
                    <p class="font-medium">{{ formatDate(evento.fecha_inicio) }}</p>
                  </div>
                </div>

                <div class="flex items-start gap-3">
                  <i class="pi pi-clock text-xl text-primary-500"></i>
                  <div>
                    <p class="text-sm text-surface-600 dark:text-surface-400">Fecha de fin</p>
                    <p class="font-medium">{{ formatDate(evento.fecha_fin) }}</p>
                  </div>
                </div>

                <div class="flex items-start gap-3">
                  <i class="pi pi-dollar text-xl text-success-500"></i>
                  <div>
                    <p class="text-sm text-surface-600 dark:text-surface-400">Precio</p>
                    <p class="font-medium font-mono text-xl">{{ evento.precio > 0 ? evento.precio + '€' : '¡Gratis!' }}</p>
                  </div>
                </div>

                <div class="flex items-start gap-3">
                  <i class="pi pi-users text-xl text-info-500"></i>
                  <div>
                    <p class="text-sm text-surface-600 dark:text-surface-400">Aforo máximo</p>
                    <p class="font-medium">{{ evento.aforo ?? 'Sin límite' }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Booking Section -->
            <div class="md:w-80 bg-surface-50 dark:bg-surface-900 p-6 rounded-2xl border border-surface-200 dark:border-surface-800 space-y-6 self-start">
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-surface-600 dark:text-surface-400">¿Cuántas personas vendréis?</label>
                <InputNumber v-model="cantidad" mode="decimal" showButtons :min="1" :max="evento.aforo || 10" fluid class="w-full" />
              </div>

              <div class="space-y-3">
                <Button 
                  label="Reservar" 
                  icon="pi pi-ticket" 
                  severity="primary" 
                  size="large" 
                  fluid 
                  class="w-full font-bold shadow-lg shadow-primary-500/20"
                  :loading="isSubmitting"
                  @click="handleReservar"
                />
                
                <div class="text-center">
                   <p class="text-2xl font-black text-primary-500">{{ (evento.precio * cantidad).toFixed(2) }}€</p>
                   <p class="text-[10px] uppercase font-bold text-surface-400">Total a pagar</p>
                </div>
              </div>

              <Divider />
              
              <Button label="Compartir evento" icon="pi pi-share-alt" severity="secondary" fluid text size="small" />
            </div>
          </div>
        </template>
      </Card>

      <!-- Event Details -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Location -->
          <Card v-if="evento.latitud || evento.longitud">
            <template #title>
              <div class="flex items-center gap-2">
                <i class="pi pi-map-marker text-primary-500"></i>
                <span>Ubicación</span>
              </div>
            </template>
            <template #content>
              <div class="space-y-2">
                <p v-if="evento.latitud" class="text-sm">
                  <span class="font-medium">Latitud:</span> {{ evento.latitud.toFixed(6) }}
                </p>
                <p v-if="evento.longitud" class="text-sm">
                  <span class="font-medium">Longitud:</span> {{ evento.longitud.toFixed(6) }}
                </p>
                <a
                  :href="`https://maps.google.com/?q=${evento.latitud},${evento.longitud}`"
                  target="_blank"
                  rel="noopener"
                  class="inline-block mt-2"
                >
                  <Button label="Ver en Google Maps" icon="pi pi-external-link" text severity="info" size="small" />
                </a>
              </div>
            </template>
          </Card>

          <!-- Restricciones -->
          <Card>
            <template #title>
              <div class="flex items-center gap-2">
                <i class="pi pi-exclamation-circle text-warning-500"></i>
                <span>Información Importante</span>
              </div>
            </template>
            <template #content>
              <div class="space-y-2">
                <p v-if="evento.limite_edad" class="text-sm">
                  <span class="font-medium">Edad mínima requerida:</span> {{ evento.limite_edad }} años
                </p>
                <p v-else class="text-sm text-surface-600">
                  No hay restricción de edad
                </p>
              </div>
            </template>
          </Card>
        </div>

        <!-- Sidebar Details -->
        <div class="space-y-6">
          <!-- Dates Summary -->
          <Card>
            <template #title>
              <i class="pi pi-info-circle"></i> Resumen de Fechas
            </template>
            <template #content>
              <div class="space-y-3 text-sm">
                <div>
                  <p class="text-surface-600 dark:text-surface-400">Inicio</p>
                  <p class="font-medium">{{ formatDateTime(evento.fecha_inicio) }}</p>
                </div>
                <Divider />
                <div>
                  <p class="text-surface-600 dark:text-surface-400">Fin</p>
                  <p class="font-medium">{{ formatDateTime(evento.fecha_fin) }}</p>
                </div>
                <Divider />
                <div>
                  <p class="text-surface-600 dark:text-surface-400">Duración aproximada</p>
                  <p class="font-medium">{{ getDuration() }}</p>
                </div>
              </div>
            </template>
          </Card>
        </div>
      </div>
    </div>

    <div v-else class="text-center py-10">
      <i class="pi pi-inbox text-3xl text-surface-400 mb-4"></i>
      <p>El evento no fue encontrado</p>
      <router-link to="/" class="inline-block mt-4">
        <Button label="Volver al inicio" icon="pi pi-home" />
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import axios from 'axios'
import { useRoute, useRouter } from 'vue-router'
import { authStore } from "@/store/auth"

const route = useRoute()
const router = useRouter()
const auth = authStore()
const swal = inject('$swal')

const evento = ref(null)
const isLoading = ref(false)
const isSubmitting = ref(false)
const cantidad = ref(1)

const formatDate = (v) => {
  if (!v) return '-'
  try {
    return new Date(v).toLocaleDateString('es-ES', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  } catch (e) {
    return v
  }
}

const formatDateTime = (v) => {
  if (!v) return '-'
  try {
    return new Date(v).toLocaleString('es-ES', {
      weekday: 'short',
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  } catch (e) {
    return v
  }
}

const getDuration = () => {
  if (!evento.value?.fecha_inicio || !evento.value?.fecha_fin) return '-'
  try {
    const start = new Date(evento.value.fecha_inicio)
    const end = new Date(evento.value.fecha_fin)
    const diffMs = end - start
    if (diffMs < 0) return '-'
    const diffHours = Math.floor(diffMs / (1000 * 60 * 60))
    const diffMins = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60))

    if (diffHours > 0) {
      return `${diffHours}h ${diffMins}m`
    }
    return `${diffMins} minutos`
  } catch (e) {
    return '-'
  }
}

const getEvento = async () => {
  isLoading.value = true
  try {
    const res = await axios.get(`/api/eventos/${route.params.id}`)
    evento.value = res.data?.data ?? res.data
  } catch (err) {
    console.error('Error:', err)
    evento.value = null
  } finally {
    isLoading.value = false
  }
}

const handleReservar = async () => {
  if (!auth.authenticated) {
    router.push({ name: 'public.login' })
    return
  }

  isSubmitting.value = true
  try {
    const response = await axios.post('/api/reservas', {
      id_evento: evento.value.id_evento,
      cantidad: cantidad.value
    })

    swal({
      icon: 'success',
      title: '¡Reserva realizada!',
      text: 'Tu reserva se ha registrado correctamente. Puedes verla en el panel de control.',
      confirmButtonText: 'Ir a mis reservas',
      showCancelButton: true,
      cancelButtonText: 'Seguir mirando'
    }).then((result) => {
      if (result.isConfirmed) {
        router.push({ name: 'app.reservas' })
      }
    })
  } catch (error) {
    console.error('Error reserving:', error)
    swal({
      icon: 'error',
      title: 'Error al reservar',
      text: error.response?.data?.message || 'No se pudo completar la reserva. Inténtalo de nuevo más tarde.'
    })
  } finally {
    isSubmitting.value = false
  }
}

onMounted(getEvento)
</script>

<style scoped>
.evento-detail-page {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

.evento-header-card :deep(.p-card-content) {
  padding: 2.5rem;
}
</style>
