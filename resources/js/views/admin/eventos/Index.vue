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
                            v-if="can('evento-create')"
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
                Administra y gestiona los eventos del sistema. Crea, edita y elimina eventos según tus permisos.
            </template>

            <template #content>
                <div v-if="isLoading" class="table-loading-skeleton space-y-3">
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
                    :filter-delay="300"
                    :global-filter-fields="['id_evento', 'nombre', 'descripcion', 'created_at']"
                >
                    <template #empty>
                        <div class="table-empty-state">
                            <i class="pi pi-inbox empty-state-icon"></i>
                            <p class="empty-state-text">No se encontraron eventos</p>
                            <p class="empty-state-subtext">Intenta ajustar los filtros de búsqueda</p>
                        </div>
                    </template>

                    <Column field="id_evento" header="ID" sortable filter class="w-[80px]">
                        <template #body="slotProps">
                            <Skeleton v-if="isLoading" width="3rem" height="1rem" />
                            <span v-else class="table-cell-id">#{{ slotProps.data.id_evento }}</span>
                        </template>
                        <template #filter="{ filterModel }">
                            <InputText v-model="filterModel.value" placeholder="ID" class="w-full" />
                        </template>
                    </Column>

                    <Column field="nombre" header="Nombre" sortable filter class="min-w-[220px]">
                        <template #body="slotProps">
                            <Skeleton v-if="isLoading" width="12rem" height="1rem" />
                            <span v-else class="table-cell-name">{{ slotProps.data.nombre || '-' }}</span>
                        </template>
                        <template #filter="{ filterModel }">
                            <InputText v-model="filterModel.value" type="text" placeholder="Buscar por nombre" />
                        </template>
                    </Column>

                    <Column field="fecha_inicio" header="Fecha Inicio" sortable class="min-w-[180px]">
                        <template #body="slotProps">
                            <Skeleton v-if="isLoading" width="8rem" height="1rem" />
                            <span v-else class="text-sm table-cell-date">
                                <i class="pi pi-calendar mr-2 text-xs opacity-70"></i>
                                {{ formatDate(slotProps.data.fecha_inicio) }}
                            </span>
                        </template>
                    </Column>

                    <Column field="precio" header="Precio" sortable class="min-w-[100px]">
                        <template #body="slotProps">
                            <Skeleton v-if="isLoading" width="5rem" height="1rem" />
                            <span v-else class="text-sm">{{ slotProps.data.precio ? '$' + slotProps.data.precio : 'Gratis' }}</span>
                        </template>
                    </Column>

                    <Column field="aforo" header="Aforo" sortable class="min-w-[100px]">
                        <template #body="slotProps">
                            <Skeleton v-if="isLoading" width="5rem" height="1rem" />
                            <span v-else class="text-sm">{{ slotProps.data.aforo || '-' }}</span>
                        </template>
                    </Column>

                    <Column field="created_at" header="Fecha de Creación" sortable class="min-w-[180px]">
                        <template #body="slotProps">
                            <Skeleton v-if="isLoading" width="8rem" height="1rem" />
                            <span v-else class="text-sm table-cell-date">
                                <i class="pi pi-calendar mr-2 text-xs opacity-70"></i>
                                {{ formatDate(slotProps.data.created_at) }}
                            </span>
                        </template>
                    </Column>

                    <Column header="Acciones" class="w-[160px]">
                        <template #body="slotProps">
                            <Skeleton v-if="isLoading" width="4rem" height="2rem" />
                            <div v-else class="flex gap-2">
                                <Button
                                    v-if="can('evento-edit')"
                                    v-tooltip.top="'Editar evento'"
                                    icon="pi pi-pencil"
                                    rounded
                                    text
                                    severity="secondary"
                                    size="small"
                                    @click="openEditDialog(slotProps.data)"
                                />
                                <Button
                                    v-if="can('evento-delete')"
                                    v-tooltip.top="'Eliminar evento'"
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
            :style="{ width: '700px' }"
            class="evento-dialog"
        >
            <div class="flex flex-col gap-4 max-h-[70vh] overflow-y-auto">
                <div>
                    <label for="evento-nombre" class="dialog-label">Nombre del evento *</label>
                    <InputText
                        v-model="evento.nombre"
                        id="evento-nombre"
                        class="w-full"
                        :class="{ 'p-invalid': hasError('nombre') }"
                        placeholder="Nombre del evento"
                    />
                    <small v-if="hasError('nombre')" class="dialog-error">
                        {{ getError('nombre') }}
                    </small>
                </div>

                <div>
                    <label for="evento-descripcion" class="dialog-label">Descripción *</label>
                    <Textarea
                        v-model="evento.descripcion"
                        id="evento-descripcion"
                        class="w-full"
                        :class="{ 'p-invalid': hasError('descripcion') }"
                        placeholder="Descripción del evento"
                        :rows="3"
                    />
                    <small v-if="hasError('descripcion')" class="dialog-error">
                        {{ getError('descripcion') }}
                    </small>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="evento-fecha-inicio" class="dialog-label">Fecha Inicio *</label>
                        <Calendar
                            v-model="evento.fecha_inicio"
                            id="evento-fecha-inicio"
                            date-format="yy-mm-dd"
                            :show-time="true"
                            class="w-full"
                            :class="{ 'p-invalid': hasError('fecha_inicio') }"
                        />
                        <small v-if="hasError('fecha_inicio')" class="dialog-error">
                            {{ getError('fecha_inicio') }}
                        </small>
                    </div>

                    <div>
                        <label for="evento-fecha-fin" class="dialog-label">Fecha Fin *</label>
                        <Calendar
                            v-model="evento.fecha_fin"
                            id="evento-fecha-fin"
                            date-format="yy-mm-dd"
                            :show-time="true"
                            class="w-full"
                            :class="{ 'p-invalid': hasError('fecha_fin') }"
                        />
                        <small v-if="hasError('fecha_fin')" class="dialog-error">
                            {{ getError('fecha_fin') }}
                        </small>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="evento-categoria" class="dialog-label">Categoría *</label>
                        <Dropdown
                            v-model="evento.id_categoria"
                            id="evento-categoria"
                            :options="categoriesList"
                            option-label="name"
                            option-value="id"
                            placeholder="Seleccionar categoría"
                            class="w-full"
                            :class="{ 'p-invalid': hasError('id_categoria') }"
                        />
                        <small v-if="hasError('id_categoria')" class="dialog-error">
                            {{ getError('id_categoria') }}
                        </small>
                    </div>

                    <div>
                        <label for="evento-organizador" class="dialog-label">Organizador *</label>
                        <Dropdown
                            v-model="evento.id_organizador"
                            id="evento-organizador"
                            :options="usersList"
                            option-label="name"
                            option-value="id_usuario"
                            placeholder="Seleccionar organizador"
                            class="w-full"
                            :class="{ 'p-invalid': hasError('id_organizador') }"
                        />
                        <small v-if="hasError('id_organizador')" class="dialog-error">
                            {{ getError('id_organizador') }}
                        </small>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="evento-precio" class="dialog-label">Precio</label>
                        <InputNumber
                            v-model="evento.precio"
                            id="evento-precio"
                            :min="0"
                            :fraction-digits="2"
                            placeholder="0.00"
                            class="w-full"
                            :class="{ 'p-invalid': hasError('precio') }"
                        />
                        <small v-if="hasError('precio')" class="dialog-error">
                            {{ getError('precio') }}
                        </small>
                    </div>

                    <div>
                        <label for="evento-aforo" class="dialog-label">Aforo</label>
                        <InputNumber
                            v-model="evento.aforo"
                            id="evento-aforo"
                            :min="1"
                            placeholder="Aforo máximo"
                            class="w-full"
                            :class="{ 'p-invalid': hasError('aforo') }"
                        />
                        <small v-if="hasError('aforo')" class="dialog-error">
                            {{ getError('aforo') }}
                        </small>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="evento-latitud" class="dialog-label">Latitud</label>
                        <InputNumber
                            v-model="evento.latitud"
                            id="evento-latitud"
                            :min="-90"
                            :max="90"
                            :fraction-digits="6"
                            placeholder="Ej: 40.7128"
                            class="w-full"
                            :class="{ 'p-invalid': hasError('latitud') }"
                        />
                        <small v-if="hasError('latitud')" class="dialog-error">
                            {{ getError('latitud') }}
                        </small>
                    </div>

                    <div>
                        <label for="evento-longitud" class="dialog-label">Longitud</label>
                        <InputNumber
                            v-model="evento.longitud"
                            id="evento-longitud"
                            :min="-180"
                            :max="180"
                            :fraction-digits="6"
                            placeholder="Ej: -74.0060"
                            class="w-full"
                            :class="{ 'p-invalid': hasError('longitud') }"
                        />
                        <small v-if="hasError('longitud')" class="dialog-error">
                            {{ getError('longitud') }}
                        </small>
                    </div>
                </div>

                <div>
                    <label for="evento-limite-edad" class="dialog-label">Límite de Edad</label>
                    <InputNumber
                        v-model="evento.limite_edad"
                        id="evento-limite-edad"
                        :min="0"
                        placeholder="Edad mínima requerida"
                        class="w-full"
                        :class="{ 'p-invalid': hasError('limite_edad') }"
                    />
                    <small v-if="hasError('limite_edad')" class="dialog-error">
                        {{ getError('limite_edad') }}
                    </small>
                </div>
            </div>

            <template #footer>
                <Button
                    severity="secondary"
                    label="Cancelar"
                    @click="closeDialog"
                    :disabled="isSubmitting"
                />
                <Button
                    v-if="eventoDialog.type === 'create'"
                    label="Crear"
                    @click="submitCreate"
                    :loading="isSubmitting"
                    :disabled="isSubmitting"
                />
                <Button
                    v-else
                    label="Guardar"
                    @click="submitUpdate"
                    :loading="isSubmitting"
                    :disabled="isSubmitting"
                />
            </template>
        </Dialog>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, inject, watch } from "vue";
