<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Resources\CategoriaResource;
use App\Models\Categoria;

// Gestiono las categorias de los eventos a traves de la API
class CategoriaController extends Controller
{
    /**
     * Saco la lista de todas las categorias con filtros y paginacion
     */
    public function index()
    {
        // Elijo como ordenar los resultados
        $orderColumn = request('order_column', 'created_at');
        if (!in_array($orderColumn, ['id_categoria', 'nombre', 'created_at'])) {
            $orderColumn = 'created_at';
        }

        $orderDirection = request('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        // Busco las categorias segun lo que el usuario pida
        $categories = Categoria::
            when(request('search_id'), function ($query) {
                $query->where('id_categoria', request('search_id'));
            })
            ->when(request('search_title'), function ($query) {
                $query->where('nombre', 'like', '%' . request('search_title') . '%');
            })
            ->when(request('search_global'), function ($query) {
                $query->where(function ($q) {
                    $q->where('id_categoria', request('search_global'))
                        ->orWhere('nombre', 'like', '%' . request('search_global') . '%');

                });
            })
            ->orderBy($orderColumn, $orderDirection)
            ->paginate(50);

        return CategoriaResource::collection($categories);
    }

    /**
     * Guardo una categoria nueva si el usuario tiene permiso
     */
    public function store(StoreCategoriaRequest $request)
    {
        $this->authorize('category-create');

        // Creo la categoria con los datos validados
        $category = Categoria::create($request->validated());

        return new CategoriaResource($category);
    }

    /**
     * Enseño los detalles de una categoria especifica
     */
    public function show(Categoria $category)
    {
        $this->authorize('category-edit');
        return new CategoriaResource($category);
    }

    /**
     * Actualizo los datos de una categoria que ya existe
     */
    public function update(Categoria $category, StoreCategoriaRequest $request)
    {
        $this->authorize('category-edit');

        // Cambio los datos de la categoria
        $category->update($request->validated());

        return new CategoriaResource($category);
    }

    /**
     * Borro la categoria para siempre de la base de datos
     */
    public function destroy(Categoria $category)
    {
        $this->authorize('category-delete');
        $category->delete();

        return response()->noContent();
    }

    /**
     * Saco la lista completa de categorias sin paginar para los selectores
     */
    public function getList()
    {
        return CategoriaResource::collection(Categoria::all());
    }
}
