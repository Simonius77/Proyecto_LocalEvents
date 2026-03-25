<template>
    <div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
        <h1 class="text-4xl font-bold text-surface-900 dark:text-surface-0 mb-4">Bienvenido a LocalEvents</h1>
        <p class="text-xl text-surface-600 dark:text-surface-400 mb-8">Tu aplicación para reservar los mejores eventos locales</p>
        
        <div class="flex gap-4 mb-12" v-if="!authStore().authenticated">
            <template v-if="!authStore().user?.name">
                <Button label="Iniciar Sesión" as="router-link" to="/login" size="large" />
                <Button label="Registrarse" as="router-link" :to="{ name: 'public.register' }" severity="secondary" size="large" />
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
                            <Button label="Ver Detalles" size="small" outlined @click="showDetalles(evento)" />
                            <Button 
                                :label="auth.authenticated ? 'Reservar' : 'Inicia sesión para reservar'" 
                                :icon="auth.authenticated ? 'pi pi-calendar-plus' : 'pi pi-lock'"
                                size="small" 
                                :severity="auth.authenticated ? 'primary' : 'secondary'"
                                @click="handleReserva(evento)" 
                            />
                        </div>
                    </template>
                </Card>
            </div>
        </div>

        <Dialog v-model:visible="displayDialog" modal :header="selectedEvento?.nombre" :style="{ width: '50rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" :dismissableMask="true">
            <template v-if="selectedEvento">
                <img v-if="selectedEvento.imagen" :src="selectedEvento.imagen" :alt="selectedEvento.nombre" class="w-full h-auto max-h-96 object-cover mb-4 rounded-md" />
                <div class="flex items-center text-sm gap-2 mb-4 text-surface-600 dark:text-surface-400">
                    <i class="pi pi-clock"></i>
                    <span>{{ new Date(selectedEvento.fecha_inicio).toLocaleDateString() }}</span>
                    <span v-if="selectedEvento.aforo" class="ml-2 bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 text-xs px-2 py-1 rounded-full">
                        Aforo: {{ selectedEvento.aforo }}
                    </span>
                </div>
                <div class="text-surface-700 dark:text-surface-300 leading-relaxed whitespace-pre-line mb-4">
                    {{ selectedEvento.descripcion }}
                </div>
                <div class="mt-6 flex justify-end font-bold text-xl text-primary">
                    {{ selectedEvento.precio > 0 ? selectedEvento.precio + '€' : 'Gratis' }}
                </div>
            </template>
        </Dialog>
    </div>
</template>

<script setup>
// Importo lo que necesito para que funcione la pagina
import { onMounted, ref } from 'vue';
import { authStore } from "@/store/auth";
import { useRouter } from 'vue-router';
import axios from 'axios';
import useReservas from '@/composables/reservas';

const router = useRouter();
const auth = authStore();
const { crearReserva } = useReservas();

const eventos = ref([]);
const loading = ref(true);

const displayDialog = ref(false);
const selectedEvento = ref(null);

// Guardo el evento elegido y abro la ventana de detalles
const showDetalles = (evento) => {
    selectedEvento.value = evento;
    displayDialog.value = true;
};

// Manejo lo que pasa cuando alguien pulsa el boton de reservar
const handleReserva = async (evento) => {
    if (!auth.authenticated) {
        // Si no ha entrado, lo mando al login
        router.push('/login');
        return;
    }
    // Si esta dentro, mando la peticion de reserva de una unidad
    await crearReserva(evento.id_evento, 1);
};

// Pido la lista de eventos al servidor
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

// Nada mas empezar, yo busco los eventos para enseñarlos
onMounted(() => {
    fetchEventos();
});
</script>
