
<!-- Vista para gestionar las categorias desde el panel de administrador -->
<template>
    <!-- Contenedor principal de la pagina -->
    <div class="categories-page">
        <!-- Tarjeta que envuelve el contenido -->
        <Card>
            <template #title>
                <div class="flex items-center justify-between w-full">
                    <span>Gestión de Categorías</span>
                    <div class="flex items-center gap-2">
                        <Button
                            label="Actualizar"
                            icon="pi pi-refresh"
                            size="small"
                            outlined
                            severity="secondary"
                            :loading="isLoading"
                            @click="getCategories"
                        />
                        <Button
                            v-if="can('category-create')"
                            label="Nueva Categoría"
                            icon="pi pi-plus"
                            size="small"
                            severity="primary"
                            @click="openCreateDialog"
                        />
                    </div>
                </div>
            </template>

            <template #subtitle>
                Administra y gestiona las categorías del sistema. Crea, edita y elimina categorías según tus permisos.
            </template>

            <!-- Contenido principal con la tabla -->
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
                <!-- Tabla de datos con filtros y paginacion -->
                <DataTable
                    v-else
                    v-model:filters="filters"
                    :value="categories || []"
                    :paginator="true"
                    :rows="10"
                    :rows-per-page-options="[10, 25, 50]"
                    data-key="id"
                    striped-rows
                    size="small"
                    :loading="isLoading"
                    filter-display="menu"
                    :filter-delay="300"
                    :global-filter-fields="['id', 'name', 'created_at']"
                >
                    <template #empty>
                        <div class="table-empty-state">
                            <i class="pi pi-inbox empty-state-icon"></i>
                            <p class="empty-state-text">No se encontraron categorías</p>
                            <p class="empty-state-subtext">Intenta ajustar los filtros de búsqueda</p>
                        </div>
                    </template>

                    <Column field="id" header="ID" sortable filter class="w-[80px]">
                        <template #body="slotProps">
                            <Skeleton v-if="isLoading" width="3rem" height="1rem" />
                            <span v-else class="table-cell-id">#{{ slotProps.data.id }}</span>
                        </template>
                        <template #filter="{ filterModel }">
                            <InputText v-model="filterModel.value" placeholder="ID" class="w-full" />
                        </template>
                    </Column>

                    <Column field="name" header="Nombre" sortable filter class="min-w-[220px]">
                        <template #body="slotProps">
                            <Skeleton v-if="isLoading" width="12rem" height="1rem" />
                            <span v-else class="table-cell-name">{{ slotProps.data.name || '-' }}</span>
                        </template>
                        <template #filter="{ filterModel }">
                            <InputText v-model="filterModel.value" type="text" placeholder="Search by name" />
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
                                    v-if="can('category-edit')"
                                    v-tooltip.top="'Editar categoría'"
                                    icon="pi pi-pencil"
                                    rounded
                                    text
                                    severity="secondary"
                                    size="small"
                                    @click="openEditDialog(slotProps.data)"
                                />
                                <Button
                                    v-if="can('category-delete')"
                                    v-tooltip.top="'Eliminar categoría'"
                                    icon="pi pi-trash"
                                    rounded
                                    text
                                    severity="danger"
                                    size="small"
                                    @click="confirmDeleteCategory(slotProps.data)"
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>

        <!-- Ventana emergente para crear o editar categorias -->
        <Dialog
            v-model:visible="categoryDialog.open"
            modal
            :header="categoryDialog.type === 'create' ? 'Crear Categoría' : 'Editar Categoría'"
            :style="{ width: '500px' }"
            class="category-dialog"
        >
            <div class="flex flex-col gap-4">
                <div>
                    <label for="category-name" class="dialog-label">Nombre de la categoría</label>
                    <InputText
                        v-model="category.name"
                        id="category-name"
                        class="w-full"
                        :class="{ 'p-invalid': hasError('name') }"
                        placeholder="Nombre de la categoría"
                    />
                    <small v-if="hasError('name')" class="dialog-error">
                        {{ getError('name') }}
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
                    v-if="categoryDialog.type === 'create'"
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
// Logica del componente de categorias
// Importacion de librerias y herramientas necesarias
import { ref, reactive, computed, onMounted, inject, watch } from "vue";
import useCategories from "@/composables/categories";
import { useAbility } from '@casl/vue';
import { FilterMatchMode, FilterOperator  } from "@primevue/core/api";

