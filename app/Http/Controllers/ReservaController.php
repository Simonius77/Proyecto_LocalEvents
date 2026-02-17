<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservaRequest;
use App\Http\Resources\ReservaResource;
use App\Models\reserva;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    public function index()
    {
        $orderColumn = request('order_column', 'created_at');
        if (!in_array($orderColumn, ['id_reserva', 'fecha_reserva', 'estado', 'created_at'])) {
            $orderColumn = 'created_at';
        }
        $orderDirection = request('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        $reservas = reserva::
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

    public function store(StoreReservaRequest $request)
    {
        // $this->authorize('reserva-create'); // Commented out until permissions are set up
        $reserva = reserva::create($request->validated());

        return new ReservaResource($reserva);
    }

    public function show(reserva $reserva)
    {
        // $this->authorize('reserva-edit'); // Commented out until permissions are set up
        return new ReservaResource($reserva);
    }

    public function update(reserva $reserva, StoreReservaRequest $request)
    {
        // $this->authorize('reserva-edit'); // Commented out until permissions are set up
        $reserva->update($request->validated());

        return new ReservaResource($reserva);
    }

    public function destroy(reserva $reserva)
    {
        // $this->authorize('reserva-delete'); // Commented out until permissions are set up
        $reserva->delete();

        return response()->noContent();
    }
}
