<?php
//Atencion!!!
//Ojo con las variables, yo programo en castellano antiguo by Simon.

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Aqui se configura el intercambio de recursos de origen cruzado o "CORS".
    | Esto determina que operaciones pueden ejecutarse en el navegador desde otro dominio.
    |
    */

    // Rutas que tendran habilitado CORS
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // Metodos HTTP permitidos (GET, POST, etc.). '*' permite todos.
    'allowed_methods' => ['*'],

    // Origenes permitidos. Hemos añadido localhost:5173 para que Vite (frontend) 
    // pueda comunicarse con Laravel (backend) sin que el navegador lo bloquee.
    'allowed_origins' => ['http://localhost:5173', 'http://127.0.0.1:5173'],

    'allowed_origins_patterns' => [],

    // Cabeceras permitidas en las peticiones.
    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    // Tiempo de vida de la configuracion en cache (en segundos).
    'max_age' => 0,

    // IMPORTANTE: Debe estar en 'true' para permitir el envio de cookies y sesiones 
    // entre el frontend y el backend cuando estan en puertos distintos.
    'supports_credentials' => true,

];
