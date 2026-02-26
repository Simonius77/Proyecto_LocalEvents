<!-- 
    Vista de creación de usuarios para la administración.
    Proporciona un formulario para dar de alta nuevos usuarios en el sistema,
    asignándoles datos personales y roles específicos.
-->
<template>
    <Panel class="flex flex-col justify-center my-10">
        <form @submit.prevent="submitForm">
            <!-- Campo: Nombre -->
            <div class="mb-3">
                <div class="flex items-center gap-3">
                    <label for="user-name">Nombre:</label>
                    <InputText v-model="user.name" id="user-name" type="text" size="small" :invalid="!!errors.name" />
                </div>
                <!-- Errores de validación frontal o del composable -->
                <div class="text-red-400 mt-1">
                    {{ errors.name }}
                </div>
                <!-- Errores de validación devueltos por la API (Laravel) -->
                <div class="mt-1">
                    <div v-for="message in validationErrors?.name" class="text-red-400">
                        {{ message }}
                    </div>
                </div>
            </div>

            <!-- Campo: Primer Apellido -->
            <div class="mb-3">
                <div class="flex items-center gap-3">
                    <label for="user-surname1">Primer apellido:</label>
                    <InputText v-model="user.surname1" id="user-surname1" type="text" size="small" :invalid="!!errors.surname1" />
                </div>
                <div class="text-red-400 mt-1">
                    {{ errors.surname1 }}
                </div>
                <div class="mt-1">
                    <div v-for="message in validationErrors?.surname1" class="text-red-400">
                        {{ message }}
                    </div>
                </div>
            </div>

            <!-- Campo: Segundo Apellido -->
            <div class="mb-3">
                <div class="flex items-center gap-3">
                    <label for="user-surname2">Segundo apellido:</label>
                    <InputText v-model="user.surname2" id="user-surname2" type="text" size="small" :invalid="!!errors.surname2" />
                </div>
                <div class="text-red-400 mt-1">
                    {{ errors.surname2 }}
                </div>
                <div class="mt-1">
                    <div v-for="message in validationErrors?.surname2" class="text-red-400">
                        {{ message }}
                    </div>
                </div>
            </div>

            <!-- Campo: Email -->
            <div class="mb-3">
                <div class="flex items-center gap-3">
                    <label for="user-email">Email:</label>
                    <InputText v-model="user.email" id="user-email" type="email" size="small" :invalid="!!errors.email" />
                </div>
                <div class="text-red-400 mt-1">
                    {{ errors.email }}
                </div>
                <div class="mt-1">
                    <div v-for="message in validationErrors?.email" class="text-red-400">
                        {{ message }}
                    </div>
                </div>
            </div>

            <!-- Campo: Contraseña -->
            <div class="mb-3">
                <div class="flex items-center gap-3">
                    <label for="user-password">Password:</label>
                    <InputText v-model="user.password" id="user-password" type="password" size="small" :invalid="!!errors.password" />
                </div>
                <div class="text-red-400 mt-1">
                    {{ errors.password }}
                </div>
                <div class="mt-1">
                    <div v-for="message in validationErrors?.password" class="text-red-400">
                        {{ message }}
                    </div>
                </div>
            </div>

            <!-- Campo: Selección de Roles -->
            <div class="mb-3">
                <div class="flex items-center gap-3">
                    <label for="user-role">Roles:</label>
                    <MultiSelect
                        v-model="user.role_id"
                        :options="roles"
                        size="small"
                        display="chip"
                        optionLabel="name"
                        optionValue="id"
                        filter
                        placeholder="Selecciona roles"
                        :invalid="!!errors.role_id"
                    />
                </div>
                <div class="text-red-400 mt-1">
                    {{ errors.role_id }}
                </div>
                <div class="mt-1">
                    <div v-for="message in validationErrors?.role" class="text-red-400">
                        {{ message }}
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="mt-4 text-right">
                <Button :disabled="isLoading" type="submit">
                    <div v-show="isLoading" class=""></div>
                    <span v-if="isLoading">Procesando...</span>
                    <span v-else>Guardar</span>
                </Button>
            </div>
        </form>
    </Panel>
</template>
<script setup>
    import { onMounted } from "vue";
    import useRoles from "@/composables/roles";
    import useUsers from "@/composables/users";

    // Extraemos la lógica de roles (para el MultiSelect) y usuarios de los composables
    const { roles, getRoles } = useRoles();
    const { user, createUser, validationErrors, isLoading, errors } = useUsers();

    /**
     * Envía el formulario para crear un nuevo usuario.
     */
    function submitForm() {
        createUser(user.value)
    }

    /**
     * Al cargar el componente, obtenemos la lista de roles disponibles de la API.
     */
    onMounted(() => {
        getRoles()
    })
</script>
