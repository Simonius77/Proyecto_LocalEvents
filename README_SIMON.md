Atencion!!!
OJO con las variables, yo programo en castellano antiguo.
 http://127.0.0.1:5173/register



Anotaciones para el desarrollador Simon
requerimientos funcionales 
relacion entre user y eventos y los roles
para el viernes 13 requerimiento funcionales y base de datos
el crud de eventos y categorias

SERVIDOR

Crear servidor
php artisan serve

Arrancar servidor
npm run dev

Crear base de datos
php artisan migrate:fresh --seed
Nota con el --seed se cargan los seeders creados por defecto

RAMA MIGRATIONS
Migrations modificados para que coincidan con los modelos
users php artisan make:model user -c -m 
events php artisan make:model evento -c -m 
categories php artisan make:model categoria -c -m 
reservas php artisan make:model reserva -c -m 
pagos php artisan make:model pago -c -m 

RAMA MODELS
Models modificados para que coincidan con los migrations
 users modificado
 categorias modificado
 evento modificado
 pagos modificado
 reservas modificado

 Los siguientes usuarios son creaados por defecto:
- **Admin**: `admin@demo.com` / `12345678`
- **Usuario**: `user@demo.com` / `12345678`

API y archivos relacionados
-La API esta en routes/api.php
-Los controladores estan en app/Http/Controllers/Api
-Los modelos estan en app/Models
-Los seeders estan en database/seeders
-Los migrations estan en database/migrations
-Las rutas estan en routes/api.php
-Los middleware estan en app/Http/Middleware
-Los requests estan en app/Http/Requests
-Los policies estan en app/Policies
-Los listeners estan en app/Listeners
-Los eventos estan en app/Events
-Los jobs estan en app/Jobs
-Los observers estan en app/Observers
-Los policies estan en app/Policies
-Los listeners estan en app/Listeners
-Los eventos estan en app/Events
-Los jobs estan en app/Jobs
-Los observers estan en app/Observers

VISTAS

--Estructura de las vistas con VUE--
se empiza con la etiqueta <template> y dentro de ella se coloca el html y se cierra con </template> al final se crea el script con <script setup> y se cierra con </script> donde va todo el codigo javascript y se importa todo lo necesario.
 
MENU LATERAL PERFIL
la vista del menu lateral de la vista perfil esta en layouts/MainSidebar.vue

IMAGENES DE EVENTOS
Las carpetas con los números 1, 2 y 3 que ves dentro de la carpeta storage/app/public (que normalmente están enlazadas a public/storage) son generadas automáticamente por una librería llamada Spatie MediaLibrary.

Aquí te explico su función de forma simple:

Organización: Cada vez que subes una imagen para un evento o un usuario, la librería crea una carpeta con el numero de ID del archivo en la base de datos.
Evitar conflictos: Al poner cada imagen en su propia carpeta numerada, se evita que dos archivos con el mismo nombre se borren el uno al otro.
Control del sistema: Esas imágenes (como eventomuestra.webp) son las que el sistema utiliza para mostrar las fotos de los eventos que ya están creados en tu base de datos.




