<template>
    <div class="eventos-page">
        <Card>
            <template #title>
                <div class="flex items-center justify-between w-full">
                    <span>Gestión de Eventos</span>
                    <div class="flex items-center gap-2">
                        <Button
                            label="Actualizar"
                            icon="pi pi-refresh"
                            size="small"
                            outlined
                            severity="secondary"
                            :loading="isLoading"
                            @click="getEventos"
                        />
                        <Button
                            v-if="can('all') || can('course-create')"
                            label="Nuevo Evento"
                            icon="pi pi-plus"
                            size="small"
                            severity="primary"
                            @click="openCreateDialog"
                        />
                    </div>
                </div>
            </template>

            <template #subtitle>
                Administra y gestiona los eventos del sistema. Crea, edita y elimina eventos.
            </template>

            <template #content>
                <div v-if="isLoading && !eventos.length" class="table-loading-skeleton space-y-3">
                    <div
                        v-for="row in skeletonRows"
                        :key="row"
                        class="flex gap-3 items-center"
                    >
                        <Skeleton width="60px" height="1.25rem" />
                        <Skeleton width="220px" height="1.25rem" />
                        <Skeleton width="160px" height="1.25rem" />
                        <div class="flex gap-2 ml-auto">
                            <Skeleton width="2.5rem" height="2.5rem" shape="circle" />
                            <Skeleton width="2.5rem" height="2.5rem" shape="circle" />
                        </div>
                    </div>
                </div>
                <DataTable
                    v-else
                    v-model:filters="filters"
                    :value="eventos || []"
                    :paginator="true"
                    :rows="10"
                    :rows-per-page-options="[10, 25, 50]"
                    data-key="id_evento"
                    striped-rows
                    size="small"
                    :loading="isLoading"
                    filter-display="menu"
                    :global-filter-fields="['id_evento', 'nombre', 'created_at']"
                >
                    <template #empty>
                        <div class="table-empty-state text-center p-8">
                            <i class="pi pi-inbox text-4xl mb-4 text-surface-400"></i>
                            <p class="font-bold text-lg mb-2">No se encontraron eventos</p>
                            <p class="text-surface-500">Intenta ajustar la búsqueda</p>
                        </div>
                    </template>

                    <Column field="id_evento" header="ID" sortable class="w-[80px]">
                        <template #body="slotProps">
                            <Skeleton v-if="isLoading && !eventos.length" width="3rem" height="1rem" />
                            <span v-else class="font-mono text-xs">#{{ slotProps.data.id_evento }}</span>
                        </template>
                    </Column>

                    <Column field="nombre" header="Nombre" sortable filter class="min-w-[220px]">
                        <template #body="slotProps">
                            <Skeleton v-if="isLoading && !eventos.length" width="12rem" height="1rem" />
                            <div v-else class="flex items-center gap-3">
                                <img v-if="slotProps.data.imagen" :src="slotProps.data.imagen" class="w-10 h-10 rounded object-cover shadow-sm" />
                                <div v-else class="w-10 h-10 rounded bg-surface-200 dark:bg-surface-700 flex items-center justify-center">
                                    <i class="pi pi-calendar text-surface-400"></i>
                                </div>
                                <span class="font-medium">{{ slotProps.data.nombre || '-' }}</span>
                            </div>
                        </template>
                        <template #filter="{ filterModel }">
                            <InputText v-model="filterModel.value" type="text" placeholder="Search by name" />
                        </template>
                    </Column>
                    
                    <Column field="fecha_inicio" header="Fecha de Inicio" sortable class="min-w-[150px]">
                        <template #body="slotProps">
                            <Skeleton v-if="isLoading && !eventos.length" width="8rem" height="1rem" />
                            <span v-else class="text-sm">
                                <i class="pi pi-clock mr-2 text-xs opacity-70"></i>
                                {{ formatDate(slotProps.data.fecha_inicio) }}
                            </span>
                        </template>
                    </Column>
                    
                    <Column field="aforo" header="Aforo" class="min-w-[100px]">
                        <template #body="slotProps">
                            <Skeleton v-if="isLoading && !eventos.length" width="4rem" height="1rem" />
                            <span v-else>{{ slotProps.data.aforo }}</span>
                        </template>
                    </Column>
                    
                    <Column field="precio" header="Precio" class="min-w-[100px]">
                        <template #body="slotProps">
                            <Skeleton v-if="isLoading && !eventos.length" width="4rem" height="1rem" />
                            <span v-else class="font-bold text-primary">{{ slotProps.data.precio > 0 ? slotProps.data.precio + '€' : 'Gratis' }}</span>
                        </template>
                    </Column>

                    <Column header="Acciones" class="w-[120px]">
                        <template #body="slotProps">
                            <Skeleton v-if="isLoading && !eventos.length" width="4rem" height="2rem" />
                            <div v-else class="flex gap-2">
                                <Button
                                    v-if="can('all') || isOwner(slotProps.data)"
                                    v-tooltip.top="'Editar'"
                                    icon="pi pi-pencil"
                                    rounded
                                    text
                                    severity="secondary"
                                    size="small"
                                    @click="openEditDialog(slotProps.data)"
                                />
                                <Button
                                    v-if="can('all') || isOwner(slotProps.data)"
                                    v-tooltip.top="'Eliminar'"
                                    icon="pi pi-trash"
                                    rounded
                                    text
                                    severity="danger"
                                    size="small"
                                    @click="confirmDeleteEvento(slotProps.data)"
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>

        <Dialog
            v-model:visible="eventoDialog.open"
            modal
            :header="eventoDialog.type === 'create' ? 'Crear Evento' : 'Editar Evento'"
            :style="{ width: '600px' }"
            class="evento-dialog"
        >
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                <div class="md:col-span-2">
                    <label class="block mb-2 font-medium">Nombre del evento</label>
                    <InputText v-model="evento.nombre" class="w-full" :class="{ 'p-invalid': hasError('nombre') }" placeholder="Ej: Concierto" />
                    <small v-if="hasError('nombre')" class="text-red-500">{{ getError('nombre') }}</small>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block mb-2 font-medium">Descripción</label>
                    <Textarea v-model="evento.descripcion" rows="3" class="w-full" :class="{ 'p-invalid': hasError('descripcion') }" />
                    <small v-if="hasError('descripcion')" class="text-red-500">{{ getError('descripcion') }}</small>
                </div>

                <div>
                    <label class="block mb-2 font-medium">Fecha Inicio</label>
                    <InputText type="datetime-local" v-model="evento.fecha_inicio" class="w-full" :class="{ 'p-invalid': hasError('fecha_inicio') }" />
                    <small v-if="hasError('fecha_inicio')" class="text-red-500">{{ getError('fecha_inicio') }}</small>
                </div>
                
                <div>
                    <label class="block mb-2 font-medium">Fecha Fin</label>
                    <InputText type="datetime-local" v-model="evento.fecha_fin" class="w-full" :class="{ 'p-invalid': hasError('fecha_fin') }" />
                    <small v-if="hasError('fecha_fin')" class="text-red-500">{{ getError('fecha_fin') }}</small>
                </div>

                <div>
                    <label class="block mb-2 font-medium">Aforo</label>
                    <InputNumber v-model="evento.aforo" mode="decimal" :useGrouping="false" class="w-full" :class="{ 'p-invalid': hasError('aforo') }" />
                    <small v-if="hasError('aforo')" class="text-red-500">{{ getError('aforo') }}</small>
                </div>
                
                <div>
                    <label class="block mb-2 font-medium">Precio</label>
                    <InputNumber v-model="evento.precio" mode="currency" currency="EUR" locale="es-ES" class="w-full" :class="{ 'p-invalid': hasError('precio') }" />
                    <small v-if="hasError('precio')" class="text-red-500">{{ getError('precio') }}</small>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block mb-2 font-medium">Categoría</label>
                    <Dropdown v-model="evento.id_categoria" :options="categoryList" optionLabel="name" optionValue="id" placeholder="Selecciona una categoría" class="w-full" :class="{ 'p-invalid': hasError('id_categoria') }" />
                    <small v-if="hasError('id_categoria')" class="text-red-500">{{ getError('id_categoria') }}</small>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block mb-2 font-medium">Imagen del Evento</label>
                    <input type="file" @change="onFileChange" accept="image/*" class="w-full p-2 border rounded border-surface-300 dark:border-surface-600 focus:border-primary focus:ring-1 focus:ring-primary outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-colors" />
                </div>
            </div>
            
            <template #footer>
                <Button severity="secondary" label="Cancelar" @click="closeDialog" :disabled="isSubmitting" />
                <Button v-if="eventoDialog.type === 'create'" label="Crear" @click="submitCreate" :loading="isSubmitting" :disabled="isSubmitting" />
                <Button v-else label="Guardar" @click="submitUpdate" :loading="isSubmitting" :disabled="isSubmitting" />
            </template>
        </Dialog>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, inject } from "vue";
