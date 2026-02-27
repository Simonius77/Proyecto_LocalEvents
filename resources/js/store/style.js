import { ref } from "vue";
import { defineStore } from "pinia";

/**
 * Explicacion de David Navarro sobre el funcionamisnto de pinia y como implementarlo en el proyecto
 * Para que sirve Pinia
 * 
 * Almacén (Store) para gestionar las preferencias de estilo de la aplicación.
 * Utiliza Pinia y cuenta con persistencia para recordar la configuración del usuario.
 */
export const
    styleStore = defineStore("styleStore", () => {

        // Estado reactivo que indica si el modo oscuro está activo
        let darkTheme = ref(false);

        /**
         * Actualiza el estado del modo oscuro.
         * @param {boolean} is_dark - Verdadero para activar el modo oscuro, falso para el claro.
         */
        function setDarkTheme(is_dark) {
            console.log('styleStore Change', darkTheme.value);
            darkTheme.value = is_dark;
        }

        /**
         * Obtiene el estado actual del modo oscuro.
         * @returns {boolean} - El valor actual de darkTheme.
         */
        function getDarkTheme() {
            console.log('styleStore Get', darkTheme.value);
            return darkTheme.value;
        }

        // Exponemos el estado y las funciones para ser usados en los componentes
        return { darkTheme, setDarkTheme, getDarkTheme };
    }, {
        // La opción persist guarda automáticamente el estado en localStorage
        persist: true
    });

