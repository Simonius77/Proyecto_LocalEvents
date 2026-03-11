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
- **Simon**: `simoncatalinafp@gmail.com` / `12345678`

VISTAS
MENU LATERAL PERFIL
la vista del menu lateral de la vista perfil esta en layouts/MainSidebar.vue




