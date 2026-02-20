<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePagosRequest;
use App\Http\Requests\UpdatePagosRequest;
use App\Http\Resources\PagosResource;
use App\Models\pagos;

class PagosController extends Controller
{
    public function index()
    {
        $orderColumn = request('order_column', 'created_at');
        if (!in_array($orderColumn, ['id_pago', 'monto', 'fecha_pago', 'estado', 'created_at'])) {
            $orderColumn = 'created_at';
        }
        $orderDirection = request('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        $pagos = pagos::
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

    public function store(StorePagosRequest $request)
    {
        // $this->authorize('pagos-create');
        $pago = pagos::create($request->validated());

        return new PagosResource($pago);
    }

    public function show(pagos $pago)
    {
        // $this->authorize('pagos-edit');
        return new PagosResource($pago);
    }

    public function update(pagos $pago, UpdatePagosRequest $request)
    {
        // $this->authorize('pagos-edit');
        $pago->update($request->validated());

        return new PagosResource($pago);
    }

    public function destroy(pagos $pago)
    {
        // $this->authorize('pagos-delete');
        $pago->delete();

        return response()->noContent();
    }
}
