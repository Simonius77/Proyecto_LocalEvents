<template>
    <div class="register-page min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full z-10">
            <!-- Logo y titulo -->
            <div class="text-center mb-8">
                <div class="logo-container mb-4">
                     <router-link to="/">
                        <img src="/images/Logo_vector.svg" alt="LocalEvents Logo" class="h-24 mx-auto drop-shadow-lg" />
                     </router-link>
                </div>
                <h2 class="text-4xl font-black text-white drop-shadow-md">
                    {{ $t('register') }}
                </h2>
                <p class="mt-3 text-lg font-medium text-white/90">
                    {{ $t('register_subtitle') || 'Únete a LocalEvents y no te pierdas nada' }}
                </p>
            </div>

            <!-- Formulario de Registro -->
            <Card class="register-card border-none shadow-2xl">
                <template #content>
                    <form @submit.prevent="submitRegister" class="space-y-6 py-2">
                        <!-- Campo para el Nombre -->
                        <div class="flex flex-col gap-2">
                            <label for="nombre" class="font-semibold text-surface-700 dark:text-surface-200">{{ $t('name') }}</label>
                            <InputText
                                id="nombre"
                                v-model="registerForm.nombre"
                                :placeholder="$t('name_placeholder') || 'Tu nombre'"
                                class="p-inputtext-lg"
                                :invalid="!!validationErrors?.nombre"
                            />
                            <small v-if="validationErrors?.nombre" class="text-red-500 font-medium">
                                {{ validationErrors.nombre[0] }}
                            </small>
                        </div>

                        <!-- Campo para los Apellidos -->
                        <div class="flex flex-col gap-2">
                            <label for="apellidos" class="font-semibold text-surface-700 dark:text-surface-200">{{ $t('surname1') }}</label>
                            <InputText
                                id="apellidos"
                                v-model="registerForm.apellidos"
                                :placeholder="$t('surname_placeholder') || 'Tus apellidos'"
                                class="p-inputtext-lg"
                                :invalid="!!validationErrors?.apellidos"
                            />
                            <small v-if="validationErrors?.apellidos" class="text-red-500 font-medium">
                                {{ validationErrors.apellidos[0] }}
                            </small>
                        </div>

                        <!-- Campo para el Email -->
                        <div class="flex flex-col gap-2">
                            <label for="email" class="font-semibold text-surface-700 dark:text-surface-200">{{ $t('email') }}</label>
                            <InputText
                                id="email"
                                type="email"
                                v-model="registerForm.email"
                                placeholder="tu@email.com"
                                class="p-inputtext-lg"
                                :invalid="!!validationErrors?.email"
                            />
                            <small v-if="validationErrors?.email" class="text-red-500 font-medium">
                                {{ validationErrors.email[0] }}
                            </small>
                        </div>

                        <!-- Campos de Contraseña y Sugerencia de Confirmacion -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-2">
                                <label for="password" class="font-semibold text-surface-700 dark:text-surface-200">{{ $t('password') }}</label>
                                <Password
                                    id="password"
                                    v-model="registerForm.password"
                                    placeholder="••••••••"
                                    toggleMask
                                    :feedback="false"
                                    size="large"
                                    :invalid="!!validationErrors?.password"
                                    fluid
                                />
                                <small v-if="validationErrors?.password" class="text-red-500 font-medium">
                                    {{ validationErrors.password[0] }}
                                </small>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label for="password_confirmation" class="font-semibold text-surface-700 dark:text-surface-200">{{ $t('confirm_password') }}</label>
                                <Password
                                    id="password_confirmation"
                                    v-model="registerForm.password_confirmation"
                                    placeholder="••••••••"
                                    toggleMask
                                    :feedback="false"
                                    size="large"
                                    :invalid="!!validationErrors?.password_confirmation"
                                    fluid
                                />
                                <small v-if="validationErrors?.password_confirmation" class="text-red-500 font-medium">
                                    {{ validationErrors.password_confirmation[0] }}
                                </small>
                            </div>
                        </div>

                        <!-- Aviso para explicar por que se pide la ubicacion -->
                        <div class="flex items-center gap-3 text-sm text-surface-600 dark:text-surface-400 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800">
                            <i class="pi pi-map-marker text-blue-500 text-xl"></i>
                            <p>{{ $t('location_notice') || 'Utilizamos tu ubicación para recomendarte los eventos más cercanos a ti automáticamente.' }}</p>
                        </div>

                        <!-- Boton de Registro con estado de carga -->
                        <Button
                            type="submit"
                            :label="$t('register')"
                            :loading="processing"
                            :disabled="processing"
                            class="w-full py-4 text-lg font-bold"
                            severity="primary"
                            raised
                        />

                        <!-- Enlace para usuarios que ya tienen cuenta -->
                        <div class="text-center pt-2">
                            <p class="text-sm font-medium text-surface-600 dark:text-surface-400">
                                {{ $t('already_have_account') || '¿Ya tienes una cuenta?' }}
                                <router-link
                                    :to="{ name: 'public.login' }"
                                    class="font-bold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                                >
                                    {{ $t('login_here') || 'Inicia sesión aquí' }}
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
import useAuth from '@/composables/auth';

const { registerForm, validationErrors, processing, submitRegister, getLocation } = useAuth();

onMounted(() => {
    getLocation();
});
</script>

<style scoped>
.register-page {
    background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('/images/Foto_banner.png');
    background-size: cover;
    background-position: center;
    position: relative;
    overflow: hidden;
}

.register-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(12px);
    border-radius: 24px;
}

:deep(.app-dark) .register-card {
    background: rgba(30, 41, 59, 0.9);
}

:deep(.p-inputtext) {
    border-radius: 12px;
}

:deep(.p-password) {
    border-radius: 12px;
}

:deep(.p-button) {
    border-radius: 12px;
}
</style>
