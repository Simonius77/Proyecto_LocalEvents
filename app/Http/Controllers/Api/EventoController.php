<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventoRequest;
use App\Http\Requests\UpdateEventoRequest;
use App\Http\Resources\EventoResource;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Mi funcion es gestionar todas las peticiones sobre eventos que llegan a la API
class EventoController extends Controller
{
    /**
     * Devuelvo la lista de eventos filtrada y paginada para que se vea en el sistema
     */
    public function index()
    {
        // Elijo como organizar los eventos segun lo que me pidan, por defecto por fecha
        $orderColumn = request('order_column', 'created_at');
        if (!in_array($orderColumn, ['id_evento', 'nombre', 'created_at'])) {
            $orderColumn = 'created_at';
        }

        $orderDirection = request('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        // Busco los eventos aplicando los filtros que el usuario haya escrito
        $eventos = Evento::when(request('id_organizador'), function ($query) {
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

    /**
     * Guardo un evento nuevo en la base de datos y le asigno el organizador
     */
    public function store(StoreEventoRequest $request)
    {
        // Saco los datos limpios y le pongo quien es el jefe (el usuario logueado)
        $validatedData = $request->validated();
        $validatedData['id_organizador'] = Auth::id();

        // Creo el evento con los datos que me han pasado
        $evento = Evento::create($validatedData);

        // Si me han mandado una foto, yo la guardo donde toca
        if ($request->hasFile('imagen')) {
            $evento->addMediaFromRequest('imagen')->toMediaCollection('imagenes_eventos');
        }

        return new EventoResource($evento);
    }

    /**
     * Enseño los datos de un evento concreto
     */
    public function show(Evento $evento)
    {
        return new EventoResource($evento);
    }

    /**
     * Actualizo los datos de un evento si el usuario tiene permiso para hacerlo
     */
    public function update(Evento $evento, UpdateEventoRequest $request)
    {
        $user = Auth::user();
        
        // Miro si es el admin o el dueño del evento para dejarle editar
        $isAuthorized = in_array($user->rol, ['admin', 'administrador']) || $user->id_usuario === $evento->id_organizador;
        
        if (!$isAuthorized) {
            return response()->json(['message' => 'No tienes permiso para editar este evento'], 403);
        }

        // Guardo los cambios en el texto
        $evento->update($request->validated());

        // Si hay una foto nueva, borro la vieja y pongo la de ahora
        if ($request->hasFile('imagen')) {
            $evento->media()->delete();
            $evento->addMediaFromRequest('imagen')->toMediaCollection('imagenes_eventos');
        }

        return new EventoResource($evento);
    }

    /**
     * Borro un evento del sistema si se tiene el permiso adecuado
     */
    public function destroy(Evento $evento)
    {
        $user = Auth::user();

        // Solo el admin o el que creo el evento pueden borrarlo
        $isAuthorized = in_array($user->rol, ['admin', 'administrador']) || $user->id_usuario === $evento->id_organizador;

        if (!$isAuthorized) {
            return response()->json(['message' => 'No tienes permiso para borrar este evento'], 403);
        }

        $evento->delete();
        return response()->noContent();
    }

    /**
     * Traigo todos los eventos sin filtros para usarlos rapido en otras partes
     */
    public function getList()
    {
        return EventoResource::collection(Evento::all());
    }
}
