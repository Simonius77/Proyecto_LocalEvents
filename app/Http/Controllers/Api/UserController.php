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


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */

    public function index()
    {
        $orderColumn = request('order_column', 'created_at');

        if (!in_array($orderColumn, ['id_usuario', 'nombre', 'created_at'])) {
            $orderColumn = 'created_at';
        }

        $orderDirection = request('order_direction', 'desc');

        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        $users = User::
            when(request('search_id'), function ($query) {
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


    // userswithtasks removed

    // usersfromgroup removed

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return UserResource
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $role = Role::find($request->role_id);

        $user = new User();
        $user->nombre = $data['nombre'];
        $user->apellidos = $data['apellidos'] ?? null;
        $user->telefono = $data['telefono'] ?? null;
        $user->email = $data['email'];
        $user->latitud = $data['latitud'] ?? null;
        $user->longitud = $data['longitud'] ?? null;
        $user->fecha_nacimiento = $data['fecha_nacimiento'] ?? null;
        $user->rol = $data['rol'] ?? 'usuario';
        $user->activo = $data['activo'] ?? true;
        $user->password = Hash::make($data['password']);

        if ($user->save()) {
            if ($role) {
                $user->assignRole($role);
            }

            return new UserResource($user);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return UserResource
     */
    public function show(User $user)
    {
        $user->load('roles');
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return UserResource
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        $role = Role::find($request->role_id);

        $user->nombre = $data['nombre'];
        $user->apellidos = $data['apellidos'] ?? $user->apellidos;
        $user->telefono = $data['telefono'] ?? $user->telefono;
        $user->email = $data['email'];
        $user->latitud = $data['latitud'] ?? $user->latitud;
        $user->longitud = $data['longitud'] ?? $user->longitud;
        $user->fecha_nacimiento = $data['fecha_nacimiento'] ?? $user->fecha_nacimiento;
        $user->rol = $data['rol'] ?? $user->rol;
        $user->activo = $data['activo'] ?? $user->activo;

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        if ($user->save()) {
            if ($role) {
                $user->syncRoles($role);
            }

            return new UserResource($user);
        }
    }



    public function updateimg(Request $request)
    {
        $user = User::find($request->id);

        if ($request->hasFile('picture')) {
            $user->media()->delete();
            $media = $user->addMediaFromRequest('picture')->preservingOriginal()->toMediaCollection('images/users');


        }
        $user = User::with('media')->find($request->id);
        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $this->authorize('user-delete');
        $user->delete();

        return response()->noContent();
    }


}