import useEventos from "@/composables/eventos";
import useCategories from "@/composables/categories";
import { useAbility } from '@casl/vue';
import { authStore } from "@/store/auth";
import { FilterMatchMode, FilterOperator } from "@primevue/core/api";

const { eventos, evento, getEventos, createEvento, updateEvento, deleteEvento, resetEvento, setEvento, hasError, getError, upsertEventoRecord, isLoading } = useEventos();
const { categoryList, getCategoryList } = useCategories();
const { can } = useAbility();
const auth = authStore();

const isOwner = (eventoItem) => {
    return auth.user?.id === eventoItem.id_organizador || auth.user?.id_usuario === eventoItem.id_organizador;
};

const swal = inject('$swal');

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    id_evento: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
    nombre: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
});

const eventoDialog = reactive({
    open: false,
    type: 'create'
});

const isSubmitting = computed(() => isLoading.value);
const skeletonRows = Array.from({ length: 5 }, (_, index) => index);

const onFileChange = (e) => {
    evento.value.imagen = e.target.files[0];
};

const openCreateDialog = () => {
    resetEvento();
    eventoDialog.type = 'create';
    eventoDialog.open = true;
};

const openEditDialog = (currentEvento) => {
    setEvento(currentEvento);
    eventoDialog.type = 'edit';
    eventoDialog.open = true;
};

const closeDialog = () => {
    eventoDialog.open = false;
    resetEvento();
};

const submitCreate = () => {
    if (isSubmitting.value) return;
    createEvento().then(createdEvento => {
        if (createdEvento) {
            upsertEventoRecord(createdEvento);
            closeDialog();
            getEventos();
        }
    });
};

const submitUpdate = () => {
    if (isSubmitting.value) return;
    updateEvento().then(updatedEvento => {
        if (updatedEvento) {
            upsertEventoRecord(updatedEvento);
            closeDialog();
            getEventos();
        }
    });
};

const confirmDeleteEvento = (currentEvento) => {
    swal({
        icon: 'warning',
        title: '¿Eliminar evento?',
        text: `El evento "${currentEvento.nombre}" se eliminará.`,
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ef4444'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteEvento(currentEvento.id_evento);
        }
    });
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

onMounted(() => {
    getCategoryList();
    getEventos();
});
</script>
