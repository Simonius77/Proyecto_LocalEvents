<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventoRequest;
use App\Http\Requests\UpdateEventoRequest;
use App\Http\Resources\EventoResource;
use App\Models\evento;

class EventoController extends Controller
{
    public function index()
    {
        $orderColumn = request('order_column', 'created_at');
        if (!in_array($orderColumn, ['id_evento', 'nombre', 'fecha_inicio', 'created_at'])) {
            $orderColumn = 'created_at';
        }
        $orderDirection = request('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }
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

    public function store(StoreEventoRequest $request)
    {
        // $this->authorize('evento-create');
        $evento = evento::create($request->validated());

        return new EventoResource($evento);
    }

    public function show(evento $evento)
    {
        // $this->authorize('evento-edit');
        return new EventoResource($evento);
    }

    public function update(evento $evento, UpdateEventoRequest $request)
    {
        // $this->authorize('evento-edit');
        $evento->update($request->validated());

        return new EventoResource($evento);
    }

    public function destroy(evento $evento)
    {
        // $this->authorize('evento-delete');
        $evento->delete();

        return response()->noContent();
    }

    public function getList()
    {
        return EventoResource::collection(evento::all());
    }
}