import useEventos from "@/composables/eventos";
import useCategories from "@/composables/categories";
import useUsers from "@/composables/users";
import { useAbility } from '@casl/vue';
import { FilterMatchMode, FilterOperator  } from "@primevue/core/api";

const FILTERS_STORAGE_KEY = 'admin_eventos_table_filters';
const { eventos, evento, getEventos, createEvento, updateEvento, deleteEvento, resetEvento, setEvento, hasError, getError, upsertEventoRecord, isLoading } = useEventos();
const { categoryList: categoriesList, getCategoryList } = useCategories();
const { users: usersList, getUsers } = useUsers();
const { can } = useAbility();

const swal = inject('$swal');
const canUseBrowserStorage = typeof window !== 'undefined';

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    id_evento: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    nombre: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
});

const eventoDialog = reactive({
    open: false,
    type: 'create'
});

const isSubmitting = computed(() => isLoading.value);
const skeletonRows = Array.from({ length: 5 }, (_, index) => index);

const saveFiltersToStorage = (currentFilters) => {
    if (!canUseBrowserStorage) return;
    try {
        window.localStorage.setItem(FILTERS_STORAGE_KEY, JSON.stringify(currentFilters));
    } catch (error) {
        console.warn('No se pudieron guardar los filtros de eventos', error);
    }
};

