<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventoRequest;
use App\Http\Requests\UpdateEventoRequest;
use App\Http\Resources\EventoResource;
use App\Models\evento;
use Illuminate\Http\Request;

// Controlador para gestionar los eventos a traves de la API
class EventoController extends Controller
{
    /**
     * Lista todos los eventos con filtros de busqueda y paginacion.
     */
    public function index()
    {
        // Ordenacion por columna y direccion
        $orderColumn = request('order_column', 'created_at');
        if (!in_array($orderColumn, ['id_evento', 'nombre', 'fecha_inicio', 'created_at'])) {
            $orderColumn = 'created_at';
        }
        $orderDirection = request('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        // Consulta con filtros para ID, Titulo o Busqueda Global
        $eventos = evento::
            when(request('search_id'), function ($query) {
                $query->where('id_evento', request('search_id'));
            })
            ->when(request('search_title'), function ($query) {
                $query->where('nombre', 'like', '%' . request('search_title') . '%');
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
     * Guarda un nuevo evento y procesa la imagen si se envia.
     */
    public function store(StoreEventoRequest $request)
    {
        // Agregamos el id_organizador a los datos validados usando el ID del usuario logueado
        $validatedData = $request->validated();
        $validatedData['id_organizador'] = \Illuminate\Support\Facades\Auth::id();

        // Crea el evento con los datos validados
        $evento = evento::create($validatedData);

        // Si el request contiene un archivo llamado 'imagen', se guarda en el storage
        if ($request->hasFile('imagen')) {
            $evento->addMediaFromRequest('imagen')->toMediaCollection('imagenes_eventos');
        }

        return new EventoResource($evento);
    }

    /**
     * Muestra la informacion de un evento especifico.
     */
    public function show(evento $evento)
    {
        return new EventoResource($evento);
    }

    /**
     * Actualiza un evento existente y su imagen.
     */
    public function update(evento $evento, UpdateEventoRequest $request)
    {
        // Actualiza los campos de texto
        $evento->update($request->validated());

        // Si se envia una nueva imagen, reemplaza la anterior
        if ($request->hasFile('imagen')) {
            $evento->media()->delete(); // Borra las imagenes previas
            $evento->addMediaFromRequest('imagen')->toMediaCollection('imagenes_eventos');
        }

        return new EventoResource($evento);
    }

    /**
     * Elimina un evento de la base de datos.
     */
    public function destroy(evento $evento)
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

    /**
     * Obtiene una lista simple de todos los eventos.
     */
    public function getList()
    {
        return EventoResource::collection(evento::all());
    }
}
