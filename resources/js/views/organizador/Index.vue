<template>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Bienvenida -->
            <Card class="col-span-full shadow-sm border-none bg-gradient-to-r from-blue-600 to-indigo-700 text-white overflow-hidden relative">
                <template #content>
                    <div class="flex items-center gap-6 p-4 relative z-10">
                        <Avatar :image="auth.user.avatar" :label="auth.user.nombre ? auth.user.nombre[0] : 'U'" size="xlarge" shape="circle" class="border-2 border-white/30 bg-white/10" />
                        <div>
                            <h1 class="text-3xl font-bold mb-1">¡Hola, {{ auth.user.nombre }}!</h1>
                            <p class="text-blue-100 opacity-90">Bienvenido a tu Panel de Organizador. Aquí podrás gestionar tus eventos y reservas.</p>
                        </div>
                    </div>
                </template>
            </Card>

            <!-- Estadísticas rápidas -->
            <Card class="shadow-sm border border-gray-100 dark:border-gray-800 hover:shadow-md transition-shadow">
                <template #title>
                    <div class="flex items-center gap-3 text-lg font-semibold text-gray-700 dark:text-gray-200">
                        <i class="pi pi-calendar text-blue-500 bg-blue-50 dark:bg-blue-900/20 p-2 rounded-lg text-sm"></i>
                        Mis Eventos
                    </div>
                </template>
                <template #content>
                    <div class="text-4xl font-bold text-gray-800 dark:text-gray-100 mb-2">0</div>
                    <p class="text-sm text-gray-500">Eventos publicados este mes</p>
                </template>
            </Card>

            <Card class="shadow-sm border border-gray-100 dark:border-gray-800 hover:shadow-md transition-shadow">
                <template #title>
                    <div class="flex items-center gap-3 text-lg font-semibold text-gray-700 dark:text-gray-200">
                        <i class="pi pi-users text-green-500 bg-green-50 dark:bg-green-900/20 p-2 rounded-lg text-sm"></i>
                        Asistentes
                    </div>
                </template>
                <template #content>
                    <div class="text-4xl font-bold text-gray-800 dark:text-gray-100 mb-2">0</div>
                    <p class="text-sm text-gray-500">Personas inscritas en total</p>
                </template>
            </Card>

            <Card class="shadow-sm border border-gray-100 dark:border-gray-800 hover:shadow-md transition-shadow">
                <template #title>
                    <div class="flex items-center gap-3 text-lg font-semibold text-gray-700 dark:text-gray-200">
                        <i class="pi pi-chart-line text-purple-500 bg-purple-50 dark:bg-purple-900/20 p-2 rounded-lg text-sm"></i>
                        Ingresos
                    </div>
                </template>
                <template #content>
                    <div class="text-4xl font-bold text-gray-800 dark:text-gray-100 mb-2">0 €</div>
                    <p class="text-sm text-gray-500">Generados por tus eventos</p>
                </template>
            </Card>

            <!-- Marcador especial rojo/naranja de Cancelaciones -->
            <Card v-if="cancelaciones.length > 0" class="col-span-full md:col-span-1 shadow-sm border-2 border-orange-200 dark:border-orange-800 bg-orange-50 dark:bg-orange-900/20 hover:shadow-md transition-shadow">
                <template #title>
                    <div class="flex items-center gap-3 text-lg font-semibold text-orange-700 dark:text-orange-400">
                        <i class="pi pi-bell text-orange-500 bg-orange-100 dark:bg-orange-900/40 p-2 rounded-lg text-sm animate-pulse"></i>
                        Cancelaciones
                    </div>
                </template>
                <template #content>
                    <div class="text-4xl font-bold text-orange-800 dark:text-orange-300 mb-2">{{ cancelaciones.length }}</div>
                    <p class="text-sm text-orange-600 dark:text-orange-500">Reservas por aprobar cancelacion</p>
                </template>
            </Card>
        </div>

        <!-- Acciones rápidas -->
        <div class="mt-8">
            <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-100">Panel de Gestion de Eventos</h2>
            <div class="flex flex-wrap gap-4 mb-6">
                <Button label="Crear Nuevo Evento" icon="pi pi-plus" class="p-button-lg rounded-xl shadow-sm hover:shadow-md transition-all px-6" @click="openCreateDialog" />
                <Button label="Refrescar Lista" icon="pi pi-refresh" severity="secondary" outlined class="p-button-lg rounded-xl px-6" @click="fetchMyEvents" :loading="isLoading" />
            </div>

            <!-- Tabla de Mis Eventos -->
            <Card class="shadow-sm border border-gray-100 dark:border-gray-800">
                <template #content>
                    <DataTable :value="eventos" :loading="isLoading" stripedRows responsiveLayout="stack" breakpoint="960px" class="p-datatable-sm">
                        <template #empty>No tienes eventos creados todavía.</template>
                        <Column field="nombre" header="Evento">
                            <template #body="slotProps">
                                <div class="flex items-center gap-3">
                                    <img v-if="slotProps.data.imagen" :src="slotProps.data.imagen" class="w-12 h-12 rounded-lg object-cover shadow-sm" />
                                    <span class="font-semibold">{{ slotProps.data.nombre }}</span>
                                </div>
                            </template>
                        </Column>
                        <Column field="fecha_inicio" header="Fecha">
                            <template #body="slotProps">
                                {{ formatDate(slotProps.data.fecha_inicio) }}
                            </template>
                        </Column>
                        <Column field="aforo" header="Aforo/Precio">
                            <template #body="slotProps">
                                <Tag :value="slotProps.data.aforo + ' plazas'" severity="info" />
                                <span class="ml-2 font-bold">{{ slotProps.data.precio > 0 ? slotProps.data.precio + '€' : 'Gratis' }}</span>
                            </template>
                        </Column>
                        <Column header="Acciones">
                            <template #body="slotProps">
                                <div class="flex gap-2">
                                    <Button icon="pi pi-pencil" severity="info" text rounded @click="openEditDialog(slotProps.data)" />
                                    <Button icon="pi pi-trash" severity="danger" text rounded @click="confirmDeleteEvento(slotProps.data)" />
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </template>
            </Card>
        </div>

        <!-- Tabla visual de cancelaciones pendientes -->
        <div v-if="cancelaciones.length > 0" class="mt-8">
            <h2 class="text-xl font-bold mb-4 text-orange-800 dark:text-orange-400 flex items-center gap-2">
                <i class="pi pi-exclamation-circle"></i> Solicitudes de Cancelacion de Clientes
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <Card v-for="canc in cancelaciones" :key="canc.id_reserva" class="border border-orange-200 shadow-sm dark:bg-gray-800">
                    <template #content>
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <p class="font-bold text-gray-800 dark:text-white">{{ canc.evento?.nombre }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Usuario: {{ canc.usuario?.name || canc.usuario?.nombre || 'Usuario' }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Entradas: {{ canc.cantidad }} (Aforo se reajustara)</p>
                            </div>
                            <Button label="Aprobar" icon="pi pi-check" severity="danger" @click="aprobarCancelacion(canc.id_reserva)" />
                        </div>
                    </template>
                </Card>
            </div>
        </div>

        <!-- Dialogo CRUD de Eventos -->
        <Dialog v-if="eventoDialog.open" v-model:visible="eventoDialog.open" modal :header="eventoDialog.type === 'create' ? 'Crear Nuevo Evento' : 'Editar Evento'" :style="{ width: '90vw', maxWidth: '600px' }" class="p-fluid">
            <div class="grid grid-cols-1 gap-4 mt-2">
                <div>
                    <label class="font-bold block mb-1">Nombre</label>
                    <InputText v-model="evento.nombre" :class="{ 'p-invalid': hasError('nombre') }" />
                    <small v-if="hasError('nombre')" class="p-error">{{ getError('nombre') }}</small>
                </div>
                <div>
                    <label class="font-bold block mb-1">Descripcion</label>
                    <Textarea v-model="evento.descripcion" rows="3" :class="{ 'p-invalid': hasError('descripcion') }" />
                    <small v-if="hasError('descripcion')" class="p-error">{{ getError('descripcion') }}</small>
                </div>
                <div>
                    <label class="font-bold block mb-1">Localizacion o Enlace Google Maps</label>
                    <div class="p-inputgroup flex-1">
                        <span class="p-inputgroup-addon">
                            <i class="pi pi-map-marker"></i>
                        </span>
                        <InputText v-model="evento.localizacion" placeholder="Pega direccion o enlace de Maps..." @blur="geocodeAddress" class="w-full" :class="{ 'p-invalid': hasError('localizacion') }" />
                    </div>
                    <small class="text-blue-500 block mt-1" v-if="evento.latitud">📍 Coordenadas detectadas: {{ evento.latitud }}, {{ evento.longitud }}</small>
                    <small class="p-error" v-if="hasError('localizacion')">{{ getError('localizacion') }}</small>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="font-bold block mb-1">Latitud</label>
                        <InputNumber v-model="evento.latitud" :minFractionDigits="2" :maxFractionDigits="10" placeholder="Ej: 39.46" class="w-full" />
                    </div>
                    <div>
                        <label class="font-bold block mb-1">Longitud</label>
                        <InputNumber v-model="evento.longitud" :minFractionDigits="2" :maxFractionDigits="10" placeholder="Ej: -0.37" class="w-full" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="font-bold block mb-1">Inicio</label>
                        <InputText type="datetime-local" v-model="evento.fecha_inicio" :class="{ 'p-invalid': hasError('fecha_inicio') }" class="w-full" />
                    </div>
                    <div>
                        <label class="font-bold block mb-1">Aforo</label>
                        <InputNumber v-model="evento.aforo" :useGrouping="false" class="w-full" placeholder="Capacidad max" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="font-bold block mb-1">Fin</label>
                        <InputText type="datetime-local" v-model="evento.fecha_fin" :class="{ 'p-invalid': hasError('fecha_fin') }" class="w-full" />
                    </div>
                    <div>
                        <label class="font-bold block mb-1">Precio (€)</label>
                        <InputNumber v-model="evento.precio" mode="currency" currency="EUR" locale="es-ES" class="w-full" placeholder="0 para gratis" />
                    </div>
                </div>
                <div>
                    <label class="font-bold block mb-1">Categoria</label>
                    <Select v-model="evento.id_categoria" :options="categoryList" optionLabel="nombre" optionValue="id_categoria" placeholder="Selecciona una categoria" class="w-full" />
                </div>
                <div>
                    <label class="font-bold block mb-1">Imagen</label>
                    <input type="file" @change="onFileChange" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                </div>
            </div>
            <template #footer>
                <Button label="Cancelar" icon="pi pi-times" @click="eventoDialog.open = false" severity="secondary" text />
                <Button :label="eventoDialog.type === 'create' ? 'Crear' : 'Guardar'" icon="pi pi-check" @click="submitEvento" :loading="isLoading" />
            </template>
        </Dialog>


    </div>
</template>

<script setup>
import { ref, reactive, onMounted, inject, watch } from 'vue';
import { authStore } from "@/store/auth";
import useCancelaciones from '@/composables/cancelaciones';
import useEventos from '@/composables/eventos';
import useCategories from '@/composables/categories';
import { useConfirm } from "primevue/useconfirm";

const auth = authStore();
const confirm = useConfirm();
const swal = inject('$swal');


const { cancelaciones, getCancelacionesPendientes, confirmarCancelacion } = useCancelaciones();
const { eventos, evento, getEventos, createEvento, updateEvento, deleteEvento, resetEvento, setEvento, hasError, getError, isLoading } = useEventos();
const { categoryList, getCategoryList } = useCategories();

// Smart Location Logic
watch(() => evento.value.localizacion, (newVal) => {
    if (!newVal) return;
    
    // Pattern 1: @lat,lng
    const geoMatch = newVal.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/);
    if (geoMatch) {
        evento.value.latitud = parseFloat(geoMatch[1]);
        evento.value.longitud = parseFloat(geoMatch[2]);
        return;
    }

    // Pattern 2: place/.../@lat,lng
    const placeMatch = newVal.match(/place\/.*\/@(-?\d+\.\d+),(-?\d+\.\d+)/);
    if (placeMatch) {
        evento.value.latitud = parseFloat(placeMatch[1]);
        evento.value.longitud = parseFloat(placeMatch[2]);
        return;
    }
});

const geocodeAddress = async () => {
    const address = evento.value.localizacion;
    if (!address || address.includes('google.com/maps')) return;
    
    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`);
        const data = await response.json();
        if (data && data.length > 0) {
            evento.value.latitud = parseFloat(data[0].lat);
            evento.value.longitud = parseFloat(data[0].lon);
        }
    } catch (e) {
        // Silently fail geocoding
    }
};

const eventoDialog = reactive({
    open: false,
    type: 'create'
});

const onMountedTasks = async () => {
    getCancelacionesPendientes();
    getCategoryList();
    fetchMyEvents();
};

onMounted(onMountedTasks);

const fetchMyEvents = () => {
    // Solo traemos los eventos que pertenecen a este organizador
    const userId = auth.user?.id || auth.user?.id_usuario;
    getEventos({ id_organizador: userId });
};

const openCreateDialog = () => {
    resetEvento();
    eventoDialog.type = 'create';
    eventoDialog.open = true;
};

const openEditDialog = (data) => {
    setEvento(data);
    eventoDialog.type = 'edit';
    eventoDialog.open = true;
};

const onFileChange = (e) => {
    evento.value.imagen = e.target.files[0];
};

const submitEvento = async () => {
    try {
        if (eventoDialog.type === 'create') {
            await createEvento();
        } else {
            await updateEvento();
        }
        eventoDialog.open = false;
        fetchMyEvents();
    } catch (e) {
        // useEventos/useToast ya manejan los errores visuales
    }
};

const confirmDeleteEvento = (data) => {
    swal({
        title: '¿Estás seguro?',
        text: `Vas a eliminar el evento "${data.nombre}". Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'No, mantener',
        confirmButtonColor: '#ef4444'
    }).then(async (result) => {
        if (result.isConfirmed) {
            await deleteEvento(data.id_evento);
            fetchMyEvents();
        }
    });
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const aprobarCancelacion = (id) => {
    confirm.confirm({
        message: '¿Estas seguro de aprobar esta cancelacion? El aforo disponible de tu evento se recalculara automáticamente.',
        header: 'Confirmar Cancelacion',
        icon: 'pi pi-exclamation-triangle',
        acceptLabel: 'Si, aprobar',
        rejectLabel: 'Cerrar',
        acceptClass: 'p-button-danger rounded-xl',
        accept: () => {
            confirmarCancelacion(id);
        }
    });
};
</script>

<style scoped>
:deep(.p-card) {
    border-radius: 1.25rem !important;
}
:deep(.p-card-content) {
    padding: 1.25rem !important;
}
</style>
