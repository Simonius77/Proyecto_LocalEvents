# Documentación del Proyecto - Simon

## Atencion

OJO con las variables, yo programo en castellano antiguo.
<http://127.0.0.1:5173/register>

## NOTA

OJO con la instalacion de Pinia, te desconfigura el proyecto, no lo instales. no instalar con el proyecto en marcha. rompe el proyecto por problemas de dependencias. Si te pasan cosas raras revisa las dependencias en el archivo package.json y package-lock.json. y borra la carpeta node_modules y vuelve a instalar las dependencias con npm install.

## Anotaciones para el desarrollador Simon

- requerimientos funcionales
- relacion entre user y eventos y los roles
- para el viernes 13 requerimiento funcionales y base de datos
- el crud de eventos y categorias

## SERVIDOR

### Crear servidor

```bash
php artisan serve
```

### Arrancar servidor

```bash
npm run dev
```

## CREAR BASE DE DATOS

```bash
php artisan migrate:fresh --seed
```

Nota con el --seed se cargan los seeders creados por defecto

## RELACIONES N-M

Relaciones Muchos a Muchos (N:M)
Estas relaciones utilizan una tabla intermedia (pivote) para conectar dos entidades. Actualmente tienes 4:

1. **Usuarios ↔ Roles**:
   Permite que un usuario tenga múltiples roles (ej: Admin y Organizador) y que un rol sea compartido por miles de usuarios.

2. **Roles ↔ Permisos**:
   Define qué capacidades tiene cada rol.
   Un rol agrupa muchos permisos, y un permiso (ej: crear-evento) puede estar presente en varios roles.

3. **Usuarios ↔ Permisos**:
   Permite asignar permisos "especiales" directamente a un usuario sin necesidad de crear un nuevo rol.

4. **Usuarios ↔ Eventos (reservas)**:
   Es una relación N:M con modelo intermedio.
   Un Usuario puede reservar muchos Eventos.
   Un Evento puede ser reservado por muchos Usuarios.
   La tabla reservas actúa como pivote pero guarda información adicional (cantidad de entradas, precio total gastado, estado del pago).

## RAMA MIGRATIONS

Migrations modificados para que coincidan con los modelos:

- users: `php artisan make:model user -c -m`
- events: `php artisan make:model evento -c -m`
- categories: `php artisan make:model categoria -c -m`
- reservas: `php artisan make:model reserva -c -m`
- pagos: `php artisan make:model pago -c -m`

## RAMA MODELS

Models modificados para que coincidan con los migrations:

- users modificado
- categorias modificado
- evento modificado
- pagos modificado
- reservas modificado

## RAMA SIMON

Mi rama de trabajo personal

## Usuarios Creados por Defecto

Los siguientes usuarios son creaados por defecto:

- **Admin**: `admin@demo.com` / `12345678`
- **Usuario**: `user@demo.com` / `12345678`
- **Organizador**: `organizador@demo.com` / `12345678`
- **Admin**: `simoncatalinafp@ibf.cat` / `12345678`
- **Organizador**: `drianny@demo.com` / `12345678`

## API y archivos relacionados

- La API esta en `routes/api.php`
- Los controladores estan en `app/Http/Controllers/Api`
- Los modelos estan en `app/Models`
- Los seeders estan en `database/seeders`
- Los migrations estan en `database/migrations`
- Las rutas estan en `routes/api.php`
- Los middleware estan en `app/Http/Middleware`
- Los requests estan en `app/Http/Requests`
- Los policies estan en `app/Policies`
- Los listeners estan en `app/Listeners`
- Los eventos estan en `app/Events`
- Los jobs estan en `app/Jobs`
- Los observers estan en `app/Observers`

## VISTAS

### Estructura de las vistas con VUE

Se empieza con la etiqueta `template` y dentro de ella se coloca el html y se cierra con `/template`. Al final se crea el script con `script setup` y se cierra con `/script` donde va todo el codigo javascript y se importa todo lo necesario.

## MENU LATERAL PERFIL

La vista del menu lateral de la vista perfil esta en `layouts/MainSidebar.vue`

## IMAGENES DE EVENTOS

Las carpetas con los números 1, 2 y 3 que ves dentro de la carpeta `storage/app/public` (que normalmente están enlazadas a `public/storage`) son generadas automáticamente por una librería llamada Spatie MediaLibrary.

Explicacion de su función de forma simple:

- **Organización**: Cada vez que subes una imagen para un evento o un usuario, la librería crea una carpeta con el numero de ID del archivo en la base de datos.
- **Evitar conflictos**: Al poner cada imagen en su propia carpeta numerada, se evita que dos archivos con el mismo nombre se borren el uno al otro.
- **Control del sistema**: Esas imágenes (como eventomuestra.webp) son las que el sistema utiliza para mostrar las fotos de los eventos que ya están creados en tu base de datos.

## GEOLOCALIZACION

Para implementar la geolocalizacion de los usuarios he creado la rama de geolocalizacion.
Los usuarios al registrarse se les pedira permiso para acceder a su ubicacion y se guardara en la base de datos.

## CATEGORIAS

Conciertos
Teatro
Gastronomia
Exposiciones
Deportes
