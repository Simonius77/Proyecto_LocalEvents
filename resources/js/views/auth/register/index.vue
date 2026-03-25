<template>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full">
            <!-- Logo y titulo -->
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold">
                    {{ $t('register') }}
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Registrate para comenzar
                </p>
            </div>

            <!-- Formulario de Registro -->
            <Card>
                <template #content>
                    <form @submit.prevent="submitRegister" class="space-y-6">
                        <!-- Campo para el Nombre -->
                        <div class="flex flex-col gap-2">
                            <label for="nombre" class="font-medium">{{ $t('name') }}</label>
                            <InputText
                                id="nombre"
                                v-model="registerForm.nombre"
                                placeholder="Tu nombre"
                                :invalid="!!validationErrors?.nombre"
                            />
                            <!-- Muestra error de validacion si el nombre falla -->
                            <small v-if="validationErrors?.nombre" class="text-red-500">
                                {{ validationErrors.nombre[0] }}
                            </small>
                        </div>

                        <!-- Campo para los Apellidos (Unificados segun la base de datos) -->
                        <div class="flex flex-col gap-2">
                            <label for="apellidos" class="font-medium">{{ $t('surname1') }}</label>
                            <InputText
                                id="apellidos"
                                v-model="registerForm.apellidos"
                                placeholder="Tus apellidos"
                                :invalid="!!validationErrors?.apellidos"
                            />
                            <!-- Muestra error de validacion si los apellidos fallan -->
                            <small v-if="validationErrors?.apellidos" class="text-red-500">
                                {{ validationErrors.apellidos[0] }}
                            </small>
                        </div>

                        <!-- Campo para el Email -->
                        <div class="flex flex-col gap-2">
                            <label for="email" class="font-medium">{{ $t('email') }}</label>
                            <InputText
                                id="email"
                                type="email"
                                v-model="registerForm.email"
                                placeholder="tu@email.com"
                                :invalid="!!validationErrors?.email"
                            />
                            <small v-if="validationErrors?.email" class="text-red-500">
                                {{ validationErrors.email[0] }}
                            </small>
                        </div>

                        <!-- Campos de Contraseña y Sugerencia de Confirmacion -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-2">
                                <label for="password" class="font-medium">{{ $t('password') }}</label>
                                <Password
                                    id="password"
                                    v-model="registerForm.password"
                                    placeholder="••••••••"
                                    toggleMask
                                    :feedback="false"
                                    :invalid="!!validationErrors?.password"
                                    fluid
                                />
                                <small v-if="validationErrors?.password" class="text-red-500">
                                    {{ validationErrors.password[0] }}
                                </small>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label for="password_confirmation" class="font-medium">{{ $t('confirm_password') }}</label>
                                <Password
                                    id="password_confirmation"
                                    v-model="registerForm.password_confirmation"
                                    placeholder="••••••••"
                                    toggleMask
                                    :feedback="false"
                                    :invalid="!!validationErrors?.password_confirmation"
                                    fluid
                                />
                                <small v-if="validationErrors?.password_confirmation" class="text-red-500">
                                    {{ validationErrors.password_confirmation[0] }}
                                </small>
                            </div>
                        </div>

                        <!-- Aviso para explicar por que se pide la ubicacion -->
                        <div class="flex items-center gap-2 text-xs text-gray-500 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <i class="pi pi-info-circle text-blue-500"></i>
                            <p>Utilizamos tu ubicacion para recomendarte los eventos mas cercanos a ti.</p>
                        </div>

                        <!-- Boton de Registro con estado de carga -->
                        <Button
                            type="submit"
                            :label="$t('register')"
                            :loading="processing"
                            :disabled="processing"
                            class="w-full"
                            size="large"
                        />

                        <!-- Enlace para usuarios que ya tienen cuenta -->
                        <div class="text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                ¿Ya tienes una cuenta?
                                <router-link
                                    :to="{ name: 'public.login' }"
                                    class="font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                                >
                                    Inicia sesion aqui
                                </router-link>
                            </p>
                        </div>
                    </form>
                </template>
            </Card>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
// Importamos la logica de autenticacion desde el composable
import useAuth from '@/composables/auth';

// Extraemos las variables y funciones necesarias para el formulario
const { registerForm, validationErrors, processing, submitRegister, getLocation } = useAuth();

// Pide la ubicacion directamente al cargar la vista
onMounted(() => {
    getLocation();
});
</script>
