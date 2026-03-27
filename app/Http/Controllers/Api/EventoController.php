<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventoRequest;
use App\Http\Requests\UpdateEventoRequest;
use App\Http\Resources\EventoResource;
use App\Models\evento;
use Illuminate\Http\Request;

// Uso este controlador para que se puedan manejar los eventos desde fuera
class EventoController extends Controller
{
    // Listo los eventos y permito que se busquen por nombre o id
    public function index()
    {
        // Elijo como ordenar la lista, por defecto por fecha de creacion
        $orderColumn = request('order_column', 'created_at');
        if (!in_array($orderColumn, ['id_evento', 'nombre', 'created_at'])) {
            $orderColumn = 'created_at';
        }
        $orderDirection = request('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        // Busco los eventos segun lo que el usuario escriba en el buscador
        $eventos = Evento::
            when(request('id_organizador'), function ($query) {
                $query->where('id_organizador', request('id_organizador'));
            })
            ->when(request('search_id'), function ($query) {
                $query->where('id_evento', request('search_id'));
            })
            ->when(request('search_global'), function ($query) {
                $query->where(function ($q) {
                    $q->where('id_evento', request('search_global'))
                        ->orWhere('nombre', 'like', '%' . request('search_global') . '%');
                });
            })
            ->orderBy($orderColumn, $orderDirection)
            ->paginate(50);

        return EventoResource::collection($eventos);
    }

    // Guardo un evento nuevo y tambien me encargo de guardar la foto si hay
    public function store(StoreEventoRequest $request)
    {
        // Anoto quien es el que crea el evento usando su id de usuario
        $validatedData = $request->validated();
        $validatedData['id_organizador'] = \Illuminate\Support\Facades\Auth::id();

        // Creo el registro en la base de datos
        $evento = evento::create($validatedData);

        // Si me mandan una imagen, yo la guardo en la carpeta de imagenes
        if ($request->hasFile('imagen')) {
            $evento->addMediaFromRequest('imagen')->toMediaCollection('imagenes_eventos');
        }

        return new EventoResource($evento);
    }

    // Enseño los datos de un solo evento
    public function show(evento $evento)
    {
        return new EventoResource($evento);
    }

    // Pongo al dia los datos de un evento cuando alguien los cambia
    public function update(evento $evento, UpdateEventoRequest $request)
    {
        $currentUserId = \Illuminate\Support\Facades\Auth::id();
        $user = \Illuminate\Support\Facades\Auth::user();
        
        $userRole = $user->rol;
        if ($userRole !== 'admin' && $userRole !== 'administrador' && $currentUserId !== $evento->id_organizador) {
            return response()->json(['message' => 'No puedes editar un evento que no es tuyo'], 403);
        }

        // Actualizo el texto del evento
        $evento->update($request->validated());

        // Si me pasan una foto nueva, yo borro la vieja y pongo la de ahora
        if ($request->hasFile('imagen')) {
            $evento->media()->delete();
            $evento->addMediaFromRequest('imagen')->toMediaCollection('imagenes_eventos');
        }

        return new EventoResource($evento);
    }

    // Borro el evento si el usuario tiene permiso para hacerlo
    public function destroy(evento $evento)
    {
        $currentUserId = \Illuminate\Support\Facades\Auth::id();
        $userRole = \Illuminate\Support\Facades\Auth::user()->rol;

        // Si soy el jefe (admin), yo puedo borrar cualquier cosa
        if ($userRole === 'admin' || $userRole === 'administrador') {
            $evento->delete();
            return response()->noContent();
        }

        // Si no soy el dueno del evento, yo no dejo que se borre
        if ($currentUserId !== $evento->id_organizador) {
            return response()->json(['message' => 'No puedes borrar un evento que no es tuyo'], 403);
        }

        $evento->delete();
        return response()->noContent();
    }

    // Traigo todos los eventos de golpe para la pagina principal
    public function getList()
    {
        return EventoResource::collection(evento::all());
    }
}
