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


// Controlador para gestionar las operaciones de los usuarios a traves de la API
class UserController extends Controller
{
    /**
     * Muestra una lista paginada de usuarios con filtros de busqueda y ordenacion.
     *
     * @return AnonymousResourceCollection
     */

    public function index()
    {
        // Define la columna por la que se ordenara, por defecto 'created_at'
        $orderColumn = request('order_column', 'created_at');

        // Valida que la columna de ordenacion sea permitida
        if (!in_array($orderColumn, ['id_usuario', 'nombre', 'created_at'])) {
            $orderColumn = 'created_at';
        }

        // Define la direccion del orden (ascendente o descendente)
        $orderDirection = request('order_direction', 'desc');

        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        // Realiza la consulta con filtros condicionales (busqueda por ID, nombre o global)
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

        // Retorna la coleccion de usuarios transformada por el recurso UserResource
        return UserResource::collection($users);
    }


    // userswithtasks removed

    // usersfromgroup removed

    /**
     * Almacena un nuevo usuario en la base de datos.
     *
     * @param  StoreUserRequest  $request
     * @return UserResource|\Illuminate\Http\JsonResponse
     */

    public function store(StoreUserRequest $request)
    {
        // Obtiene los datos validados del request
        $data = $request->validated();

        // Creamos el usuario mapeando los campos del frontend a la base de datos
        $user = new User();
        $user->nombre = $data['name'] ?? ($data['nombre'] ?? '');
        
        // Juntamos los apellidos si vienen por separado
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
        // Encripta la contraseña
        $user->password = Hash::make($data['password']);

        // Guarda el usuario y asignamos los roles si hay
        if ($user->save()) {
            if (isset($data['role_id'])) {
                // Usamos syncRoles para asignar el grupo de IDs
                $user->syncRoles($data['role_id']);
            }

            return new UserResource($user);
        }

        return response()->json(['message' => 'Error al guardar el usuario'], 500);
    }

    /**
     * Muestra los detalles de un usuario especifico.
     *
     * @param  User $user
     * @return UserResource
     */
    public function show(User $user)
    {
        // Carga la relacion de roles y devuelve el recurso
        $user->load('roles');
        return new UserResource($user);
    }

    /**
     * Actualiza los datos de un usuario existente.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return UserResource|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        // Obtiene los datos validados
        $data = $request->validated();
        
        // Mapeamos los nombres de los campos de ingles (frontend) a español (base de datos)
        // Si no vienen en ingles, usamos los que vengan en español
        $user->nombre = $data['name'] ?? ($data['nombre'] ?? $user->nombre);
        
        // Juntamos los apellidos si vienen por separado desde el panel admin
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

        // Actualiza la contraseña solo si se proporciona una nueva
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        // Guarda los cambios
        if ($user->save()) {
            // Sincronizamos los roles si se han enviado IDs de roles
            if (isset($data['role_id'])) {
                // syncRoles acepta un array de IDs, perfecto para lo que envia el MultiSelect
                $user->syncRoles($data['role_id']);
            }

            return new UserResource($user);
        }

        return response()->json(['message' => 'Error al actualizar el usuario'], 500);
    }



    /**
     * Actualiza la imagen de perfil del usuario utilizando MediaLibrary.
     *
     * @param Request $request
     * @return UserResource
     */
    public function updateimg(Request $request)
    {
        $user = User::find($request->id_usuario);

        // Si se recibe un archivo, elimina el anterior y agrega el nuevo
        if ($request->hasFile('picture')) {
            $user->media()->delete();
            $user->addMediaFromRequest('picture')->preservingOriginal()->toMediaCollection('images/users');


        }

        // Recarga el usuario con su media y lo retorna
        $user = User::with('media')->find($request->id_usuario);
        return new UserResource($user);
    }

    /**
     * Elimina el usuario especificado de la base de datos.
     *
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        // Verifica que el usuario tenga permiso para borrar
        $this->authorize('user-delete');
        $user->delete();

        // Retorna una respuesta vacia con codigo 204
        return response()->noContent();
    }


}
