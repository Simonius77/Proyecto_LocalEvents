<script setup>
    import { onMounted } from 'vue'
    import useAuth from './composables/auth';

    onMounted(() => {
    useAuth().getUserSignIn()
})
</script>

<template>
    <Toast position="bottom-right" />
    <ConfirmDialog />
    <router-view />
</template>
