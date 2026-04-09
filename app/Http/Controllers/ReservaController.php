<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservaRequest;
use App\Http\Resources\ReservaResource;
use App\Models\Reserva;
use Illuminate\Http\Request;

// Controlador para gestionar las reservas desde la web (fuera de la API)
class ReservaController extends Controller
{
    /**
     * Saco la lista de todas las reservas con buscador y paginacion
     */
    public function index()
    {
        // Elijo como ordenar las reservas
        $orderColumn = request('order_column', 'created_at');
        if (!in_array($orderColumn, ['id_reserva', 'fecha_reserva', 'estado', 'created_at'])) {
            $orderColumn = 'created_at';
        }

        $orderDirection = request('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        // Busco las reservas filtrando segun lo que el usuario escriba
        $reservas = Reserva::
            when(request('search_id'), function ($query) {
                $query->where('id_reserva', request('search_id'));
            })
            ->when(request('search_global'), function ($query) {
                $query->where(function ($q) {
                    $q->where('id_reserva', request('search_global'))
                        ->orWhere('estado', 'like', '%' . request('search_global') . '%');
                });
            })
            ->orderBy($orderColumn, $orderDirection)
            ->paginate(50);

        return ReservaResource::collection($reservas);
    }

    /**
     * Guardo una reserva nueva en la base de datos
     */
    public function store(StoreReservaRequest $request)
    {
        // Registro la reserva con los datos que han pasado la validacion
        $reserva = Reserva::create($request->validated());

        return new ReservaResource($reserva);
    }

    /**
     * Enseño los datos de una sola reserva por su ID
     */
    public function show(Reserva $reserva)
    {
        return new ReservaResource($reserva);
    }

    /**
     * Cambio los datos de una reserva que ya tenemos guardada
     */
    public function update(Reserva $reserva, StoreReservaRequest $request)
    {
        // Actualizo la reserva con lo que me mandan ahora
        $reserva->update($request->validated());

        return new ReservaResource($reserva);
    }

    /**
     * Borro la reserva de la base de datos para siempre
     */
    public function destroy(Reserva $reserva)
    {
        $reserva->delete();

        return response()->noContent();
    }
}
