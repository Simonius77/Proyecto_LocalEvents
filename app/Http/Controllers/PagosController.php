<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePagosRequest;
use App\Http\Resources\PagosResource;
use App\Models\Pagos;
use Illuminate\Http\Request;

// Controlador para gestionar los pagos desde la parte web del sistema
class PagosController extends Controller
{
    /**
     * Saco la lista de pagos con posibilidad de buscar y ordenar
     */
    public function index()
    {
        // Elijo por que columna queremos ordenar la lista
        $orderColumn = request('order_column', 'created_at');
        if (!in_array($orderColumn, ['id_pago', 'monto', 'fecha_pago', 'estado', 'created_at'])) {
            $orderColumn = 'created_at';
        }
        
        $orderDirection = request('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        // Busco los pagos en la base de datos aplicando los filtros
        $pagos = Pagos::
            when(request('search_id'), function ($query) {
                $query->where('id_pago', request('search_id'));
            })
            ->when(request('search_global'), function ($query) {
                $query->where(function ($q) {
                    $q->where('id_pago', request('search_global'))
                        ->orWhere('estado', 'like', '%' . request('search_global') . '%');
                });
            })
            ->orderBy($orderColumn, $orderDirection)
            ->paginate(50);

        return PagosResource::collection($pagos);
    }

    /**
     * Guardo un nuevo registro de pago
     */
    public function store(StorePagosRequest $request)
    {
        // Registro el pago con los datos que ya estan validados
        $pago = Pagos::create($request->validated());

        return new PagosResource($pago);
    }

    /**
     * Enseño los datos de un pago concreto
     */
    public function show(Pagos $pago)
    {
        return new PagosResource($pago);
    }

    /**
     * Actualizo la informacion de un pago que ya existe
     */
    public function update(Pagos $pago, StorePagosRequest $request)
    {
        // Guardo los nuevos datos para el pago
        $pago->update($request->validated());

        return new PagosResource($pago);
    }

    /**
     * Borro el pago de nuestra base de datos
     */
    public function destroy(Pagos $pago)
    {
        $pago->delete();

        return response()->noContent();
    }
}