// Uso de composables y utilidades de la aplicacion
const FILTERS_STORAGE_KEY = 'admin_categories_table_filters';
const {categories, category, getCategories, createCategory, updateCategory, deleteCategory, resetCategory, setCategory, hasError, getError, upsertCategoryRecord, isLoading } = useCategories();
const { can } = useAbility();

// Inyeccion de servicios externos como SweetAlert
const swal = inject('$swal');
const canUseBrowserStorage = typeof window !== 'undefined';

// Estado de los filtros de la tabla
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    id: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
});

// Control del dialogo de categorias
const categoryDialog = reactive({
    open: false,
    type: 'create'
});

const isSubmitting = computed(() => isLoading.value);
const skeletonRows = Array.from({ length: 5 }, (_, index) => index);

// Guarda los filtros en el navegador para no perderlos al recargar
const saveFiltersToStorage = (currentFilters) => {
    if (!canUseBrowserStorage) return;
    try {
        window.localStorage.setItem(FILTERS_STORAGE_KEY, JSON.stringify(currentFilters));
    } catch (error) {
        console.warn('No se pudieron guardar los filtros de categorías', error);
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
            id: { ...filters.value.id, ...parsed.id },
            name: { ...filters.value.name, ...parsed.name }
        };
    } catch (error) {
        console.warn('No se pudieron restaurar los filtros de categorías', error);
    }
};

watch(filters, (newFilters) => {
    saveFiltersToStorage(newFilters);
}, { deep: true });

// Abre el dialogo para crear una nueva categoria
const openCreateDialog = () => {
    resetCategory();
    categoryDialog.type = 'create';
    categoryDialog.open = true;
};

// Abre el dialogo para editar una categoria existente
const openEditDialog = (currentCategory) => {
    setCategory(currentCategory);
    categoryDialog.type = 'edit';
    categoryDialog.open = true;
};

// Cierra la ventana emergente y limpia los datos
const closeDialog = () => {
    categoryDialog.open = false;
    resetCategory();
};

// Envia la peticion para crear la categoria
const submitCreate = () => {
    if (isSubmitting.value) return;

    createCategory()
        .then(createdCategory => {
            if (createdCategory) {
                upsertCategoryRecord(createdCategory);
                closeDialog();
            }
        });
};

// Envia la peticion para actualizar la categoria
const submitUpdate = () => {
    if (isSubmitting.value) return;

    updateCategory()
        .then(updatedCategory => {
            if (updatedCategory) {
                upsertCategoryRecord(updatedCategory);
                closeDialog();
            }
        });
};

// Llama al servicio para borrar la categoria por su id
const performDelete = (id) => {
    deleteCategory(id);
};

// Muestra una alerta para confirmar la eliminacion
const confirmDeleteCategory = (currentCategory) => {
    if (!swal) {
        performDelete(currentCategory.id);
        return;
    }

    swal({
        icon: 'warning',
        title: '¿Eliminar categoría?',
        text: `La categoría "${currentCategory.name}" se eliminará de forma permanente.`,
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ef4444'
    }).then((result) => {
        if (result.isConfirmed) {
            performDelete(currentCategory.id);
        }
    });
};

// Convierte la fecha a un formato legible
const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

// Se ejecuta cuando el componente se monta en la pantalla
onMounted(() => {
    restoreFiltersFromStorage();
    getCategories();
});
</script>
