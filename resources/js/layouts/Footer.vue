<template>
    <footer class="bg-surface-50 dark:bg-surface-900 border-t border-surface-200 dark:border-surface-800 py-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex flex-col items-center md:items-start">
                    <router-link to="/" class="flex items-center gap-2 mb-2">
                        <img src="/images/logo.png" alt="Logo" class="h-8 w-auto" onerror="this.style.display='none'" />
                        <span class="text-xl font-bold text-primary">LocalEvents</span>
                    </router-link>
                    <p class="text-surface-500 text-sm">copyright 2026</p>
                </div>

                <div class="flex flex-col items-center md:items-end">
                    <h3 class="font-bold mb-3 text-surface-700 dark:text-surface-300">policas del sitio web</h3>
                    <ul class="flex flex-wrap justify-center gap-4 list-none p-0 m-0">
                        <li>
                            <router-link to="/legal/aviso-legal" class="text-surface-600 dark:text-surface-400 hover:text-primary transition-colors text-sm">
                                aviso legal
                            </router-link>
                        </li>
                        <li>
                            <router-link to="/legal/privacidad" class="text-surface-600 dark:text-surface-400 hover:text-primary transition-colors text-sm">
                                politcas de privacidad
                            </router-link>
                        </li>
                        <li>
                            <router-link to="/legal/cookies" class="text-surface-600 dark:text-surface-400 hover:text-primary transition-colors text-sm">
                                politicas de cookies
                            </router-link>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup>
// defino este componente para que sea el pie de pagina de todo el sitio
</script>
