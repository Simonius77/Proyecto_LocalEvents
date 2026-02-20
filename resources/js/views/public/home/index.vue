<template>
    <div class="space-y-8">
        <div class="flex flex-col items-center justify-center min-h-[40vh] text-center">
            <h1 class="text-4xl font-bold text-surface-900 dark:text-surface-0 mb-4">Bienvenido a LocalEvents</h1>
            <p class="text-xl text-surface-600 dark:text-surface-400 mb-8">Tu aplicación para reservar los mejores eventos locales</p>

            <div class="flex gap-4">
                <template v-if="!authStore().user?.name">
                    <Button label="Iniciar Sesión" as="router-link" to="/login" size="large" />
                    <Button label="Registrarse" as="router-link" to="/register" severity="secondary" size="large" />
                </template>
                <template v-else>
                    <Button label="Ir al Dashboard" as="router-link" to="/app" size="large" />
                </template>
            </div>
        </div>

        <section>
            <h2 class="text-2xl font-semibold mb-4">Próximos eventos</h2>

            <div v-if="isLoading" class="text-center py-8">Cargando eventos...</div>

            <div v-else>
                <div v-if="events.length === 0" class="text-center text-surface-600">No hay eventos disponibles.</div>

                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="e in events" :key="e.id_evento" class="bg-white dark:bg-surface-900 border rounded-lg p-4 shadow-sm">
                        <h3 class="text-lg font-bold mb-1 text-surface-900 dark:text-surface-0">{{ e.nombre }}</h3>
                        <p class="text-sm text-surface-600 dark:text-surface-400 mb-2 truncate">{{ e.descripcion }}</p>
                        <p class="text-sm text-surface-600 mb-1">Fecha: <span class="font-medium">{{ formatDate(e.fecha_inicio) }}</span></p>
                        <p class="text-sm text-surface-600 mb-2">Precio: <span class="font-medium">{{ e.precio ?? 'Gratis' }}</span></p>
                        <div class="flex items-center justify-between mt-4">
                            <Button as="router-link" :to="`/evento/${e.id_evento}`" label="Ver" size="small" />
                            <span class="text-xs text-surface-500">Aforo: {{ e.aforo ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { authStore } from "@/store/auth"

const events = ref([])
const isLoading = ref(false)

const getEvents = async () => {
    isLoading.value = true
    try {
        const res = await axios.get('/api/eventos')
        events.value = res.data?.data ?? res.data ?? []
    } catch (err) {
        events.value = []
    } finally {
        isLoading.value = false
    }
}

const formatDate = (v) => {
    if (!v) return '-'
    try {
        return new Date(v).toLocaleString()
    } catch (e) {
        return v
    }
}

onMounted(getEvents)
</script>
