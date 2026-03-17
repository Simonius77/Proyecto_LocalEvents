<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Http\Resources\EventoResource;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function index()
    {
        $orderColumn = request('order_column', 'fecha_inicio');

        if (!in_array($orderColumn, ['id_evento', 'nombre', 'fecha_inicio', 'precio'])) {
            $orderColumn = 'fecha_inicio';
        }

        $orderDirection = request('order_direction', 'desc');

        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        $eventos = Evento::with(['categoria', 'organizador'])
            ->when(request('search_id'), function ($query) {
                $query->where('id_evento', request('search_id'));
            })
            ->when(request('search_nombre'), function ($query) {
                $query->where('nombre', 'like', '%' . request('search_nombre') . '%');
            })
            ->when(request('search_categoria'), function ($query) {
                $query->where('id_categoria', request('search_categoria'));
            })
            ->when(request('search_global'), function ($query) {
                $query->where(function ($q) {
                    $q->where('id_evento', request('search_global'))
                      ->orWhere('nombre', 'like', '%' . request('search_global') . '%');
                });
            })
            ->orderBy($orderColumn, $orderDirection)
            ->paginate(20);

        return EventoResource::collection($eventos);
    }

    public function store(\App\Http\Requests\StoreEventoRequest $request)
    {
        // Agregamos el id_organizador a los datos validados usando el ID del usuario logueado
        $validatedData = $request->validated();
        $validatedData['id_organizador'] = \Illuminate\Support\Facades\Auth::id();

        // Crea el evento con los datos validados
        $evento = Evento::create($validatedData);

        // Si el request contiene un archivo llamado 'imagen', se guarda en el storage
        if ($request->hasFile('imagen')) {
            $evento->addMediaFromRequest('imagen')->toMediaCollection('imagenes_eventos');
        }

        return new EventoResource($evento->load(['categoria', 'organizador']));
    }

    public function show(Evento $evento)
    {
        return new EventoResource(
            $evento->load(['categoria', 'organizador', 'reservas'])
        );
    }

    public function update(\App\Http\Requests\UpdateEventoRequest $request, Evento $evento)
    {
        $evento->update($request->validated());

        // Si se envia una nueva imagen, reemplaza la anterior
        if ($request->hasFile('imagen')) {
            $evento->media()->delete(); // Borra las imagenes previas
            $evento->addMediaFromRequest('imagen')->toMediaCollection('imagenes_eventos');
        }

        return new EventoResource(
            $evento->load(['categoria', 'organizador'])
        );
    }

    public function destroy(Evento $evento)
    {
        // Sacamos el ID del usuario que esta logueado
        $currentUserId = \Illuminate\Support\Facades\Auth::id();
        $userRole = \Illuminate\Support\Facades\Auth::user()->rol;

        // Si es admin le dejamos borrar (opcional, pero suele ser util)
        if ($userRole === 'admin') {
            $evento->delete();
            return response()->noContent();
        }

        // Si no es el dueño, no le dejamos borrar y mandamos un error 403
        // Comparamos el ID del usuario con el id_organizador del evento
        if ($currentUserId !== $evento->id_organizador) {
            return response()->json(['message' => 'No puedes borrar un evento que no es tuyo'], 403);
        }

        $evento->delete();

        return response()->noContent();
    }
}
