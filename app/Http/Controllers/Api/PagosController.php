<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePagosRequest;
use App\Http\Requests\UpdatePagosRequest;
use App\Http\Resources\PagosResource;
use App\Models\pagos;

// Controlador para gestionar las operaciones de pagos a traves de la API
class PagosController extends Controller
{
    /**
     * Muestra una lista de pagos con soporte para filtros y ordenacion.
     * 
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        // Define la columna por la que se ordenara, por defecto 'created_at'
        $orderColumn = request('order_column', 'created_at');
        // Valida que la columna solicitada este permitida para evitar errores
        if (!in_array($orderColumn, ['id_pago', 'monto', 'fecha_pago', 'estado', 'created_at'])) {
            $orderColumn = 'created_at';
        }

        // Define la direccion de la ordenacion (ascendente o descendente)
        $orderDirection = request('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        // Realiza la consulta aplicando filtros de busqueda si existen
        $pagos = pagos::
            when(request('search_id'), function ($query) {
                // Filtra por un ID de pago especifico
                $query->where('id_pago', request('search_id'));
            })
            ->when(request('search_global'), function ($query) {
                // Realiza una busqueda global por ID o por el estado del pago
                $query->where(function ($q) {
                    $q->where('id_pago', request('search_global'))
                        ->orWhere('estado', 'like', '%' . request('search_global') . '%');
                });
            })
            ->orderBy($orderColumn, $orderDirection)
            ->paginate(50); // Pagina los resultados de 50 en 50

        // Retorna la coleccion de pagos convertida a formato API mediante PagosResource
        return PagosResource::collection($pagos);
    }

    /**
     * Almacena un nuevo registro de pago en la base de datos.
     *
     * @param StorePagosRequest $request Datos validados del nuevo pago
     * @return PagosResource El pago recien creado
     */
    public function store(StorePagosRequest $request)
    {
        // Crea el pago utilizando los datos validados del request
        $pago = pagos::create($request->validated());

        return new PagosResource($pago);
    }

    /**
     * Muestra la informacion detallada de un pago especifico.
     *
     * @param pagos $pago Instancia del modelo pago inyectada automaticamente
     * @return PagosResource
     */
    public function show(pagos $pago)
    {
        return new PagosResource($pago);
    }

    /**
     * Actualiza la informacion de un pago existente en la base de datos.
     *
     * @param pagos $pago Instancia del pago a actualizar
     * @param UpdatePagosRequest $request Nuevos datos validados
     * @return PagosResource
     */
    public function update(pagos $pago, UpdatePagosRequest $request)
    {
        // Actualiza el registro con la informacion proporcionada
        $pago->update($request->validated());

        return new PagosResource($pago);
    }

    /**
     * Elimina un registro de pago de la base de datos.
     *
     * @param pagos $pago Instancia del pago a eliminar
     * @return \Illuminate\Http\Response Respuesta vacia con codigo 204
     */
    public function destroy(pagos $pago)
    {
        // Elimina el registro de pago
        $pago->delete();

        // Retorna una respuesta de exito sin contenido
        return response()->noContent();
    }
}
