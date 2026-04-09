<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePagosRequest;
use App\Http\Requests\UpdatePagosRequest;
use App\Http\Resources\PagosResource;
use App\Models\Pagos;

// Me encargo de gestionar los pagos de los usuarios a traves de la API
class PagosController extends Controller
{
    /**
     * Saco la lista de pagos con filtros para que el admin pueda revisarlos
     */
    public function index()
    {
        // Elijo por que columna ordenar los pagos
        $orderColumn = request('order_column', 'created_at');
        if (!in_array($orderColumn, ['id_pago', 'monto', 'fecha_pago', 'estado', 'created_at'])) {
            $orderColumn = 'created_at';
        }

        $orderDirection = request('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        // Busco los pagos aplicando los filtros del buscador
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
     * Guardo un pago nuevo en el sistema
     */
    public function store(StorePagosRequest $request)
    {
        // Registro el pago con los datos validados
        $pago = Pagos::create($request->validated());

        return new PagosResource($pago);
    }

    /**
     * Enseño los detalles de un pago especifico
     */
    public function show(Pagos $pago)
    {
        return new PagosResource($pago);
    }

    /**
     * Actualizo los datos de un pago (como cambiar su estado)
     */
    public function update(Pagos $pago, UpdatePagosRequest $request)
    {
        // Guardo los cambios en los datos del pago
        $pago->update($request->validated());

        return new PagosResource($pago);
    }

    /**
     * Borro el registro del pago de la base de datos
     */
    public function destroy(Pagos $pago)
    {
        $pago->delete();
        return response()->noContent();
    }
}
