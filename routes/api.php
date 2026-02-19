<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PermissionController;

use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Rutas protegidas por autenticacion
Route::group(['middleware' => 'auth:sanctum'], function() {
    //Explicacion.
//El Route apiResource de Laravel crea automáticamente las rutas para index, show, store, update y destroy,
// por lo que no es necesario definir cada una de ellas manualmente. 
//Si necesitas alguna ruta adicional, como la de updateimg, 
//puedes definirla por separado como se muestra a continuación:
    Route::apiResource('users', UserController::class);
    Route::post('users/updateimg', [UserController::class,'updateimg']);


    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('roles', RoleController::class);

    Route::get('role-list', [RoleController::class, 'getList']);
    Route::get('role-permissions/{id}', [PermissionController::class, 'getRolePermissions']);
    Route::put('/role-permissions', [PermissionController::class, 'updateRolePermissions']);
    Route::apiResource('permissions', PermissionController::class);
    
    Route::get('/user', [ProfileController::class, 'user']);
    Route::get('/user/signin', [ProfileController::class, 'user']);
    Route::put('/user', [ProfileController::class, 'update']);
   
    // Ruta para obtener las habilidades del usuario autenticado
    Route::get('abilities', function(Request $request) {
        return $request->user()->roles()->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('name')
            ->unique()
            ->values()
            ->toArray();
    });
});
// Rutas públicas
//que hace el Route apiResource de Laravel?
//El Route apiResource de Laravel es una función que genera automáticamente las rutas para un controlador de recursos, siguiendo las convenciones RESTful. Esto significa que crea rutas para las acciones comunes como index, show, store, update y destroy, lo que facilita la creación de APIs de manera rápida y eficiente. Por ejemplo, al usar Route::apiResource('users', UserController::class), se generarán rutas para listar usuarios, mostrar un usuario específico, crear un nuevo usuario, actualizar un usuario existente y eliminar un usuario.
// En este caso, se están definiendo rutas para los recursos de usuarios, categorías, roles y permisos, así como rutas para el inicio de sesión, registro y cierre de sesión. Además, se incluye una ruta para obtener las habilidades del usuario autenticado.
Route::apiResource('/posts', PostController::class);
Route::apiResource('category-list', CategoryController::class);
Route::post('/login', [ProfileController::class, 'login']);
//esta es la ruta de registro.
Route::post('/register', [ProfileController::class, 'register']);
//este es el logout, no se si es necesario, pero lo dejo por si acaso
Route::post('/logout', [ProfileController::class, 'logout']);
/*
Route::get('category-list', [CategoryController::class, 'getList']);

Route::get('/posts/{post}', [PostController::class, 'show']);

Route::delete('/posts/{post}', [PostController::class, 'destroy']);
*/

