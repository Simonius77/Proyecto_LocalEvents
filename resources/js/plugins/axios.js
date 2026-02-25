import _ from 'lodash';
window._ = _;

/**
 * Cargamos la libreria Axios para realizar peticiones HTTP a nuestro servidor Laravel.
 * Esta libreria maneja automaticamente el token CSRF mediante la cookie "XSRF-TOKEN".
 */

import axios from 'axios';
window.axios = axios;

// URL base de la API. Forzamos el uso de 127.0.0.1 para evitar errores de red en Windows
// derivados de la resolucion de 'localhost' (que a veces intenta usar IPv6 ::1).
window.axios.defaults.baseURL = 'http://127.0.0.1:8000';

// Identifica las peticiones como AJAX y pide respuesta en JSON.
// Esto es CRITICO para que el servidor no intente redireccionarte y falle la conexion.
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';

// Permite el envio de credenciales (cookies de sesion) en cada peticion.
window.axios.defaults.withCredentials = true

// Interceptor de respuesta para manejar errores comunes de autenticacion.
window.axios.interceptors.response.use(
    response => response,
    error => {
        // Si el servidor devuelve 401 (No autorizado) o 419 (Sesion expirada),
        // redirigimos automaticamente al usuario a la pantalla de login.
        if (error.response?.status === 401 || error.response?.status === 403 || error.response?.status === 419) {
            if (location.pathname !== '/login') {
                location.assign('/login')
            }
        }

        return Promise.reject(error)
    }
)
