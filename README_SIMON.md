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

 POSTMAN
 Urls para postman
 EVENTOS:
Listar todos: GET http://localhost:8000/api/eventos
Crear uno nuevo: POST http://localhost:8000/api/eventos
Ver uno específico: GET http://localhost:8000/api/eventos/1
Actualizar uno: PUT http://localhost:8000/api/eventos/1
Eliminar uno: DELETE http://localhost:8000/api/eventos/1
RESERVAS:
Listar todos: GET http://localhost:8000/api/reservas
Crear una nueva: POST http://localhost:8000/api/reservas
Ver una específica: GET http://localhost:8000/api/reservas/1
Actualizar una: PUT http://localhost:8000/api/reservas/1
Eliminar una: DELETE http://localhost:8000/api/reservas/1
PAGOS:
Listar todos: GET http://localhost:8000/api/pagos
Crear uno nuevo: POST http://localhost:8000/api/pagos
Ver uno específico: GET http://localhost:8000/api/pagos/1
Actualizar uno: PUT http://localhost:8000/api/pagos/1
Eliminar uno: DELETE http://localhost:8000/api/pagos/1





