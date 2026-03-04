<template>
    <div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
        <h1 class="text-4xl font-bold text-surface-900 dark:text-surface-0 mb-4">Bienvenido a LocalEvents</h1>
        <p class="text-xl text-surface-600 dark:text-surface-400 mb-8">Tu aplicación para reservar los mejores eventos locales</p>
        
        <div class="flex gap-4 mb-12" v-if="!authStore().authenticated">
            <template v-if="!authStore().user?.name">
                <Button label="Iniciar Sesión" as="router-link" to="/login" size="large" />
                <Button label="Registrarse" as="router-link" to="/register" severity="secondary" size="large" />
            </template>
        </div>
        <div class="flex gap-4 mb-12" v-else>
            <Button label="Ir al Dashboard" as="router-link" to="/admin" size="large" />
        </div>

        <div class="w-full max-w-7xl px-4 mt-8">
            <h2 class="text-3xl font-bold text-surface-900 dark:text-surface-0 mb-8 text-left">Próximos Eventos</h2>
            
            <div v-if="loading" class="flex justify-center my-12">
                <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
            </div>
            
            <div v-else-if="eventos.length === 0" class="text-center text-surface-500 my-12">
                No hay eventos disponibles en este momento.
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-left">
                <Card v-for="evento in eventos" :key="evento.id" class="flex flex-col h-full overflow-hidden hover:shadow-lg transition-transform hover:-translate-y-1">
                    <template #header>
                        <img 
                            v-if="evento.imagen" 
                            :src="evento.imagen" 
                            :alt="evento.nombre"
                            class="w-full h-48 object-cover"
                        />
                        <div v-else class="w-full h-48 bg-surface-200 dark:bg-surface-800 flex items-center justify-center">
                            <i class="pi pi-calendar text-4xl text-surface-400"></i>
                        </div>
                    </template>
                    <template #title>
                        <div class="text-xl font-bold truncate" :title="evento.nombre">{{ evento.nombre }}</div>
                    </template>
                    <template #subtitle>
                        <div class="flex items-center text-sm gap-2 mt-1">
                            <i class="pi pi-clock"></i>
                            <span>{{ new Date(evento.fecha_inicio).toLocaleDateString() }}</span>
                            <span v-if="evento.aforo" class="ml-2 bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 text-xs px-2 py-1 rounded-full">
                                Aforo: {{ evento.aforo }}
                            </span>
                        </div>
                    </template>
                    <template #content>
                        <p class="text-surface-600 dark:text-surface-400 line-clamp-3 mt-2">
                            {{ evento.descripcion }}
                        </p>
                    </template>
                    <template #footer>
                        <div class="mt-auto pt-4 flex justify-between items-center w-full">
                            <span class="font-bold text-lg text-primary">{{ evento.precio > 0 ? evento.precio + '€' : 'Gratis' }}</span>
                            <!-- En el futuro se creara la vista de detalle del evento publico -->
                            <!-- <Button label="Ver Detalles" size="small" as="router-link" :to="'/eventos/' + evento.id" /> -->
                            <Button label="Ver Detalles" size="small" outlined />
                        </div>
                    </template>
                </Card>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { authStore } from "@/store/auth";
import axios from 'axios';

const auth = authStore();
const eventos = ref([]);
const loading = ref(true);

const fetchEventos = async () => {
    try {
        const response = await axios.get('/api/eventos-list');
        eventos.value = response.data.data || [];
    } catch (error) {
        console.error("Error al cargar los eventos:", error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchEventos();
});
</script>
