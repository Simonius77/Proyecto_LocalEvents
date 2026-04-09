<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

// Me encargo de gestionar los usuarios del sistema a traves de la API
class UserController extends Controller
{
    /**
     * Saco la lista de usuarios con filtros para que el admin pueda verlos
     */
    public function index()
    {
        // Elijo como ordenar la lista, por defecto uso la fecha de creacion
        $orderColumn = request('order_column', 'created_at');
        if (!in_array($orderColumn, ['id_usuario', 'nombre', 'created_at'])) {
            $orderColumn = 'created_at';
        }

        $orderDirection = request('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        // Busco usuarios segun los filtros que el usuario haya puesto en el buscador
        $users = User::with('roles')
            ->when(request('search_id'), function ($query) {
                $query->where('id_usuario', request('search_id'));
            })
            ->when(request('search_nombre'), function ($query) {
                $query->where('nombre', 'like', '%' . request('search_nombre') . '%');
            })
            ->when(request('search_global'), function ($query) {
                $query->where(function ($q) {
                    $q->where('id_usuario', request('search_global'))
                        ->orWhere('nombre', 'like', '%' . request('search_global') . '%');
                });
            })
            ->orderBy($orderColumn, $orderDirection)
            ->paginate(500);

        return UserResource::collection($users);
    }

    /**
     * Guardo un usuario nuevo en la base de datos
     */
    public function store(StoreUserRequest $request)
    {
        // Saco los datos que han pasado la validacion
        $data = $request->validated();

        // Creo el usuario mapeando los campos del frontend a mis columnas de la BD
        $user = new User();
        $user->nombre = $data['name'] ?? ($data['nombre'] ?? '');
        
        // Junto los apellidos si vienen por separado (panel de control)
        if (isset($data['surname1'])) {
            $user->apellidos = $data['surname1'] . (isset($data['surname2']) ? ' ' . $data['surname2'] : '');
        } else {
            $user->apellidos = $data['apellidos'] ?? null;
        }

        $user->telefono = $data['telefono'] ?? null;
        $user->email = $data['email'];
        $user->latitud = $data['latitud'] ?? null;
        $user->longitud = $data['longitud'] ?? null;
        $user->fecha_nacimiento = $data['fecha_nacimiento'] ?? null;
        $user->rol = $data['rol'] ?? 'usuario';
        $user->activo = $data['activo'] ?? true;
        // Encripto la contraseña para que sea segura
        $user->password = Hash::make($data['password']);

        // Si se guarda bien, le pongo los roles que le toquen
        if ($user->save()) {
            if (isset($data['role_id'])) {
                $user->syncRoles($data['role_id']);
            }
            return new UserResource($user);
        }

        return response()->json(['message' => 'Error al guardar el usuario'], 500);
    }

    /**
     * Enseño los datos de un usuario por su ID
     */
    public function show(User $user)
    {
        // Cargo sus roles para que el front sepa que permisos tiene
        $user->load('roles');
        return new UserResource($user);
    }

    /**
     * Cambio los datos de un usuario que ya existe
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        
        // Actualizo los campos basicos del usuario
        $user->nombre = $data['name'] ?? ($data['nombre'] ?? $user->nombre);
        
        if (isset($data['surname1'])) {
            $user->apellidos = $data['surname1'] . (isset($data['surname2']) ? ' ' . $data['surname2'] : '');
        } else {
            $user->apellidos = $data['apellidos'] ?? $user->apellidos;
        }

        $user->telefono = $data['telefono'] ?? $user->telefono;
        $user->email = $data['email'] ?? $user->email;
        $user->latitud = $data['latitud'] ?? $user->latitud;
        $user->longitud = $data['longitud'] ?? $user->longitud;
        $user->fecha_nacimiento = $data['fecha_nacimiento'] ?? $user->fecha_nacimiento;
        $user->rol = $data['rol'] ?? $user->rol;
        $user->activo = $data['activo'] ?? $user->activo;

        // Si me pasan una contraseña nueva, la cambio
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        // Guardo los cambios y sincronizo los roles si hace falta
        if ($user->save()) {
            if (isset($data['role_id'])) {
                $user->syncRoles($data['role_id']);
            }
            return new UserResource($user);
        }

        return response()->json(['message' => 'Error al actualizar el usuario'], 500);
    }

    /**
     * Actualizo la foto de perfil del usuario
     */
    public function updateimg(Request $request)
    {
        $user = User::find($request->id_usuario);

        // Si me pasan una foto, borro la que habia y pongo esta
        if ($request->hasFile('picture')) {
            $user->media()->delete();
            $user->addMediaFromRequest('picture')->preservingOriginal()->toMediaCollection('images/users');
        }

        // Refresco los datos para que incluya la nueva imagen
        $user = User::with('media')->find($request->id_usuario);
        return new UserResource($user);
    }

    /**
     * Borro a un usuario del sistema para siempre
     */
    public function destroy(User $user)
    {
        // Miro si el usuario tiene permiso para borrar a otros
        $this->authorize('user-delete');
        $user->delete();

        return response()->noContent();
    }
}
