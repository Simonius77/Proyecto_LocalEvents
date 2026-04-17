<template>
    <div class="login-page min-h-screen flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full z-10">
            <!-- Logo y título -->
            <div class="text-center mb-8">
                <div class="logo-container mb-4">
                     <router-link to="/">
                        <img src="/images/Logo_vector.svg" alt="LocalEvents Logo" class="h-24 mx-auto drop-shadow-lg" />
                     </router-link>
                </div>
                <h2 class="text-4xl font-black text-white drop-shadow-md">
                    {{ $t('login') }}
                </h2>
                <p class="mt-3 text-lg font-medium text-white/90">
                    {{ $t('welcome_back_subtitle') || 'Descubre lo mejor de tu ciudad' }}
                </p>
            </div>

            <!-- Formulario -->
            <Card class="login-card border-none shadow-2xl">
                <template #content>
                    <form @submit.prevent="submitLogin" class="space-y-6 py-2">
                        <!-- Email -->
                        <div class="flex flex-col gap-2">
                            <label for="email" class="font-semibold text-surface-700 dark:text-surface-200">{{ $t('email') }}</label>
                            <InputText
                                id="email"
                                type="email"
                                v-model="loginForm.email"
                                placeholder="tu@email.com"
                                class="p-inputtext-lg"
                                :class="{ 'p-invalid': validationErrors?.email }"
                            />
                            <small v-if="validationErrors?.email" class="text-red-500 font-medium">
                                <div v-for="message in validationErrors.email" :key="message">
                                    {{ message }}
                                </div>
                            </small>
                        </div>

                        <!-- Password -->
                        <div class="flex flex-col gap-2">
                            <label for="password" class="font-semibold text-surface-700 dark:text-surface-200">{{ $t('password') }}</label>
                            <Password
                                id="password"
                                v-model="loginForm.password"
                                placeholder="••••••••"
                                :toggleMask="true"
                                :feedback="false"
                                size="large"
                                inputClass="w-full"
                                :class="{ 'p-invalid': validationErrors?.password }"
                                fluid
                            />
                            <small v-if="validationErrors?.password" class="text-red-500 font-medium">
                                <div v-for="message in validationErrors.password" :key="message">
                                    {{ message }}
                                </div>
                            </small>
                        </div>

                        <!-- Remember me y Forgot password -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <Checkbox
                                    v-model="loginForm.remember"
                                    inputId="remember"
                                    binary
                                />
                                <label for="remember" class="text-sm font-medium cursor-pointer text-surface-600 dark:text-surface-400">
                                    {{ $t('remember_me') }}
                                </label>
                            </div>
                            <router-link
                                :to="{ name: 'auth.forgot-password' }"
                                class="text-sm font-bold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                            >
                                {{ $t('forgot_password') }}
                            </router-link>
                        </div>

                        <!-- Submit Button -->
                        <Button
                            type="submit"
                            :label="$t('login')"
                            :loading="processing"
                            :disabled="processing"
                            class="w-full py-4 text-lg font-bold"
                            severity="primary"
                            raised
                        />

                        <!-- Register link -->
                        <div class="text-center pt-2">
                            <p class="text-sm font-medium text-surface-600 dark:text-surface-400">
                                {{ $t('no_account_link') || '¿No tienes una cuenta aún?' }}
                                <router-link
                                    :to="{ name: 'public.register' }"
                                    class="font-bold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                                >
                                    {{ $t('register_free') || 'Regístrate gratis' }}
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
import useAuth from '@/composables/auth';

const { loginForm, validationErrors, processing, submitLogin } = useAuth();
</script>

<style scoped>
.login-page {
    background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('/images/Foto_banner.png');
    background-size: cover;
    background-position: center;
    position: relative;
    overflow: hidden;
}

.login-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(12px);
    border-radius: 24px;
}

:deep(.app-dark) .login-card {
    background: rgba(30, 41, 59, 0.9);
}

:deep(.p-inputtext) {
    border-radius: 12px;
}

:deep(.p-button) {
    border-radius: 12px;
}
</style>
