import axios from 'axios';
import { ref } from "vue";
import { defineStore } from "pinia";

export const authStore = defineStore("authStore", () => {

    let user = ref({ name: '', nombre: '', apellidos: '', roles: [], rol: '' });
    let authenticated = ref(false);
    let token = ref(null);

    function setToken(newToken) {
        token.value = newToken;
    }

    async function login(data) {
        // ...Existing code or update if needed
    }
    async function getUser(data) {

        await axios.get('/api/user').then(response => {
            user.value = response.data.data
            authenticated.value = true
            console.log('getUser AT: true ');
            console.log(user.value);
        }).catch(error => {
            console.log('getUser: error ');
            user.value = {}
            authenticated.value = false
        })
    }

    async function getUserSignIn(data) {
        await axios.get('/api/user/signin').then(response => {
            user.value = response.data.data
            authenticated.value = true
        }).catch(error => {
            console.log('getUserSignIn: error ');
            user.value = { name: '', nombre: '', apellidos: '', roles: [], rol: '' }
            authenticated.value = false
        })
    }
    function logout() {
        user.value = { name: '', nombre: '', apellidos: '', roles: [], rol: '' }
        authenticated.value = false
        token.value = null
    }

    function is(roleName) {
        return user.value?.roles?.some(role => (role.nombre === roleName || role.name === roleName)) || false;
    }

    return { user, authenticated, token, setToken, login, is, getUser, getUserSignIn, logout };
}, {
    persist: {
        storage: sessionStorage,
    }
});