const restoreFiltersFromStorage = () => {
    if (!canUseBrowserStorage) return;
    try {
        const stored = window.localStorage.getItem(FILTERS_STORAGE_KEY);
        if (!stored) return;
        const parsed = JSON.parse(stored);
        filters.value = {
            global: { ...filters.value.global, ...parsed.global },
            id_evento: { ...filters.value.id_evento, ...parsed.id_evento },
            nombre: { ...filters.value.nombre, ...parsed.nombre }
        };
    } catch (error) {
        console.warn('No se pudieron restaurar los filtros de eventos', error);
    }
};

watch(filters, (newFilters) => {
    saveFiltersToStorage(newFilters);
}, { deep: true });

const formatDate = (v) => {
    if (!v) return '-';
    try {
        return new Date(v).toLocaleString('es-ES');
    } catch (e) {
        return v;
    }
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

    createEvento()
        .then(createdEvento => {
            if (createdEvento) {
                upsertEventoRecord(createdEvento);
                closeDialog();
            }
        });
};

const submitUpdate = () => {
    if (isSubmitting.value) return;

    updateEvento()
        .then(updatedEvento => {
            if (updatedEvento) {
                upsertEventoRecord(updatedEvento);
                closeDialog();
            }
        });
};

const performDelete = (id) => {
    deleteEvento(id);
};

const confirmDeleteEvento = (currentEvento) => {
    if (!swal) {
        performDelete(currentEvento.id_evento);
        return;
    }

    swal({
        icon: 'warning',
        title: '¿Eliminar evento?',
        text: `El evento "${currentEvento.nombre}" se eliminará de forma permanente.`,
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ef4444'
    }).then((result) => {
        if (result.isConfirmed) {
            performDelete(currentEvento.id_evento);
        }
    });
};

onMounted(async () => {
    restoreFiltersFromStorage();
    await getEventos();
    await getCategoryList();
    await getUsers();
});
</script>
